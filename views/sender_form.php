<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="serwersms-header"></div>
<?php include_once 'messages.php'; ?>
<div class="wrap">
    <form method="post">

        <h2><?php _e('Add new sender name', 'serwersms'); ?></h2>
        <?php
        echo __('Possibilities and limitations:', 'serwersms').'<br />';
        echo __('The name can not be number 9-digit (eg 500600700)', 'serwersms').'<br />';
        echo __('The name can not be short number (eg 71200)', 'serwersms').'<br />';
        echo __('The name can contain up to 11 characters in the range az, AZ, 0-9, and additionally special characters like: space, dot, dash', 'serwersms').'<br />';
        echo __('The name can not contain 4 or more digits in the string (eg Test2011)', 'serwersms').'<br />';
        echo __('It is forbidden to add the names of which may be misleading, eg POLICE. Names such will be rejected.', 'serwersms').'<br />';
        echo __('If you add a name that uniquely identifies a brand or product, you will need to send permission to use are exact name.', 'serwersms');
                ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php _e('Sender name','serwersms'); ?></th>
                    <td><input type="text" value="" name="sender"></td>
                </tr>
            </tbody>
        </table>
        <?php
            submit_button(__('Add', 'serwersms'));
        ?>
        <input type="hidden" name="settings-updated" value="1" />
    </form>
    
    <a href="?page=serwersms" class="button"><?php _e('Back','serwersms'); ?></a>
    
</div>