<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-09-10 16:47:20
 * @LastEditors: iowen
 * @LastEditTime: 2023-04-04 00:36:37
 * @FilePath: \onenav\templates\login\bind.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 
$is_bind_type      = io_get_option('bind_email','');
$bind_type         = io_get_option('bind_type','');
$bind_current_type = 'email';
$bind_title        = __('绑定邮箱完成注册','i_theme');
$action_1          = "register_after_bind_email";
$task              = 'bind';
$bind_placeholder  = __('输入邮箱','i_theme');
$type              = '';

$redirect_to = esc_url(home_url());
if ( isset( $_REQUEST['redirect'] ) || isset( $_REQUEST['redirect_to'] ) ){
    $redirect_to = isset($_REQUEST['redirect']) ?  $_REQUEST['redirect'] : $_REQUEST['redirect_to']; 
}
if(empty($bind_type)){
    wp_safe_redirect($redirect_to);
}
if($is_bind_type!='must'&&!is_user_logged_in()){
    wp_safe_redirect($redirect_to);
}elseif(is_user_logged_in()){
    $user  = wp_get_current_user();
    $email = $user->user_email;
    if (count($bind_type) == 1) {
        switch ($bind_type[0]) {
            case 'email':
                if($email){
                    wp_safe_redirect($redirect_to);
                }else{
                    $bind_current_type = 'email';
                }
                break;
            case 'phone':
                $phone = io_get_user_phone($user->ID);
                if($phone){
                    wp_safe_redirect($redirect_to);
                }else{
                    $bind_current_type = 'phone';
                }
                break;
        }
    }else{
        if(!$email){
            $bind_current_type = 'email';
        }else{
            $phone = io_get_user_phone($user->ID);
            if(!$phone){
                $bind_current_type = 'phone';
            }
        }
        if($email && $phone){
            wp_safe_redirect($redirect_to);
        }
    }
} else {
    if ($is_bind_type == 'null' && !isset($_GET['type'])) {
        wp_safe_redirect($redirect_to);
    }
    if ($is_bind_type == 'must' && !isset($_GET['type'])) { //执行绑定
        if (count($bind_type) == 1) {
            $bind_current_type = $bind_type[0];
        }
        $task = 'new';
        if (!session_id())
            session_start();
        if (!isset($_SESSION['temp_oauth']) || (isset($_SESSION['temp_oauth']) && empty($_SESSION['temp_oauth'])))
            wp_safe_redirect($redirect_to);
        $type = maybe_unserialize($_SESSION['temp_oauth'])['type'];
        switch ($type) {
            case 'sina':
                $type = 'weibo';
                break;
            case 'wechat_gzh':
                $type = 'wechat-gzh';
                break;
            case 'wechat_dyh':
                $type = 'wechat-dyh';
                break;
            case 'wx':
                $type = 'wechat';
                break;
        }
    } elseif ($is_bind_type == 'must' && isset($_GET['type']) && !is_user_logged_in()) {
        wp_safe_redirect($redirect_to);
    }
}
if ($bind_current_type == 'email') {
    $bind_title = __('绑定邮箱', 'i_theme');
} else {
    $bind_title       = __('绑定手机号', 'i_theme');
    $bind_placeholder = __('输入手机号','i_theme');
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php io_auto_theme_mode() ?>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $bind_title ?> - <?php bloginfo('name') ?></title>
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
.openlogin-<?php echo $type  ?>-a{pointer-events:none;filter:grayscale(100%);-webkit-filter:grayscale(100%);-moz-filter:grayscale(100%);-ms-filter:grayscale(100%);-o-filter:grayscale(100%);filter:url("data:image/svg+xml;utf8,#grayscale");filter:progid:DXImageTransform.Microsoft.BasicImage(grayscale=1);-webkit-filter:grayscale(1)}
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
                                <div class="sign-header h4 mb-3 mb-md-4"><?php echo $bind_title ?></div>
                                <?php if($is_bind_type=="must"&&!isset($_GET['type'])): ?>
                                <ul class="nav nav-justified mb-4" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-new-tab" data-toggle="pill" data-btn="<?php _e('确定','i_theme') ?>" data-action="<?php echo $action_1 ?>" href="#pills-new" role="tab" aria-controls="pills-new" aria-selected="true"><?php echo $bind_title ?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-old-tab" data-toggle="pill" data-btn="<?php _e('登录并绑定','i_theme') ?>" data-action="user_login" href="#pills-old" role="tab" aria-controls="pills-old" aria-selected="false"><?php _e('绑定现有账号','i_theme') ?></a>
                                    </li>
                                </ul>
                                <?php endif; ?>
                                <form method="post" action="" class="form-validate wp-user-form mb-3" id="wp_login_form">
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-new" role="tabpanel" aria-labelledby="pills-new-tab">
                                            <input type="hidden" name="action" value="<?php echo $action_1 ?>" />
                                            <input type="hidden" name="old_bind" value="1" />
                                            <input type="hidden" name="bind_type" value="<?php echo $bind_current_type ?>" />
                                            <input type="hidden" name="task" value="<?php echo $task ?>" />
                                            <div class="form-group mb-3">
                                                <input type="text" name="email_phone" tabindex="2" id="user_email" placeholder="<?php echo $bind_placeholder ?>" size="30" class="form-control"/> 
                                            </div> 
                                            <div class="form-group mb-3 verification" style="display:none">
                                                <input type="text" name="verification_code" tabindex="3" id="verification_code" placeholder="验证码"  size="6" class="form-control"/> 
                                                <a href="javascript:;" class="btn-token col-form-label text-sm" data-action="reg_email_or_phone_token"><?php _e('发送验证码','i_theme') ?></a>
                                            </div> 
                                        </div>
                                        <div class="tab-pane fade" id="pills-old" role="tabpanel" aria-labelledby="pills-old-tab">
                                            <div class="form-group mb-3">
                                                <input type="text" name="username" placeholder="<?php io_get_option('user_login_phone',false)?_e('用户名/手机号/邮箱','i_theme'):_e('用户名或邮箱','i_theme') ?>" class="form-control">
                                            </div>
                                            <div class="form-group position-relative mb-3">
                                                <input type="password" name="password" placeholder="<?php _e('密码','i_theme') ?>" class="form-control">
                                                <div class="password-show-btn" data-show="0"><i class="iconfont icon-chakan-line"></i></div>
                                            </div> 
                                            <input type="hidden" name="redirect_to" value="<?php echo esc_url_raw( $redirect_to ); ?>" />
                                        </div>
                                        <div class="mb-3"><?php do_action('io_bind_form'); ?></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <?php echo get_captcha_input_html($action_1,'form-control') ?>
                                    </div> 
                                    <button id="submit" type="submit" class="btn btn-shadow vc-red btn-hover-dark btn-block submit-btn"><?php _e('确定','i_theme') ?></button>
                                    <?php if (is_user_logged_in() && $is_bind_type=='must') { ?>
                                    <a href="<?php echo esc_url(wp_logout_url(home_url())) ?>" class="btn vc-red btn-outline btn-block mt-3"><?php _e('退出登录', 'i_theme') ?></a>
                                    <?php } ?>
                                    <div class="login-form mt-4 mb-n4 d-none"><?php do_action('io_login_form'); ?></div>
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
    <script type="text/javascript">//ajax 提交数据 
        $("#pills-new-tab").click(function() {
            var t = $(this);
            $('input[name="action"]').val(t.data("action"));
            $('input.submit-btn').val(t.data("btn"));
            $('button.submit-btn').text(t.data("btn"));
            $('.login-form').addClass('d-none');
            if($('.image-captcha')[0]){
                $('.image-captcha').data("id",t.data("action"));
                $('[name="image_captcha"]').val('');
                $('.image-captcha').click();
            }
        });
        $("#pills-old-tab").click(function() {
            var t = $(this);
            $('input[name="action"]').val(t.data("action"));
            $('input.submit-btn').val(t.data("btn"));
            $('button.submit-btn').text(t.data("btn"));
            $('.login-form').removeClass('d-none');
            if($('.image-captcha')[0]){
                $('.image-captcha').data("id",t.data("action"));
                $('[name="image_captcha"]').val('');
                $('.image-captcha').click();
            }
        });
    </script>
    <?php do_action( 'login_footer' ); ?>
    <?php wp_footer(); ?>
</body>
</html>
