<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Model\Table\AppTable;

/**
 * ChannelCategories Model
 *
 *
 * @method \App\Model\Entity\ChannelCategory newEmptyEntity()
 * @method \App\Model\Entity\ChannelCategory newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ChannelCategory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ChannelCategory get($primaryKey, $options = [])
 * @method \App\Model\Entity\ChannelCategory findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ChannelCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ChannelCategory[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ChannelCategory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ChannelCategory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 */
class ChannelCategoriesTable extends AppTable
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

        $this->setTable('channel_categories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->scalar('channel_category_name')
            ->maxLength('channel_category_name', 255, '255文字数以内を入力してください。')
            ->requirePresence('channel_category_name', 'create', '必要な項目です。')
            ->notEmptyString('channel_category_name', '入力してください。');

        return $validator;
    }
}
