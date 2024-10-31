<?php
if (!defined('ABSPATH')) exit;

class SerwerSms
{

    private static $initiated = false;
    private static $authorized = false;
    private static $api;
    private static $key = 'J$HS@94y32heEHdDY0a2AW';

    public static function ssms_init()
    {

        if (!self::$initiated) {
            self::ssms_init_hooks();
        }
    }

    public static function ssms_init_hooks()
    {

        self::$initiated = true;

        if (class_exists('SerwerSMS\SerwerSMS')) {
            global $errors;
            $options = get_option('serwersms_settings');
            $page = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : '';
            $action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : '';

            try {
                if (isset($options['username'])) {
                    self::$api = new SerwerSMS\SerwerSMS($options['username'], self::ssms_get_password());
                } else {
                    update_option('serwersms_auth',false);
                }

            } catch (Exception $e) {

                update_option('serwersms_auth',false);
                switch ($e->getMessage()) {
                    case 'Username is empty':
                        $errors[] = __('Username is empty', 'serwersms');
                        if (is_admin() and is_user_logged_in() and $page != 'serwersms' and preg_match('/serwersms/', $page) and $action != 'update') {
                            wp_redirect(admin_url('admin.php?page=serwersms'));
                            exit;
                        }
                        break;
                    case 'Password is empty':
                        $errors[] = __('Password is empty', 'serwersms');
                        if (is_admin() and is_user_logged_in() and $page != 'serwersms' and preg_match('/serwersms/', $page) and $action != 'update') {
                            wp_redirect(admin_url('admin.php?page=serwersms'));
                            exit;
                        }
                        break;
                    default:
                        $errors[] = $e->getMessage();
                        break;
                }
            }

            self::ssms_api_connect();
        }

        add_filter('plugin_action_links', array('SerwerSms', 'ssms_settings_link'), 10, 2);
        add_action('admin_menu', array('SerwerSms', 'ssms_admin_menu'));
        add_action('admin_init', array('SerwerSms', 'ssms_admin_init'));
        add_action('register_form', array('SerwerSms', 'ssms_register_form'));
        add_filter('registration_errors', array('SerwerSms', 'ssms_registration_errors'), 10, 3);
        add_action('user_register', array('SerwerSms', 'ssms_user_register'));
        add_action('show_user_profile', array('SerwerSms', 'ssms_show_extra_profile_fields'));
        add_action('edit_user_profile', array('SerwerSms', 'ssms_show_extra_profile_fields'));
        add_action('personal_options_update', array('SerwerSms', 'ssms_save_extra_profile_fields'));
        add_action('edit_user_profile_update', array('SerwerSms', 'ssms_save_extra_profile_fields'));
        add_action('login_enqueue_scripts', array('SerwerSms', 'ssms_load_login_scripts'));
        load_plugin_textdomain('serwersms', false, basename(dirname(__FILE__)) . '/languages/');
    }

    public static function ssms_admin_menu()
    {
        add_menu_page('SerwerSMS.pl', 'SerwerSMS.pl', 'manage_options', 'serwersms', null, SERWERSMS_PLUGIN_URL . 'image/serwersms_ico.png', 117);
        $settings_page = add_submenu_page('serwersms', 'SerwerSMS.pl', __('Settings', 'serwersms'), 'manage_options', 'serwersms', array('SerwerSms', 'ssms_settings_page'));
        $message_page = add_submenu_page('serwersms', 'SerwerSMS.pl', __('Send SMS', 'serwersms'), 'manage_options', 'serwersms_message', array('SerwerSms', 'ssms_message_page'));
        $contacts_page = add_submenu_page('serwersms', 'SerwerSMS.pl', __('Contacts', 'serwersms'), 'manage_options', 'serwersms_contacts', array('SerwerSms', 'ssms_contacts_page'));
        add_submenu_page('serwersms', 'SerwerSMS.pl', __('Groups', 'serwersms'), 'manage_options', 'serwersms_groups', array('SerwerSms', 'ssms_groups_page'));
        $reports_page = add_submenu_page('serwersms', 'SerwerSMS.pl', __('Delivery reports', 'serwersms'), 'manage_options', 'serwersms_reports', array('SerwerSms', 'ssms_reports_page'));
        $recived_page = add_submenu_page('serwersms', 'SerwerSMS.pl', __('Received messages', 'serwersms'), 'manage_options', 'serwersms_recived', array('SerwerSms', 'ssms_recived_page'));
        add_submenu_page('serwersms', 'SerwerSMS.pl', __('Payments', 'serwersms'), 'manage_options', 'serwersms_payments', array('SerwerSms', 'ssms_payments_page'));
        $account_page = add_submenu_page('serwersms', 'SerwerSMS.pl', __('State of account', 'serwersms'), 'manage_options', 'serwersms_account', array('SerwerSms', 'ssms_account_page'));

        add_action('admin_print_styles-' . $settings_page, array('SerwerSms', 'ssms_load_styles'));
        add_action('admin_print_styles-' . $account_page, array('SerwerSms', 'ssms_load_styles'));
        add_action('admin_print_scripts-' . $message_page, array('SerwerSms', 'ssms_load_scripts'));
        add_action('admin_print_scripts-' . $contacts_page, array('SerwerSms', 'ssms_load_scripts'));
        add_action('admin_print_scripts-' . $recived_page, array('SerwerSms', 'ssms_load_scripts'));
        add_action('admin_print_scripts-' . $reports_page, array('SerwerSms', 'ssms_load_scripts'));
    }

