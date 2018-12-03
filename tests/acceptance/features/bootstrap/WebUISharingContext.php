<?php
/**
 * ownCloud
 *
 * @author Artur Neumann <artur@jankaritech.com>
 * @copyright Copyright (c) 2017 Artur Neumann artur@jankaritech.com
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License,
 * as published by the Free Software Foundation;
 * either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Page\FilesPage;
use Page\FilesPageElement\SharingDialog;
use Page\PublicLinkFilesPage;
use Page\SharedWithYouPage;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;
use TestHelpers\AppConfigHelper;
use TestHelpers\SetupHelper;

require_once 'bootstrap.php';

/**
 * WebUI SharingContext context.
 */
class WebUISharingContext extends RawMinkContext implements Context {

	/**
	 *
	 * @var FilesPage
	 */
	private $filesPage;

	/**
	 *
	 * @var PublicLinkFilesPage
	 */
	private $publicLinkFilesPage;

	/**
	 *
	 * @var SharedWithYouPage
	 */
	private $sharedWithYouPage;

	/**
	 *
	 * @var SharingDialog
	 */
	private $sharingDialog;

	/**
	 *
	 * @var FeatureContext
	 */
	private $featureContext;

	/**
	 *
	 * @var WebUIGeneralContext
	 */
	private $webUIGeneralContext;

	/**
	 *
	 * @var WebUIFilesContext
	 */
	private $webUIFilesContext;
	private $createdPublicLinks = [];

	private $oldMinCharactersForAutocomplete = null;
	private $oldFedSharingFallbackSetting = null;

	/**
	 * WebUISharingContext constructor.
	 *
	 * @param FilesPage $filesPage
	 * @param PublicLinkFilesPage $publicLinkFilesPage
	 * @param SharedWithYouPage $sharedWithYouPage
	 */
	public function __construct(
		FilesPage $filesPage,
		PublicLinkFilesPage $publicLinkFilesPage,
		SharedWithYouPage $sharedWithYouPage
	) {
		$this->filesPage = $filesPage;
		$this->publicLinkFilesPage = $publicLinkFilesPage;
		$this->sharedWithYouPage = $sharedWithYouPage;
	}

	/**
	 *
	 * @param string $name
	 * @param string $url
	 *
	 * @return void
	 */
	private function addToListOfCreatedPublicLinks($name, $url) {
		$this->createdPublicLinks[] = ["name" => $name, "url" => $url];
	}

