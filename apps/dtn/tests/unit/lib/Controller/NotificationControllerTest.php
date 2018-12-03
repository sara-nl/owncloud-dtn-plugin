<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OCA\DTN\Tests\Controller;

/**
 * Description of NotificationControllerTest
 *
 * @author antoonp
 */
use OCA\DTN\Controller\NotificationController;
use OCP\IConfig;
use OCP\IUserManager;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\ILogger;

class NotificationControllerTest extends \Test\TestCase {

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

    public function testAddNotification() {
        /* both receiver and sender id must be defined */
        $this->assertArrayHasKey('error', $this->getNotificationController()->addNotification(NULL, NULL, NULL));
        $this->assertArrayHasKey('error', $this->getNotificationController()->addNotification('receiverId', NULL, NULL));
        $this->assertArrayHasKey('error', $this->getNotificationController()->addNotification(NULL, 'senderId', NULL));

        /* both receiver and sender set, still expect error because user can not be found */
        $this->assertArrayHasKey('error', $this->getNotificationController()->addNotification('receiverId', 'senderId', NULL));
    }

    private function getNotificationController($request = NULL, $user = NULL) {
        $this->request = $request === NULL ? $this->createMock(IRequest::class) : $request;
        if ($user !== NULL) {
            $this->userSession
                    ->expects($this->any())
                    ->method('getUser')
                    ->will($this->returnValue($_user));
        }
        return new NotificationController(
                'dtn', $this->request, $this->userManager, $this->userSession, $this->config, $this->logger
        );
    }

}
