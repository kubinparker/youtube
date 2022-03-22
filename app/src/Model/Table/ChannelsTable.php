<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

use App\Model\Table\AppTable;


/**
 * Channels Model
 *
 * @method \App\Model\Entity\Channel newEmptyEntity()
 * @method \App\Model\Entity\Channel newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Channel[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Channel get($primaryKey, $options = [])
 * @method \App\Model\Entity\Channel findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Channel patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Channel[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Channel|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Channel saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Channel[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Channel[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Channel[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Channel[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ChannelsTable extends AppTable
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

        $this->setTable('channels');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }
}
