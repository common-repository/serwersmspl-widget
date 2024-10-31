<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
    <h2>
        <?php _e('Contacts list', 'serwersms'); ?>
        <a href="?page=<?php echo $_REQUEST['page']; ?>&action=add&group_id=<?php echo $group_id; ?>" class="add-new-h2"><?php _e('Add a new contact','serwersms'); ?></a>
    </h2>
    <?php if($groups_select): ?>
    <form method="get" id="form_group">
        <?php _e('Show the contacts from a group','serwersms'); ?>:
        <?php SerwerSms::ssms_group_field_render(); ?>
        <input type="hidden" name="page" value="serwersms_contacts" />
    </form>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="page" value="<?php sanitize_text_field($_REQUEST['page']); ?>" />
        <?php $contactList->search_box(__('Search','serwersms'), 'search_id'); ?>
    </form>
    
    <form id="movies-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
        <?php
            $contactList->display();  
        ?>
    </form>
    
</div>