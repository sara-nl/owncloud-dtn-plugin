<?php

/**
 * Copyright 2018 SURFsara (http://www.surfsara.nl)
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
