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
use OCP\IUser;
use OCA\DTN\Util;

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
     * @param type $receiverDTNUID
     * @param type $senderDTNUID
     * @param type $files
     * @param type $message
     * @return type
     */
    public function addNotification($receiverDTNUID, $senderDTNUID, $message = NULL) {
        try {
            if (!isset($receiverDTNUID) || !isset($senderDTNUID)) {
                return [
                    "error" => "Both user DTN receiver id and sender id must be specified."
                ];
            } else {
                $_user = Util::findUserForDTNUserId($receiverDTNUID);

                if (isset($_user)) {
                    $notificationManager = \OC::$server->getNotificationManager();
                    $notification = $notificationManager->createNotification();
                    $notification->setApp('dtn')
                            ->setUser($_user->getUID())
                            ->setDateTime(new \DateTime())
                            ->setObject('dtn', 'new_file_transfer')
                            ->setSubject('dtn', [$senderDTNUID])
                            ->setMessage('dtn', isset($message) ? [0 => $message] : [0 => ""]);
                    $notificationManager->notify($notification);
                    return [
                        "message" => "Notification has landed."
                    ];
                } else {
                    return [
                        "error" => "Unable to retrieve user with user id '$receiverDTNUID'"
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

}
