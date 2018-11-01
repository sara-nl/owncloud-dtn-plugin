<?php
script('dtn', 'personalSettings');
style('dtn', 'dtn');
?>
<div id="dtnPluginSettings" class="dtn-settings section">
    <h2 class="app-name"><?php p($l->t('DTN plugin settings')); ?></h2>

    <div class="settings">
        <div class="setting dtnUID">
            <label>Your DTN user id:</label><input id="dtnUID" name="dtnUID" type="text" value="<?php echo $_['dtnUID']; ?>"><div class="notification"><span></span></div>
        </div>
        <div class="setting dtnAgentIP">
            <label>DTN Agent server ip and port(optional):</label><input id="dtnAgentIP" name="dtnAgentIP" type="text" value="<?php echo $_['dtnAgentIP']; ?>"><div class="notification"><span></span></div>
        </div>
    </div>
</div>
