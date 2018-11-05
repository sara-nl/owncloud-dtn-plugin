<?php

/*
 * SURFsara
 */

namespace OCA\DTN\Panels;

use OCP\IConfig;
use OCP\IUserSession;
use OCP\Settings\ISettings;
use OCP\Template;

/**
 * Description of Personal
 *
 * @author antoonp
 */
class Personal implements ISettings {

    /** @var IConfig */
    protected $config;
    protected $userSession;

    public function __construct(IConfig $config, IUserSession $userSession) {
        $this->config = $config;
        $this->userSession = $userSession;
    }

    public function getPanel() {
        $tmpl = new Template('dtn', 'personalSettings');
        $tmpl->assign('dtnUID', $this->config->getUserValue($this->userSession->getUser()->getUID(), 'dtn', 'dtnUID'));
        return $tmpl;
    }

    public function getPriority(): int {
        return 0;
    }

    public function getSectionID(): string {
        return 'additional';
    }

}
