<?php
/**
 * ownCloud
 *
 * @author Bijay Sharma/Moheet Shrestha <trainees@jankaritech.com>
 * @copyright Copyright (c) 2017 Bijay Sharma/Moheet Shrestha trainees@jankaritech.com
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

/**
 * Favorites page.
 */
class FavoritesPage extends FilesPageBasic {
	
	/**
	 *
	 * @var string $path
	 */
	protected $path = '/index.php/apps/files/?view=favorites';
	protected $fileNamesXpath = "//span[@class='nametext']";
	protected $fileNameMatchXpath = "//span[@class='nametext' and .=%s]";
	protected $fileListXpath = ".//div[@id='app-content-favorites']//tbody[@id='fileList']";
	protected $emptyContentXpath = ".//div[@id='app-content-favorites']//div[@id='emptycontent']";
	
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
}
