<?php
script('dtn', 'adminSettings');
style('dtn', 'dtn');
?>
<div id="dtnPluginAdminSettings" class="dtn-settings section">
    <h2 class="app-name"><?php p($l->t('DTN Plugin')); ?></h2><hr>
    <h3 class="app-name"><?php p($l->t('DTN agent settings')); ?></h3>

    <div class="settings">
        <div class="setting dtnAgentIP">
            <label>DTN Agent server ip and port(optional):</label><input id="dtnAgentIP" name="dtnAgentIP" type="text" value="<?php echo $_['dtnAgentIP']; ?>"><div class="notification"><span></span></div>
        </div>
    </div>
</div>
