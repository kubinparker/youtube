<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ChannelsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ChannelsTable Test Case
 */
class ChannelsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ChannelsTable
     */
    protected $Channels;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Channels',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Channels') ? [] : ['className' => ChannelsTable::class];
        $this->Channels = $this->getTableLocator()->get('Channels', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Channels);

        parent::tearDown();
    }
}
