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

namespace OCA\DTN\AppInfo;

use OCP\AppFramework\App;
use OCA\DTN\Controller\DtnController;
use OCA\DTN\Controller\ConfigProviderController;
use OCP\IContainer;

/**
 * The application configuration
 *
 * @author antoonp
 */
class Application extends App {

    public function __construct(array $urlParams = []) {
        parent::__construct('dtn', $urlParams);

        $container = $this->getContainer();
        $server = $container->getServer();
        $container->registerService('DtnController', function (IContainer $container) use ($server) {
            return new DtnController(
                    $container->query('AppName'), $server->getRequest(), $server->getUserSession(), $server->getConfig(), $container->query('Logger')
            );
        });

        $container->registerService('ConfigProviderController', function (IContainer $container) use ($server) {
            return new ConfigProviderController(
                    $container->query('AppName'), $server->getRequest(), $server->getUserManager(), $server->getUserSession(), $server->getConfig(), $container->query('Logger')
            );
        });

        $container->registerService('DtnSettingsController', function (IContainer $container) use ($server) {
            return new ConfigProviderController(
                    $container->query('AppName'), $server->getRequest(), $server->getUserManager(), $server->getUserSession(), $server->getConfig(), $container->query('Logger')
            );
        });

        /**
         * This setting inactivates the code integrity check !! 
         * It must be removed when the app goes into production 
         * @todo Remove this setting
         */
//        $server->getConfig()->setSystemValue('integrity.check.disabled', true);
        /* Reset the system setting to false again */
//        $server->getConfig()->setSystemValue('integrity.check.disabled', false);
    }

}
