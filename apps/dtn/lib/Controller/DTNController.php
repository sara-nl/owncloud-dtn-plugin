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
use GuzzleHttp\Client as GuzzleClient;

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
     * This method performs the call to the DTN agent using the specified parameters.
     * @return string (JSON) the result of the DTN transfer call.
     * @NoAdminRequired
     */
    public function transferFiles() {
        $_dtnAgentMessage = $this->createExternalNotificationMessage($this->request);
        if (!isset($_dtnAgentMessage["error"]) && isset($_dtnAgentMessage["message"])) {
            foreach ($files as $_file) {
                array_push($fileNames, trim($_file, '/'));
            }
            /* Now call the DTN agent */
            $_transferResponse = $this->dtnAgentExternalNotification($_dtnAgentMessage["message"]);
            $message = "Your files will be transfered using the DTN";
            
            if (isset($_transferResponse['error']))
                $message = 'An error has occured, the files may not have been transfered.';
            else
                $result = $_transferResponse["result"];

            $this->log($result, 'info');
            return [
                "message" => $message,
                "dtnAgentResponse" => $result
//                "senderFullDataPath" => "$dataPath/$senderUID/files",
//                "senderOwnCloudUID" => $senderUID,
//                "receiverDTNUID" => $receiverDNTUID,
//                "files" => $fileNames
            ];
        } else {
            return [
                "error" => $_dtnAgentMessage["error"],
                "message" => "failure"
            ];
        }
    }

    /**
     * 
     * @param IRequest $request
     */
    private function createExternalNotificationMessage($request) {
        $_result = [];
        $files = $this->request->getParam('files');
        if (is_array($files) && count($files) > 0) {
            $dataPath = $this->config->getSystemValue('datadirectory');
            $senderUID = $this->userSession->getUser()->getUID();
            $receiverDNTUID = $this->request->getParam('receiverDTNUID');
            $receiverType = $this->request->getParam('receiverType');
            $fileNames = [];
            $_message = ["recipients" => [], "files" => [], "sender" => []];
            /* prepare and sanitize file names */
            if ('email' === $receiverType)
                array_push($_message["recipients"], ["type" => $receiverType, "email" => $receiverDNTUID]);
            foreach ($files as $_file) {
                if (isset($_file["filePath"]) && isset($_file["fileName"]) && isset($_file["fileSize"])) {
                    array_push($_message["files"], [
                        "type" => "name",
                        "name" => "$dataPath/$senderUID/files" . trim($_file["filePath"], '/'),
                        "metadata" => [
                            "name" => $_file["fileName"],
                            "size" => $_file["fileSize"]
                    ]]);
                }
            }
            /* Set sender details */
            $_senderEmail = "";
            array_push($_message["sender"], [
                "type" => "email",
                "email" => $_senderEmail
            ]);
            $_result["message"] = $_message;
        } else
            $_result["error"] = "No files selected";
        return $_result;
    }

    /**
     * Performs the actual call to the DTN agent.
     * @param array $files
     */
    private function dtnAgentExternalNotification($message = []) {
        $_result = [];
        try {
//            $_data = [
//                "recipients" =>
//                [[
//                "type" => "owncloud",
//                "email" => "email"
//                    ]],
//                "files" => [
//                    [
//                        "type" => "name",
//                        "name" => "the file location",
//                        "metadata" => [
//                            "name" => "the file name",
//                            "size" => 1234567890
//                        ]
//                    ]
//                ],
//                "sender" => [
//                    "type" => "email",
//                    "email" => "receiver@theotherendoftheworld.com"
//                ]
//            ];
            $_url = 'https://172.27.242.113:3001/external_notification';
            $client = new GuzzleClient();
            $_response = $client->post($_url, [
                'json' => $message,
                'verify' => FALSE
            ]);
//            $this->log('status code: ' . $_response->getStatusCode(), 'info');
            $_result['result'] = json_decode($_response->getBody()->getContents());
        } catch (Exception $ex) {
            $this->logger->logException($ex);
            $_result['error'] = 'An error has occurred.';
        }
        return $_result;
    }

    private function log($level, $message = NULL) {
        if (isset($exception) || $level === 'error')
            $this->logger->logException($exception);
        else
            $this->logger->log($level, $message);
    }

}
