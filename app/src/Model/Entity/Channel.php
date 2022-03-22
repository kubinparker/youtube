<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Channel Entity
 *
 * @property int $id
 * @property string $code
 * @property string $channel_name
 * @property int $count_register
 * @property string $thumbnail_default
 * @property string $thumbnail_medium
 * @property string $thumbnail_high
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime $updated_at
 */
class Channel extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'code' => true,
        'channel_name' => true,
        'count_register' => true,
        'thumbnail_default' => true,
        'thumbnail_medium' => true,
        'thumbnail_high' => true,
        'created_at' => true,
        'updated_at' => true,
    ];
}
