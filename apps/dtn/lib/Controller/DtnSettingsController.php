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

use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\IConfig;
use OCP\ILogger;

/**
 * Description of DtnSettings
 *
 * @author antoonp
 */
class DtnSettingsController extends Controller {

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
     * @NoAdminRequired
     * @param type $key
     * @param type $value
     */
    public function setUserSetting($key, $value) {
        if (isset($key) && isset($value)) {
            $this->config->setUserValue($this->userSession->getUser()->getUID(), 'dtn', $key, trim($value));
        }
    }

    /**
     * @param type $key
     * @param type $value
     */
    public function setAdminSetting($key, $value) {
        if (isset($key) && isset($value)) {
            $this->config->setAppValue('dtn', $key, trim($value));
        }
    }

}
