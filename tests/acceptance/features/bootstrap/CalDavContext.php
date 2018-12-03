<?php
/**
 * @author Lukas Reschke <lukas@owncloud.com>
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

require __DIR__ . '/../../../../lib/composer/autoload.php';

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use GuzzleHttp\Message\ResponseInterface;
use TestHelpers\HttpRequestHelper;

/**
 * CalDav functions
 */
class CalDavContext implements \Behat\Behat\Context\Context {
	/**
	 * @var ResponseInterface
	 */
	private $response;
	/**
	 * @var array
	 */
	private $responseXml = '';

	/**
	 * @var FeatureContext
	 */
	private $featureContext;

	/**
	 * @BeforeScenario @caldav
	 *
	 * @param BeforeScenarioScope $scope
	 *
	 * @return void
	 */
	public function setUpScenario(BeforeScenarioScope $scope) {
		// Get the environment
		$environment = $scope->getEnvironment();
		// Get all the contexts you need in this context
		$this->featureContext = $environment->getContext('FeatureContext');
		$this->responseXml = '';
	}

	/**
	 * @AfterScenario @caldav
	 *
	 * @return void
	 */
	public function afterScenario() {
		$davUrl = $this->featureContext->getBaseUrl()
			. '/remote.php/dav/calendars/admin/MyCalendar';
		HttpRequestHelper::delete(
			$davUrl,
			$this->featureContext->getAdminUsername(),
			$this->featureContext->getAdminPassword()
		);
	}

	/**
	 * @When user :user requests calendar :calendar using the new WebDAV API
	 *
	 * @param string $user
	 * @param string $calendar
	 *
	 * @return void
	 */
	public function userRequestsCalendarUsingTheAPI($user, $calendar) {
		$davUrl = $this->featureContext->getBaseUrl()
			. "/remote.php/dav/calendars/$calendar";

		$this->response = HttpRequestHelper::get(
			$davUrl, $user, $this->featureContext->getPasswordForUser($user)
		);
	}

	/**
	 * @Then the CalDAV HTTP status code should be :code
	 *
	 * @param int $code
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function theCalDavHttpStatusCodeShouldBe($code) {
		if ((int)$code !== $this->response->getStatusCode()) {
			throw new \Exception(
				\sprintf(
					'Expected %s got %s',
					(int)$code,
					$this->response->getStatusCode()
				)
			);
		}

		$body = $this->response->getBody()->getContents();
		if ($body && \substr($body, 0, 1) === '<') {
			$reader = new Sabre\Xml\Reader();
			$reader->xml($body);
			$this->responseXml = $reader->parse();
		}
	}

	/**
	 * @Then the CalDAV exception should be :message
	 *
	 * @param string $message
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function theCalDavExceptionShouldBe($message) {
		$this->featureContext->theDavExceptionShouldBe($message, $this->responseXml);
	}

	/**
	 * @Then the CalDAV error message should be :message
	 *
	 * @param string $message
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function theCalDavErrorMessageShouldBe($message) {
		$this->featureContext->theDavErrorMessageShouldBe($message, $this->responseXml);
	}

	/**
	 * @Given user :user has successfully created a calendar named :name
	 *
	 * @param string $user
	 * @param string $name
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function userHasCreatedACalendarNamed($user, $name) {
		$davUrl = $this->featureContext->getBaseUrl()
			. "/remote.php/dav/calendars/$user/$name";

		$body
			= '<c:mkcalendar xmlns:c="urn:ietf:params:xml:ns:caldav" ' .
			'xmlns:d="DAV:" xmlns:a="http://apple.com/ns/ical/" ' .
			'xmlns:o="http://owncloud.org/ns"><d:set><d:prop><d:displayname>' .
			'test</d:displayname><o:calendar-enabled>1</o:calendar-enabled>' .
			'<a:calendar-color>#21213D</a:calendar-color>' .
			'<c:supported-calendar-component-set><c:comp name="VEVENT"/>' .
			'</c:supported-calendar-component-set></d:prop></d:set>' .
			'</c:mkcalendar>';

		$this->response = HttpRequestHelper::sendRequest(
			$davUrl, "MKCALENDAR", $user,
			$this->featureContext->getPasswordForUser($user), null, $body
		);
		$this->theCalDavHttpStatusCodeShouldBe(201);
	}
}
