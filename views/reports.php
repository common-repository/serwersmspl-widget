<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php include_once 'messages.php'; ?>
<div class="wrap">
    <form method="get" id="form_reports">
    <input type="hidden" name="page" value="serwersms_reports" />
    <h2>
        <?php _e('Delivery reports', 'serwersms'); ?>
        <select name="status" id="reports_select">
            <?php
                $statuses = array(
                    'sent' => __('Sent','serwersms'),
                    'delivered' => __('Delivered','serwersms'),
                    'undelivered' => __('Undelivered','serwersms'),
                    'unsent' => __('Unsent','serwersms')
                );
                foreach($statuses as $k => $s){
                    echo '<option value="'.$k.'" ';
                    selected($k,$status);
                    echo '>'.$s.'</option>';
                }
            ?>
        </select>
        <a href="?page=serwersms_message" class="add-new-h2"><?php _e('Send a new message','serwersms'); ?></a>
    </h2>
    </form>
    <form method="post">
        <input type="hidden" name="page" value="<?php sanitize_text_field($_REQUEST['page']); ?>" />
        <?php $list->search_box(__('Search','serwersms'), 'search_id'); ?>
      </form>
    
    <?php
        $list->display();
        
    ?>
    
</div>