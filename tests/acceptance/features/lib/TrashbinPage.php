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

namespace Page;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;

/**
 * Trashbin page.
 */
class TrashbinPage extends FilesPageBasic {

	/**
	 *
	 * @var string $path
	 */
	protected $path = '/index.php/apps/files/?view=trashbin';
	protected $fileNamesXpath = "//span[contains(@class,'nametext') and not(contains(@class,'innernametext'))]";
	protected $fileNameMatchXpath = "//span[contains(@class,'nametext') and not(contains(@class,'innernametext')) and .=%s]";
	protected $fileListXpath = ".//div[@id='app-content-trashbin']//tbody[@id='fileList']";
	protected $emptyContentXpath = ".//div[@id='app-content-trashbin']//div[@id='emptycontent']";
	protected $deleteAllSelectedBtnXpath = ".//*[@id='app-content-trashbin']//*[@class='delete-selected']";
	protected $restoreAllSelectedBtnXpath = ".//*[@id='app-content-trashbin']//*[@class='undelete']";
	protected $selectAllFilesCheckboxXpath = "//label[@for='select_all_trash']";

	/**
	 * @return string
	 */
	protected function getFileListXpath() {
		return $this->fileListXpath;
	}

	/**
	 * @return string
	 */
	protected function getFileNamesXpath() {
		return $this->fileNamesXpath;
	}

	/**
	 * @return string
	 */
	protected function getFileNameMatchXpath() {
		return $this->fileNameMatchXpath;
	}

	/**
	 * @return string
	 */
	protected function getEmptyContentXpath() {
		return $this->emptyContentXpath;
	}

	/**
	 * @throws ElementNotFoundException
	 * @return NodeElement
	 */
	public function findRestoreAllSelectedFilesBtn() {
		$restoreAllSelectedBtn = $this->find(
			"xpath", $this->restoreAllSelectedBtnXpath
		);
		if ($restoreAllSelectedBtn === null) {
			throw new ElementNotFoundException(
				__METHOD__ .
				" xpath $this->restoreAllSelectedBtnXpath " .
				"could not find button to restore all selected files"
			);
		}
		return $restoreAllSelectedBtn;
	}

	/**
	 *
	 * @param Session $session
	 *
	 * @return void
	 */
	public function restoreAllSelectedFiles(Session $session) {
		$this->findRestoreAllSelectedFilesBtn()->click();
		$this->waitForAjaxCallsToStartAndFinish($session);
		$this->waitTillFileRowsAreReady($session);
	}

	/**
	 *
	 * @param string $fname
	 * @param Session $session
	 *
	 * @return void
	 */
	public function restore($fname, Session $session) {
		$row = $this->findFileRowByName($fname, $session);
		$row->restore();
	}
}