	/**
	 * @When /^the user shares the (?:file|folder) "([^"]*)" with the (?:(remote|federated)\s)?user "([^"]*)" using the webUI$/
	 * @Given /^the user has shared the (?:file|folder) "([^"]*)" with the (?:(remote|federated)\s)?user "([^"]*)" using the webUI$/
	 *
	 * @param string $folder
	 * @param string $remote
	 * @param string $user
	 * @param int $maxRetries
	 * @param boolean $quiet
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function theUserSharesTheFileFolderWithTheUserUsingTheWebUI(
		$folder, $remote, $user, $maxRetries = STANDARDRETRYCOUNT, $quiet = false
	) {
		$this->filesPage->waitTillPageIsloaded($this->getSession());
		try {
			$this->filesPage->closeDetailsDialog();
		} catch (Exception $e) {
			//we don't care
		}
		$this->sharingDialog = $this->filesPage->openSharingDialog(
			$folder, $this->getSession()
		);
		$user = $this->featureContext->substituteInLineCodes($user);
		if ($remote === "remote") {
			$this->sharingDialog->shareWithRemoteUser(
				$user, $this->getSession(), $maxRetries, $quiet
			);
		} else {
			$this->sharingDialog->shareWithUser(
				$user, $this->getSession(), $maxRetries, $quiet
			);
		}
		$this->theUserClosesTheShareDialog();
	}

	/**
	 * @When the user shares the file/folder :folder with the group :group using the webUI
	 * @Given the user has shared the file/folder :folder with the group :group using the webUI
	 *
	 * @param string $folder
	 * @param string $group
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function theUserSharesTheFileFolderWithTheGroupUsingTheWebUI(
		$folder, $group
	) {
		$this->filesPage->waitTillPageIsloaded($this->getSession());
		try {
			$this->filesPage->closeDetailsDialog();
		} catch (Exception $e) {
			//we don't care
		}
		$this->sharingDialog = $this->filesPage->openSharingDialog(
			$folder, $this->getSession()
		);
		$this->sharingDialog->shareWithGroup($group, $this->getSession());
		$this->theUserClosesTheShareDialog();
	}

	/**
	 * @When the user opens the share dialog for the file/folder :name
	 * @Given the user has opened the share dialog for the file/folder :name
	 *
	 * @param string $name
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function theUserOpensTheShareDialogForTheFileFolder($name) {
		$this->filesPage->waitTillPageIsloaded($this->getSession());
		$this->sharingDialog = $this->filesPage->openSharingDialog(
			$name, $this->getSession()
		);
	}

	/**
	 * @When the user creates a new public link for the file/folder :name using the webUI
	 * @Given the user has created a new public link for the file/folder :name using the webUI
	 *
	 * @param string $name
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function theUserCreatesANewPublicLinkForUsingTheWebUI($name) {
		$this->theUserCreatesANewPublicLinkForUsingTheWebUIWith($name);
	}

	/**
	 * @When the user creates a new public link for the file/folder :name using the webUI with
	 * @Given the user has created a new public link for the file/folder :name using the webUI with
	 *
	 * @param string $name
	 * @param TableNode $settings table with the settings and no header
	 *                            possible settings: name, permission,
	 *                            password, expiration, email
	 *                            the permissions values has to be written exactly
	 *                            the way its written in the UI
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function theUserCreatesANewPublicLinkForUsingTheWebUIWith(
		$name, TableNode $settings = null
	) {
		$this->filesPage->waitTillPageIsloaded($this->getSession());
		//close any open sharing dialog
		//if there is no dialog open and we try to close it
		//an exception will be thrown, but we do not care
		try {
			$this->filesPage->closeDetailsDialog();
		} catch (Exception $e) {
		}
		$this->sharingDialog = $this->filesPage->openSharingDialog(
			$name, $this->getSession()
		);
		$publicShareTab = $this->sharingDialog->openPublicShareTab();
		if ($settings !== null) {
			$settingsArray = $settings->getRowsHash();
			if (!isset($settingsArray['name'])) {
				$settingsArray['name'] = null;
			}
			if (!isset($settingsArray['permission'])) {
				$settingsArray['permission'] = null;
			}
			if (!isset($settingsArray['password'])) {
				$settingsArray['password'] = null;
			}
			if (!isset($settingsArray['expiration'])) {
				$settingsArray['expiration'] = null;
			}
			if (!isset($settingsArray['email'])) {
				$settingsArray['email'] = null;
			}
			$linkName = $publicShareTab->createLink(
				$this->getSession(),
				$settingsArray ['name'],
				$settingsArray ['permission'],
				$settingsArray ['password'],
				$settingsArray ['expiration'],
				$settingsArray ['email']
			);
			if ($settingsArray['name'] !== null) {
				PHPUnit_Framework_Assert::assertSame(
					$settingsArray ['name'], $linkName,
					"set and retrieved public link names are not the same"
				);
			}
		} else {
			$linkName = $publicShareTab->createLink($this->getSession());
		}
		$linkUrl = $publicShareTab->getLinkUrl($linkName);
		$this->addToListOfCreatedPublicLinks($linkName, $linkUrl);
	}
	
	/**
	 * @When the user closes the share dialog
	 * @Given the user has closed the share dialog
	 *
	 * @return void
	 */
	public function theUserClosesTheShareDialog() {
		// The close button is for the whole details dialog.
		$this->filesPage->closeDetailsDialog();
	}

	/**
	 * @When the user types :input in the share-with-field
	 * @Given the user has typed :input in the share-with-field
	 *
	 * @param string $input
	 *
	 * @return void
	 */
	public function theUserTypesInTheShareWithField($input) {
		$this->sharingDialog->fillShareWithField($input, $this->getSession());
	}

