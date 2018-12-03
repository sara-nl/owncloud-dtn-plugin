<?php

/*
 * SURFsara
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
        
        /* Setup users */
//        $_user = $this->createMock('\OCP\IUser');
//        $_user->expects($this->any())
//                ->method('getUID')
//                ->will($this->returnValue('someUserId'));
//        $_users = [];
//        $_users[0] = $_user;
//        $this->config->expects($this->any())
//                ->method('getUsersForUserValue')
//                ->will($this->returnValue($_users));
//        $this->server->expects($this->any())
//                ->method('getConfig')
//                ->will($this->returnValue($this->config));
//        
//        
//        $this->assertNotNull(\OCA\DTN\Util::findUserForDTNUserId('someUserId'));
    }

}
