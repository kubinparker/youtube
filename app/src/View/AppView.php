<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\View;

use Cake\View\View;
use Cake\View\Helper\HtmlHelper;

/**
 * Application View
 *
 * Your application's default view class
 *
 * @link https://book.cakephp.org/4/en/views.html#the-app-view
 */
class AppView extends View
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // $query = $this->request->getQueryParams();

        /**
         * Pagination
         */
        $prefix = $this->request->getParam('prefix');
        $config = [
            'number' => '<a class="button button-clear" href="{{url}}">{{text}}</a>',
            'first' => '<a class="button button-clear" href="{{url}}">{{text}}</a>',
            'last' => '<a class="button button-clear" href="{{url}}">{{text}}</a>',
            'nextActive' => '<a class="button button-clear" href="{{url}}">{{text}}</a>',
            'prevActive' => '<a class="button button-clear" href="{{url}}">{{text}}</a>',
            'nextDisabled' => '<a class="button button-clear" disabled>{{text}}</a>',
            'prevDisabled' => '<a class="button button-clear" disabled">{{text}}</a>',
            'current' => '<a class="button page-current" disabled>{{text}}</a>',
        ];
        if ($prefix !== 'Admin') {
            $config = [
                'number' => '<a class="button button-clear" href="{{url}}">{{text}}</a>',
                'first' => '<a class="button button-clear" href="{{url}}">{{text}}</a>',
                'last' => '<a class="button button-clear" href="{{url}}">{{text}}</a>',
                'nextActive' => '<a class="button button-clear" href="{{url}}">{{text}}</a>',
                'prevActive' => '<a class="button button-clear" href="{{url}}">{{text}}</a>',
                'nextDisabled' => '<a class="button button-clear">{{text}}</a>',
                'prevDisabled' => '<a class="button button-clear">{{text}}</a>',
                'current' => '<a class="button page-current">{{text}}</a>',
            ];
        }
        $this->Paginator->setTemplates($config);

        // $this->loadHelper('Form', ['templates' => ['error' => '<div class="message error" id="{{id}}">{{content}}</div>']]);
    }
}
