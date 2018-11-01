<?php

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
            \OC::$server->getL10NFactory(), \OC::$server->getLogger()
    );
}, function () {
    $l = \OC::$server->getL10N('dtn');
    return [
        'id' => 'dtn',
        'name' => $l->t('DTN'),
    ];
});

