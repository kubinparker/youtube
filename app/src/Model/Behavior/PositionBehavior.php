<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use ArrayObject;
use Cake\ORM\Behavior;
use Cake\Event\EventInterface;
use Psy\Command\WhereamiCommand;
use Cake\Datasource\EntityInterface;
use Cake\Database\Expression\QueryExpression;



/**
 * Position Behavior.
 *
 * データ並び順を設定
 *
 */
class PositionBehavior extends Behavior
{

    /**
     * Defaults
     *
     * @var array
     */
    protected $_defaults = [
        'field' => 'position',
        'group' => [],
        'groupMove' => false,
        'order' => 'ASC',
        'recursive' => -1,
    ];
    protected $_defaultConfig = [
        'field' => 'position',
        'group' => [],
        'groupMove' => false,
        'order' => 'ASC',
        'recursive' => -1,
    ];


    protected $_old_position = 0;
    protected $_old_group_conditions = array();


    /**
     * Constructor hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     * @param array<string, mixed> $config The configuration settings provided to this behavior.
     * @return void
     */
    public function initialize(array $config): void
    {
        $model = parent::table();
        $this->_alias = $model->getAlias();
        $settings = $config + $this->_defaultConfig;
        $settings['Model'] = $model;
        $this->settings[$this->_alias] = $settings;
    }


    /**
     * 並び替えの有無
     * 
     * */
    public function enablePosition()
    {
        extract($this->settings[$this->_alias]);
        return ($field && $Model->getSchema()->getColumn($field));
    }


    /**
     * 並び順グループ設定
     *
     * */
    public function groupConditions($id)
    {
        extract($this->settings[$this->_alias]);
        $cond = [];

        if ($group && $id) {
            $group = (array) $group;
            $_cond = [$Model->aliasField($Model->getPrimaryKey()) => $id];

            $data = $Model->find('all', [
                'conditions' => $_cond,
                'recursive' => $recursive,
            ])->first();
            $model_name = $this->_alias;
            foreach ($group as $column) {
                if (strpos($column, '.') !== false) {
                    $_model = explode('.', $column);
                    if (count($_model) == 2) {
                        $model_name = $_model[0];
                        $column = $_model[1];
                    }
                }

                if ($data->has($column)) {
                    $cond[$model_name . '.' . $column] = $data->{$column};
                }
            }
        }
        return $cond;
    }


    /**
     * The display position of data is changed.
     * 並び順を変更する
     *
     * @param  Integer    $id  primary key
     * @param  String    $dir Moving direction
     *                   [top, bottom, up, down]
     * @return bool
     */
    public function movePosition($id, $dir)
    {
        extract($this->settings[$this->_alias]);

        if (!$this->enablePosition()) {
            return false;
        }
        $conditions = $this->groupConditions($id);

        $position_field = $Model->aliasField($field);
        $primary_key = $Model->aliasField($Model->getPrimaryKey());


        $data = $Model->find()->select([$primary_key, $position_field])->where([$primary_key => $id])->first();
        if ($data) {
            $position = $data->{$field};

            if ($dir === 'top') {
                $expression = new QueryExpression(__('{0} = {0} + 1', $field));
                $Model->updateAll([$expression], array_merge([$field . ' < ' => $position], $conditions));
                $Model->updateAll([$field => 1], [$primary_key => (int)$id]);
            } else if ($dir === 'bottom') {
                $count = $Model->find('all', [
                    'fields' => [$primary_key],
                    'conditions' => $conditions,
                    'recursive' => $recursive
                ])->count();
                $expression = new QueryExpression(__('{0} = {0} - 1', $field));
                $Model->updateAll([$expression], array_merge([$field . ' >' => $position], $conditions));
                $Model->updateAll([$field => $count], [$primary_key => (int)$id]);
            } else if ($dir === 'up') {
                if (1 < $position) {
                    $expression = new QueryExpression(__('{0} = {0} + 1', $field));
                    $Model->updateAll([$expression], array_merge([$position_field => $position - 1], $conditions));
                    $Model->updateAll([$field => $position - 1], [$primary_key => (int)$id]);
                }
            } else if ($dir === 'down') {
                $count = $Model->find('all', [
                    'fields' => [$primary_key],
                    'conditions' => $conditions,
                    'recursive' => $recursive
                ])->count();

                if ($position < $count) {
                    $expression = new QueryExpression(__('{0} = {0} - 1', $field));
                    $Model->updateAll([$expression], array_merge([$position_field => $position + 1], $conditions));
                    $Model->updateAll([$field => $position + 1], [$primary_key => (int)$id]);
                }
            } else {
                return false;
            }
            return true;
        }
        return false;
    }


