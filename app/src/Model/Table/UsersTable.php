<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Model\Table\AppTable;
use \Cake\Event\EventInterface;
use \Cake\Datasource\EntityInterface;
use \ArrayObject;

/**
 * Users Model
 *
 *
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class UsersTable extends AppTable
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

        $this->setTable('users');
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
            ->scalar('login_id')
            ->maxLength('login_id', 20, '２０文字数以内を入力してください。')
            ->requirePresence('login_id', null, '入力してください。')
            ->notEmptyString('login_id', '入力してください。');

        $validator
            ->scalar('password')
            ->maxLength('password', 255, '２５５文字数以内を入力してください。')
            ->requirePresence('password', 'create', '入力してください。')
            ->notEmptyString('password', '入力してください。', 'create');

        $validator
            ->scalar('full_name')
            ->maxLength('full_name', 20, '２０文字数以内を入力してください。')
            ->requirePresence('full_name', null, '入力してください。')
            ->notEmptyString('full_name', '入力してください。');

        $validator
            ->scalar('email')
            ->maxLength('email', 200, '２００文字数以内を入力してください。')
            ->notEmptyString('email', '入力してください。')
            ->email('email', false, 'メールアドレスのフォーマットが間違います。');

        return $validator;
    }

    /**
     * Handles the saving of children associations and executing the afterSave logic
     * once the entity for this table has been saved successfully.
     *
     * @param \Cake\Event\EventInterface $event the entity to be saved
     * @param \Cake\Datasource\EntityInterface $entity the entity to be saved
     * @param \ArrayObject $options the options to use for the save operation
     * 
     */

    public function beforeSave($event, $entity, $options)
    {
        // encode pass
        parent::beforeSave($event, $entity, $options);
    }

    public function findAuth(\Cake\ORM\Query $query, array $options)
    {

        $query
            ->select()
            ->where();

        return $query;
    }
}
