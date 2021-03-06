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
use OCP\IUserSession;
use OCP\IConfig;
use OCP\ILogger;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Description of DtnController
 *
 * @author antoonp
 */
class DtnController extends Controller {

//    private $userSession;
//    private $config;
    private $logger;

    function __construct($appName, IRequest $request, IUserSession $userSession, IConfig $config, ILogger $logger) {
        parent::__construct($appName, $request);

        $this->request = $request;
        $this->userSession = $userSession;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * This method performs the call to the DTN agent using the specified parameters.
     * @return string (JSON) the result of the DTN transfer call.
     * @NoAdminRequired
     */
    public function transferFiles() {
        $_dtnAgentMessage = $this->createInternalNotificationMessage();
        if (!isset($_dtnAgentMessage["error"]) && isset($_dtnAgentMessage["message"])) {
            /* Now call the DTN agent */
            $_transferResponse = $this->dtnAgentInternalNotification($_dtnAgentMessage["message"]);
            $message = "Your files will be transfered using the DTN";
            $result = '';
            if (isset($_transferResponse['error'])) {
                $message = 'An error has occured, the files may not have been transfered.';
            } else {
                $result = $_transferResponse["result"];
            }

            return [
                "message" => $message,
                "dtnAgentResponse" => $result
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
    private function createInternalNotificationMessage() {
        $_result = [];
        $files = $this->request->getParam('files');
        if (is_array($files) && count($files) > 0) {
            $dataPath = $this->config->getSystemValue('datadirectory');
            $senderUID = $this->userSession->getUser()->getUID();
            $receiverDNTUID = $this->request->getParam('receiverDTNUID');
            $receiverType = $this->request->getParam('receiverType');
            $_message = ["recipients" => [], "files" => []];
            /* prepare and sanitize file names */
            if ('email' === $receiverType) {
                array_push($_message["recipients"], ["type" => $receiverType, "email" => $receiverDNTUID]);
            }
            foreach ($files as $_file) {
                if (isset($_file["filePath"]) && isset($_file["fileName"]) && isset($_file["fileSize"])) {
                    array_push($_message["files"], [
                        "type" => "path",
                        "path" => "$senderUID/files/" . trim(trim($_file["filePath"], '/') . '/' . $_file["fileName"], '/'),
                        "metadata" => [
                            "name" => $_file["fileName"],
                            "size" => intval($_file["fileSize"])
                    ]]);
                }
            }
            /* Set sender details */
//            $this->logger->log('info', 'system id: ' . $this->config->getSystemValue('id'));
            $_senderDTNUID = $this->config->getUserValue($this->userSession->getUser()->getUID(), 'dtn', 'dtnUID');
            $_message["sender"] = [
                "type" => "email",
                "email" => $_senderDTNUID
            ];
            $_result["message"] = $_message;
        } else {
            $_result["error"] = "No files selected";
        }
        return $_result;
    }

    /**
     * Performs the actual call to the DTN agent.
     * @param array $files
     */
    private function dtnAgentInternalNotification($message = []) {
        $_result = [];
        try {
            $_dtnAgentIp = $this->config->getAppValue('dtn', 'dtnAgentIP');
            if (!isset($_dtnAgentIp) || trim($_dtnAgentIp) === '') {
                throw new \Exception('DTN agent server ip not found. Has this been set ?');
            }
            $_url = "https://$_dtnAgentIp/internal_notification";
            $client = new GuzzleClient();
            $_response = $client->post($_url, [
                'json' => $message,
                'verify' => FALSE
            ]);
//            $this->log('status code: ' . $_response->getStatusCode(), 'info');
            $_result['result'] = json_decode($_response->getBody()->getContents());
        } catch (\Exception $ex) {
            $this->logger->logException($ex);
            $_result['error'] = 'An error has occurred.';
        }
        return $_result;
    }

}
