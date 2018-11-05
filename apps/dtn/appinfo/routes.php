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
        $this, ['routes' => [
        [
            'name' => 'dtn#transferFiles',
            'url' => '/transferfiles',
            'verb' => 'GET'
        ],
        [
            'name' => 'config_provider#getDataLocationInfo',
            'url' => '/config/datalocationinfo/{receiverDTNUID}',
            'verb' => 'GET'
        ],
        [
            'name' => 'notification#addNotification',
            'url' => '/notifier/notification',
            'verb' => 'POST'
        ],
        [
            'name' => 'dtn_settings#setUserSetting',
            'url' => '/dtnsettings/user/{key}',
            'verb' => 'POST'
        ],
        [
            'name' => 'dtn_settings#setAdminSetting',
            'url' => '/dtnsettings/admin/{key}',
            'verb' => 'POST'
        ]
    ]
        ]
);
