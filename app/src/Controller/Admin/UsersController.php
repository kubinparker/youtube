<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Utility\Inflector;

use Cake\Mailer\MailerAwareTrait;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    use MailerAwareTrait;

    /**
     * @param Cake\Event\EventInterface $event;
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->modelName = Inflector::camelize($this->modelClass);
        parent::beforeFilter($event);

        // Viewに渡す
        $this->set('ModelName', $this->modelName);
    }

    /**
     * 一覧画面のデータを習得機能（Index method）
     */
    public function index()
    {
        $this->setList();
        $this->filter_condition_construct = [
            'login_id' => [' like' => '%{login_id}%'],
            'full_name' => [' like' => '%{full_name}%'],
        ];

        if ($this->request->is(['post'])) {

            $data = $this->request->getData();

            $id = isset($data['id']) ? trim($data['id']) : false;

            if ($id && $user = parent::_detail($id)) {
                // メール送信する関数
                // this->getMailer('/app/src/Mailer/mail_config_file')->send('function_in_[mail_config_file]', [$user]);
                $this->getMailer('MailConfig')->send('user_info', [$user]);
            }
        }


        $cond = $this->build_conditions(true);


        parent::_lists($cond, ['limit' => 20, 'order' => [$this->modelName . '.created_at' => 'DESC']]);
    }


    /**
     * 編集機能（Edit method）
     *
     * @param integer|null $id.
     */
    public function edit($id = null)
    {
        // ユーザー変更する時、前のパスワードを使うかどうかを判断する
        if ($this->request->is(['post', 'put'])) {
            if (parent::_detail($id) && $data = $this->request->getData()) {
                if (isset($data['password']) && trim($data['password']) == '') {
                    $this->request = $this->request->withoutData('password');
                }
            }
        }
        // 一般の編集機能
        $this->set('user', parent::_edit($id));
    }


    /**
     * 削除機能（Delete method）
     *
     * @param integer|null $id.
     * Redirects to index.
     */
    public function delete($id, $type, $columns = null)
    {
        parent::_delete($id, $type);
    }

    protected function setList()
    {
        $list = [];

        $list['query_param'] = $this->request->getQueryParams();
        if (!empty($list)) {
            $this->set(array_keys($list), $list);
        }

        return $list;
    }
}
