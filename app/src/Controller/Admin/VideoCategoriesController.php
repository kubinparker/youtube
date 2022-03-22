<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Utility\Inflector;

/**
 * Categories Controller
 *
 * @property \App\Model\Table\VideoCategoriesTable $VideoCategories
 * @method \App\Model\Entity\VideoCatergory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class VideoCategoriesController extends AppController
{

    /**
     * @param Cake\Event\EventInterface $event;
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->modelName = Inflector::camelize($this->modelClass);
        parent::beforeFilter($event);

        // Once added, you can let CakePHP build the internal structure if the table is already holding some rows:
        // @https://book.cakephp.org/4/en/orm/behaviors/tree.html
        $this->{$this->modelName}->recover();

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
        $this->filter_condition_construct = [
            'video_catergory_name' => [' like' => '%{video_catergory_name}%'],
        ];
        $cond = $this->build_conditions(true);

        $data = $this->{$this->modelName}->find('treeList', ['valuePath' => 'video_catergory_name', 'spacer' => '＿＿'])->where($cond)->all();

        $this->set(
            $this->{$this->modelName}->getTable(),
            $data
        );
        // parent::_lists($cond, ['limit' => 20, 'order' => [$this->modelName . '.created_at' => 'ASC']]);
    }

    /**
     * Edit method
     *
     * @param string|null $id ChannelCategory id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = 0)
    {
        $this->setList();
        // 一般の編集機能

        if ($this->request->is(['post', 'put'])) {
            $category = $this->_detail($id);
            $data = $this->request->getData();
            // if has child and parent_id of this item and post data 'category_parent_id' is diffirent 
            if ($category && $category->category_parent_id != $data['category_parent_id'] && $this->{$this->modelName}->findByCategoryParentId($id)->count() > 0) {
                $this->Flash->error(__('更新できません。'));
                return $this->redirect(['action' => 'edit', $id]);
            }
        }

        $this->set('video_category', parent::_edit($id));
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
        $this->request->allowMethod(['get']);

        $category = $this->VideoCategories->get($id);
        // do not remove child not
        // $this->VideoCategories->removeFromTree($category); 
        if ($this->VideoCategories->delete($category)) {
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

        $list['parent_categories'] = $this->{$this->modelName}->find('treeList', ['valuePath' => 'video_catergory_name', 'spacer' => '＿＿'])->toArray();

        $this->set(array_keys($list), $list);
        return $list;
    }
}
