<?php

/**
 * SURFsara
 */

namespace OCA\DTN\AppInfo;

/**
 * The routes configuration of the DTN controller.
 */
$application = new Application();
$application->registerRoutes(
        $this, [
    'routes' => [
        [
            'name' => 'dtn#transferFiles',
            'url' => '/transferfiles',
            'verb' => 'GET'
        ],
        [
            'name' => 'dtn#index',
            'url' => '/',
            'verb' => 'GET'
        ],
        [
            'name' => 'config_provider#getDataLocationInfo',
            'url' => '/config/datalocationinfo',
            'verb' => 'GET',
        ]
    ]
        ]
);
