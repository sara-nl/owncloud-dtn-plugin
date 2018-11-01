<?php

/*
 * SURFsara
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
    public static function findUserForDTNUserId(string $dtnUID) {
        $_user = NULL;
        try {
            if (isset($dtnUID) && trim($dtnUID) !== '') {
                $_users = \OC::$server->getConfig()->getUsersForUserValue('dtn', 'dtnUID', $dtnUID);
//                $_users = $this->config->getUsersForUserValue('dtn', 'dtnUID', $dtnUID);
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
