<?php
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
