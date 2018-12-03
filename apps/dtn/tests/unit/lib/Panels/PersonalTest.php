<?php

/**
 * SURFsara
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
