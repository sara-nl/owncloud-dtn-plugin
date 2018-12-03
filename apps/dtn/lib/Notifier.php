<?php

/**
 * @copyright (c) 2018, SURFsara
 */

namespace OCA\DTN;

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

    function __construct(\OCP\L10N\IFactory $factory) {
        $this->factory = $factory;
    }

    public function prepare(INotification $notification, $languageCode) {

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
                            (string) $l->t('A message has been send to you by "%1$s"', $params)
                    );
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
                return $notification;

            default:
                // Unknown subject => Unknown notification => throw
                throw new \InvalidArgumentException();
        }
    }

}
