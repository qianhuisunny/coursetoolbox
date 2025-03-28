<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-02-09 21:11:15
 * @LastEditors: iowen
 * @LastEditTime: 2024-01-18 21:06:25
 * @FilePath: \onenav\inc\functions\io-login.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }


/*----------------------------------连续登录验证--------------------------------------*/
if (io_get_option('login_limit', 5) > 0) {
    add_action('wp_login_failed', 'io_login_failed_action', 10, 1);
    add_filter('authenticate', 'io_login_authenticate_filter', 10, 3);
    add_filter('shake_error_codes', 'io_shake_error_codes');
    //登录成功后清除登录失败记录
    add_action('wp_login', 'io_wp_login_action', 10, 2);
}
function io_login_failed_action($username){
    $key   = md5(IOTOOLS::get_ip());
    $data  = array(
        'limit' => 1,
        'time'  => current_time('timestamp'),
    );
    $login_failed = get_option('io_login_failed');
    if ($login_failed && is_array($login_failed) && isset($login_failed[$key])) {
        $data['limit'] = $login_failed[$key]['limit'] + 1;
    }

    if ($data['limit'] <= io_get_option('login_limit', 5)) {
        $login_failed[$key] = $data;
        update_option('io_login_failed', $login_failed, false);
    } else {
        $_data              = $login_failed[$key];
        $_data['limit'] += 1;
        $login_failed[$key] = $_data;
        update_option('io_login_failed', $login_failed, false);
    }
}

function io_login_authenticate_filter($user, $username, $password){
    $key          = md5(IOTOOLS::get_ip());
    $login_failed = get_option('io_login_failed');
    if (!$login_failed || !is_array($login_failed) || !isset($login_failed[$key])) {
        return $user;
    }

    $data  = $login_failed[$key];
    if ($data['limit'] >= io_get_option('login_limit', 5)) {
        $time = (current_time('timestamp') - $data['time']);
        if ($time < MINUTE_IN_SECONDS * io_get_option('login_limit_time', 10)) {
            remove_filter('authenticate', 'wp_authenticate_username_password', 20);
            remove_filter('authenticate', 'wp_authenticate_email_password', 20);

            return new WP_Error('too_many_retries', sprintf(__('已多次登录失败，请%s后重试！', 'i_theme'), io_get_time_diff_title(MINUTE_IN_SECONDS * io_get_option('login_limit_time', 10) - $time)));
        } else {
            //多次登录失败，但是已经超过限制时间，重置
            $data['limit']      = ceil(io_get_option('login_limit', 5) / 2);
            $data['time']       = current_time('timestamp');
            $login_failed[$key] = $data;
            update_option('io_login_failed', $login_failed, false);
        }
    }

	return $user;
}

function io_shake_error_codes($error_codes){
	$error_codes[]	= 'too_many_retries';
	return $error_codes;
}

function io_get_time_diff_title($diff) {
    // 如果大于一分钟，则返回分钟
    if ($diff >= 60) {
        $m = ceil($diff / 60);
        return "{$m}分钟";
    } else {
        // 如果小于一分钟，则返回秒
        return "{$diff}秒";
    }
}

function io_wp_login_action($user_login, $user){
    $key          = md5(IOTOOLS::get_ip());
    $login_failed = get_option('io_login_failed');

    
    //清理过期数据
    if ($login_failed && is_array($login_failed)) {
        foreach ($login_failed as $k => $v) {
            if ($v['time'] < (current_time('timestamp') - (MINUTE_IN_SECONDS * io_get_option('login_limit_time', 10)))) {
                unset($login_failed[$k]);
            }
            if ($key == $k) {
                unset($login_failed[$k]);
            }
        }
        update_option('io_login_failed', $login_failed, false);
    }
}
/*----------------------------------连续登录验证 END----------------------------------*/

/**
 * 默认登录页css
 */
