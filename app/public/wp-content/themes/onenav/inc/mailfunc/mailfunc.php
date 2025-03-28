<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-08-20 10:24:32
 * @LastEditors: iowen
 * @LastEditTime: 2023-03-28 00:10:10
 * @FilePath: \onenav\inc\mailfunc\mailfunc.php
 * @Description: 
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }


$functions = array(
    'class.Async.Task',
    'class.Async.Email',
    'option',
    'templet',
);

foreach ($functions as $function) {
    $path = 'inc/mailfunc/' . $function . '.php';
    require get_theme_file_path($path);
}

/* 实例化异步任务类实现注册异步任务钩子 */
new AsyncEmail();


/**
 * 评论回复邮件
 *
 * @since 2.0.0
 * @param $comment_id
 * @param $comment_object
 * @return void 
 */
function io_comment_mail_notify($comment_id, $comment_object) { 
    $admin_notify = '1'; // admin 要不要收回复通知 ( '1'=要 ; '0'=不要 )
    $admin_email = get_bloginfo ('admin_email'); // $admin_email 可改为你指定的 e-mail.
    $comment = get_comment($comment_id);
    $comment_author = trim($comment->comment_author);
    $comment_date = trim($comment->comment_date);
    $comment_link = htmlspecialchars(get_comment_link($comment_id));
    $comment_content = nl2br($comment->comment_content);
    $comment_author_email = trim($comment->comment_author_email);
    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
    $parent_comment = !empty($parent_id) ? get_comment($parent_id) : null;
    $parent_email = $parent_comment ? trim($parent_comment->comment_author_email) : '';
    $post = get_post($comment_object->comment_post_ID);
    $post_author_email = get_user_by( 'id' , $post->post_author)->user_email;

    if( $comment_object->comment_approved != 1 ){
        $args = array(
            'postTitle' => $post->post_title,
            'commentAuthor' => $comment_author,
            'commentContent' => $comment_content,
            'commentLink' => $comment_link,
        );
        io_async_mail( $admin_email, sprintf( __('%s上的文章有了新的回复', 'i_theme'), get_bloginfo('name') ), io_templet_comment_admin($args));
        return;
    }

    $notify = 1; // 默认全部提醒
    $spam_confirmed = $comment->comment_approved;
    //给父级评论提醒
    if ($parent_id != '' && $spam_confirmed != 'spam' && $notify == '1' && $parent_email != $comment_author_email) {
        $parent_author = trim($parent_comment->comment_author);
        $parent_comment_date = trim($parent_comment->comment_date);
        $parent_comment_content = nl2br($parent_comment->comment_content);
        $args = array(
            'parentAuthor' => $parent_author,
            'parentCommentDate' => $parent_comment_date,
            'parentCommentContent' => $parent_comment_content,
            'postTitle' => $post->post_title,
            'commentAuthor' => $comment_author,
            'commentDate' => $comment_date,
            'commentContent' => $comment_content,
            'commentLink' => $comment_link
        );
        if(filter_var( $parent_email, FILTER_VALIDATE_EMAIL)){
            io_async_mail($parent_email, sprintf( __('%1$s在%2$s中回复你', 'i_theme'), $comment_object->comment_author, $post->post_title ), io_templet_reply($args));
        }
        if ($parent_comment->user_id) {
            io_create_message($parent_comment->user_id, $comment->user_id, $comment_author, 'comment', sprintf(__('我在%1$s中回复了你', 'i_theme'), $post->post_title), $comment_content);
        }
        
    }

    //给文章作者的通知
    if($post_author_email != $comment_author_email && $post_author_email != $parent_email){
        $args = array(
            'postTitle' => $post->post_title,
            'commentAuthor' => $comment_author,
            'commentContent' => $comment_content,
            'commentLink' => $comment_link
        );
        if(filter_var( $post_author_email, FILTER_VALIDATE_EMAIL)){
            io_async_mail( $post_author_email, sprintf( __('%1$s在%2$s中回复你', 'i_theme'), $comment_author, $post->post_title ), io_templet_comment($args));
        }
        io_create_message($post->post_author, 0, 'System', 'notification', sprintf(__('%1$s在%2$s中回复你', 'i_theme'), $comment_author, $post->post_title), $comment_content);
    }

    //给管理员通知
    if($post_author_email != $admin_email && $parent_id != '' && $admin_notify == '1'){
        $args = array(
            'postTitle' => $post->post_title,
            'commentAuthor' => $comment_author,
            'commentContent' => $comment_content,
            'commentLink' => $comment_link,
            'verify' => '0'
        );
        io_async_mail( $admin_email, sprintf( __('%s上的文章有了新的回复', 'i_theme'), get_bloginfo('name') ), io_templet_comment_admin($args));
    
    }
}
//add_action('comment_post', 'io_comment_mail_notify');
add_action('wp_insert_comment', 'io_comment_mail_notify' , 99, 2 );


/**
 * WP登录提醒
 *
 * @since 2.0.0
 * @param string $user_login
 * @return void
 */
