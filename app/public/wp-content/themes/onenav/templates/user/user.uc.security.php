<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:02
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-23 20:39:23
 * @FilePath: \onenav\templates\user\user.uc.security.php
 * @Description: 
 */
?>
<?php get_header(); ?>


<?php 
global $current_user; 
$bind_type = io_get_option('bind_type',array('email'));
?>
    <div class="user-bg" style="background-image: url(<?php echo io_get_user_cover($current_user->ID ,"full") ?>)">
    </div>
    <div id="content" class="container user-area my-4">
        <div class="row">
            <div class="sidebar col-md-3 user-menu">
            <?php load_template(get_theme_file_path('/templates/user/user.menu.php')); ?>
            </div>
            <div id="user" class="col-md-9">
                <div class="author-meta-r d-none mb-5 d-md-block">
                    <div class="h2 text-white mb-3"><?php echo $current_user->display_name; ?>
                        <small class="text-xs"><span class="badge vc-violet2 btn-outline mt-2">
                            <?php echo io_get_user_cap_string($current_user->ID) ?>
                        </span></small>
                    </div>
                    <div class="text-white text-sm"><?php echo get_user_desc($current_user->ID); ?></div>
                </div> 
                <div class="card">
                <div class="card-body">
                    <div class="text-lg pb-3 border-bottom border-light border-2w mb-3"><?php _e('安全信息','i_theme') ?></div>  
                    <?php echo io_get_security_info_bind_btn($current_user, 'email') ?>
                    <?php if(io_get_option('bind_phone',false)): ?>
                    <?php echo io_get_security_info_bind_btn($current_user, 'phone') ?>
                    <?php endif; ?> 
                    <?php echo io_get_security_info_bind_btn($current_user, 'password') ?>
                    <?php echo io_bind_oauth_html( $current_user->ID ) ?>
                </div>
                </div>
            </div>
        </div>
	</div> 
<script type="text/javascript">
    (function($){ 
        $('.unbound-open-id').on('click',function(){ 
            var t = $(this);
            ioConfirm("<?php _e('你确定要解除绑定？','i_theme') ?>","<div><?php _e('解绑前请先设置邮箱和密码，否则可能造成账号丢失！','i_theme') ?><br><br><?php _e('是否继续？','i_theme') ?></div>",
            function(result){
                if(result){
                    var data = {
                        user_id:t.data('user_id'),
                        type:t.data('type'),
                        action:t.data('action')
                    };
                    unbound_ajax(data);
                }else{
                    console.log( '取消操作！');
                }
            }); 
            var unbound_ajax = function(data){
                jQuery.ajax({
                    url: theme.ajaxurl, 
                    data : data,
                    type: 'POST',
                    dataType: 'json',
                })
                .done(function(response) {  
                    if(response.status == 1){
                        location.reload();
                    }
                    showAlert(response); 
                })
                .fail(function() {  
                    showAlert(JSON.parse('{"status":4,"msg":"<?php _e('网络错误！','i_theme') ?>"}'));
                });
            }
        }); 
    })(jQuery);
</script> 
<?php io_add_captcha_js_html(); ?>
<?php get_footer(); ?>