function io_custom_login_style(){
    $login_color = io_get_option('login_color',array('color-l'=>'','color-r'=>''));
    echo '<style type="text/css">
    body{background:'.$login_color['color-l'].';background:-o-linear-gradient(45deg,'.$login_color['color-l'].','.$login_color['color-r'].');background:linear-gradient(45deg,'.$login_color['color-l'].','.$login_color['color-r'].');height:100vh}
    .login h1 a{background-image:url('.io_get_option('logo_small_light',get_template_directory_uri() .'/images/logo.png').');width:180px;background-position:center center;background-size:80px}
    .login-container{position:relative;display:flex;align-items:center;justify-content:center;height:100vh}
    .login-body{position:relative;display:flex;margin:0 1.5rem}
    .login-img{display:none}
    .img-bg{color:#fff;padding:2rem;bottom:-2rem;left:0;top:-2rem;right:0;border-radius:10px;background-image:url('.io_get_option('login_ico',get_template_directory_uri() .'/images/login.jpg').');background-repeat:no-repeat;background-position:center center;background-size:cover}
    .img-bg h2{font-size:2rem;margin-bottom:1.25rem}
    #login{position:relative;background:#fff;border-radius:10px;padding:28px;width:280px;box-shadow:0 1rem 3rem rgba(0,0,0,.175)}
    .flex-fill{flex:1 1 auto}
    .position-relative{position:relative}
    .position-absolute{position:absolute}
    .shadow-lg{box-shadow:0 1rem 3rem rgba(0,0,0,.175)!important}
    .footer-copyright{bottom:0;color:rgba(255,255,255,.6);text-align:center;margin:20px;left:0;right:0}
    .footer-copyright a{color:rgba(255,255,255,.6);text-decoration:none}
    #login form{-webkit-box-shadow:none;-moz-box-shadow:none;box-shadow:none;border-width:0;padding:0}
    #login form .forgetmenot{float:none}
    .login #login_error,.login .message,.login .success{border-left-color:#40b9f1;box-shadow:none;background:#d4eeff;border-radius:6px;color:#2e73b7}
    .login #login_error{border-left-color:#f1404b;background:#ffd4d6;color:#b72e37}
    #login form p.submit{padding:20px 0 0}
    #login form p.submit .button-primary{float:none;background-color:#f1404b;font-weight:bold;color:#fff;width:100%;height:40px;border-width:0;text-shadow:none!important;border-color:none;transition:.5s}
    #login form input{box-shadow:none!important;outline:none!important}
    #login form p.submit .button-primary:hover{background-color:#444}
    .login #backtoblog,.login #nav{padding:0}
    .login .privacy-policy-page-link{text-align:left;margin:0}
    @media screen and (min-width:768px){.login-body{width:1200px}
    .login-img{display:block}
    #login{margin-left:-60px;padding:40px}
    }
</style>';
}
/**
 * 默认登录页html BEGIN
 */
function io_login_header(){
    echo '<div class="login-container">
    <div class="login-body">
        <div class="login-img shadow-lg position-relative flex-fill">
            <div class="img-bg position-absolute">
                <div class="login-info">
                    <h2>'. get_bloginfo('name') .'</h1>
                    <p>'. get_bloginfo('description') .'</p>
                </div>
            </div>
        </div>';
}
/**
 * 默认登录页html END
 */
function io_login_footer(){
    echo '</div><!--login-body END-->
    </div><!--login-container END-->
    <div class="footer-copyright position-absolute">
            <span>Copyright © <a href="'. esc_url(home_url()) .'" class="text-white-50" title="'. get_bloginfo('name') .'" rel="home">'. get_bloginfo('name') .'</a></span> 
    </div>';
}
if (!io_get_option('user_center',false) && io_get_option('modify_default_style',false)) {
    add_action('login_head', 'io_custom_login_style');
    add_action('login_header', 'io_login_header');
    add_action('login_footer', 'io_login_footer');

    //登录页面的LOGO链接为首页链接
    add_filter('login_headerurl', function () {
        return esc_url(home_url());
    });
    //登陆界面logo的title为博客副标题
    add_filter('login_headertext', function () {
        return get_bloginfo('description');
    });
}
/**
 * 获取注册时验证标题
 * 'email' 'phone'
 * @param mixed $page reg or lost_verify
 * @return string
 */
function get_reg_name($page = 'reg'){
    $title = '';
    $types = io_get_option("{$page}_type",array('email'));
    if (count($types) == 1) {
        foreach ($types as $v) {
            switch ($v) {
                case 'email':
                    $title .= __('邮箱', 'i_theme');
                    break;
                case 'phone':
                    $title .= __('手机号', 'i_theme');
                    break;
            }
        }
    }else{
        $title = __('邮箱或手机号', 'i_theme');
    }
    return $title;
}

/**
 * 验证方式判断
 * @param mixed $to
 * @param mixed $type
 * @param mixed $page reg or lost_verify
 * @return array
 */
function reg_form_judgment($to, $type ='', $page = 'reg'){
    $reg_type = $type ?: io_get_option("{$page}_type", array('email'));
    $error = '';
    $type = '';
    if (!$reg_type || !$to) {
        return array('type' => $type, 'to' => $to, 'error'=>(array('status' => 3, 'msg' => __('参数传入错误','i_theme' ))));
    }

    if ( is_array($reg_type) && count($reg_type) == 1) {
        foreach ($reg_type as $v) {
            $data  = io_filter_var_to($to,$v);
            $type  = isset($data['type'])?$data['type']:'';
            $error = isset($data['error'])?$data['error']:'';
        }
    } else {
        if($reg_type){
            $data  = io_filter_var_to($to,$reg_type);
            $type  = isset($data['type'])?$data['type']:'';
            $error = isset($data['error'])?$data['error']:'';
        }
        if (is_numeric($to)) {
            if (IOSMS::is_phone_number($to)) {
                $type = 'phone';
            }
        } elseif (filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $type = 'email';
        } else {
            $error = array(
                "status" => 3,
                "msg" => __('手机号或邮箱格式错误！', 'i_theme')
            );
        }
    }
    return array('type' => $type, 'to' => $to, 'error' => $error);
}
function io_filter_var_to($to, $type){
    $data = array();
    switch ($type) {
        case 'email':
            $data['type'] = 'email';
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $data['error'] = array(
                    "status" => 3,
                    "msg" => __('邮箱格式错误', 'i_theme')
                );
            }
            break;
        case 'phone':
            $data['type'] = 'phone';
            if (!IOSMS::is_phone_number($to)) {
                $data['error'] = array(
                    "status" => 3,
                    "msg" => __('手机号格式错误！', 'i_theme')
                );
            }
            break;
    }
    return $data;
}