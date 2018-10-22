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
        $message = "Your files will be transfered using the DTN";
        $result = "success";
        $dataPath = $this->config->getSystemValue('datadirectory');
        $senderUID = $this->userSession->getUser()->getUID();
        $receiverDNTUID = $this->request->getParam('receiverDTNUID');
        $files = $this->request->getParam('files');
        $fileNames = [];
        /* prepare and sanitize file names */
        if(is_array($files) && count($files) > 0) {
            foreach ($files as $_file) {
                array_push($fileNames, trim($_file, '/'));
            }
        } else {
            $message = "No files selected";
            $result = "failure";
        }
        return [
            "message" => $message,
            "result" => $result,
            "senderFullDataPath" => "$dataPath/$senderUID/files",
            "senderOwnCloudUID" => $senderUID,
            "receiverDTNUID" => $receiverDNTUID,
            "files" => $fileNames
        ];
    }

    private function log($message, $level = NULL) {
        $this->logger->log($level, $message);
    }

}
