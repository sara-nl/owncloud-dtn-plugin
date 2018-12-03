<?php
/**
 * ownCloud
 *
 * @author Artur Neumann <artur@jankaritech.com>
 * @copyright Copyright (c) 2018 Artur Neumann artur@jankaritech.com
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

use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;

/**
 * PageObject for the Notifications area
 */
class NotificationsAppDialog extends OwncloudPage {
	private $notificationContainerXpath = "//div[@class='notification']";
	private $notificationTitleXpath = "//h3[@class='notification-title']";
	private $notificationLinkXpath = "//a[@class='notification-link']";
	private $notificationMessageXpath = "//p[@class='notification-message']";
	
	/**
	 *
	 * @return array with notifications details title,link,message
	 */
	public function getAllNotifications() {
		$notifications = $this->findAll("xpath", $this->notificationContainerXpath);
		$notificationsArray = [];
		foreach ($notifications as $notification) {
			$title = $notification->find("xpath", $this->notificationTitleXpath);
			if ($title === null) {
				throw new ElementNotFoundException(
					__METHOD__ .
					" could not find notification title with xpath $this->notificationTitleXpath"
				);
			}
			$link = $notification->find("xpath", $this->notificationLinkXpath);
			if ($link === null) {
				throw new ElementNotFoundException(
					__METHOD__ .
					" could not find notification link with xpath $this->notificationLinkXpath"
				);
			}
			$message = $notification->find("xpath", $this->notificationMessageXpath);
			if ($message === null) {
				throw new ElementNotFoundException(
					__METHOD__ .
					" could not find notification message with xpath $this->notificationMessageXpath"
				);
			}
			$notificationsArray[] = [
				'title' => $title->getText(),
				'link' => $link->getAttribute('href'),
				'message' => $message->getText()
			];
		}
		return $notificationsArray;
	}

	/**
	 *
	 * @return \Page\Notification[]
	 */
	public function getAllNotificationObjects() {
		$notificationsElement = $this->findAll("xpath", $this->notificationContainerXpath);
		$notificationObjects = [];
		foreach ($notificationsElement as $notificationElement) {
			/**
			 *
			 * @var Notification $notificationObject
			 */
			$notificationObject = $this->getPage("Notification");
			$notificationObject->setElement($notificationElement);
			$notificationObjects[] = $notificationObject;
		}
		return $notificationObjects;
	}
}
