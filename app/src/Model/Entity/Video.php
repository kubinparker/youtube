<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Video Entity
 *
 * @property int $id
 * @property string $code
 * @property string $title
 * @property string $thumbnail_default
 * @property string $thumbnail_medium
 * @property string $thumbnail_high
 * @property \Cake\I18n\FrozenTime $published_at
 * @property int $view_counts
 * @property string $channel_code
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 */
class Video extends Entity
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
        'title' => true,
        'thumbnail_default' => true,
        'thumbnail_medium' => true,
        'thumbnail_high' => true,
        'published_at' => true,
        'view_counts' => true,
        'channel_code' => true,
        'created_at' => true,
        'updated_at' => true,
    ];
}
