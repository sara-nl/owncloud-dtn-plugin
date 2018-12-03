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

namespace OCA\DTN\Tests\Controller;

/**
 * Description of DtnSettingsControllerTest
 * @author antoonp
 */
use OCA\DTN\Controller\DtnSettingsController;
use OCP\IConfig;
use OCP\IUserManager;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\ILogger;

class DtnSettingsControllerTest extends \Test\TestCase {

    /** @var IRequest | \PHPUnit_Framework_MockObject_MockObject */
    private $request;

    /** @var IConfig | \PHPUnit_Framework_MockObject_MockObject */
    private $config;

    /** @var IUserManager | \PHPUnit_Framework_MockObject_MockObject */
    private $userManager;

    /** @var IUser */
    protected $user;

    /** @var IUserSession | \PHPUnit_Framework_MockObject_MockObject */
    private $userSession;

    /** @var ILogger | \PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    public function setUp() {
        parent::setUp();
        $this->config = $this->createMock(IConfig::class);
        $this->userManager = $this->createMock(IUserManager::class);
        $this->userSession = $this->getMockBuilder(IUserSession::class)
                ->disableOriginalConstructor()
                ->getMock();
        $_user = $this->createMock('\OCP\IUser');
        $_user->expects($this->any())
                ->method('getUID')
                ->will($this->returnValue('someUserId'));
        $this->userSession
                ->expects($this->any())
                ->method('getUser')
                ->will($this->returnValue($_user));
        $this->logger = $this->createMock(ILogger::class);
    }

    public function testSetUserSetting() {
        $this->getDtnSettingsController()->setUserSetting('1', 'one');
        $this->assertEquals('just no exception', 'just no exception');
    }

    public function testSetAdminSetting() {
        $this->getDtnSettingsController()->setAdminSetting('1', 'one');
        $this->assertEquals('just no exception', 'just no exception');
    }

    private function getDtnSettingsController($request = NULL) {
        $this->request = $request === NULL ? $this->createMock(IRequest::class) : $request;
        return new DtnSettingsController(
                'dtn', $this->request, $this->userManager, $this->userSession, $this->config, $this->logger
        );
    }

}
