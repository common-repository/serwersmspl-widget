<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class SerwerSMS_Widget extends WP_Widget{
    
    function __construct(){
        load_plugin_textdomain('serwersms', false, basename(dirname(__FILE__)) . '/languages/');
        
        parent::__construct(
                    'serwersms_widget',
                    __('SerwerSMS.pl Widget','serwersms'),
                    array('description' => __('Form to adding contacts','serwersms'))
                );
    }
    
    public function widget($args, $instance){
        
        echo $args['before_widget'];
        
        if(!empty($instance['title'])){
            echo $args['before_title'].apply_filters('widget_title',$instance['title']).$args['after_title'];
        }
        
        ?>

        <div class="serwersms_loading" style="display: none">
            <p style="text-align: center"><img src="<?php echo plugins_url( 'image/loading.png', __FILE__) ?>" /></p>
        </div>

        <div class="serwersms_message" style="display: none">
            <p class="serwersms_txt"></p>
            <p><input type="button" class="serwersms_back" value="<?php _e('Back','serwersms'); ?>" /></p>
        </div>

        <div class="serwersms_form_phone">
            <p><?php echo apply_filters('widget_description',$instance['description']); ?></p>
            <p>
                <label for="serwersms_phone"><?php _e('Phone:','serwersms'); ?></label>
                <input type="text" name="serwersms_phone" id="serwersms_phone" />
            </p>
            <p>
                <input type="button" class="serwersms_submit" id="serwersms_add" value="<?php _e('Add','serwersms'); ?>" />
                <input type="button" class="serwersms_submit" id="serwersms_remove" value="<?php _e('Delete','serwersms'); ?>" />
            </p>
        </div>

        <div class="serwersms_form_code" style="display: none">
            <p><?php _e('Enter Your SMS Code:','serwersms'); ?></p>
            <p>
                <label for="serwersms_code"><?php _e('SMS Code:','serwersms'); ?></label>
                <input type="text" name="serwersms_code" id="serwersms_code" />
                <input type="hidden" name="serwersms_operation" value="" class="serwersms_operation" />
            </p>
            <p>
                <input type="button" class="serwersms_code_submit" value="<?php _e('Continue','serwersms'); ?>" />
            </p>
        </div>
        
        <?php
        echo $args['after_widget'];
    }
    
    public function form($instance){
        
        $title = !empty($instance['title']) ? $instance['title'] : __('Newsletter SMS','serwersms');
        $description = !empty($instance['description']) ? $instance['description'] : __('Enter your phone number','serwersms');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo esc_attr($description); ?></textarea>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance){
        
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['description'] = (!empty($new_instance['description'])) ? strip_tags($new_instance['description']) : '';
        
        return $instance;
    }
    
}

function serwersms_register_widgets() {
	register_widget('SerwerSMS_Widget');
}

add_action( 'widgets_init', 'serwersms_register_widgets' );