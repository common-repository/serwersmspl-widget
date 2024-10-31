<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="serwersms-header"></div>
<?php include_once 'messages.php'; ?>
<div class="wrap">
    <form action="options.php" method="post">
        <h2>SerwerSMS.pl - <?php _e('settings', 'serwersms'); ?></h2>
        <?php
            settings_fields('SerwerSmsAccessSettingsPage');
            do_settings_sections('SerwerSmsAccessSettingsPage');
            submit_button(__('Save', 'serwersms'));
        ?>
    </form>
    
    <?php if(SerwerSms::ssms_api_connect()): ?>
    
    <br /><hr /><br />
    <form action="options.php" method="post">
        <?php
            settings_fields('SerwerSmsConfigurationSettingsPage');
            do_settings_sections('SerwerSmsConfigurationSettingsPage');
        ?>
    <br /><hr /><br />
        <?php
            do_settings_sections('SerwerSmsRegistrationSettingsPage');
            submit_button(__('Save', 'serwersms'));
        ?>
    </form>
    <?php endif; ?>
    
</div>