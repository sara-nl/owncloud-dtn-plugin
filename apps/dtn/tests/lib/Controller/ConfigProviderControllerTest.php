<?php

/**
 * SURFsara
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
        $this->user = $this->createMock('\OCP\IUser');
        /* Setup user */
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
        \printf(' appName: ' . $this->user->getUID());
        /* no id provided must return message key */
        $_result = $this->configProviderController->getDataLocationInfo();
        $this->assertArrayHasKey('message', $_result);
        
        

        \printf($_result['message']);
    }

}
