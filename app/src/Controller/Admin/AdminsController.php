<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Utility\Inflector;
use Cake\Event\EventInterface;


/**
 * Admins Controller
 */
class AdminsController extends \App\Controller\AppController
{

    /**
     * @param Cake\Event\EventInterface $event;
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->modelName = Inflector::camelize($this->modelClass);
        parent::beforeFilter($event);
        // Viewに渡す
        $this->set('ModelName', $this->modelName);
        $this->Auth->deny(['index', 'logout']);
    }

    /**
     * Admin dashboard or login screen
     */
    public function index()
    {
        $layout = "plain";
        $view = "login";

        $users = [];

        if ($this->request->is(['post', 'put'])) {
            $users = $this->Auth->identify();
            if ($users) {
                $this->Auth->setUser($users);
            }

            if (empty($users) || !$users) {
                $this->Flash->set('アカウント名またはパスワードが違います', [
                    'element' => 'error'
                ]);
            }
        }
        if ($this->isLogin()) {
            $layout = "admin";
            $view = "index";
        }
        // Gbiz
        // else {
        //     $state = sha1(md5(uniqid(openssl_random_pseudo_bytes(256), true)));
        //     $nonce = sha1(md5(uniqid(openssl_random_pseudo_bytes(256), true)));

        //     if (isset($_GET['code'])) {
        //         $get_token = $this->curl_Gbiz($_GET['code']);

        //         if (isset($get_token['error']) || !isset($get_token["access_token"]) || empty($get_token["access_token"])) {
        //             $this->redirect('/admin/');
        //         } else {
        //             $data = $this->curl_Gbiz(null, $get_token["access_token"]);
        //         }
        //     } else {
        //         $this->redirect(__('https://stg.gbiz-id.go.jp/oauth/authorize?client_id=200137testsinseitochigikansentaisakucom&response_type=code&state={0}&scope=openid%20email%20profile&nonce={1}', $state, $nonce));
        //     }
        // }
        $this->render($view, $layout);
    }

    /**
     * Index method
     */
    public function logout()
    {
        $this->redirect($this->Auth->logout());
    }
}