	/**
	 * @When the user sets the sharing permissions of :userName for :fileName using the webUI to
	 * @Given the user has set the sharing permissions of :userName for :fileName using the webUI to
	 *
	 * @param string $userName
	 * @param string $fileName
	 * @param TableNode $permissionsTable table with two columns and no heading
	 *                                    first column one of the permissions
	 *                                    (share|edit|create|change|delete)
	 *                                    second column yes|no
	 *                                    not mentioned permissions will not be
	 *                                    touched
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function theUserSetsTheSharingPermissionsOfForOnTheWebUI(
		$userName, $fileName, TableNode $permissionsTable
	) {
		$userName = $this->featureContext->substituteInLineCodes($userName);
		$this->theUserOpensTheShareDialogForTheFileFolder($fileName);
		$this->sharingDialog->setSharingPermissions(
			$userName, $permissionsTable->getRowsHash()
		);
	}

	/**
	 * @When the user accepts the offered remote shares using the webUI
	 * @Given the user has accepted the offered remote shares
	 *
	 * @return void
	 */
	public function theUserAcceptsTheOfferedRemoteShares() {
		foreach (\array_reverse($this->filesPage->getOcDialogs()) as $ocDialog) {
			$ocDialog->accept($this->getSession());
		}
	}

	/**
	 * @When the administrator sets the minimum characters for sharing autocomplete to :minCharacters
	 * @Given the administrator has set the minimum characters for sharing autocomplete to :minCharacters
	 *
	 * @param string $minCharacters
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function setMinCharactersForAutocomplete($minCharacters) {
		if ($this->oldMinCharactersForAutocomplete === null) {
			$oldMinCharactersForAutocomplete = SetupHelper::runOcc(
				['config:system:get', 'user.search_min_length']
			)['stdOut'];
			$this->oldMinCharactersForAutocomplete = \trim(
				$oldMinCharactersForAutocomplete
			);
		}
		$minCharacters = (int) $minCharacters;
		SetupHelper::runOcc(
			[
				'config:system:set',
				'user.search_min_length',
				'--value',
				$minCharacters
			]
		);
	}

	/**
	 * @Given the administrator has allowed http fallback for federation sharing
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function allowHttpFallbackForFedSharing() {
		if ($this->oldFedSharingFallbackSetting === null) {
			$oldFedSharingFallbackSetting = SetupHelper::runOcc(
				['config:system:get', 'sharing.federation.allowHttpFallback']
			)['stdOut'];
			$this->oldFedSharingFallbackSetting = \trim(
				$oldFedSharingFallbackSetting
			);
		}
		SetupHelper::runOcc(
			[
				'config:system:set',
				'sharing.federation.allowHttpFallback',
				'--type boolean',
				'--value true',
			]
		);
	}

	/**
	 * @When the user declines the offered remote shares using the webUI
	 * @Given the user has declined the offered remote shares
	 *
	 * @return void
	 */
	public function theUserDeclinesTheOfferedRemoteShares() {
		foreach (\array_reverse($this->filesPage->getOcDialogs()) as $ocDialog) {
			$ocDialog->clickButton($this->getSession(), 'Cancel');
		}
	}

