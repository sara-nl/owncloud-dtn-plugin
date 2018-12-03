<?php
/**
 * @author Sujith Haridasan <sharidasan@owncloud.com>
 *
 * @copyright Copyright (c) 2018, ownCloud GmbH
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace Tests\Core\Command\User;

use OC\Core\Command\User\ResetPassword;
use OC\Core\Controller\LostController;
use OC\Helper\EnvironmentHelper;
use OC\Mail\Message;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserManager;
use OCP\Mail\IMailer;
use OCP\Security\ISecureRandom;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Test\TestCase;

/**
 * Class ResetPasswordTest
 *
 * @group DB
 * @package Tests\Core\Command\User
 */
class ResetPasswordTest extends TestCase {
	/** @var  IUserManager | \PHPUnit_Framework_MockObject_MockObject */
	private $userManager;
	/** @var  IConfig | \PHPUnit_Framework_MockObject_MockObject */
	private $config;
	/** @var  ITimeFactory | \PHPUnit_Framework_MockObject_MockObject */
	private $timeFactory;
	/** @var  EnvironmentHelper | \PHPUnit_Framework_MockObject_MockObject */
	private $environmentHelper;
	/** @var LostController | \PHPUnit_Framework_MockObject_MockObject */
	private $lostController;
	/** @var  ResetPassword */
	private $resetPassword;
	protected function setUp() {
		parent::setUp();

		$this->userManager = $this->createMock(IUserManager::class);
		$this->config = $this->createMock(IConfig::class);
		$this->timeFactory = $this->createMock(ITimeFactory::class);
		$this->environmentHelper = $this->createMock(EnvironmentHelper::class);
		$this->lostController = $this->createMock(LostController::class);
		$this->resetPassword = new ResetPassword($this->userManager, $this->config, $this->timeFactory,
			$this->environmentHelper, $this->lostController);
	}

	public function testResetPasswordFromEnv() {
		$input = $this->createMock(InputInterface::class);
		$output = $this->createMock(OutputInterface::class);

		$input->expects($this->once())
			->method('getArgument')
			->willReturn('foo');
		$input->expects($this->exactly(3))
			->method('getOption')
			->willReturnMap([
				['send-email', false],
				['output-link', false],
				['password-from-env', true]
			]);

		$this->environmentHelper->expects($this->once())
			->method('getEnvVar')
			->willReturn('fooPass');

		$user = $this->createMock(IUser::class);
		$user->expects($this->once())
			->method('setPassword')
			->willReturn(true);

		$this->userManager->expects($this->once())
			->method('get')
			->willReturn($user);

		$this->assertNull($this->invokePrivate($this->resetPassword, 'execute', [$input, $output]));
	}

	public function testDisplayLink() {
		$input = $this->createMock(InputInterface::class);
		$output = $this->createMock(OutputInterface::class);

		$input->expects($this->once())
			->method('getArgument')
			->willReturn('foo');

		$input->expects($this->exactly(3))
			->method('getOption')
			->willReturnMap([
				['send-email', false],
				['output-link', true],
				['password-from-env', false]
			]);

		$user = $this->createMock(IUser::class);
		$user->method('getUID')
			->willReturn('foo');

		$this->userManager->expects($this->once())
			->method('get')
			->willReturn($user);

		$this->lostController->expects($this->once())
			->method('generateTokenAndLink')
			->with('foo')
			->willReturn(['http://localhost/foo/bar/123AbcFooBar/foo', '123AbcFooBar']);

		$output->expects($this->once())
			->method('writeln')
			->with('The password reset link is: http://localhost/foo/bar/123AbcFooBar/foo');

		$this->invokePrivate($this->resetPassword, 'execute', [$input, $output]);
	}

	public function testEmailLink() {
		$input = $this->createMock(InputInterface::class);
		$output = $this->createMock(OutputInterface::class);

		$input->expects($this->once())
			->method('getArgument')
			->willReturn('foo');

		$input->expects($this->exactly(3))
			->method('getOption')
			->willReturnMap([
				['send-email', true],
				['output-link', false],
				['password-from-env', false]
			]);

		$user = $this->createMock(IUser::class);

		$user->method('getEMailAddress')
			->willReturn('foo@bar.com');

		$user->method('getUID')
			->willReturn('foo');

		$this->userManager->expects($this->once())
			->method('get')
			->willReturn($user);

		$this->lostController->expects($this->once())
			->method('generateTokenAndLink')
			->with('foo')
			->willReturn(['http://localhost/foo/bar/123AbcFooBar/foo', '123AbcFooBar']);
		$this->lostController->expects($this->once())
			->method('sendEmail')
			->with('foo', '123AbcFooBar', 'http://localhost/foo/bar/123AbcFooBar/foo');

		$output->expects($this->once())
			->method('writeln')
			->with('The password reset link is: http://localhost/foo/bar/123AbcFooBar/foo');

		$this->assertEquals(0, $this->invokePrivate($this->resetPassword, 'execute', [$input, $output]));
	}

	public function testEmailLinkFailure() {
		$input = $this->createMock(InputInterface::class);
		$output = $this->createMock(OutputInterface::class);

		$input->expects($this->once())
			->method('getArgument')
			->willReturn('foo');

		$input->expects($this->exactly(3))
			->method('getOption')
			->willReturnMap([
				['send-email', true],
				['output-link', false],
				['password-from-env', false]
			]);

		$user = $this->createMock(IUser::class);

		$user->expects($this->once())
			->method('getEMailAddress')
			->willReturn(null);
		$user->method('getUID')
			->willReturn('foo');

		$this->userManager->expects($this->once())
			->method('get')
			->willReturn($user);

		$output->expects($this->once())
			->method('writeln')
			->with('<error>Email address is not set for the user foo</error>');
		$this->invokePrivate($this->resetPassword, 'execute', [$input, $output]);
	}
}
