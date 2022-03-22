<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Model\Table\AppTable;

/**
 * VideoCatergories Model
 *
 *
 * @method \App\Model\Entity\VideoCatergory newEmptyEntity()
 * @method \App\Model\Entity\VideoCatergory newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\VideoCatergory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VideoCatergory get($primaryKey, $options = [])
 * @method \App\Model\Entity\VideoCatergory findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\VideoCatergory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\VideoCatergory[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VideoCatergory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VideoCatergory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VideoCatergory[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VideoCatergory[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\VideoCatergory[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VideoCatergory[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class VideoCategoriesTable extends AppTable
{
    public $attaches = [
        'images' => [],
        'files' => []
    ];
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('video_categories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Tree', [
            'parent' => 'category_parent_id', // Use this instead of parent_id
            'left' => 'tree_left', // Use this instead of lft
            'right' => 'tree_right', // Use this instead of rght
            'level' => 'level'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('video_catergory_name')
            ->maxLength('video_catergory_name', 255, '255文字数以内を入力してください。')
            ->requirePresence('video_catergory_name', 'create', '必要な項目です。')
            ->notEmptyString('video_catergory_name', '入力してください。');

        $validator
            ->integer('category_parent_id')
            ->allowEmptyString('category_parent_id', null, 'create');

        return $validator;
    }
}
