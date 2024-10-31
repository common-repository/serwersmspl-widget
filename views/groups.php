<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
    <h2>
        <?php _e('Groups', 'serwersms'); ?>
        <a href="?page=<?php echo sanitize_text_field($_REQUEST['page']); ?>&action=add" class="add-new-h2"><?php _e('Add a new group','serwersms'); ?></a>
    </h2>

    <form method="post">
        <input type="hidden" name="page" value="<?php sanitize_text_field($_REQUEST['page']); ?>" />
        <?php $list->search_box(__('Search','serwersms'), 'search_id'); ?>
      </form>
    
    <form id="movies-filter" method="get">
        <input type="hidden" name="page" value="<?php echo sanitize_text_field($_REQUEST['page']); ?>" />
        <?php
            $list->display();  
        ?>
    </form>
    
</div>