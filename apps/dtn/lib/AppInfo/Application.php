<?php

/**
 * SURFsara
 */

namespace OCA\DTN\AppInfo;

use OCP\AppFramework\App;
use OCA\DTN\controller\DTNController;
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
        $container->registerService('DTNController', function (IContainer $container) use ($server) {
            return new DTNController(
                    $container->query('AppName'), $server->getRequest(), $server->getUserSession(), $server->getConfig(), $container->query('Logger')
            );
        });

        $container->registerService('ConfigProviderController', function (IContainer $container) use ($server) {
            return new ConfigProviderController(
                    $container->query('AppName'), $server->getRequest(), $server->getUserManager(), $server->getUserSession(), $server->getConfig(), $container->query('Logger')
            );
        });
        
//        $server->getConfig()->setUserValue('admin', 'dtn', 'dtnID', 'test01@dtn.nl');
//        $server->getConfig()->setUserValue('antoon', 'dtn', 'dtnID', 'antoon@dtn.nl');
//        $server->getConfig()->setUserValue('pietje', 'dtn', 'dtnID', 'pietje@dtn.nl');
    }

}
