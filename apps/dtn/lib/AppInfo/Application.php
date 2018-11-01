<?php

/**
 * SURFsara
 */

namespace OCA\DTN\AppInfo;

use OCP\AppFramework\App;
use OCA\DTN\controller\DtnController;
use OCA\DTN\controller\ConfigProviderController;
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

//        $server->getConfig()->deleteAppValue('dtn', 'dtnUID');
//        $server->getConfig()->setUserValue('admin', 'dtn', 'dtnUID', 'admin@dtn-agent.com');
//        $server->getConfig()->setUserValue('antoon', 'dtn', 'dtnUID', 'antoonp@dtn-agent.com');
//        $server->getConfig()->setUserValue('antoon', 'dtn', 'dtnID', 'antoon@dtn.nl');
//        $server->getConfig()->setUserValue('pietje', 'dtn', 'dtnID', 'pietje@dtn.nl');
    }

}
