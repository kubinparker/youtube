<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * VideosFixture
 */
class VideosFixture extends TestFixture
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
                'title' => 'Lorem ipsum dolor sit amet',
                'thumbnail_default' => 'Lorem ipsum dolor sit amet',
                'thumbnail_medium' => 'Lorem ipsum dolor sit amet',
                'thumbnail_high' => 'Lorem ipsum dolor sit amet',
                'published_at' => '2022-03-16 09:15:00',
                'view_counts' => 1,
                'channel_code' => 'Lorem ipsum dolor sit amet',
                'created_at' => '2022-03-16 09:15:00',
                'updated_at' => '2022-03-16 09:15:00',
            ],
        ];
        parent::init();
    }
}
