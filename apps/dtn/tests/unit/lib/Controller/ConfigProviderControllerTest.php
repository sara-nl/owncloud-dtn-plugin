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

use Test\TestCase;
use OCP\IUser;
use OCP\IRequest;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\IConfig;
use OCP\ILogger;
use OCA\DTN\Controller\ConfigProviderController;

/**
 * Description of ConfigProviderControllerTest
 *
 * @author antoonp
 */
class ConfigProviderControllerTest extends TestCase {

    /** @var string */
    private $appName = 'dtn';

    /** @var IRequest */
    private $request;

    /** @var IUser */
    private $user;

    /** @var IUserManager */
    private $userManager;

    /** @var IUserSession */
    private $userSession;

    /** @var IConfig */
    private $config;

    /** @var ILogger logger */
    private $logger;

    /** @var ConfigProviderController */
    private $configProviderController;

    protected function setUp() {
        $this->request = $this->getMockBuilder('\OCP\IRequest')
                ->disableOriginalConstructor()
                ->getMock();
        /* Setup user */
        $this->user = $this->createMock('\OCP\IUser');
        $this->user->expects($this->any())
                ->method('getUID')
                ->will($this->returnValue('admin'));
        $this->userManager = $this->createMock('OCP\IUserManager');
        $this->userSession = $this->createMock('OCP\IUserSession');
        $this->config = $this->createMock('OCP\IConfig');
        $this->logger = $this->createMock('OCP\ILogger');


        /**
         * 
         * @param type $appName
         * @param IRequest $request
         * @param IUserManager $userManager
         * @param IUserSession $userSession
         * @param IConfig $config
         * @param ILogger $logger
         */
        $this->configProviderController = new ConfigProviderController(
                $this->appName, $this->request, $this->userManager, $this->userSession, $this->config, $this->logger
        );
    }

    public function testGetDataLocationInfo() {
        /* no id provided must return message key */
        $_result = $this->configProviderController->getDataLocationInfo('admin');
        $this->assertArrayHasKey('message', $_result);
    }

}
