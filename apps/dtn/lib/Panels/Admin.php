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
 * Description of Admin
 *
 * @author antoonp
 */
class Admin implements ISettings {

    /** @var IConfig */
    protected $config;
    protected $userSession;

    public function __construct(IConfig $config, IUserSession $userSession) {
        $this->config = $config;
        $this->userSession = $userSession;
    }

    public function getPanel() {
        $tmpl = new Template('dtn', 'adminSettings');
        $tmpl->assign('dtnAgentIP', $this->config->getAppValue('dtn', 'dtnAgentIP'));
        return $tmpl;
    }

    public function getPriority(): int {
        return 0;
    }

    public function getSectionID(): string {
        return 'additional';
    }

}
