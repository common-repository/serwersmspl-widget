<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php include_once 'messages.php'; ?>
<div class="wrap">
    <form method="get" id="form_recive">
    <input type="hidden" name="page" value="serwersms_recived" />
    <h2>        
        <?php _e('Recived messages', 'serwersms'); ?>
        <select name="type" id="type_recive">
            <?php
                $types = array('eco','2way','nd','ndi');
                foreach($types as $t){
                    $selected = ($t == $type) ? 'selected="selected"' : '';
                    echo '<option value="'.$t.'" '.$selected.'>'.strtoupper($t).'</option>';
                }
            ?>
        </select>
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