function io_wp_login_notify($user_login){ 
    $admin_email = get_bloginfo ('admin_email');
    $subject = __('你的博客空间登录提醒', 'i_theme');
    $args = array(
        'loginName' => $user_login,
        'ip' => IOTOOLS::get_ip()
    );
    io_async_mail( $admin_email, $subject, io_templet_login($args));
}
//add_action('wp_login', 'io_wp_login_notify', 10, 1);

/**
 * WP登录错误提醒
 *
 * @since 2.0.0
 * @param string $login_name
 * @return void
 */
function io_wp_login_failure_notify($login_name){
    $admin_email = get_bloginfo ('admin_email');
    $subject = __('你的博客空间登录错误警告', 'i_theme');
    $args = array(
        'loginName' => $login_name,
        'ip' => IOTOOLS::get_ip()
    );
    io_async_mail( $admin_email, $subject, io_templet_login_fail($args));
}
//add_action('wp_login_failed', 'io_wp_login_failure_notify', 10, 1);


/**
 * 更改找回密码邮件中的内容
 *
 * @since 2.0.0
 * @param $message
 * @param $key
 */
function io_reset_password_message( $message, $key, $user_login, $user_data ) {
    if(!is_admin()) io_ajax_is_robots();
    if (!$user_data) {
        if (strpos($_POST['user_login'], '@')) {
            $user_data = get_user_by('email', trim($_POST['user_login']));
        } else {
            $login     = trim($_POST['user_login']);
            $user_data = get_user_by('login', $login);
        }
    }
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;
    $reset_link = network_site_url('wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode($user_login), 'login') ;

    $args = array('home' => home_url(), 'userLogin' => $user_login, 'resetPassLink' => $reset_link);

    io_mail( $user_email, sprintf( __('你的登录密码重置链接-%1$s', 'i_theme'), get_bloginfo('name') ), io_templet_findpass($args));
}
add_filter('retrieve_password_message', 'io_reset_password_message', 10, 4);

/**
 * 用户提交链接向管理员发送邮件 
 */
function io_add_links_submit_email_to_admin($data){
	$args = array(
		'link_name'         => esc_attr($data['link_name']),
		'link_url'          => esc_url($data['link_url']),
		'link_description'  => !empty($data['link_description']) ? esc_attr($data['link_description']) : '无',
		'link_image'        => !empty($data['link_image']) ? esc_attr($data['link_image']) : '空',
        'link_admin'        => admin_url('link-manager.php?orderby=visible&order=asc'),
	);
    io_async_mail( get_option('admin_email'), sprintf( __('[%s]新的友情链接待审核', 'i_theme'), get_bloginfo('name') ),io_templet_add_links($args)); 
}
add_action('io_ajax_add_links_submit_success', 'io_add_links_submit_email_to_admin', 99);

/**
 * 通知用户
 * 邮件 短信 或者站内信等
 * #TODO 
 * @param mixed $type 
 * @param mixed $to
 * @param mixed $msg
 * @return void
 */
function io_notify_user($type, $to = '', $msg = ''){

}


/**
 * 用户绑定手机号通知
 * 
 * @param mixed $user_id
 * @param mixed $type
 * @param mixed $new_to
 * @param mixed $old_to
 * @return void
 */
function io_user_bind_new_email_or_phone_notice($user_id, $type, $new_to, $old_to){
    $user = get_userdata($user_id);

    $blog_name = get_bloginfo('name');
    $new_to = io_get_hide_info($new_to, $type);
    $old_to = $old_to ? io_get_hide_info($old_to, $type) : false;

    if('email' === $type){
        $title       = $old_to ? __('邮箱修改成功', 'i_theme') : __('邮箱绑定成功', 'i_theme');
        $info_text   = $old_to ? __('您的账号绑定的邮箱已修改', 'i_theme') : __('您的账号已成功绑定邮箱', 'i_theme');
        $action_text = $old_to ? sprintf(__('由 %s 修改为 %s', 'i_theme'), $old_to, $new_to) : __('邮箱：', 'i_theme') . $new_to;
    } else {
        $title       = $old_to ? __('手机号修改成功', 'i_theme') : __('手机号绑定成功', 'i_theme');
        $info_text   = $old_to ? __('您的账号绑定的手机号已修改', 'i_theme') : __('您的账号已成功绑定手机号', 'i_theme');
        $action_text = $old_to ? sprintf(__('由 %s 修改为 %s', 'i_theme'), $old_to, $new_to) : __('手机号：', 'i_theme') . $new_to;
    }
    $message = __('您好，', 'i_theme') . $user->display_name . '!<br />';
    $message .= $info_text . '<br />';
    $message .= $action_text . '<br/><br/>';
    $message .= __('如非您本人操作，请及时与客服联系！', 'i_theme');
    
    io_mail( $user->user_email, '['.$blog_name.']'.$title, $message);
}
add_action('io_user_bind_new_email_or_phone', 'io_user_bind_new_email_or_phone_notice', 99, 4);
