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

namespace OCA\DTN\Tests\Panels;

/**
 * Description of Personal
 *
 * @author antoonp
 */
use OCA\DTN\Panels\Personal;
use PHPUnit_Framework_MockObject_MockObject;

class PersonalTest extends \Test\TestCase {

    /** @var Personal */
    private $panel;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $config;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $userSession;

    public function setUp() {
        parent::setUp();
        $this->config = $this->getMockBuilder(\OCP\IConfig::class)->getMock();
        $this->userSession = $this->getMockBuilder(\OCP\IUserSession::class)->getMock();
        $_user = $this->createMock('\OCP\IUser');
        $_user->expects($this->any())
                ->method('getUID')
                ->will($this->returnValue('someUserId'));
        $this->userSession
                ->expects($this->any())
                ->method('getUser')
                ->will($this->returnValue($_user));
        $this->panel = new Personal(
                $this->config, $this->userSession);
    }

    public function testGetPanel() {
        $_templateHtml = $this->panel->getPanel()->fetchPage();
        $this->assertContains('<div id="dtnPluginUserSettings" class="dtn-settings section">', $_templateHtml);
    }

    public function testGetPriority() {
        $this->assertTrue(is_integer($this->panel->getPriority()));
    }

    public function testGetSectionID() {
        $this->assertEquals('additional', $this->panel->getSectionID());
    }

}
