<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ChannelsFixture
 */
class ChannelsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'code' => 'Lorem ipsum dolor sit amet',
                'channel_name' => 'Lorem ipsum dolor sit amet',
                'count_register' => 1,
                'thumbnail_default' => 'Lorem ipsum dolor sit amet',
                'thumbnail_medium' => 'Lorem ipsum dolor sit amet',
                'thumbnail_high' => 'Lorem ipsum dolor sit amet',
                'created_at' => '2022-03-16 09:16:31',
                'updated_at' => '2022-03-16 09:16:31',
            ],
        ];
        parent::init();
    }
}
