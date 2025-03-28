<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:00
 * @LastEditors: iowen
 * @LastEditTime: 2023-03-30 00:58:36
 * @FilePath: \onenav\templates\login\lost.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php io_auto_theme_mode() ?>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php _e( '找回密码', 'i_theme' ); ?> - <?php bloginfo('name') ?></title>
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
                                <div class="sign-header h4 mb-3 mb-md-4"><?php _e('找回密码','i_theme') ?></div>
                                <?php echo io_get_password_reset_from() ?> 
                                <div class="mt-3 text-muted">
                                    <small><a href="<?php echo esc_url(io_add_redirect(home_url().'/login/?action=register')) ?>" class="signup"><?php _e('注册','i_theme') ?></a> | <a href="<?php echo esc_url(io_add_redirect(home_url().'/login/')) ?>" class="signup"><?php _e('登录','i_theme') ?></a></small> 
                                </div>
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
</body >
</html>