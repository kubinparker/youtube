<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Utility\Inflector;

class NewsController extends AppController
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
    }

    /**
     * 一覧画面のデータを習得機能（Index method）
     */
    public function index()
    {
        $this->setList();
        $this->filter_condition_construct = [
            'title' => [' like' => '%{title}%']
        ];

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
        $this->setList();
        // 一般の編集機能
        $this->set('news', parent::_edit($id));
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
