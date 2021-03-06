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
/* Load the dtn script with the Files app */
$eventDispatcher = \OC::$server->getEventDispatcher();
$eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
    script('dtn', 'dtn');
    style('dtn', 'dtn');
});

/* Register the DTN notifier with the notification manager */
$notificationManager = \OC::$server->getNotificationManager();
$notificationManager->registerNotifier(function () {
    return new OCA\DTN\Notifier(
            \OC::$server->getL10NFactory()
    );
}, function () {
    $l = \OC::$server->getL10N('dtn');
    return [
        'id' => 'dtn',
        'name' => $l->t('DTN'),
    ];
});

