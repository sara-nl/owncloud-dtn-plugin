<?php

/**
 * SURFsara
 */

namespace OCA\DTN\controller;

use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\IConfig;
use OCP\ILogger;

/**
 * Description of DTNController
 *
 * @author antoonp
 */
class DTNController extends Controller {

//    private $userSession;
//    private $config;
    private $logger;

    function __construct($appName, IRequest $request, IUserSession $userSession, IConfig $config, ILogger $logger) {
        parent::__construct($appName, $request);

        $this->request = $request;
        $this->userSession = $userSession;
        $this->config = $config;
        $this->logger = $logger;

        $this->log('DTNController initialized: ' . print_r($this->config->getAppKeys('dtn'), TRUE), 'info');
    }

    /**
     * 
     * @return type
     * @NoCSRFRequired
     */
    public function index() {

        return [
            'message' => 'DTNController says Hi',
            'result' => 'success',
            'session' => $this->userSession->getUser()->getUID(),
            'config' => $this->config,
        ];
    }

    /**
     * 
     * @return type
     * @NoAdminRequired
     */
    public function transferFiles() {
        $dataPath = $this->config->getSystemValue('datadirectory');
        $senderUID = $this->userSession->getUser()->getUID();
        return [
            "message" => "Your files will be transfered using the DTN",
            "result" => "success",
            "senderFullDataPath" => "$dataPath/$senderUID/files",
            "UID" => $senderUID
        ];
    }

    private function log($message, $level = NULL) {
        $this->logger->log($level, $message);
    }

}
