<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OCA\DTN\controller;

use OCP\Notification\INotifier;
use OCP\Notification\INotification;

/**
 * Description of Notifier
 *
 * @author antoonp
 */
class Notifier implements INotifier {

    /** @var \OCP\L10N\IFactory */
    protected $factory;

    /** @var \OCP\ILogger */
    protected $logger;

    function __construct(\OCP\L10N\IFactory $factory, \OCP\ILogger $logger) {
        $this->factory = $factory;
        $this->logger = $logger;
    }

    public function prepare(INotification $notification, $languageCode) {
        $this->logger->log(\OCP\Util::INFO, 'prepare method called');

        if ($notification->getApp() !== 'dtn') {
            // Not my app => throw
            throw new \InvalidArgumentException();
        }

        // Read the language from the notification
        $l = $this->factory->get('dtn', $languageCode);

        switch ($notification->getObjectType()) {
            // Deal with known subjects
            case 'dtn':
                $params = $notification->getSubjectParameters();
                if (isset($params[0])) {
                    $notification->setParsedSubject(
                            (string) $l->t('One or more files have been send to you by "%1$s"', $params)
                    );
//                } else {
//                    $notification->setParsedSubject(
//                            (string) $l->t('"%1$s" shared "%3$s" with you', $params)
//                    );
                }

                $messageParams = $notification->getMessageParameters();
                if (isset($messageParams[0]) && is_array($messageParams[0])) { // an array with the transfered files
                    $_message = count($messageParams) > 0 ? "The following files have been transfered to you:" : "No further details";
                    foreach ($messageParams as $_nextMessage) {
                        $_message .= "\n$_nextMessage";
                    }
                    $notification->setParsedMessage(
                            (string) $l->t($_message, $messageParams)
                    );
                } else if (isset($messageParams[0]) && is_string($messageParams[0])) {
                    $notification->setParsedMessage(
                            (string) $l->t('%1$s', $messageParams)
                    );
                } else {
                    $notification->setParsedMessage(
                            (string) $l->t('No further details', $messageParams)
                    );
                }

                // Deal with the actions for a known subject
//                foreach ($notification->getActions() as $action) {
//                    switch ($action->getLabel()) {
//                        case 'accept':
//                            $action->setParsedLabel(
//                                            (string) $l->t('Accept')
//                                    )
//                                    ->setPrimary(true);
//                            break;
//
//                        case 'decline':
//                            $action->setParsedLabel(
//                                    (string) $l->t('Decline')
//                            );
//                            break;
//                    }
//
//                    $notification->addParsedAction($action);
//                }
                return $notification;

            default:
                // Unknown subject => Unknown notification => throw
                throw new \InvalidArgumentException();
        }
    }

}
