<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-07-04 21:36:40
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-04 01:03:19
 * @FilePath: \onenav\inc\action\ajax-post.php
 * @Description: 
 */

//前台投稿
function io_ajax_new_posts_sites(){
    if (!io_get_option('is_contribute',true)) {
        io_error (json_encode(array('status' => 1,'msg' => __('投稿功能已关闭','i_theme'))));
    }
    if (!wp_verify_nonce($_POST['_wpnonce'],'posts_submit')){
        io_error('{"status":4,"msg":"'.__('对不起!您没有通过安全检查','i_theme').'"}');
    } 
    $delay = io_get_option('contribute_time',30); 
    if( isset($_COOKIE["tougao"]) && ( time() - $_COOKIE["tougao"] ) < $delay ){
        io_error('{"status":2,"msg":"'.sprintf( __('您投稿也太勤快了吧，“%s”秒后再试！', 'i_theme'), $delay - ( time() - $_COOKIE["tougao"] ) ).'"}');
    } 

    //表单变量初始化
    $title      = isset($_POST['post_title']) ? htmlspecialchars($_POST['post_title']) : false;
    $content    = isset($_POST['post_content']) ? $_POST['post_content'] : false;
    $category   = isset($_POST['category']) ? htmlspecialchars($_POST['category']) : false;
    $action     = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : false;
    $keywords   = isset($_POST['tags'] ) ? htmlspecialchars($_POST['tags']) : '';
    

    if (empty($title)) {
        io_error(json_encode(array('status' => 4,'msg' =>__('请填写文章标题','i_theme'))));
    }
    if (empty($content)) {
        io_error(json_encode(array('status' => 4,'msg' =>__('还未填写任何内容','i_theme'))));
    }

    $option = io_get_option('post_tg_opt',array(
        'is_publish'   => false,
        'tag_limit'    => 5,
        'title_limit'  => array(
            'width'  => 5,
            'height' => 30,
        ),
    ));

    if (io_strlen($title) > $option['title_limit']['height']) {
        io_error(json_encode(array('status' => 4,'msg' => sprintf(__('标题太长了，不能超过%s个字','i_theme'), $option['title_limit']['height']))));
    }
    if (io_strlen($title) < $option['title_limit']['width']) {
        io_error(json_encode(array('status' => 4,'msg' =>__('标题太短！','i_theme'))));
    }
    if (io_strlen($content) < 10) {
        io_error(json_encode(array('status' => 4,'msg' =>__('文章内容过少','i_theme'))));
    }
    if (empty($category)) {
        io_error(json_encode(array('status' => 4,'msg' =>__('请选择文章分类','i_theme'))));
    }
    if (!empty($keywords) && 0!=$option['tag_limit']){
        if( count(preg_split("/,|，|\s|\n/", $keywords)) > $option['tag_limit'] ) {
            io_error('{"status":4,"msg":"'.sprintf(__('标签不能超过%s个！','i_theme'), $option['tag_limit']).'"}');
        }
    }

    $is_publish = false;
    if ($option['is_publish']) {
        if($option['auto_category'])
            $category = $option['auto_category'];
        $is_publish = true;
    }

    $u_id = get_current_user_id();
    if (!$u_id) {
        if (empty($_POST['user_name'])) {
            io_error (array('status' => 3, 'msg' => __('请输入昵称！','i_theme')));
        }
        if (empty($_POST['contact_details'])) {
            io_error (array('status' => 3, 'msg' => __('请输入联系方式！','i_theme')));
        }
        $title       = $title . '[投稿人：' . esc_attr($_POST['user_name']) . ',联系：' . esc_attr($_POST['contact_details']) . ']';
    }

    //人机验证
    io_ajax_is_robots();

    $category   = array($category);

    if(!empty($keywords) && !$option['is_publish'] && io_get_option('tag_temp',true)) {
        $content = '<span style="color:red">&lt;删除&gt;</span><h1>剪切下方关键字到标签：</h1>'.PHP_EOL. $keywords.PHP_EOL.'<h1>正文：</h1><span style="color:red">&lt;/删除&gt;</span>'.PHP_EOL . $content;
    }

    $post_data = array(
        'post_title'     => $title,
        'post_status'    => 'pending',
        'post_author'    => $u_id,
        'post_content'   => wp_kses_post($content),
        'post_category'  => $category,
        'comment_status' => 'open',
    ); 

    if(!empty($keywords) && $option['is_publish'] || !io_get_option('tag_temp',true)) {
        $keywords = preg_split("/,|，|\s|\n/", $keywords);
        $post_data['tags_input'] = $keywords;
    }

    if ($is_publish) {
        $post_data['post_status'] = 'publish';
    }

    //保存文章
    $in_id = wp_insert_post($post_data, 1);

    if (is_wp_error($in_id)) {
        io_error(json_encode(array('status' => 4, 'reset'=>1, 'msg' =>$in_id->get_error_message())));
    }
    if (!$in_id) {
        io_error(json_encode(array('status' => 4, 'reset'=>1, 'msg' =>__('投稿失败！','i_theme'))));
    }

    setcookie("tougao", time(), time()+$delay+10, '/', '', false);
    $send = array(
        'status' => 1, 
        'msg'    =>__('投稿成功！','i_theme')
    );
    if($u_id){
        $send['goto'] = get_permalink($in_id);
    }
    if(!$is_publish){
        do_action('io_contribute_to_publish', get_post($in_id));
    }
    io_error($send);

}
add_action('wp_ajax_io_posts_submit', 'io_ajax_new_posts_sites');
add_action('wp_ajax_nopriv_io_posts_submit', 'io_ajax_new_posts_sites');

//编辑器上传图片
function io_ajax_img_upload(){
    $file_id = 'file';
    if (empty($_FILES[$file_id])) {
        io_error(array('status' => 2,'msg' =>__('上传信息错误，请重新选择文件','i_theme')));
    }

    if (!wp_verify_nonce($_POST['_wpnonce'],'edit_file_upload')){
        io_error('{"status":2,"msg":"'.__('对不起!您没有通过安全检查','i_theme').'"}');
    } 

    $max_size = io_get_option('post_tg_opt', 1024, 'img_size');
    if(0==$max_size){
        io_error(array('status' => 2,'msg' =>__('图片上传功能已关闭。','i_theme')));
    }
    //文件类型判断
    if (!stristr($_FILES[$file_id]['type'], 'image')) {
        io_error(array('status' => 2,'msg' =>__('文件不属于图片格式','i_theme')));
    }

    //文件大小判断
    if ($_FILES[$file_id]['size'] > $max_size * 1024) {
        io_error(array('status' => 2,'msg' => sprintf(__('图片大小不能超过 %s kb','i_theme'),$max_size)));
    }
    //开始上传
    $_img = IOTOOLS::addImg($_FILES[$file_id],$file_id);
    if (!empty($_img['id'])) {
        io_error(array('status' => 1,'src' => $_img['src'], 'img_id' => $_img['id'],'msg' =>__('上传成功！','i_theme')));
    }

    io_error(array('status' => 4,'msg' =>__('上传失败！','i_theme')));
}
add_action('wp_ajax_edit_file_upload', 'io_ajax_img_upload');
add_action('wp_ajax_nopriv_edit_file_upload', 'io_ajax_img_upload');
