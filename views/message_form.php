<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php include_once 'messages.php'; ?>
<div class="wrap">
    <form method="post">

        <h2><?php _e('Send SMS', 'serwersms'); ?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php _e('Send to','serwersms'); ?></th>
                    <td>
                        <input type="radio" name="send_to" value="1" id="send_to_group" <?php if(!$phone) echo 'checked="checked"'; ?>>
                        <label for="send_to_group"><?php _e('Group','serwersms'); ?></label>
                        
                        <?php SerwerSms::ssms_group_field_render(); ?>
                            
                        <br />
                        <input type="radio" name="send_to" value="2" id="send_to_number" <?php if($phone) echo 'checked="checked"'; ?>>
                        <label for="send_to_number"><?php _e('Phone','serwersms'); ?></label>
                        <input type="text" name="phone" value="<?php if($phone) echo $phone; ?>" id="phone" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Sender name','serwersms'); ?></th>
                    <td><?php SerwerSms::ssms_sender_field_render(); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Message','serwersms'); ?></th>
                    <td><textarea name="message" rows="5" cols="60" id="text"></textarea>
                        <div id="chars-counter">
                            <?php _e('Entered','serwersms'); ?> <span id="chars" style="font-weight: bold;">0</span> <?php _e('characters','serwersms'); ?>.
                            <?php _e('To send','serwersms'); ?> <span id="messages" style="font-weight: bold;">0</span> SMS.
                        </div>
                        <input type="hidden" name="chars-limit" value="<?php echo $chars_limit; ?>" id="chars-limit" />
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
            submit_button(__('Send', 'serwersms'));
        ?>
        <input type="hidden" name="settings-updated" value="1" />
        <input type="hidden" name="action" value="send" />
    </form>
    
</div>