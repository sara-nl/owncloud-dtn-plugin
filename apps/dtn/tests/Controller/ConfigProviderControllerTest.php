<?php

/**
 * SURFsara
 */
namespace OCA\DTN\controller;
use Test\TestCase;
use OCP\IUser;
use OCP\IRequest;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\IConfig;
use OCA\DTN\controller\ConfigProviderController;

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

    /** @var ConfigProviderController */
    private $configProviderController;

    protected function setUp() {
        $this->request = $this->getMockBuilder('\OCP\IRequest')
                ->disableOriginalConstructor()
                ->getMock();
        $this->user = $this->createMock('\OCP\IUser');
        $this->user->expects($this->any())
                ->method('getUID')
                ->will($this->returnValue('user1'));
        $this->userManager = $this->createMock('OCP\IUserManager');
        $this->userSession = $this->createMock('OCP\IUserSession');
        $this->config = $this->createMock('OCP\IConfig');


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
                $this->appName, $this->request, $this->userManager, $this->userSession, $this->config
        );
    }

    public function testGetDataLocationInfo() {
        
    }

}
