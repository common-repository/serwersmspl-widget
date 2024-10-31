<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('init','serwersms_register_session');
add_action('wp_enqueue_scripts','serwersms_add_scripts');
add_action('login_enqueue_scripts','serwersms_add_scripts');
add_action('wp_ajax_serwersms_process','serwersms_process');
add_action('wp_ajax_nopriv_serwersms_process','serwersms_process');
load_plugin_textdomain('serwersms', false, basename(dirname(__FILE__)) . '/languages/');

function serwersms_register_session(){
    if( !session_id() )
        session_start();
}

function serwersms_add_scripts(){
    
    wp_enqueue_script('jquery');
    wp_enqueue_script('widget',plugins_url('/js/widget.js',__FILE__));
    
    if ( isset($_SERVER['HTTPS']) )
       $protocol = 'https://';
    else
       $protocol = 'http://';
    
    $admin_ajax_url = admin_url( 'admin-ajax.php', $protocol );
    
    wp_localize_script('widget','ajax_options',array('admin_ajax_url' => $admin_ajax_url));  
}

function serwersms_process(){

    $json = null;
    $phone = null;
    $code = null;
    $settings = get_option('serwersms_settings');
    $options = get_option('serwersms_configuration');
    $confirmed = true;
    
    $group = (isset($options['group']) and $options['group'] != null) ? (int) $options['group'] : 'none';
    $sender = (isset($options['sender_name']) and $options['sender_name'] != null) ? $options['sender_name'] : '';
    
    if(isset($_POST['phone']) and !empty($_POST['phone'])){
        $phone = sanitize_text_field($_POST['phone']);
        $phone = preg_replace("/\D/","",$phone);
        $verify_phone = preg_match("/^\+?[0-9]{8,20}/",$phone);
    } else {
        $phone = false;
        $verify_phone = false;
    }
    
    if(isset($_POST['code']) and !empty($_POST['code'])){
        $code = (int) $_POST['code'];
    } else {
        $code = false;
    }
    
    if(isset($_POST['operation']) and !empty($_POST['operation'])){
        $operation = sanitize_text_field($_POST['operation']);
    } else {
        $operation = false;
    }
    
    if(class_exists('SerwerSMS\SerwerSMS')){
        try{
            $serwersms = new SerwerSMS\SerwerSMS($settings['username'], SerwerSms::ssms_get_password());
        } catch (Exception $ex) {
            $json['status'] = 3;
            $json['mess'] = $ex->getMessage();
        }
    }
    
    if(is_object($serwersms)){
        switch($operation){

            case 'add':

                if($phone){

                    if($verify_phone){
                        try{
                            $result = $serwersms->contacts->index($group,$phone);
                        } catch (Exception $ex) {
                            $json['status'] = 3;
                            $json['mess'] = $ex->getMessage();
                        }
                    }

                    if(!$verify_phone){

                        $json['status'] = 3;
                        $json['mess'] = __('Wrong phone','serwersms');
                        $confirmed = false;

                    } elseif(isset($result->items) and count($result->items) > 0){

                        $json['status'] = 3;
                        $json['mess'] = __('Your phone already exists in our database','serwersms');
                        $confirmed = false;

                    } elseif(isset($options['verification_code']) and $options['verification_code']){

                        $_SESSION['serwersms_phone'] = $phone;
                        $confirmed = false;

                        $code = rand(1000,9999);
                        $text = __('Your verification code is','serwersms').' '.$code;

                        try{
                            $result = $serwersms->messages->sendSms($phone,$text,$sender);
                        } catch (Exception $ex) {
                            $json['status'] = 3;
                            $json['mess'] = $ex->getMessage();
                        }

                        if(isset($result->success) and $result->success === true){
                            $_SESSION['serwersms_code'] = $code;
                            $json['status'] = 2;
                        }
                    }

                } elseif($code){

                    if(isset($_SESSION['serwersms_code']) and $code == $_SESSION['serwersms_code']){
                        $confirmed = true;
                        $phone = (isset($_SESSION['serwersms_phone'])) ? sanitize_text_field($_SESSION['serwersms_phone']) : false;
                    } else {
                        $confirmed = false;
                        $json['status'] = 2;
                        $json['mess'] = __('Code is incorrect. Please try again.','serwersms');
                    }

                } else {
                    $json['status'] = 3;
                    $json['mess'] = __('No data','serwersms');
                }

                if($confirmed and $phone){

                    try{
                        $result = $serwersms->contacts->add($group,$phone);
                    } catch (Exception $ex) {
                        $json['status'] = 3;
                        $json['mess'] = $ex->getMessage();
                    }

                    if(isset($result->success) and $result->success === true){
                        $json['status'] = 3;
                        $json['mess'] = __('Thank you! Your phone has been correctly added.','serwersms');
                        unset($_SESSION['serwersms_phone']);
                        unset($_SESSION['serwersms_code']);
                    } else {
                        $json['status'] = 3;
                        $json['mess'] = __('Error. Please try again.','serwersms');
                    }
                }


                break;

            case 'delete':

                if($phone){

                    if($verify_phone){
                        try{
                            $result = $serwersms->contacts->index($group,$phone);
                        } catch (Exception $ex) {
                            $json['status'] = 3;
                            $json['mess'] = $ex->getMessage();
                        }
                    }

                    if(!$verify_phone){

                        $json['status'] = 3;
                        $json['mess'] = __('Wrong phone','serwersms');
                        $confirmed = false;

                    } elseif(!isset($result->items) or count($result->items) == 0){

                        $json['status'] = 3;
                        $json['mess'] = __('Your phone not exists in our database','serwersms');
                        $confirmed = false;

                    } elseif(isset($options['verification_code']) and $options['verification_code']){

                        $_SESSION['serwersms_phone'] = $phone;
                        $confirmed = false;

                        $code = rand(1000,9999);
                        $text = __('Your verification code is','serwersms').' '.$code;

                        try{
                            $result = $serwersms->messages->sendSms($phone,$text,$sender);
                        } catch (Exception $ex) {
                            $json['status'] = 3;
                            $json['mess'] = $ex->getMessage();
                        }

                        if(isset($result->success) and $result->success === true){
                            $_SESSION['serwersms_code'] = $code;
                            $json['status'] = 2;
                        }
                    }

                } elseif($code){

                    if(isset($_SESSION['serwersms_code']) and $code == $_SESSION['serwersms_code']){
                        $confirmed = true;
                        $phone = (isset($_SESSION['serwersms_phone'])) ? sanitize_text_field($_SESSION['serwersms_phone']) : false;
                    } else {
                        $confirmed = false;
                        $json['status'] = 2;
                        $json['mess'] = __('Code is incorrect. Please try again.','serwersms');
                    }

                } else {
                    $json['status'] = 3;
                    $json['mess'] = __('No data','serwersms');
                }

                if($confirmed and $phone){

                    try{
                        $result = $serwersms->contacts->index($group,$phone);
                        if(isset($result->items) and count($result->items) > 0){
                            $contacts = array();
                            foreach($result->items as $contact){
                                $contacts[] = $contact->id;
                            }
                            $result = $serwersms->contacts->delete($contacts);
                        }
                    } catch (Exception $ex) {
                        $json['status'] = 3;
                        $json['mess'] = $ex->getMessage();
                    }

                    if(isset($result->success) and $result->success === true){
                        $json['status'] = 3;
                        $json['mess'] = __('Your phone has been correctly deleted.','serwersms');
                        unset($_SESSION['serwersms_phone']);
                        unset($_SESSION['serwersms_code']);
                    } else {
                        $json['status'] = 3;
                        $json['mess'] = __('Error. Please try again.','serwersms');
                    }
                }

                break;
                
            case 'registration':
                
                $sender = (isset($options['sender_name_registration']) and $options['sender_name_registration'] != null) ? $options['sender_name_registration'] : '';

                $code = rand(1000,9999);
                $text = __('Your verification code is','serwersms').' '.$code;

                if(
                    isset($_SESSION['serwersms_code']) and
                    !empty($_SESSION['serwersms_code']) and
                    isset($_SESSION['serwersms_phone']) and
                    ($_SESSION['serwersms_phone'] == $phone or !$verify_phone)
                ){
                    $json['status'] = 2;
                    
                } else {
                    
                    $_SESSION['serwersms_phone'] = $phone;
                    
                    try{
                        $result = $serwersms->messages->sendSms($phone,$text,$sender);
                    } catch (Exception $ex) {
                        $json['status'] = 3;
                        $json['mess'] = $ex->getMessage();
                    }
                    if(isset($result->success) and $result->success === true){
                        $_SESSION['serwersms_code'] = $code;
                        $json['status'] = 2;
                    }
                }
                
                break;
                
            case 'verification':
                
                if(isset($_SESSION['serwersms_code']) and $_SESSION['serwersms_code'] == $code){
                    $json['success'] = true;
                } else {
                    $json['success'] = false;
                    $json['error'] = __('<strong>ERROR</strong>: Invalid SMS code.','serwersms');
                }
                
                break;
        }
    }
    
    echo json_encode($json);
    die();
}