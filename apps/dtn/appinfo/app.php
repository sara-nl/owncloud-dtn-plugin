<?php

/* Load the dtn script with the Files app */
$eventDispatcher = \OC::$server->getEventDispatcher();
$eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
    script('dtn', 'dtn');
});
