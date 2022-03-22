<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;
use TheNetworg\OAuth2\Client\Provider\Azure;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{
    /**
     * Displays a view
     *
     * @param string ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\View\Exception\MissingTemplateException When the view file could not
     *   be found and in debug mode.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found and not in debug mode.
     * @throws \Cake\View\Exception\MissingTemplateException In debug mode.
     */
    public function display(string ...$path): ?Response
    {
        if (!$path) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            return $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }

    public function author()
    {
    }

    public function author1()
    {

        $provider = new Azure([
            'clientId'          => 'a841f706-2ab7-4710-b80f-3a668e4a2fd1',
            'clientSecret'      => 'hPH7Q~nr-4xn.ztK6dcFU9Bsxp4IlBej0QfIm',
            'redirectUri'       => 'https://gmo29.caters.jp/author1',
            //Optional
            'scopes'            => ['user.read', 'calendars.read', 'calendars.readwrite', 'OnlineMeetings.read', 'OnlineMeetings.readwrite',],
            //Optional
            'defaultEndPointVersion' => '2.0'
        ]);

        // Set to use v2 API, skip the line or set the value to Azure::ENDPOINT_VERSION_1_0 if willing to use v1 API
        $provider->defaultEndPointVersion = Azure::ENDPOINT_VERSION_2_0;

        $baseGraphUri = $provider->getRootMicrosoftGraphUri(null);
        $provider->scope = ['openid profile email offline_access ' . $baseGraphUri . '/User.Read', __('{0}/Calendars.Read', $baseGraphUri)];
        if (isset($_GET['code']) && $this->Session->check('OAuth2.state') && isset($_GET['state'])) {

            if ($_GET['state'] == $this->Session->read('OAuth2.state')) {
                $this->Session->delete('OAuth2.state');

                // Try to get an access token (using the authorization code grant)
                /** @var AccessToken $token */
                $token = $provider->getAccessToken('authorization_code', [
                    'scope' => $provider->scope,
                    'code' => $_GET['code'],
                ]);

                $this->Session->write('token', $token);
            } else {
                $this->redirect(['action' => 'author']);
            }
        } else {
            // Check local server's session data for a token
            // and verify if still valid 
            /** @var ?AccessToken $token */
            $token = $this->Session->read('token');
            //
            if (isset($token) && $token) {
                if ($token->hasExpired()) {
                    if (!is_null($token->getRefreshToken())) {
                        $token = $provider->getAccessToken('refresh_token', [
                            'scope' => $provider->scope,
                            'refresh_token' => $token->getRefreshToken()
                        ]);
                    } else {
                        $token = null;
                    }
                }
            }
            //
            // If the token is not found in 
            // if (!isset($token)) {
            $authorizationUrl = $provider->getAuthorizationUrl(['scope' => $provider->scope]);
            $this->Session->write(['OAuth2.state' => $provider->getState()]);

            header('Location: ' . $authorizationUrl);
            exit;
            // }
        }

        // $me = $provider->get($provider->getRootMicrosoftGraphUri($token) . '/v1.0/me/events', $token);

        $this->set('access_token', $token->getToken());
    }
}
