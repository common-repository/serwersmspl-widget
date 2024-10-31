<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
global $errors;
global $success;
?>
<?php if((isset($_POST['settings-updated']) and $_POST['settings-updated'] == 1) or (isset($errors) and !empty($errors)) or (isset($success) and !empty($success))):?>
    <?php if(isset($errors) && count($errors)>0):?>
        <div class="error"><p>
            <?php
                $errors = array_unique($errors);
                foreach ($errors as $error) 
                    print_r($error . '<br />');
            ?>
            </p></div>
    <?php elseif(isset($success) && count($success)>0): ?>
    <div class="updated"><p>
            <?php 
                foreach ($success as $s)
                    print_r($s . '<br />');
            ?>
        </p></div>
    <?php else: ?>
        <div class="updated"><p><?php _e('Changes have been saved','serwersms'); ?></p></div>
    <?php endif;?>
<?php endif;?>