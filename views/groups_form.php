<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="serwersms-header"></div>
<?php include_once 'messages.php'; ?>
<div class="wrap">
    <form method="post">

        <h2>
        <?php 
        if(isset($group_id)){
            _e('Edit group', 'serwersms');
        } else {
            _e('Add a new group', 'serwersms');
        }
        ?>
        </h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php _e('Name','serwersms'); ?></th>
                    <td><input type="text" name="name" value="<?php if(isset($fields['name'])) echo $fields['name']; ?>"></td>
                </tr>
            </tbody>
        </table>
        <?php
        
        if(isset($group_id)){
            wp_nonce_field( 'edit_group_'.((int) $group_id) );
            submit_button(__('Save', 'serwersms'));
            ?>
            <input type="hidden" name="id" value="<?php echo (int) $group_id; ?>" />
            <?php
        } else {
            submit_button(__('Add', 'serwersms'));
        }
        
        ?>
        <input type="hidden" name="settings-updated" value="1" />
    </form>
    
</div>