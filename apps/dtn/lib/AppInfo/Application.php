<?php

/**
 * SURFsara
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
         * @todo Remove this setting This setting inactivates the code integrity check !! 
         * It must be removed when the app goes into production */
        $server->getConfig()->setSystemValue('integrity.check.disabled', true);
        /* Reset the system setting to false again */
//        $server->getConfig()->setSystemValue('integrity.check.disabled', false);
    }

}