	/**
	 * @When the public accesses the last created public link using the webUI
	 * @Given the public has accessed the last created public link using the webUI
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function thePublicAccessesTheLastCreatedPublicLinkUsingTheWebUI() {
		$lastCreatedLink = \end($this->createdPublicLinks);
		$path = \str_replace(
			$this->featureContext->getBaseUrl(),
			"",
			$lastCreatedLink['url']
		);
		$this->publicLinkFilesPage->setPagePath($path);
		$this->publicLinkFilesPage->open();
		$this->publicLinkFilesPage->waitTillPageIsLoaded($this->getSession());
		$this->webUIGeneralContext->setCurrentPageObject($this->publicLinkFilesPage);
	}

	/**
	 * @When the public adds the public link to :server as user :username with the password :password using the webUI
	 * @Given the public has added the public link to :server as user :username with the password :password using the webUI
	 *
	 * @param string $server
	 * @param string $username
	 * @param string $password
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function thePublicAddsThePublicLinkToUsingTheWebUI(
		$server, $username, $password
	) {
		if (!$this->publicLinkFilesPage->isOpen()) {
			throw new Exception('Not on public link page!');
		}
		$server = $this->featureContext->substituteInLineCodes($server);
		$this->publicLinkFilesPage->addToServer($server);
		// addToServer takes us from the public link page to the login page
		// of the remote server, waiting for us to login.
		$this->webUIGeneralContext->loginAs($username, $password);
	}

	/**
	 * @When /^the user (declines|accepts) the share "([^"]*)" offered by user "([^"]*)" using the webUI$/
	 * @Given /^the user has (declined|accepted) the share "([^"]*)" offered by user "([^"]*)" using the webUI$/
	 *
	 * @param string $action
	 * @param string $share
	 * @param string $offeredBy
	 *
	 * @return void
	 */
	public function userReactsToShareOfferedByUsingWebUI(
		$action, $share, $offeredBy
	) {
		$this->webUIFilesContext->theUserBrowsesToTheSharedWithYouPage();
		$fileRows = $this->sharedWithYouPage->findAllFileRowsByName(
			$share, $this->getSession()
		);
		
		$found = false;
		foreach ($fileRows as $fileRow) {
			if ($offeredBy === $fileRow->getSharer()) {
				if (\substr($action, 0, 6) === "accept") {
					$fileRow->acceptShare($this->getSession());
				} else {
					$fileRow->declineShare($this->getSession());
				}
				$found = true;
				break;
			}
		}
		if ($found === false) {
			throw new Exception(
				__METHOD__ .
				" could not find share '$share' offered by '$offeredBy'"
			);
		}
	}

	/**
	 * @Then only :userOrGroupName should be listed in the autocomplete list on the webUI
	 *
	 * @param string $userOrGroupName
	 *
	 * @return void
	 */
	public function onlyUserOrGroupNameShouldBeListedInTheAutocompleteList(
		$userOrGroupName
	) {
		$autocompleteItems = $this->sharingDialog->getAutocompleteItemsList();
		PHPUnit_Framework_Assert::assertCount(
			1,
			$autocompleteItems,
			"expected 1 autocomplete item but there are " . \count($autocompleteItems)
		);
		PHPUnit_Framework_Assert::assertContains(
			$userOrGroupName,
			$autocompleteItems,
			"'$userOrGroupName' not in autocomplete list"
		);
	}

	/**
	 * @Then all users and groups that contain the string :requiredString in their name should be listed in the autocomplete list on the webUI
	 *
	 * @param string $requiredString
	 *
	 * @return void
	 */
	public function allUsersAndGroupsThatContainTheStringInTheirNameShouldBeListedInTheAutocompleteList(
		$requiredString
	) {
		$this->allUsersAndGroupsThatContainTheStringInTheirNameShouldBeListedInTheAutocompleteListExcept(
			$requiredString, '', ''
		);
	}

	/**
	 * @Then all users and groups that contain the string :requiredString in their name should be listed in the autocomplete list on the webUI except :userOrGroup :notToBeListed
	 *
	 * @param string $requiredString
	 * @param string $userOrGroup
	 * @param string $notToBeListed
	 *
	 * @return void
	 */
	public function allUsersAndGroupsThatContainTheStringInTheirNameShouldBeListedInTheAutocompleteListExcept(
		$requiredString, $userOrGroup, $notToBeListed
	) {
		if ($userOrGroup === 'group') {
			$notToBeListed
				= $this->sharingDialog->groupStringsToMatchAutoComplete($notToBeListed);
		}
		$autocompleteItems = $this->sharingDialog->getAutocompleteItemsList();
		// Keep separate arrays of users and groups, because the names can overlap
		$createdElements = [];
		$createdElements['groups'] = $this->sharingDialog->groupStringsToMatchAutoComplete(
			$this->featureContext->getCreatedGroups()
		);
		$createdElements['users'] = $this->featureContext->getCreatedUserDisplayNames();
		$numExpectedItems = 0;
		foreach ($createdElements as $elementArray) {
			foreach ($elementArray as $internalName => $displayName) {
				// Matching should be case-insensitive on the internal or display name
				if (((\stripos($internalName, $requiredString) !== false)
					|| (\stripos($displayName, $requiredString) !== false))
					&& ($displayName !== $notToBeListed)
					&& ($displayName !== $this->featureContext->getCurrentUser())
					&& ($displayName !== $this->featureContext->getCurrentUserDisplayName())
				) {
					PHPUnit_Framework_Assert::assertContains(
						$displayName,
						$autocompleteItems,
						"'$displayName' not in autocomplete list"
					);
					$numExpectedItems = $numExpectedItems + 1;
				}
			}
		}

		PHPUnit_Framework_Assert::assertCount(
			$numExpectedItems,
			$autocompleteItems,
			"expected $numExpectedItems in autocomplete list but there are " . \count($autocompleteItems)
		);

		PHPUnit_Framework_Assert::assertNotContains(
			$notToBeListed,
			$this->sharingDialog->getAutocompleteItemsList()
		);
	}

