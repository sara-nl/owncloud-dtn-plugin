<?php

/*
 * SURFsara
 */

namespace OCA\DTN\Controller;

use OCP\AppFramework\ApiController;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\IConfig;
use OCP\IUserManager;
use OCP\ILogger;
use OCA\DTN\Util;

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
    public function getDataLocationInfo($receiverDTNUID) {
        $this->logger->log('info', $receiverDTNUID);
        if ($receiverDTNUID === NULL) {
            return [
                "message" => "Receiver id must be provided"
            ];
        } else {
            $dataPath = $this->config->getSystemValue('datadirectory');
            $_receiver = Util::findUserForDTNUserId($receiverDTNUID);
            if (isset($_receiver)) {
                $_receiverUID = $_receiver->getUID();
                return [
                    "message" => "dataLocationInfo called",
                    "dataPath" => $dataPath,
                    "receiverOwnCloudUID" => $_receiver->getUID(),
                    "receiverFullDataPath" => "$dataPath/$_receiverUID/files"
                ];
            } else {
                return [
                    "message" => "The receiver could not be determined for receiverId: $receiverDTNUID"
                ];
            }
        }
    }

}