    public static function ssms_settings_link($links, $file)
    {
        if ($file == plugin_basename(SERWERSMS_PLUGIN_DIR . '/serwersms.php')) {
            $settings_link = '<a href="admin.php?page=serwersms">' . __('Settings', 'serwersms') . '</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    public static function ssms_admin_init()
    {
        register_setting('SerwerSmsAccessSettingsPage', 'serwersms_settings');
        register_setting('SerwerSmsConfigurationSettingsPage', 'serwersms_configuration');
        add_filter('pre_update_option_serwersms_settings', array('SerwerSms', 'ssms_data_correction'));
        wp_register_style('serwersms', SERWERSMS_PLUGIN_URL . 'css/serwersms.css');
        wp_register_script('serwersms', SERWERSMS_PLUGIN_URL . 'js/serwersms.js');

        add_settings_section(
            'serwersms_settings_page_section',
            __('Access data', 'serwersms'),
            array('SerwerSms', 'ssms_access_section_desc'),
            'SerwerSmsAccessSettingsPage'
        );

        add_settings_field(
            'username',
            __('Username', 'serwersms'),
            array('SerwerSms', 'ssms_username_field_render'),
            'SerwerSmsAccessSettingsPage',
            'serwersms_settings_page_section'
        );

        add_settings_field(
            'password',
            __('Password', 'serwersms'),
            array('SerwerSms', 'ssms_password_field_render'),
            'SerwerSmsAccessSettingsPage',
            'serwersms_settings_page_section'
        );

        if (self::ssms_api_connect()) {

            add_settings_field(
                'reset',
                __('Reset', 'serwersms'),
                array('SerwerSms', 'ssms_reset_field_render'),
                'SerwerSmsAccessSettingsPage',
                'serwersms_settings_page_section'
            );
        }

        add_settings_section(
            'serwersms_configuration_page_section',
            __('Widget settings', 'serwersms'),
            array('SerwerSms', 'ssms_configuration_section_desc'),
            'SerwerSmsConfigurationSettingsPage'
        );

        add_settings_field(
            'verification_code',
            __('Send verification SMS Code', 'serwersms'),
            array('SerwerSms', 'ssms_verification_field_render'),
            'SerwerSmsConfigurationSettingsPage',
            'serwersms_configuration_page_section',
            array('name' => 'verification_code')
        );

        add_settings_field(
            'sender_name',
            __('Sender name', 'serwersms'),
            array('SerwerSms', 'ssms_sender_field_render'),
            'SerwerSmsConfigurationSettingsPage',
            'serwersms_configuration_page_section',
            array('name' => 'sender_name')
        );

        add_settings_field(
            'group',
            __('Group of contacts', 'serwersms'),
            array('SerwerSms', 'ssms_group_field_render'),
            'SerwerSmsConfigurationSettingsPage',
            'serwersms_configuration_page_section',
            array('name' => 'group')
        );

        add_settings_section(
            'serwersms_registration_page_section',
            __('Registration form', 'serwersms'),
            array('SerwerSms', 'ssms_registration_section_desc'),
            'SerwerSmsRegistrationSettingsPage'
        );

        add_settings_field(
            'phone_registration_field',
            __('Phone field', 'serwersms'),
            array('SerwerSms', 'ssms_verification_field_render'),
            'SerwerSmsRegistrationSettingsPage',
            'serwersms_registration_page_section',
            array('name' => 'phone_registration_field')
        );

        add_settings_field(
            'verification_code_registration',
            __('Send verification SMS Code', 'serwersms'),
            array('SerwerSms', 'ssms_verification_field_render'),
            'SerwerSmsRegistrationSettingsPage',
            'serwersms_registration_page_section',
            array('name' => 'verification_code_registration')
        );

        add_settings_field(
            'sender_name_registration',
            __('Sender name', 'serwersms'),
            array('SerwerSms', 'ssms_sender_field_render'),
            'SerwerSmsRegistrationSettingsPage',
            'serwersms_registration_page_section',
            array('name' => 'sender_name_registration')
        );

        add_settings_field(
            'group_registration',
            __('Group of contacts', 'serwersms'),
            array('SerwerSms', 'ssms_group_field_render'),
            'SerwerSmsRegistrationSettingsPage',
            'serwersms_registration_page_section',
            array('name' => 'group_registration')
        );

    }

    public static function ssms_register_form()
    {

        $options = get_option('serwersms_configuration');
        $phone = (!empty($_POST['phone'])) ? sanitize_text_field($_POST['phone']) : '';

        include_once("views/registration.php");
    }

    public static function ssms_registration_errors($errors, $sanitized_user_login, $user_email)
    {

        $options = get_option('serwersms_configuration');

        if ($options['phone_registration_field'] == 1) {
            if (empty($_POST['phone']) || !empty($_POST['phone']) && trim($_POST['phone']) == '') {
                $errors->add('phone_error', __('<strong>Error</strong>: You must include a phone.', 'serwersms'));
            } elseif (isset($_POST['phone']) and !preg_match("/^[\+\d\s]+$/", $_POST['phone'])) {
                $errors->add('phone_error', __('<strong>Error</strong>: The phone contains invalid characters', 'serwersms'));
            } elseif ($options['verification_code_registration'] == 1 and (!isset($_POST['code']) or !isset($_SESSION['serwersms_code']) or $_POST['code'] != $_SESSION['serwersms_code'])) {
                $errors->add('verification_error', __('<strong>Error</strong>: Incorrect SMS code', 'serwersms'));
            }
        }
        return $errors;

    }

    public static function ssms_user_register($user_id)
    {

        if (isset($_POST['phone']) and !empty($_POST['phone'])) {

            $options = get_option('serwersms_configuration');
            $settings = get_option('serwersms_settings');
            $phone = sanitize_text_field($_POST['phone']);

            update_user_meta($user_id, 'phone', $phone);

            if (isset($options['group_registration']) and !empty($options['group_registration'])) {
                $user_info = get_userdata($user_id);
                $group_id = (int)$options['group_registration'];
                if (self::ssms_api_connect()) {
                    try {
                        self::$api->contacts->add($group_id, $phone, array('first_name' => $user_info->user_login, 'email' => $user_info->user_email));
                    } catch (Exception $ex) {

                    }
                }
            }
            unset($_SESSION['serwersms_code']);
        }
    }

    public static function ssms_show_extra_profile_fields($user)
    {
        ?>
        <h3><?php _e('Additional fields', 'serwersms'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="phone"><?php _e('Phone', 'serwersms'); ?></label></th>
                <td>
                    <input type="text" name="phone" id="phone"
                           value="<?php echo esc_attr(get_the_author_meta('phone', $user->ID)); ?>"
                           class="regular-text"/>
                </td>
            </tr>
        </table>
        <?php
    }

    public static function ssms_save_extra_profile_fields($user_id)
    {

        if (!current_user_can('edit_user', $user_id))
            return false;

        if (isset($_POST['phone']) and !empty($_POST['phone'])) {

            update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
        }
    }

    public static function ssms_load_styles()
    {
        wp_enqueue_style('serwersms');
    }

    public static function ssms_load_scripts()
    {
        wp_enqueue_script('serwersms');
    }

    public static function ssms_load_login_scripts()
    {
        wp_enqueue_script('jquery');
        wp_register_script('serwersms-register', SERWERSMS_PLUGIN_URL . 'js/registration.js');
        wp_enqueue_script('serwersms-register');
    }

    public static function ssms_access_section_desc()
    {
        _e('Enter your API username and password. If you do not have an account yet, register now at ', 'serwersms');
        echo ' <a href="https://panel.serwersms.pl" target="_blank">panel.serwersms.pl</a>.<br />';
        _e('To create WebAPI user, go to menu in Client Panel: Ustawienia Interfejsów -> HTTPS XML API -> Użytkownicy API ', 'serwersms');
    }

    public static function ssms_configuration_section_desc()
    {

    }

    public static function ssms_registration_section_desc()
    {

    }

    public static function ssms_data_correction($new_value, $old_value = null)
    {
        $new_value['password'] = self::ssms_encode_password($new_value['password']);
        if ($new_value['reset'] == 1) {
            $new_value['username'] = '';
            $new_value['password'] = '';
            update_option('serwersms_auth',false);
        }
        return $new_value;
    }

    public static function ssms_get_password()
    {
        $options = get_option('serwersms_settings');
        return self::ssms_decode_password($options['password']);
    }

    public static function ssms_settings_page()
    {
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : null;
        $result = false;
        switch ($action) {
            case 'add_sender':

                if (isset($_POST['sender'])) {
                    if (self::ssms_api_connect()) {
                        try {
                            $result = self::$api->senders->add(sanitize_text_field($_POST['sender']));
                        } catch (Exception $ex) {
                            global $errors;
                            $errors[] = $ex->getMessage();
                        }
                    }

                    if ($result) {
                        global $success;
                        $success[] = __('Sender name has been added', 'serwersms');
                    }
                }
                include_once("views/sender_form.php");
                break;

            default:
                include_once("views/settings.php");
                break;
        }
    }

    public static function ssms_username_field_render()
    {
        $options = get_option('serwersms_settings');
        $username = (isset($options['username'])) ? $options['username'] : '';
        if (self::ssms_api_connect()) {
            $display = 'disabled="disabled"';
        } else {
            $display = '';
        }
        ?>
        <input type='text' name='serwersms_settings[username]' value='<?php echo $username; ?>' <?php echo $display; ?>>
        <?php
    }

    public static function ssms_password_field_render()
    {
        if (self::ssms_api_connect()) {
            $display = 'disabled="disabled"';
        } else {
            $display = '';
        }
        ?>
        <input type='password' name='serwersms_settings[password]' value='' <?php echo $display; ?>>
        <?php

    }

    public static function ssms_reset_field_render()
    {
        ?>
        <input type='checkbox' name='serwersms_settings[reset]' value='1'>
        <?php
    }

    public static function ssms_verification_field_render($args)
    {
        $options = get_option('serwersms_configuration');
        ?>
        <input type='checkbox'
               name='serwersms_configuration[<?php echo $args['name']; ?>]' <?php checked(isset($options[$args['name']])); ?>
               value='1'>
        <?php
    }

    public static function ssms_sender_field_render($args = array())
    {
        $options = get_option('serwersms_configuration');
        $name = (isset($args['name']) and !empty($args['name'])) ? $args['name'] : 'sender_name';
        if (is_object(self::$api) and class_exists('SerwerSMS\SerwerSMS')) {
            try {
                $serwersms[] = self::$api->senders->index();
                $serwersms[] = self::$api->senders->index(array('predefined' => true));
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        ?>
        <select name='serwersms_configuration[<?php echo $name; ?>]'>
            <option value="">(<?php _e('Random number', 'serwersms'); ?>) SMS ECO</option>
            <?php
            if (isset($serwersms[0]->items)) {
                foreach ($serwersms as $sms) {
                    foreach ($sms->items as $sender) {
                        if ($sender->status == 'authorized') {
                            echo '<option value="' . $sender->name . '"';
                            selected((isset($options[$name]) ? $options[$name] : ''), $sender->name);
                            echo '>' . $sender->name . '</option>';
                        } else {
                            echo '<option value="' . $sender->name . '" disabled="disabled">' . $sender->name . '</option>';
                        }
                    }
                }
            }
            ?>
        </select>
        <a href="?page=serwersms&action=add_sender"
           class="button"><?php _e('Add a new sender name', 'serwersms'); ?></a>
        <?php
    }

    public static function ssms_group_field_render($args = array())
    {

        $options = get_option('serwersms_configuration');

        if (isset($_GET['group_id']) and !empty($_GET['group_id'])) {
            $select_name = 'group_id';
            $current_group = (int)$_GET['group_id'];
            $empty_field = null;
        } elseif (isset($_GET['id']) and !empty($_GET['id']) and isset($_GET['source']) and $_GET['source'] == 'groups') {
            $select_name = 'group_id';
            $current_group = (int)$_GET['id'];
            $empty_field = null;
        } else {
            $name = (isset($args['name']) and !empty($args['name'])) ? $args['name'] : '';
            $select_name = 'serwersms_configuration[' . $name . ']';
            $current_group = (isset($options[$name])) ? (int)$options[$name] : '';
            $empty_field = '<option value=""></option>';
        }

        switch ($_REQUEST['page']) {
            case 'serwersms_message':
                $select_name = 'group_id';
                $empty_field = null;
                break;
            case 'serwersms_contacts':
                $select_name = 'group_id';
                break;
        }

        if (is_object(self::$api) and class_exists('SerwerSMS\SerwerSMS')) {
            try {
                $serwersms = self::$api->groups->index();
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        ?>
        <select name='<?php echo $select_name; ?>' id="group_select">
            <?php
            echo $empty_field;
            if (isset($serwersms->items)) {
                foreach ($serwersms->items as $group) {
                    echo '<option value="' . $group->id . '"';
                    selected($current_group, $group->id);
                    echo '>' . $group->name . '</option>';
                }
            }
            ?>
        </select>
        <a href="?page=serwersms_groups&action=add" class="button"><?php _e('Add a new group', 'serwersms'); ?></a>
        <?php
    }

    public static function ssms_contacts_page()
    {

        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }
        if (!class_exists('SerwerSms_List_Table')) {
            require_once('class.serwersms.listtable.php');
        }

        global $errors;
        global $success;
        $contactList = new SerwerSms_List_Table();
        if ($contactList->current_action()) {
            $action = $contactList->current_action();
        } elseif (isset($_GET['action']) and $_GET['action'] != null) {
            $action = sanitize_text_field($_GET['action']);
        } else {
            $action = null;
        }

        switch ($action) {

            case 'add':

                if (isset($_POST['phone'])) {
                    $groups_select = null;
                    $params['first_name'] = sanitize_text_field($_POST['first_name']);
                    $params['last_name'] = sanitize_text_field($_POST['last_name']);
                    $params['company'] = sanitize_text_field($_POST['company']);
                    $params['email'] = sanitize_text_field($_POST['email']);
                    $group_id = (int)sanitize_text_field($_POST['group_id']);
                    $phone = preg_replace("/[\s\.,\-]/", "", trim(sanitize_text_field($_POST['phone'])));

                    if (preg_match("/[\+\d]{8,18}/", $phone)) {
                        if (self::ssms_api_connect()) {
                            try {
                                self::$api->contacts->add($group_id, $phone, $params);
                            } catch (Exception $ex) {
                                $errors[] = $ex->getMessage();
                            }
                        }
                    } else {
                        $errors[] = __('Invalid phone number', 'serwersms');
                    }

                    if (!empty($errors)) {
                        include_once("views/contacts_form.php");
                    } else {
                        $success[] = __('Contact has been added', 'serwersms');
                        include_once('views/messages.php');
                        ?>
                        <a href="?page=serwersms_contacts&group_id=<?php echo $group_id; ?>"
                           class="button"><?php _e('Back', 'serwersms'); ?></a>
                        <?php
                    }
                } else {
                    include_once("views/contacts_form.php");
                }
                break;

            case 'edit':

                if (isset($_GET['id']) and !isset($_POST['id'])) {

                    check_admin_referer('edit_item_' . $_GET['id']);

                    $contact_id = (int)$_GET['id'];
                    $group_id = (int)$_GET['group_id'];

                    if (self::ssms_api_connect()) {
                        try {
                            $result = self::$api->contacts->view($contact_id);
                        } catch (Exception $ex) {
                            $errors[] = $ex->getMessage();
                        }
                    }

                    if (isset($result->phone)) {
                        $fields['phone'] = $result->phone;
                        $fields['firstname'] = $result->first_name;
                        $fields['lastname'] = $result->last_name;
                        $fields['company'] = $result->company;
                        $fields['email'] = $result->email;
                    } else {
                        $fields['phone'] = null;
                        $fields['firstname'] = null;
                        $fields['lastname'] = null;
                        $fields['company'] = null;
                        $fields['email'] = null;
                    }

                    include_once("views/contacts_form.php");

                } elseif (isset($_POST['id'])) {
                    $fields['first_name'] = sanitize_text_field($_POST['first_name']);
                    $fields['last_name'] = sanitize_text_field($_POST['last_name']);
                    $fields['company'] = sanitize_text_field($_POST['company']);
                    $fields['email'] = sanitize_text_field($_POST['email']);
                    $phone = preg_replace("/[\s\.,\-]/", "", trim(sanitize_text_field($_POST['phone'])));
                    $fields['phone'] = $phone;
                    $group_id = (int)sanitize_text_field($_POST['group_id']);
                    $options = get_option('serwersms_configuration');
                    $contact_id = (int)$_POST['id'];
                    check_admin_referer('edit_contact_' . $contact_id);
                    if (preg_match("/[\+\d]{8,18}/", $phone)) {
                        if (self::ssms_api_connect()) {
                            try {
                                self::$api->contacts->edit($contact_id, $group_id, $phone, $fields);
                                $groups = self::$api->groups->index();
                            } catch (Exception $ex) {
                                $errors[] = $ex->getMessage();
                            }
                        }
                    } else {
                        $errors[] = __('Invalid phone number', 'serwersms');
                    }

                    if (!empty($errors)) {
                        include_once("views/contacts_form.php");
                    } else {
                        $success[] = __('Contact has been updated', 'serwersms');
                        include_once('views/messages.php');
                        ?>
                        <a href="?page=serwersms_contacts&group_id=<?php echo $group_id; ?>"
                           class="button"><?php _e('Back', 'serwersms'); ?></a>
                        <?php
                    }

                } else {
                    $errors[] = __('Not found ID', 'serwersms');
                }
                break;

            case 'delete':

                global $errors;
                if (isset($_GET['id'])) {

                    if (is_array($_GET['id']) and !empty($_GET['id'])) {

                        foreach ($_GET['id'] as $contact) {
                            $contact_id[] = (int)$contact;
                        }

                    } else {
                        check_admin_referer('delete_item_' . $_GET['id']);
                        $contact_id = (int)$_GET['id'];
                    }

                    if (self::ssms_api_connect()) {
                        try {
                            self::$api->contacts->delete($contact_id);
                        } catch (Exception $ex) {
                            $errors[] = $ex->getMessage();
                        }
                    }
                    if (empty($errors)) {
                        $success[] = __('Contact has been deleted', 'serwersms');
                        include_once('views/messages.php');
                        ?>
                        <a href="?page=serwersms_contacts" class="button"><?php _e('Back', 'serwersms'); ?></a>
                        <?php
                    }
                }

                break;

            default:

                $contacts = null;
                $groups = null;
                $groups_select = false;
                $data = array();
                $options = get_option('serwersms_configuration');
                $page = (isset($_GET['paged'])) ? (int)$_GET['paged'] : 0;
                $search = (isset($_POST['s'])) ? sanitize_text_field($_POST['s']) : null;
                $limit = 20;
                $total_items = 0;
                $group_default = (isset($options['group']) and $options['group'] != null) ? (int)$options['group'] : 'none';
                $group_id = (isset($_GET['group_id']) and $_GET['group_id'] != null) ? (int)$_GET['group_id'] : $group_default;

                if (self::ssms_api_connect() and $group_id) {
                    $params = array(
                        'page' => $page,
                        'limit' => $limit
                    );
                    try {
                        $contacts = self::$api->contacts->index($group_id, $search, $params);
                        $all_pages = (int)$contacts->paging->count;
                        $params['page'] = $all_pages;
                        $last_page = self::$api->contacts->index($group_id, $search, $params);
                        $groups = self::$api->groups->index();
                    } catch (Exception $ex) {
                        $errors[] = $ex->getMessage();
                    }
                }

                if (isset($groups->items)) {
                    $groups_select = true;
                }

                if (isset($contacts->items) and isset($last_page->items)) {
                    $last_contacts = count($last_page->items);
                    $total_items = ($limit * $all_pages) - ($limit - $last_contacts);
                    foreach ($contacts->items as $contact) {
                        $data[] = array(
                            'id' => $contact->id,
                            'first' => $contact->phone,
                            'firstname' => $contact->first_name,
                            'lastname' => $contact->last_name,
                            'company' => $contact->company,
                            'email' => $contact->email,
                            'group_id' => $group_id
                        );
                    }
                }

                $contactList->table_name = 'contacts';
                $contactList->per_page = $limit;
                $contactList->total_items = $total_items;
                $contactList->data = $data;
                $contactList->columns = array(
                    'first' => __('Phone', 'serwersms'),
                    'firstname' => __('First name', 'serwersms'),
                    'lastname' => __('Last name', 'serwersms'),
                    'company' => __('Company', 'serwersms'),
                    'email' => __('E-mail', 'serwersms'),
                );

                $contactList->prepare_items();

                include_once("views/contacts.php");
                break;
        }
    }

    public static function ssms_groups_page()
    {

        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }
        if (!class_exists('SerwerSms_List_Table')) {
            require_once('class.serwersms.listtable.php');
        }

        global $errors;
        global $success;
        $list = new SerwerSms_List_Table();
        if ($list->current_action()) {
            $action = $list->current_action();
        } elseif (isset($_GET['action']) and $_GET['action'] != null) {
            $action = sanitize_text_field($_GET['action']);
        } else {
            $action = null;
        }

        switch ($action) {

            case 'add':

                if (isset($_POST['name'])) {
                    $name = sanitize_text_field($_POST['name']);
                    $options = get_option('serwersms_configuration');

                    if (self::ssms_api_connect()) {
                        try {
                            self::$api->groups->add($name);
                        } catch (Exception $ex) {
                            $errors[] = $ex->getMessage();
                        }
                    }

                    if (!empty($errors)) {
                        include_once("views/groups_form.php");
                    } else {
                        $success[] = __('Group has been added', 'serwersms');
                        include_once('views/messages.php');
                        ?>
                        <a href="?page=serwersms_groups" class="button"><?php _e('Back', 'serwersms'); ?></a>
                        <?php
                    }
                } else {
                    include_once("views/groups_form.php");
                }
                break;

            case 'edit':

                if (isset($_GET['id']) and !isset($_POST['id'])) {

                    check_admin_referer('edit_item_' . $_GET['id']);

                    $group_id = (int)$_GET['id'];
                    if (self::ssms_api_connect()) {
                        try {
                            $result = self::$api->groups->view($group_id);
                        } catch (Exception $ex) {
                            $errors[] = $ex->getMessage();
                        }
                    }

                    if (isset($result->name)) {
                        $fields['name'] = $result->name;
                    } else {
                        $fields['name'] = null;
                    }

                    include_once("views/groups_form.php");

                } elseif (isset($_POST['id'])) {
                    $name = sanitize_text_field($_POST['name']);
                    $group_id = (int)$_POST['id'];
                    check_admin_referer('edit_group_' . $group_id);

                    if (self::ssms_api_connect()) {
                        try {
                            self::$api->groups->edit($group_id, $name);
                        } catch (Exception $ex) {
                            $errors[] = $ex->getMessage();
                        }
                    }

                    if (!empty($errors)) {
                        include_once("views/groups_form.php");
                    } else {
                        $success[] = __('Group name has been updated', 'serwersms');
                        include_once('views/messages.php');
                        ?>
                        <a href="?page=serwersms_groups" class="button"><?php _e('Back', 'serwersms'); ?></a>
                        <?php
                    }

                } else {
                    $errors[] = __('Not found ID', 'serwersms');
                }
                break;

            case 'delete':

                global $errors;

                if (isset($_GET['id'])) {

                    if (is_array($_GET['id']) and !empty($_GET['id'])) {

                        foreach ($_GET['id'] as $group) {
                            $groups[] = (int)$group;
                        }

                    } else {
                        check_admin_referer('delete_item_' . $_GET['id']);
                        $groups = (int)$_GET['id'];
                    }

                    if (self::ssms_api_connect()) {
                        try {
                            self::$api->groups->delete($groups);
                        } catch (Exception $ex) {
                            $errors[] = $ex->getMessage();
                        }
                    }

                    if (empty($errors)) {
                        $success[] = __('Group has been deleted', 'serwersms');
                        include_once('views/messages.php');
                        ?>
                        <a href="?page=serwersms_groups" class="button"><?php _e('Back', 'serwersms'); ?></a>
                        <?php
                    }
                }

                break;

            default:

                $records = null;
                $data = array();
                $options = get_option('serwersms_configuration');
                $page = (isset($_GET['paged'])) ? (int)$_GET['paged'] : 0;
                $search = (isset($_POST['s'])) ? sanitize_text_field($_POST['s']) : null;
                $limit = 20;
                $total_items = 0;

                if (self::ssms_api_connect()) {
                    $params = array(
                        'page' => $page,
                        'limit' => $limit
                    );
                    try {
                        $records = self::$api->groups->index($search, $params);
                        $all_pages = (int)$records->paging->count;
                        $params['page'] = $all_pages;
                        $last_page = self::$api->groups->index($search, $params);
                    } catch (Exception $ex) {
                        $errors[] = $ex->getMessage();
                    }
                }

                if (isset($records->items) and isset($last_page->items)) {
                    $last_records = count($last_page->items);
                    $total_items = ($limit * $all_pages) - ($limit - $last_records);
                    foreach ($records->items as $record) {
                        $data[] = array(
                            'id' => $record->id,
                            'first' => $record->name,
                            'count' => $record->count
                        );
                    }
                }

                $list->table_name = 'groups';
                $list->per_page = $limit;
                $list->total_items = $total_items;
                $list->data = $data;
                $list->columns = array(
                    'first' => __('Name', 'serwersms'),
                    //'count' => __('Number of contacts','serwersms'),
                );

                $list->prepare_items();

                include_once("views/groups.php");
                break;
        }
    }

    public static function ssms_reports_page()
    {

        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }
        if (!class_exists('SerwerSms_List_Table')) {
            require_once('class.serwersms.listtable.php');
        }

        global $errors;

        $reports = null;
        $data = array();
        $options = get_option('serwersms_configuration');
        $page = (isset($_GET['paged'])) ? (int)$_GET['paged'] : 0;
        $search = (isset($_POST['s'])) ? sanitize_text_field($_POST['s']) : null;
        $status = (isset($_GET['status'])) ? sanitize_text_field($_GET['status']) : 'sent';
        $limit = 20;
        $total_items = 0;

        switch ($status) {
            case 'sent':
            case 'delivered':
            case 'undelivered':
            case 'unsent':
            case 'in_progress':
            case 'saved':
                $status = $status;
                break;
            default:
                $status = 'sent';
                break;
        }

        if (self::ssms_api_connect()) {
            $params = array(
                'phone' => $search,
                'status' => $status,
                'page' => $page,
                'limit' => $limit
            );
            try {
                $reports = self::$api->messages->reports($params);
                $all_pages = (int)$reports->paging->count;
                $params['page'] = $all_pages;
                $last_page = self::$api->messages->reports($params);
            } catch (Exception $ex) {
                $errors[] = $ex->getMessage();
            }
        }

        if (isset($reports->items) and isset($last_page->items)) {
            $last_contacts = count($last_page->items);
            $total_items = ($limit * $all_pages) - ($limit - $last_contacts);
            $statuses = array(
                'sent' => __('Sent', 'serwersms'),
                'delivered' => __('Delivered', 'serwersms'),
                'undelivered' => __('Undelivered', 'serwersms'),
                'unsent' => __('Unsent', 'serwersms'),
                'in_progress' => __('In progress', 'serwersms'),
                'saved' => __('Saved', 'serwersms')
            );
            $reasons = array(
                'message_expired' => __('Message expired', 'serwersms'),
                'unsupported_number' => __('Unsupported number', 'serwersms'),
                'message_rejected' => __('Message rejected', 'serwersms'),
                'missed_call' => __('Missed call', 'serwersms'),
                'wrong_number' => __('Wrong number', 'serwersms'),
                'limit_exhausted' => __('Limit exhausted', 'serwersms'),
                'lock_send' => __('Sending is locked', 'serwersms'),
                'wrong_message' => __('Wrong message', 'serwersms'),
                'operator_error' => __('Operator error', 'serwersms'),
                'wrong_sender_name' => __('Wrong sender name', 'serwersms'),
                'number_is_blacklisted' => __('Number exist on the blacklist', 'serwersms'),
                'sending_to_foreign_networks_is_locked' => __('Sending to foreign networks is locked', 'serwersms'),
                'no_permission_to_send_messages' => __('No permission to send messages', 'serwersms'),
                'other_error' => __('Other error', 'serwersms')
            );
            foreach ($reports->items as $report) {

                $status_name = $statuses[trim($report->status)];
                $sent = (isset($report->sent)) ? $report->sent : null;
                $delivered = (isset($report->delivered)) ? $report->delivered : null;
                $phone = (isset($report->phone)) ? $report->phone : null;
                $text = (isset($report->text)) ? $report->text : null;
                $sender = (isset($report->sender)) ? $report->sender : null;
                $reason = (isset($report->reason) and $report->reason) ? $reasons[trim($report->reason)] : null;
                $data[] = array(
                    'id' => $report->id,
                    'phone' => $phone,
                    'text' => $text,
                    'sender' => $sender,
                    'type' => strtoupper($report->type),
                    'status' => $status_name,
                    'sent' => $sent,
                    'delivered' => $delivered,
                    'reason' => $reason
                );
            }
        }

        $list = new SerwerSms_List_Table();
        $list->per_page = $limit;
        $list->total_items = $total_items;
        $list->data = $data;
        $list->bulk_actions = false;
        $list->columns = array(
            'phone' => __('Phone', 'serwersms'),
            'text' => __('Message', 'serwersms'),
            'sender' => __('Sender', 'serwersms'),
            'type' => __('Type', 'serwersms'),
            'status' => __('Status', 'serwersms'),
            'sent' => __('Time of sending', 'serwersms'),
            'delivered' => __('Time of a report', 'serwersms'),
            'reason' => __('Note', 'serwersms')
        );

        $list->prepare_items();

        include_once("views/reports.php");
    }

    public static function ssms_recived_page()
    {

        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }
        if (!class_exists('SerwerSms_List_Table')) {
            require_once('class.serwersms.listtable.php');
        }

        global $errors;

        $recived = null;
        $data = array();
        $options = get_option('serwersms_configuration');
        $page = (isset($_GET['paged'])) ? (int)$_GET['paged'] : 0;
        $search = (isset($_POST['s'])) ? sanitize_text_field($_POST['s']) : null;
        $limit = 20;
        $total_items = 0;
        $type = (isset($_GET['type'])) ? sanitize_text_field($_GET['type']) : 'eco';

        switch ($type) {
            default:
            case 'eco':
                $type = 'eco';
                break;
            case '2way':
                $type = '2way';
                break;
            case 'nd':
                $type = 'nd';
                break;
            case 'ndi':
                $type = 'ndi';
                break;
        }

        if (self::ssms_api_connect()) {
            $params = array(
                'phone' => $search,
                'page' => $page,
                'limit' => $limit
            );

            try {
                $recived = self::$api->messages->recived($type, $params);
                $all_pages = (int)$recived->paging->count;
                $params['page'] = $all_pages;
                $last_page = self::$api->messages->recived($type, $params);
            } catch (Exception $ex) {
                $errors[] = $ex->getMessage();
            }
        }

        if (isset($recived->items) and isset($last_page->items)) {
            $last_contacts = count($last_page->items);
            $total_items = ($limit * $all_pages) - ($limit - $last_contacts);

            foreach ($recived->items as $recive) {

                $blacklist = ($recive->blacklist) ? __('Yes', 'serwersms') : __('No', 'serwersms');
                $data[] = array(
                    'id' => $recive->id,
                    'phone' => $recive->phone,
                    'text' => $recive->text,
                    'type' => strtoupper($recive->type),
                    'recived' => $recive->recived,
                    'blacklist' => $blacklist
                );
            }
        }

        $list = new SerwerSms_List_Table();
        $list->per_page = $limit;
        $list->total_items = $total_items;
        $list->data = $data;
        $list->bulk_actions = false;
        $list->columns = array(
            'phone' => __('Phone', 'serwersms'),
            'text' => __('Message', 'serwersms'),
            'type' => __('Type', 'serwersms'),
            'recived' => __('Recived', 'serwersms'),
            'blacklist' => __('Blacklist', 'serwersms')
        );

        $list->prepare_items();

        include_once("views/recived.php");
    }

    public static function ssms_payments_page()
    {

        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }
        if (!class_exists('SerwerSms_List_Table')) {
            require_once('class.serwersms.listtable.php');
        }

        global $errors;

        $records = null;
        $data = array();
        $options = get_option('serwersms_configuration');
        $page = (isset($_GET['paged'])) ? (int)$_GET['paged'] : 0;
        $search = (isset($_POST['s'])) ? sanitize_text_field($_POST['s']) : null;
        $limit = 20;
        $total_items = 0;

        if (self::ssms_api_connect()) {
            $params = array(
                'page' => $page,
                'limit' => $limit
            );
            try {
                $records = self::$api->payments->index($params);
                $all_pages = (isset($records->paging->count)) ? (int)$records->paging->count : 0;
                $params['page'] = $all_pages;
                $last_page = self::$api->payments->index($params);
            } catch (Exception $ex) {
                $errors[] = $ex->getMessage();
            }
        }

        if (isset($records->items) and isset($last_page->items)) {
            $last_items = count($last_page->items);
            $total_items = ($limit * $all_pages) - ($limit - $last_items);

            $statuses = array(
                'paid' => __('Paid', 'serwersms'),
                'not_paid' => __('Not paid', 'serwersms')
            );

            foreach ($records->items as $record) {

                $status = $statuses[trim($record->state)];
                $payment_to = (isset($record->payment_to)) ? $record->payment_to : null;
                $data[] = array(
                    'id' => $record->id,
                    'number' => $record->number,
                    'state' => $status,
                    'paid' => $record->paid . ' PLN',
                    'total' => $record->total . ' PLN',
                    'payment_to' => $payment_to,
                    'preview' => '<a href="' . $record->url . '" target="_blank">' . __('Show', 'serwersms') . '</a>'
                );
            }
        }

        $list = new SerwerSms_List_Table();
        $list->per_page = $limit;
        $list->total_items = $total_items;
        $list->data = $data;
        $list->bulk_actions = false;
        $list->columns = array(
            'number' => __('Number', 'serwersms'),
            'state' => __('State', 'serwersms'),
            'paid' => __('Paid', 'serwersms'),
            'total' => __('Total', 'serwersms'),
            'payment_to' => __('Payment to', 'serwersms'),
            'preview' => __('Preview', 'serwersms')
        );

        $list->prepare_items();

        include_once("views/payments.php");
    }

    public static function ssms_message_page()
    {

        global $errors;
        $action = (isset($_POST['action'])) ? sanitize_text_field($_POST['action']) : null;
        switch ($action) {

            case 'send':

                if (isset($_POST['message']) and ($_POST['phone'] or $_POST['serwersms_configuration'])) {

                    $result = false;
                    $message = sanitize_text_field($_POST['message']);
                    $sender = sanitize_text_field($_POST['serwersms_configuration']['sender_name']);
                    $send_to = sanitize_text_field($_POST['send_to']);
                    $phones = preg_replace("/[\s\.\-]/", "", trim(sanitize_text_field($_POST['phone'])));
                    $group_id = (int)sanitize_text_field($_POST['group_id']);

                    if ($send_to == '2') {

                        $params = array();
                        $phone = explode(",", $phones);

                    } else {
                        $params = array(
                            'group_id' => $group_id
                        );
                        $phone = null;
                    }

                    if (self::ssms_api_connect()) {
                        try {
                            $result = self::$api->messages->sendSms($phone, $message, $sender, $params);
                        } catch (Exception $ex) {
                            $errors[] = $ex->getMessage();
                        }
                    }

                    if ($result) {
                        global $success;
                        $success[] = __('Message has been sent', 'serwersms');
                    }

                } else {
                    $errors[] = __('No data', 'serwersms');
                }

            default:

                $account = null;
                $contact = null;
                $groups = null;
                $groups_select = null;
                $options = get_option('serwersms_configuration');
                $contact_id = (isset($_GET['id']) and $_GET['id'] != null and isset($_GET['source']) and $_GET['source'] == 'contacts') ? (int)$_GET['id'] : null;
                $group_id = (isset($_GET['id']) and $_GET['id'] != null and isset($_GET['source']) and $_GET['source'] == 'groups') ? (int)$_GET['id'] : (int)$options['group'];
                $chars_limit = 0;

                if (self::ssms_api_connect()) {
                    try {
                        $account = self::$api->account->limits();
                        $groups = self::$api->groups->index();
                        if ($contact_id) {
                            check_admin_referer('send_item_' . $contact_id);
                            $contact = self::$api->contacts->view($contact_id);
                        } else {
                            $contact = null;
                        }
                    } catch (Exception $ex) {
                        $errors[] = $ex->getMessage();
                    }
                }

                if (is_object($account)) {
                    $chars_limit = $account->items[0]->chars_limit;
                    $chars_limit = explode("/", $chars_limit);
                    $chars_limit = $chars_limit[0];
                } else {
                    $chars_limit = 0;
                }

                if (is_object($contact)) {
                    $phone = $contact->phone;
                } else {
                    $phone = null;
                }

                if (is_object($groups)) {
                    foreach ($groups->items as $group) {
                        $groups_select[$group->id] = $group->name;
                    }
                }

                include_once("views/message_form.php");
                break;
        }
    }

    public static function ssms_account_page()
    {

        global $errors;
        $account = null;
        $help = null;
        $result = array();

        if (self::ssms_api_connect()) {
            try {
                $account = self::$api->account->limits(array('show_type' => true));
                $help = self::$api->account->help();
            } catch (Exception $ex) {
                $errors[] = $ex->getMessage();
            }
        }

        if (is_object($account)) {
            $result['type'] = strtoupper($account->account->type);
            $result['eco'] = $account->items[0]->value;
            $result['full'] = $account->items[1]->value;
        }

        if (is_object($help)) {
            $result['help'] = (array)$help;
        }

        include_once("views/account.php");
    }

    public static function ssms_api_connect()
    {
        if (get_option('serwersms_auth') == true) {
            $result = true;
        } elseif (self::$authorized) {
            $result = true;
        } elseif (class_exists('SerwerSMS\SerwerSMS')) {
            if (is_object(self::$api)) {

                try {
                    $serwersms = self::$api->account->limits();
                } catch (Exception $e) {
                    global $errors;
                    $errors[] = $e->getMessage();
                }

                if (isset($serwersms->items[0]->type)) {
                    $result = true;
                    self::$authorized = true;
                } else {
                    $result = false;
                }
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }


        $page = (isset($_REQUEST['page']) and !empty($_REQUEST['page'])) ? $_REQUEST['page'] : '';
        $action = (isset($_REQUEST['action']) and !empty($_REQUEST['action'])) ? $_REQUEST['action'] : '';

        update_option('serwersms_auth',$result);

        if (is_admin() and is_user_logged_in() and $result === false and $page != 'serwersms' and preg_match('/serwersms/', $page) and $action != 'update') {
            wp_redirect(admin_url('admin.php?page=serwersms'));
            exit;
        }

        return $result;
    }

    public
    static function ssms_decode_password($value = '')
    {

        $version = 0;
        $explode = explode('.', PHP_VERSION);
        if (count($explode) > 0) {
            $version = $explode[0];
        }

        if ($version < 7) {

            return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, hash('sha256', self::$key, true), base64_decode(strtr($value, '-_,', '+/=')), MCRYPT_MODE_ECB));

        } else {

            if ($value == '') {
                return '';
            }

            $c = base64_decode($value);
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len = 32);
            $ciphertext_raw = substr($c, $ivlen + $sha2len);
            $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, self::$key, $options = OPENSSL_RAW_DATA, $iv);
            $calcmac = hash_hmac('sha256', $ciphertext_raw, self::$key, $as_binary = true);
            if (hash_equals($hmac, $calcmac)) {
                return $original_plaintext;
            }
        }
    }

    public
    static function ssms_encode_password($value = '')
    {

        $version = 0;
        $explode = explode('.', PHP_VERSION);
        if (count($explode) > 0) {
            $version = $explode[0];
        }

        if ($version < 7) {

            return strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, hash('sha256', self::$key, true), $value, MCRYPT_MODE_ECB)), '+/=', '-_,');

        } else {

            $plaintext = $value;
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($plaintext, $cipher, self::$key, $options = OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, self::$key, $as_binary = true);

            return base64_encode($iv . $hmac . $ciphertext_raw);
        }
    }
}
