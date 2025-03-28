<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:00
 * @LastEditors: iowen
 * @LastEditTime: 2023-04-23 11:09:01
 * @FilePath: \onenav\templates\login\reg.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<?php
$is_reg = io_get_option('reg_verification', false);
$redirect_to = esc_url(home_url());
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
<title><?php _e( '注册', 'i_theme' ); ?> - <?php bloginfo('name') ?></title>
<link rel="shortcut icon" href="<?php echo io_get_option('favicon','') ?>">
<link rel="apple-touch-icon" href="<?php echo io_get_option('apple_icon','') ?>">
<meta name='robots' content='noindex,nofollow' />
<?php do_action('login_head'); ?>
<?php wp_head() ?>
<?php $login_color = io_get_option('login_color',array()); ?>
<style type="text/css">
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
                            <?php if ( io_is_close_register() )  { ?>
                            <div class="content">
                                <div class="sign-header h4 mb-2 mb-md-5"><?php _e( '禁止注册', 'i_theme' ); ?></div>
                                <p class="reg-error"><i class="iconfont icon-tishi"></i> <?php _e('请联系管理员！','i_theme')  ?></p>
                                <div class=" text-muted">
                                    <small><?php _e( '已有账号?', 'i_theme' ); ?> <a href="<?php echo esc_url(io_add_redirect(home_url().'/login/')) ?>" class="signup"><?php _e( '登录', 'i_theme' ); ?></a></small> 
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="content">
                                <?php if (!is_user_logged_in()) { ?>
                                <div class="sign-header h4 mb-2 mb-md-4"><?php _e('注册','i_theme') ?></div>
                                <form name="registerform" class="form-validate wp-user-form" id="wp_login_form">
                                    <div class="form-group mb-3">
                                        <input type="text" name="user_login" tabindex="1" id="user_login" placeholder="<?php _e('用户名','i_theme') ?>" size="30" class="form-control"/> 
                                    </div> 
                                    <?php if($is_reg){ ?>
                                    <div class="form-group mb-3">
                                        <input type="text" name="email_phone" tabindex="2" id="user_email" placeholder="<?php echo get_reg_name() ?>" size="30" class="form-control"/> 
                                    </div> 
                                    <div class="form-group mb-3 verification" style="display:none">
                                        <input type="text" name="verification_code" tabindex="3" id="verification_code" placeholder="验证码" size="6" class="form-control"/> 
                                        <a href="javascript:;" class="btn-token col-form-label text-sm" data-action="reg_email_or_phone_token"><?php _e('发送验证码','i_theme') ?></a>
                                    </div> 
                                    <?php } ?>
                                    <div class="form-group position-relative mb-3">
                                        <input type="password" name="user_pass" tabindex="4" id="user_pwd1" placeholder="<?php _e('密码','i_theme') ?>" size="30" class="form-control"/> 
                                        <div class="password-show-btn" data-show="0"><i class="iconfont icon-chakan-line"></i></div>
                                    </div> 
                                    <div class="form-group position-relative mb-3">
                                        <input type="password" name="user_pass2" tabindex="5" id="user_pwd2" placeholder="<?php _e('确认密码','i_theme') ?>" size="30" class="form-control"/> 
                                        <div class="password-show-btn" data-show="0"><i class="iconfont icon-chakan-line"></i></div>
                                    </div> 
                                    <div class="form-group mb-3">
                                        <?php echo get_captcha_input_html('user_register','form-control') ?>
                                    </div> 
                                    <div class="login-form mb-3"><?php do_action('register_form'); ?></div>
                                    <?php echo io_get_agreement_input() ?>
                                    <div class="d-flex my-3">
                                        <input type="hidden" name="action" value="user_register" />
                                        <input type="hidden" name="user_reg" value="ok" />
                                        <button id="submit" type="submit" name="submit" class="btn btn-shadow vc-red btn-hover-dark btn-block"><?php _e( '注册', 'i_theme' ); ?></button>
                                        <a href="<?php echo esc_url(home_url()) ?>" class="btn vc-red btn-outline btn-block mt-0 ml-4"><?php _e( '首页', 'i_theme' ); ?></a>
                                    </div> 
                                    <div class=" text-muted">
                                        <small><?php _e( '已有账号?', 'i_theme' ); ?> <a href="<?php echo esc_url(io_add_redirect(home_url().'/login/')) ?>" class="signup"><?php _e( '登录', 'i_theme' ); ?></a></small> 
                                    </div>
                                    <div class="login-form mt-4"><?php do_action('io_login_form'); ?></div>
                                    <input type="hidden" name="redirect_to" value="<?php echo esc_url_raw( $redirect_to ); ?>" />
                                </form> 
                                <?php } else { ?>
                                <div class="sign-header h4 mb-2 mb-md-5"><?php _e( '注册成功', 'i_theme' ); ?></div> 
                                <div class="d-flex mt-3">
                                    <a href="<?php echo wp_logout_url( home_url() ); ?>" class="btn btn-shadow vc-red btn-hover-dark btn-block"><?php _e( '退出登录', 'i_theme' ); ?></a>
                                    <?php
                                    if (current_user_can('manage_options')) {
                                        echo '&nbsp;&nbsp;<a href="' . admin_url() . '" class="btn vc-red btn-outline btn-block mt-0 ml-4">' . sprintf(__( '管理站点', 'i_theme' )) . '</a>';
                                    } else {
                                        echo '&nbsp;&nbsp;<a href="/user/settings" class="btn vc-red btn-outline btn-block mt-0 ml-4">' . sprintf(__( '用户中心', 'i_theme' )) . '</a>';
                                    }
                                    ?>
                                </div>
                                <p class="text-xs text-muted mt-3"><i class="iconfont icon-tishi"></i><?php _e( '3秒后自动跳转', 'i_theme' ); ?></p>
                                <script type="text/javascript">setTimeout(location.href="<?php echo $redirect_to ?>",3000);</script>
                                <?php } ?>
                            </div>
                            <?php } ?>
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
</body >
</html>
