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
script('dtn', 'personalSettings');
style('dtn', 'dtn');
?>
<div id="dtnPluginUserSettings" class="dtn-settings section">
    <h2 class="app-name"><?php p($l->t('DTN Plugin')); ?></h2><hr>
    <h3 class="app-name"><?php p($l->t('DTN user settings')); ?></h3>

    <div class="settings">
        <div class="setting dtnUID">
            <label>Your DTN user id:</label><input id="dtnUID" name="dtnUID" type="text" value="<?php echo $_['dtnUID']; ?>"><div class="notification"><span></span></div>
        </div>
    </div>
</div>
