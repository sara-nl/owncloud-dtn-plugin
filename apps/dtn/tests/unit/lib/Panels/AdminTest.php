<?php

/**
 * SURFsara
 */

namespace OCA\DTN\Tests\Panels;

/**
 * Description of AdminTest
 *
 * @author antoonp
 */
use OCA\DTN\Panels\Admin;
use PHPUnit_Framework_MockObject_MockObject;

class AdminTest extends \Test\TestCase {

    /** @var Admin */
    private $panel;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $config;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $userSession;

    public function setUp() {
        parent::setUp();
        $this->config = $this->getMockBuilder(\OCP\IConfig::class)->getMock();
        $this->userSession = $this->getMockBuilder(\OCP\IUserSession::class)->getMock();
        $this->panel = new Admin(
                $this->config, $this->userSession);
    }

    public function testGetPanel() {
        $_templateHtml = $this->panel->getPanel()->fetchPage();
        $this->assertContains('<div id="dtnPluginAdminSettings" class="dtn-settings section">', $_templateHtml);
    }

    public function testGetPriority() {
        $this->assertTrue(is_integer($this->panel->getPriority()));
    }

    public function testGetSectionID() {
        $this->assertEquals('additional', $this->panel->getSectionID());
    }

}
