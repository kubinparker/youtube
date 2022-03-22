<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Utility\Inflector;

/**
 * Categories Controller
 *
 * @property \App\Model\Table\ChannelCategoriesTable $ChannelCategories
 * @method \App\Model\Entity\ChannelCategory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ChannelCategoriesController extends AppController
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
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->setList();
        $cond = $this->build_conditions(true);
        parent::_lists($cond, ['limit' => 20, 'order' => [$this->modelName . '.created_at' => 'ASC']]);
    }

    /**
     * Edit method
     *
     * @param string|null $id ChannelCategory id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->setList();
        // 一般の編集機能
        $this->set('channel_category', parent::_edit($id));
    }

    /**
     * Delete method
     *
     * @param string|null $id Category id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $category = $this->Categories->get($id);
        if ($this->Categories->delete($category)) {
            $this->Flash->success(__('The category has been deleted.'));
        } else {
            $this->Flash->error(__('The category could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    public function setList()
    {
        $list = [];
        $list['query_param'] = $this->request->getQueryParams();

        $this->set(array_keys($list), $list);
        return $list;
    }
}
