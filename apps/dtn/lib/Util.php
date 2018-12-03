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

namespace OCA\DTN;

/**
 * Description of Util
 *
 * @author antoonp
 */
class Util {

    /**
     * Finds and returns the user with the specified DTN user id. Returns NULL if the user is not found.
     * @param string $dtnUID
     * @return \OCP\IUser
     */
    public static function findUserForDTNUserId(string $dtnUID = 'someUserId') {
        $_user = NULL;
        try {
            if (isset($dtnUID) && trim($dtnUID) !== '') {
                $_users = \OC::$server->getConfig()->getUsersForUserValue('dtn', 'dtnUID', $dtnUID);
                if (count($_users) == 1) {
                    $_user = \OC::$server->getUserManager()->get($_users[0]);
//                    $_user = $this->userManager->get($_users[0]);
                } else if (count($_users) > 1) {
                    throw new \Exception("DTN user id not unique ($dtnUID)");
                }
            }
        } catch (\Exception $ex) {
            $this->logger->log('error', "An exception has occurred when trying to lookup user with UID '$dtnUID'.");
            $this->logger->logException($ex);
        }
        return $_user;
    }

}
