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
