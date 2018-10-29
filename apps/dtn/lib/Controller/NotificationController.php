<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OCA\DTN\controller;

use OCP\AppFramework\ApiController;
use OCP\IRequest;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\IConfig;
use OCP\ILogger;

/**
 * Description of NotificationController
 *
 * @author antoonp
 */
class NotificationController extends ApiController {

    private $userManager;
    private $logger;

    /**
     * 
     * @param type $appName
     * @param IRequest $request
     * @param IUserManager $userManager
     * @param IUserSession $userSession
     * @param IConfig $config
     * @param ILogger $logger
     */
    function __construct($appName, IRequest $request, IUserManager $userManager, IUserSession $userSession, IConfig $config, ILogger $logger) {
        parent::__construct($appName, $request);

        $this->request = $request;
        $this->userManager = $userManager;
        $this->userSession = $userSession;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @NoCSRFRequired
     * @CORS
     * 
     * @param type $userEMail
     * @param type $senderID
     * @param type $files
     * @param type $message
     * @return type
     */
    public function addNotification($userEMail, $senderID, $files = [], $message = NULL) {
        try {
            if (!isset($userEMail) || !isset($senderID)) {
                return [
                    "error" => "Both user ownCloud eMail address and sender id must be specified."
                ];
            } else {
                /* set message */

                $_user = $this->findUser($userEMail);

                if (isset($_user)) {
                    $notificationManager = \OC::$server->getNotificationManager();
                    $notification = $notificationManager->createNotification();
                    $notification->setApp('dtn')
                            ->setUser($_user->getUID())
                            ->setDateTime(new \DateTime())
                            ->setObject('dtn', 'new_file_transfer')
                            ->setSubject('dtn', [$senderID])
                            ->setMessage('dtn', isset($message) ? [0 => $message] : $files);
                    $notificationManager->notify($notification);
                    return [
                        "message" => "Notification has landed."
                    ];
                } else {
                    return [
                        "error" => "Unable to retrieve user with UID '$userEMail'"
                    ];
                }
            }
        } catch (Exception $ex) {
            $this->logger->log(\OCP\Util::ERROR, 'An exception has occurred when adding a notification.');
            $this->logger->logException($ex);
            return [
                "error" => "An exception has occurred when adding a notification."
            ];
        }
    }

    /**
     * Finds and returns the user with the specified email address. Returns NULL if the user is not found.
     * @param string $emailAddress
     * @return \OCP\IUser
     */
    private function findUser(string $emailAddress) {
        $_user = NULL;
        try {
            $this->userManager->callForAllUsers(function ($user) use (&$_user, $emailAddress) {
                if ($user->getEMailAddress() === $emailAddress) {
                    $_user = $user;
                }
            });
        } catch (Exception $ex) {
            $this->logger->log(\OCP\Util::ERROR, "An exception has occurred when trying to lookup user with UID '$emailAddress'.");
            $this->logger->logException($ex);
        }
        return $_user;
    }

}