	/**
	 * @Then the users own name should not be listed in the autocomplete list on the webUI
	 *
	 * @return void
	 */
	public function theUsersOwnNameShouldNotBeListedInTheAutocompleteList() {
		PHPUnit_Framework_Assert::assertNotContains(
			$this->filesPage->getMyDisplayname(),
			$this->sharingDialog->getAutocompleteItemsList()
		);
	}

	/**
	 * @Then a tooltip with the text :text should be shown near the share-with-field on the webUI
	 *
	 * @param string $text
	 *
	 * @return void
	 */
	public function aTooltipWithTheTextShouldBeShownNearTheShareWithField($text) {
		PHPUnit_Framework_Assert::assertEquals(
			$text,
			$this->sharingDialog->getShareWithTooltip()
		);
	}

	/**
	 * @Then the autocomplete list should not be displayed on the webUI
	 *
	 * @return void
	 */
	public function theAutocompleteListShouldNotBeDisplayed() {
		PHPUnit_Framework_Assert::assertEmpty(
			$this->sharingDialog->getAutocompleteItemsList()
		);
	}

	/**
	 * @Then /^the (file|folder) "([^"]*)" should be marked as shared(?: with "([^"]*)")? by "([^"]*)" on the webUI$/
	 *
	 * @param string $fileOrFolder
	 * @param string $itemName
	 * @param string $sharedWithGroup
	 * @param string $sharerName
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function theFileFolderShouldBeMarkedAsSharedBy(
		$fileOrFolder, $itemName, $sharedWithGroup, $sharerName
	) {
		//close any open sharing dialog
		//if there is no dialog open and we try to close it
		//an exception will be thrown, but we do not care
		try {
			$this->filesPage->closeDetailsDialog();
		} catch (Exception $e) {
		}
		
		$row = $this->filesPage->findFileRowByName($itemName, $this->getSession());
		$sharingBtn = $row->findSharingButton();
		PHPUnit_Framework_Assert::assertSame(
			$sharerName, $this->filesPage->getTrimmedText($sharingBtn)
		);
		$sharingDialog = $this->filesPage->openSharingDialog(
			$itemName, $this->getSession()
		);
		PHPUnit_Framework_Assert::assertSame(
			$sharerName, $sharingDialog->getSharerName()
		);
		if ($fileOrFolder === "folder") {
			PHPUnit_Framework_Assert::assertContains(
				"folder-shared.svg",
				$row->findThumbnail()->getAttribute("style")
			);
			$detailsDialog = $this->filesPage->getDetailsDialog();
			PHPUnit_Framework_Assert::assertContains(
				"folder-shared.svg",
				$detailsDialog->findThumbnail()->getAttribute("style")
			);
		}
		if ($sharedWithGroup !== "") {
			PHPUnit_Framework_Assert::assertSame(
				$sharedWithGroup,
				$sharingDialog->getSharedWithGroupName()
			);
		}
	}

	/**
	 * @Then the file/folder :item should be in state :state in the shared-with-you page on the webUI
	 *
	 * @param string $item
	 * @param string $state
	 *
	 * @return void
	 */
	public function assertShareIsInStateOnWebUI($item, $state) {
		$this->webUIFilesContext->theUserBrowsesToTheSharedWithYouPage();
		$fileRow = $this->sharedWithYouPage->findFileRowByName(
			$item, $this->getSession()
		);
		PHPUnit_Framework_Assert::assertSame($state, $fileRow->getShareState());
	}

