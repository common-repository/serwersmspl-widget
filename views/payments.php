<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php include_once 'messages.php'; ?>
<div class="wrap">
    <h2>
        <?php _e('Payments', 'serwersms'); ?>
    </h2>
    
    <?php
        $list->display();
    ?>
    
</div>