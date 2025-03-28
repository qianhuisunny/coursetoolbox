<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:00
 * @LastEditors: iowen
 * @LastEditTime: 2023-03-30 00:56:58
 * @FilePath: \onenav\templates\login\login.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 
$redirect_to = ''; 
if ( (isset( $_REQUEST['redirect'] ) && !empty($_REQUEST['redirect'])) || (isset( $_REQUEST['redirect_to'] ) && !empty($_REQUEST['redirect_to'])) ){
    $redirect_to = isset($_REQUEST['redirect']) ?  $_REQUEST['redirect'] : $_REQUEST['redirect_to'];  
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php io_auto_theme_mode() ?>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php _e( '登录', 'i_theme' ); ?> - <?php bloginfo('name') ?></title>
<link rel="shortcut icon" href="<?php echo io_get_option('favicon','') ?>">
<link rel="apple-touch-icon" href="<?php echo io_get_option('apple_icon','') ?>">
<meta name='robots' content='noindex,nofollow' /> 
<?php do_action('login_head'); ?>
<?php wp_head() ?>
<?php $login_color = io_get_option('login_color',array()); ?>
<style> 
:root {
--bg-color-l: <?php echo $login_color['color-l'] ?>;
--bg-color-r: <?php echo $login_color['color-r'] ?>;
}
</style>
</head>
<body <?php body_class(io_body_class()); ?> >
<?php dark_mode_js() ?>
    <div class="page login-page">
        <div class="container d-flex align-items-center">
            <div class="form-holder has-shadow">
                <div class="row no-gutters">
                    <!-- Logo & Information Panel-->
                    <div class="col-md-6 col-lg-7 col-xl-8 my-n5 d-none d-md-block">
                        <div class="info d-flex p-5 mr-n5 position-relative login-img rounded-xl shadow-lg" style="background-image: url(<?php echo io_get_option('login_ico','') ?>);">
                            <div class="content position-absolute mr-5 pr-5">
                                <div class="logo">
                                    <h1><?php bloginfo('name') ?></h1>
                                </div>
                                <p><?php bloginfo('description') ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- Form Panel    -->
                    <div class="col-12 col-md-6 col-lg-5 col-xl-4 bg-blur rounded-xl shadow-lg">
                        <div class="form d-flex align-items-center p-4 p-md-5">
                            <div class="content">
                                <div class="sign-header h4 mb-3 mb-md-4"><?php _e('欢迎回来','i_theme') ?></div>
                                <form method="post" action="" class="form-validate wp-user-form" id="wp_login_form">
                                    <input type="hidden" name="action" value="user_login" />
                                    <div class="form-group mb-3">
                                        <input type="text" name="username" placeholder="<?php io_get_option('user_login_phone',false)?_e('用户名/手机号/邮箱','i_theme'):_e('用户名或邮箱','i_theme') ?>" class="form-control">
                                    </div>
                                    <div class="form-group position-relative mb-3">
                                        <input type="password" name="password" placeholder="<?php _e('密码','i_theme') ?>" class="form-control">
                                        <div class="password-show-btn" data-show="0"><i class="iconfont icon-chakan-line"></i></div>
                                    </div> 
                                    <div class="form-group mb-3">
                                        <?php echo get_captcha_input_html('user_login','form-control') ?>
                                    </div> 
                                    <div class="custom-control custom-checkbox text-xs mb-3">
                                        <input type="checkbox" class="custom-control-input" checked="checked" name="rememberme" id="check1" value="forever">
                                        <label class="custom-control-label" for="check1"><?php _e('记住我的登录信息','i_theme') ?></label>
                                    </div> 
                                    <?php echo io_get_agreement_input() ?>
                                    <div class="login-form mb-3"><?php do_action('login_form'); ?></div>
                                    <div class="d-flex mb-3">
                                        <button id="submit" type="submit" class="btn btn-shadow vc-red btn-hover-dark btn-block"><?php _e('登录','i_theme') ?></button>
                                        <a href="<?php echo esc_url(home_url()) ?>" class="btn vc-red btn-outline btn-block mt-0 ml-4"><?php _e('首页','i_theme') ?></a>
                                    </div> 
                                    <div class=" text-muted">
                                        <small><?php _e('没有账号？','i_theme') ?><a href="<?php echo esc_url(io_add_redirect(home_url().'/login/?action=register')) ?>" class="signup"><?php _e('注册','i_theme') ?></a> / <a href="<?php echo esc_url(io_add_redirect(home_url().'/login/?action=lostpassword')) ?>" class="signup"><?php _e('找回密码','i_theme') ?></a></small> 
                                    </div>
                                    <div class="login-form mt-4"><?php do_action('io_login_form'); ?></div>
                                    <input type="hidden" name="redirect_to" value="<?php echo esc_url_raw( $redirect_to ); ?>" />
                                </form> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-copyright my-4">
            <div class="text-white-50 text-center">
                <small><?php io_copyright('text-white-50',true) ?></small> 
            </div>
        </div>
    </div>
    <?php do_action( 'login_footer' ); ?>
    <?php wp_footer(); ?>

</body>
</html>