	/**
	 * @Then the file/folder :item shared by :sharedBy should be in state :state in the shared-with-you page on the webUI
	 *
	 * @param string $item
	 * @param string $sharedBy
	 * @param string $state
	 *
	 * @return void
	 */
	public function assertShareSharedByIsInStateOnWebUI($item, $sharedBy, $state) {
		$this->webUIFilesContext->theUserBrowsesToTheSharedWithYouPage();
		$fileRows = $this->sharedWithYouPage->findAllFileRowsByName(
			$item, $this->getSession()
		);
		$found = false;
		$currentState = null;
		foreach ($fileRows as $fileRow) {
			if ($sharedBy === $fileRow->getSharer()) {
				$found = true;
				$currentState = $fileRow->getShareState();
				break;
			}
		}
		PHPUnit_Framework_Assert::assertTrue(
			$found, "could not find item called $item shared by $sharedBy"
		);
		PHPUnit_Framework_Assert::assertSame($state, $currentState);
	}

	/**
	 * @Then the file/folder :item should be in state :state in the shared-with-you page on the webUI after a page reload
	 *
	 * @param string $item
	 * @param string $state
	 *
	 * @return void
	 */
	public function assertSharesIsInStateOnWebUIAfterPageReload($item, $state) {
		$this->webUIGeneralContext->theUserReloadsTheCurrentPageOfTheWebUI();
		$this->sharedWithYouPage->waitForAjaxCallsToStartAndFinish($this->getSession());
		$this->assertShareIsInStateOnWebUI($item, $state);
	}
	
	/**
	 * @Then /^it should not be possible to share the (?:file|folder) "([^"]*)"(?: with "([^"]*)")? using the webUI$/
	 *
	 * @param string $fileName
	 * @param string|null $shareWith
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function itShouldNotBePossibleToShareUsingTheWebUI(
		$fileName, $shareWith = null
	) {
		$sharingWasPossible = false;
		try {
			$this->theUserSharesTheFileFolderWithTheUserUsingTheWebUI(
				$fileName, null, $shareWith, 2, true
			);
			$sharingWasPossible = true;
		} catch (ElementNotFoundException $e) {
			$possibleMessages = [
				"could not find share-with-field",
				"could not find sharing button in fileRow",
				"could not share with '$shareWith'"
			];
			$foundMessage = false;
			foreach ($possibleMessages as $message) {
				$foundMessage = \strpos($e->getMessage(), $message);
				if ($foundMessage !== false) {
					break;
				}
			}
			if ($foundMessage === false) {
				throw new Exception(
					'exception message has to contain' .
					' "could not find share-with-field",' .
					' "could not find sharing button in fileRow" or' .
					' "could not share with \'...\'"but was: "' .
					$e->getMessage() . '"'
				);
			}
		}
		if ($sharingWasPossible === true) {
			throw new Exception("It was possible to share the file");
		}
	}

	/**
	 * This will run before EVERY scenario.
	 * It will set the properties for this object.
	 *
	 * @BeforeScenario @webUI
	 *
	 * @param BeforeScenarioScope $scope
	 *
	 * @return void
	 */
	public function before(BeforeScenarioScope $scope) {
		// Get the environment
		$environment = $scope->getEnvironment();
		// Get all the contexts you need in this context
		$this->featureContext = $environment->getContext('FeatureContext');
		$this->webUIGeneralContext = $environment->getContext('WebUIGeneralContext');
		$this->webUIFilesContext = $environment->getContext('WebUIFilesContext');
		$this->setupSharingConfigs();
	}

