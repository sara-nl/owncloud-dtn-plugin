<?php

/**
 * @copyright (c) 2018, SURFsara
 */

namespace OCA\DTN\Tests;

/**
 * Description of NotifierTest
 *
 * @author antoonp
 */
use OCA\DTN\Notifier;
use PHPUnit_Framework_MockObject_MockObject;

class NotifierTest extends \Test\TestCase {

    /** @var Notifier */
    protected $notifier;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $factory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $il10n;

    protected function setUp() {
        parent::setUp();
        $this->factory = $this->createMock(\OCP\L10N\IFactory::class);
        $this->il10n = $this->createMock(\OCP\IL10N::class);
        $this->factory->expects($this->any())
                ->method('get')
                ->will($this->returnValue($this->il10n));
        $this->notifier = new Notifier($this->factory);
    }

    public function testPrepare() {
        $_notification = $this->createMock(\OCP\Notification\INotification::class);
        $_notification->expects($this->any())
                ->method('getApp')
                ->will($this->returnValue('dtn'));
        $_notification->expects($this->any())
                ->method('getObjectType')
                ->will($this->returnValue('dtn'));

        $_result = $this->notifier->prepare($_notification, 'EN_en');
        $this->assertNotNull($_result);
    }

}
