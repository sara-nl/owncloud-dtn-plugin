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
