<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OCA\DTN\Tests\Controller;

/**
 * Description of DtnControllerTest
 *
 * @author antoonp
 */
use \OCA\DTN\Controller\DtnController;
use OCP\IConfig;
use OCP\IRequest;
use OC\User\Session;
use OCP\ILogger;
use OC\AppFramework\Http\Request;

class DtnControllerTest extends \Test\TestCase {

    /** @var IRequest | \PHPUnit_Framework_MockObject_MockObject */
    private $request;

    /** @var IConfig | \PHPUnit_Framework_MockObject_MockObject */
    private $config;

    /** @var IUser */
    protected $user;

    /** @var Session | \PHPUnit_Framework_MockObject_MockObject */
    private $userSession;

    /** @var ILogger | \PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var ISecureRandom | \PHPUnit_Framework_MockObject_MockObject */
    protected $secureRandom;

    /** @var string */
    protected $stream = 'fakeinput://data';

    /** @var CsrfTokenManager | \PHPUnit_Framework_MockObject_MockObject */
    protected $csrfTokenManager;

    public function setUp() {
        parent::setUp();
        $this->config = $this->createMock(IConfig::class);
        $this->userSession = $this->getMockBuilder(Session::class)
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
        $this->secureRandom = $this->getMockBuilder('\OCP\Security\ISecureRandom')->getMock();
        $this->csrfTokenManager = $this->getMockBuilder('\OC\Security\CSRF\CsrfTokenManager')
                        ->disableOriginalConstructor()->getMock();
    }

    public function testTransferFiles() {
        $_result01 = $this->getDtnController()->transferFiles();
        /* No files selected generates error and message */
        $this->assertArrayHasKey('error', $_result01);
        $this->assertArrayHasKey('message', $_result01);
        /* set some files in the request */
        $_vars = [
            'get' => [
                "recipients" => [
                    "type" => "email",
                    "email" => "admin@dtn-global.com"
                ],
                "files" => [
                    "type" => "path",
                    "path" => "\/antoon\/files\/ownCloud Manual.pdf",
                    "metadata" => [
                        "name" => "ownCloud Manual.pdf",
                        "size" => 4917168
                    ]
                ],
                "sender" => [
                    "type" => "email",
                    "email" => "antoon@dtn-global.com"
                ]
            ],
            'method' => 'GET',
        ];
        $_dtnController = $this->getDtnController($this->getRequest($_vars));
        $_result02 = $_dtnController->transferFiles();
        /* No DTN agent set generates message */
        $this->assertArrayHasKey('message', $_result02);
        $this->assertArrayNotHasKey('error', $_result02);
    }

    private function getRequest($vars) {
        return new Request(
                $vars, $this->secureRandom, $this->config, $this->csrfTokenManager, $this->stream
        );
    }

    private function getDtnController($request = NULL) {
        $this->request = $request === NULL ? $this->createMock(IRequest::class) : $request;
        return new DtnController(
                'dtn', $this->request, $this->userSession, $this->config, $this->logger
        );
    }

}
