<?php

/**
 * Copyright 2018 SURFsara (http://www.surfsara.nl)
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
     * Returns file base location information (relative to the server-configured data directory) 
     * of the user with the specified DTN user id.
     * 
     * @return []
     * @NoCSRFRequired
     * @CORS
     */
    public function getDataLocationInfo($receiverDTNUID = NULL) {
        $this->logger->log('info', $receiverDTNUID);
        if ($receiverDTNUID === NULL || trim($receiverDTNUID) === '') {
            return [
                "message" => "Receiver id must be provided (id='$receiverDTNUID')"
            ];
        } else {
            $_receiver = Util::findUserForDTNUserId($receiverDTNUID);
            if (isset($_receiver)) {
                $_receiverUID = $_receiver->getUID();
                return [
                    "userDataPath" => "$_receiverUID/files"
                ];
            } else {
                return [
                    "message" => "The receiver could not be determined for receiverId: $receiverDTNUID"
                ];
            }
        }
    }

}
