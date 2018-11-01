<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OCA\DTN\controller;

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
        $this->logger->log('info', "setting pair $key : $value");
        if(isset($key) && isset($value)) {
            $this->config->setUserValue($this->userSession->getUser()->getUID(), 'dtn', $key, $value);
        }
    }
}
