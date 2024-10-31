<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="serwersms-header"></div>
<?php include_once 'messages.php'; ?>
<div class="wrap">
    <form method="post">

        <h2>
        <?php 
        if(isset($contact_id)){
            _e('Edit contact', 'serwersms');
        } else {
            _e('Add new contact', 'serwersms');
        }
        ?>
        </h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php _e('First name','serwersms'); ?></th>
                    <td><input type="text" name="first_name" value="<?php if(isset($fields['firstname'])) echo $fields['firstname']; ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Last name','serwersms'); ?></th>
                    <td><input type="text" name="last_name" value="<?php if(isset($fields['lastname'])) echo $fields['lastname']; ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Company name','serwersms'); ?></th>
                    <td><input type="text" name="company" value="<?php if(isset($fields['company'])) echo $fields['company']; ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Phone','serwersms'); ?></th>
                    <td><input type="text" name="phone" value="<?php if(isset($fields['phone'])) echo $fields['phone']; ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('E-mail','serwersms'); ?></th>
                    <td><input type="text" name="email" value="<?php if(isset($fields['email'])) echo $fields['email']; ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Group','serwersms'); ?></th>
                    <td>
                        <?php SerwerSms::ssms_group_field_render(); ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
        
        if(isset($contact_id)){
            wp_nonce_field( 'edit_contact_'.((int) $contact_id) );
            submit_button(__('Save', 'serwersms'));
            ?>
            <input type="hidden" name="id" value="<?php echo (int) $contact_id; ?>" />
            <?php
        } else {
            submit_button(__('Add', 'serwersms'));
        }
        
        ?>
        <input type="hidden" name="settings-updated" value="1" />
    </form>
    
</div>