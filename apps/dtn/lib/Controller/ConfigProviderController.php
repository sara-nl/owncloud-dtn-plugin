<?php

/*
 * SURFsara
 */

namespace OCA\DTN\controller;

use OCP\AppFramework\ApiController;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\IConfig;
use OCP\IUserManager;
use OCP\ILogger;

/**
 * Description of ConfigProviderController
 *
 * @author antoonp
 */
class ConfigProviderController extends ApiController {

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
     */
    public function index() {
        $_url = $this->request->getPathInfo();
        return [
            "message" => "Hi there. nothing to see here: $_url"
        ];
    }

    /**
     * Returns file base location information of the user that has the specified email set.
     * E.g.:
     * http://172.27.242.113/apps/dtn/config/datalocationinfo?receiverId=antoon.prins@surfsara.nl
     * 
     * @return []
     * @NoCSRFRequired
     * @CORS
     */
    public function getDataLocationInfo() {
        if ($this->request->getParam('receiverId') === NULL) {
            return [
                "message" => "Receiver id must be provided"
            ];
        } else {
            $receiverId = $this->request->getParam('receiverId');
            $dataPath = $this->config->getSystemValue('datadirectory');
            $_receiver = $this->findUser($receiverId);
            if (isset($_receiver)) {
                $_receiverUID = $_receiver->getUID();
                return [
                    "message" => "dataLocationInfo called",
                    "dataPath" => $dataPath,
                    "receiverId" => $_receiver->getUID(),
                    "receiverFullDataPath" => "$dataPath/$_receiverUID/files"
                ];
            } else {
                return [
                    "message" => "The receiver could not be determined for receiverId: $receiverId"
                ];
            }
        }
    }

    /**
     * Finds and returns the user with the specified email address. Returns NULL if the user is not found.
     * @param string $emailAddress
     * @return type
     */
    private function findUser(string $emailAddress) {
        $_user = NULL;
        $_this = $this;
        $this->userManager->callForAllUsers(function ($user) use (&$_user, $emailAddress, $_this) {
            if ($user->getEMailAddress() === $emailAddress) {
                $_user = $user;
            }
        });
        return $_user;
    }

    /**
     * Logs the specified message at the specified level.
     * @param type $message
     * @param type $level
     */
    private function log($message, string $level = NULL) {
        $this->logger->log($level, $message);
    }

}