	/**
	 * After Scenario. Sets back old settings
	 *
	 * @AfterScenario @webUI
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function tearDownScenario() {
		//TODO make a function that can be used for different settings
		if ($this->oldMinCharactersForAutocomplete === "") {
			SetupHelper::runOcc(['config:system:delete', 'user.search_min_length']);
		} elseif ($this->oldMinCharactersForAutocomplete !== null) {
			SetupHelper::runOcc(
				[
					'config:system:set',
					'user.search_min_length',
					'--value',
					$this->oldMinCharactersForAutocomplete
				]
			);
		}

		if ($this->oldFedSharingFallbackSetting === "") {
			SetupHelper::runOcc(
				['config:system:delete', 'sharing.federation.allowHttpFallback']
			);
		} elseif ($this->oldFedSharingFallbackSetting !== null) {
			SetupHelper::runOcc(
				[
					'config:system:set',
					'sharing.federation.allowHttpFallback',
					'--type boolean',
					'--value',
					$this->oldFedSharingFallbackSetting
				]
			);
		}
	}

	/**
	 * @return void
	 */
	private function setupSharingConfigs() {
		$settings = [
			[
				'capabilitiesApp' => 'files_sharing',
				'capabilitiesParameter' => 'api_enabled',
				'testingApp' => 'core',
				'testingParameter' => 'shareapi_enabled',
				'testingState' => true
			],
			[
				'capabilitiesApp' => 'files_sharing',
				'capabilitiesParameter' => 'public@@@enabled',
				'testingApp' => 'core',
				'testingParameter' => 'shareapi_allow_links',
				'testingState' => true
			],
			[
				'capabilitiesApp' => 'files_sharing',
				'capabilitiesParameter' => 'public@@@upload',
				'testingApp' => 'core',
				'testingParameter' => 'shareapi_allow_public_upload',
				'testingState' => true
			],
			[
				'capabilitiesApp' => 'files_sharing',
				'capabilitiesParameter' => 'group_sharing',
				'testingApp' => 'core',
				'testingParameter' => 'shareapi_allow_group_sharing',
				'testingState' => true
			],
			[
				'capabilitiesApp' => 'files_sharing',
				'capabilitiesParameter' => 'share_with_group_members_only',
				'testingApp' => 'core',
				'testingParameter' => 'shareapi_only_share_with_group_members',
				'testingState' => false
			],
			[
				'capabilitiesApp' => 'files_sharing',
				'capabilitiesParameter' => 'share_with_membership_groups_only',
				'testingApp' => 'core',
				'testingParameter' => 'shareapi_only_share_with_membership_groups',
				'testingState' => false
			],
			[
				'capabilitiesApp' => 'files_sharing',
				'capabilitiesParameter' =>
					'user_enumeration@@@enabled',
				'testingApp' => 'core',
				'testingParameter' =>
					'shareapi_allow_share_dialog_user_enumeration',
				'testingState' => true
			],
			[
				'capabilitiesApp' => 'files_sharing',
				'capabilitiesParameter' =>
					'user_enumeration@@@group_members_only',
				'testingApp' => 'core',
				'testingParameter' =>
					'shareapi_share_dialog_user_enumeration_group_members',
				'testingState' => false
			],
			[
				'capabilitiesApp' => 'federation',
				'capabilitiesParameter' => 'outgoing',
				'testingApp' => 'files_sharing',
				'testingParameter' => 'outgoing_server2server_share_enabled',
				'testingState' => true
			],
			[
				'capabilitiesApp' => 'federation',
				'capabilitiesParameter' => 'incoming',
				'testingApp' => 'files_sharing',
				'testingParameter' => 'incoming_server2server_share_enabled',
				'testingState' => true
			]
		];

		$change = AppConfigHelper::setCapabilities(
			$this->featureContext->getBaseUrl(),
			$this->featureContext->getAdminUsername(),
			$this->featureContext->getAdminPassword(),
			$settings,
			$this->webUIGeneralContext->getSavedCapabilitiesXml()[$this->featureContext->getBaseUrl()]
		);
		$this->webUIGeneralContext->addToSavedCapabilitiesChanges($change);
	}
}
