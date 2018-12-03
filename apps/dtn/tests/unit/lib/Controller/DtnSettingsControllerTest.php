<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
