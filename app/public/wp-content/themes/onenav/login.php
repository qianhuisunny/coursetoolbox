<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:00
 * @LastEditors: iowen
 * @LastEditTime: 2021-11-23 19:27:13
 * @FilePath: \onenav\login.php
 * @Description: 
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header('Access-Control-Allow-Headers:x-requested-with,content-type');

global $wpdb,$user_ID;

$action  = (isset($_GET['action']) ) ? $_GET['action'] : 0; 
$redirect_to = esc_url(home_url());
if ( (isset( $_REQUEST['redirect'] ) && !empty($_REQUEST['redirect'])) || (isset( $_REQUEST['redirect_to'] ) && !empty($_REQUEST['redirect_to'])) ){
    $redirect_to = isset($_REQUEST['redirect']) ?  $_REQUEST['redirect'] : $_REQUEST['redirect_to'];  
}
if ( $action === "bind" ) { 
    get_template_part( 'templates/login/bind' );
} elseif ( $action === "register" ) { 
    get_template_part( 'templates/login/reg' );
} elseif ( $action === "lostpassword" ) {
    get_template_part( 'templates/login/lost' );
} else {
    if (!$user_ID) {  
        get_template_part( 'templates/login/login' );
    } else { 
        if(user_can($user_ID,'manage_options'))
            wp_safe_redirect(admin_url());
        else
            wp_safe_redirect($redirect_to);
        //echo "<script type='text/javascript'>window.location='". esc_url(home_url()) ."'</script>";
    }

}
