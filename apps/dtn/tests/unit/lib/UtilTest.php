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
 * Description of UtilTest
 * 
 * @group 
 * @author antoonp
 */
use OCP\IUser;
use PHPUnit_Framework_MockObject_MockObject;

class UtilTest extends \Test\TestCase {

    /** @var IUser */
    protected $user;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $userManager;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $config;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $server;

    protected function setUp() {
        parent::setUp();
        $this->config = $this->createMock('OCP\IConfig');
        $this->server = $this->createMock(\OC\Server::class);
    }

    public function testFindUserForDTNUserId() {
        /* This user does not exist */
        $this->assertNull(\OCA\DTN\Util::findUserForDTNUserId('xyz'));
    }

}