    /**
     * グループ設定ありの並び順変更の有無
     * @param  Model  $Model [description]
     * @return [type]        [description]
     */
    public function enableGroupMove()
    {
        extract($this->settings[$this->_alias]);
        return $groupMove;
    }

    /**
     * 並び替えを再設定
     * @param \ArrayObject $conditions
     * */
    public function resetPosition($conditions = [])
    {
        extract($this->settings[$this->_alias]);
        if ($this->enablePosition()) {
            $model_name = $Model->alias;
            $position_field = $Model->aliasField($field);
            $primary_key = $Model->aliasField($Model->getPrimaryKey());

            $conditions = array_merge($this->groupConditions($id), $conditions);

            $position = 1;
            $data = $Model->find('all', array(
                'order' => $position_field . ' ' . $order,
                'conditions' => $conditions,
                'recursive' => $recursive
            ));
            foreach ($data as $value) {
                $value->{$position_field} = $position;
                $Model->save($value, ['conditions' => $conditions]);
                ++$position;
            }
        }
    }


    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        extract($this->settings[$this->_alias]);
        if ($this->enablePosition()) {
            $this->movePosition($entity->id, 'bottom');
        }
        return true;
    }


    /**
     * とりあえずUpdateAll関数使うとbeforeSave Callback関数が使えない
     * 
     * @param Cake\Event\EventInterface $event
     * @param Cake\Datasource\EntityInterface $entity
     * @param \ArrayObject $options
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        extract($this->settings[$this->_alias]);

        if ($entity->isNew()) {
            $entity->{$field} = 0;
        }
        if ($this->enablePosition() && !empty($group) && $this->enableGroupMove()) {
            // 保存前のデータ取得
            $primary_key = $Model->aliasField($Model->getPrimaryKey());

            if ($entity->id) {

                $old = $Model->find('all', array(
                    'conditions' => array($primary_key => $entity->id),
                ))->first();

                if (!empty($old)) {
                    // グループ変更チェック
                    $_isGroupUpdated = false;
                    foreach ($group as $_col) {
                        if ($Model->hasField($_col)) {
                            if ($entity->{$_col} != $old->{$_col}) {
                                $_isGroupUpdated = true;
                                break;
                            }
                        }
                    }
                    if ($_isGroupUpdated) {
                        foreach ($group as $_col) {
                            $this->_old_group_conditions[$Model->aliasField($_col)] = $old->{$_col};
                            $this->_old_position = $old->{$field};
                        }
                    }
                }
            }
        }

        return true;
    }


    /**
     * とりあえずUpdateAll関数使うとafterSave Callback関数が使えない
     * 
     * @param Cake\Event\EventInterface $event
     * @param Cake\Datasource\EntityInterface $entity
     * @param \ArrayObject $options
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        extract($this->settings[$this->_alias]);
        $created = $entity->isNew();
        if ($created) {
            if ($this->enablePosition()) {
                $primary_key = $Model->aliasField($Model->getPrimaryKey());
                $position_field = $Model->aliasField($field);
                $cond = $this->groupConditions($entity->id);

                $r = false;

                $save = array();
                if (strtoupper($order) === 'DESC') {
                    $query_ = $Model->find('all', [
                        'conditions' => $cond
                    ]);
                    $count = $query_->count();
                    $save = array($position_field => $count);
                    $cond = array($primary_key => $entity->id);
                } else {
                    $save = [new QueryExpression(__('{0} = {0} + 1', $field))];
                }
                return $Model->updateAll($save, $cond);
            }
        } else {
            if ($this->enablePosition() && !empty($group) && !empty($this->_old_group_conditions)) {
                $position_field = $Model->aliasField($field);

                $expression = new QueryExpression(__('{0} = {0} - 1', $field));

                // 保存前のグループの並び順
                $this->_old_group_conditions[$position_field . ' >'] = $this->_old_position;

                $Model->updateAll(
                    [$expression],
                    $this->_old_group_conditions
                );
                // 保存後のグループの並び順
                return $this->afterSave($event, $entity->setNew(true), $options);
            }
        }
    }
}
