<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-07-04 21:42:55
 * @LastEditors: iowen
 * @LastEditTime: 2023-04-19 22:51:26
 * @FilePath: \onenav\inc\action\ajax-sites.php
 * @Description: 
 */

//提交网址
function io_ajax_new_sites(){  
    if (!io_get_option('is_contribute',true)) {
        io_error (json_encode(array('status' => 1,'msg' => __('投稿功能已关闭','i_theme'))));
    }
    if (!wp_verify_nonce($_POST['_wpnonce'],"tougao_robot")){
        io_error('{"status":4,"msg":"'.__('对不起!您没有通过安全检查','i_theme').'"}');
    }
    $delay = io_get_option('contribute_time',30); 
    if( isset($_COOKIE["tougao"]) && ( time() - $_COOKIE["tougao"] ) < $delay ){
        io_error('{"status":2,"msg":"'.sprintf( __('您投稿也太勤快了吧，“%s”秒后再试！', 'i_theme'), $delay - ( time() - $_COOKIE["tougao"] ) ).'"}');
    } 
    //表单变量初始化
    $_tougao_ico    = isset($_FILES['tougao_ico'])?$_FILES['tougao_ico']:[];

    $sites_type     = isset( $_POST['sites_type'] ) ? trim(htmlspecialchars($_POST['sites_type'])) : '';
    $sites_link     = isset( $_POST['link'] ) ? trim(htmlspecialchars($_POST['link'])) : '';
    $sites_sescribe = isset( $_POST['sescribe'] ) ? trim(htmlspecialchars($_POST['sescribe'])) : '';
    $title          = isset( $_POST['post_title'] ) ? trim(htmlspecialchars($_POST['post_title'])) : '';
    $category       = isset( $_POST['category'] ) ? sanitize_key($_POST['category']) : '0';
    $sites_ico      = isset( $_POST['tougao_ico'] ) ? trim(htmlspecialchars($_POST['tougao_ico'])) : '';
    $wechat_id      = isset( $_POST['wechat_id'] ) ? trim(htmlspecialchars($_POST['wechat_id'])) : '';
    $content        = isset( $_POST['post_content'] ) ? trim(htmlspecialchars($_POST['post_content'])) : '';
    $keywords       = isset( $_POST['tags'] ) ? trim(htmlspecialchars($_POST['tags'])) : '';


    $typename = __('网站','i_theme');
    if( $sites_type == 'wechat' ) $typename = __('公众号','i_theme');

    $option      = io_get_option('sites_tg_opt',array(
        'is_publish'    => false,
        'auto_category' => '',
        'tag_limit'     => 5,
        'img_size'      => 64
    ));
    $is_publish = false;
    if($option['is_publish']){
        if($option['auto_category'])
            $category = $option['auto_category'];
        $is_publish = true;
    }

    // 表单项数据验证
    if ( empty($title) ) {
        io_error('{"status":4,"msg":"'.$typename.__('名称必须填写！','i_theme').'"}');
    }
    if ( mb_strlen($title) > 30 ) {
        io_error('{"status":4,"msg":"'.$typename.__('名称长度不得超过30字。','i_theme').'"}');
    }

    if( title_exists($title,'sites') ) {
        io_error('{"status":4,"msg":"'.__('存在相同的名称，请不要重复提交哦！','i_theme').'"}');
    }

    if ( $sites_type=='sites' && empty($sites_link) ){
        io_error('{"status":3,"msg":"'.$typename.__('链接必须填写！','i_theme').'"}');
    }
    if ( (!empty($sites_link) && !preg_match("/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,8}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/", $sites_link)) ){
        io_error('{"status":3,"msg":"'.$typename.__('链接必须符合URL格式。','i_theme').'"}');
    }

    if( $sites_type=='sites' && link_exists($sites_link)) {
        io_error('{"status":4,"msg":"'.__('存在相同的链接地址，请不要重复提交哦！','i_theme').'"}');
    }

    if ( empty($sites_sescribe) ) {
        io_error('{"status":4,"msg":"'.$typename.__('简介必须填写！','i_theme').'"}');
    }
    if ( mb_strlen($sites_sescribe) > 80 ) {
        io_error('{"status":4,"msg":"'.$typename.__('简介长度不得超过80字。','i_theme').'"}');
    }
    
    if ( $category == "0" ){
        io_error('{"status":4,"msg":"'.__('请选择分类。','i_theme').'"}');
    }

    if ( !empty(get_term_children($category, 'favorites'))){
        io_error('{"status":4,"msg":"'.__('不能选用父级分类目录。','i_theme').'"}');
    }

    if (!empty($keywords) && 0!=$option['tag_limit']){
        if( count(preg_split("/,|，|\s|\n/", $keywords)) > $option['tag_limit'] ) {
            io_error('{"status":4,"msg":"'.sprintf(__('标签不能超过%s个！','i_theme'), $option['tag_limit']).'"}');
        }
    }

    if ( $sites_type=='wechat' && empty($wechat_id)) {
        io_error('{"status":4,"msg":"'.__('必须添加微信号。','i_theme').'"}');
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
    
    //执行人机验证
    io_ajax_is_robots();
    
    //上传图片
    $oldimg_id = 0;
    if(!empty($_tougao_ico) && $_tougao_ico['error']=="0"){
        if(0==$option['img_size']){
            io_error(array('status' => 2,'msg' =>__('图片上传功能已关闭。','i_theme')));
        }
        if ($_tougao_ico['size'] > ($option['img_size'] * 1024)) {
            io_error('{"status":3,"msg":"'.sprintf(__('图片大小不能超过 %s kb','i_theme'),$option['img_size']).'"}'); 
        }  
        $_img = IOTOOLS::addImg($_tougao_ico,'tougao_ico');
        $sites_ico = $_img["src"];
        $oldimg_id = $_img["id"];
    }
    
    //if(!empty($_wechat_qr) && $_wechat_qr['error']==0){
    //    $_img = IOTOOLS::addImg($_wechat_qr,'wechat_qr',$oldimg_id);
    //    $wechat_qr = $_img["src"];
    //}  //根据微信号通过api生成二维码


    if(!empty($keywords) && !$option['is_publish'] && io_get_option('tag_temp',true)) {
        $content = '<span style="color:red">&lt;删除&gt;</span><h1>剪切下方关键字到标签：</h1>'.PHP_EOL. $keywords.PHP_EOL.'<h1>正文：</h1><span style="color:red">&lt;/删除&gt;</span>'.PHP_EOL . $content;
    }
    $post_data = array(
        'comment_status'   => 'open',
        'ping_status'      => 'closed',
        'post_author'      => $u_id,//用于投稿的用户ID
        'post_title'       => $title,
        'post_content'     => $content,
        'post_status'      => 'pending',
        'post_type'        => 'sites',
        //'tax_input'        => array( 'favorites' => array($category) ) //游客不可用
    );
    

    if ($is_publish) {
        $post_data['post_status'] = 'publish';
    }

    //if(!empty($keywords) && $option['is_publish']){
    //    $post_data['tags_input'] = preg_split("/,|，|\s|\n/", $keywords);//设置文章tag
    //} 
    // 将文章插入数据库
    $in_id = wp_insert_post( $post_data );
    if ($in_id != 0){
        add_post_meta($in_id, '_sites_type', $sites_type);
        add_post_meta($in_id, '_sites_sescribe', $sites_sescribe);
        add_post_meta($in_id, '_sites_link', $sites_link);
        add_post_meta($in_id, '_sites_order', '0');
        if( !empty($sites_ico))
            add_post_meta($in_id, '_thumbnail', $sites_ico); 
        if( !empty($wechat_id))
            add_post_meta($in_id, '_wechat_id', $wechat_id); 
        wp_set_post_terms( $in_id, array($category), 'favorites'); //设置文章分类

        if(!empty($keywords) && $option['is_publish'] || !io_get_option('tag_temp',true)) {
            wp_set_post_terms( $in_id, preg_split("/,|，|\s|\n/", $keywords), 'sitetag'); //设置文章tag
        }

        setcookie("tougao", time(), time()+$delay+10, '/', '', false);
        if (!$is_publish) {
            do_action('io_contribute_to_publish', get_post($in_id));
        }
        io_error('{"status":1,"msg":"'.__('投稿成功！','i_theme').'"}');
    }else{
        io_error('{"status":4,"reset":1, "msg":"'.__('投稿失败！','i_theme').'"}');
    }
}
add_action('wp_ajax_nopriv_io_sites_submit', 'io_ajax_new_sites');  
add_action('wp_ajax_io_sites_submit', 'io_ajax_new_sites');


function io_ajax_get_sites_seo_data(){
    $url = esc_url($_REQUEST['url']);
    if (empty($url)) {
        exit;
    }
    $cache_key = 'seo_' . md5($url);
    $sites_seo = wp_cache_get($cache_key, 'sites_seo');
    if (!$sites_seo) {
        $http = new Yurun\Util\HttpRequest;

        $api_url = "https://apis.5118.com/weight";

        $http->headers([
            'Content-Type'  => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Authorization' => '9E5DD16996FE4427AD40C8DA7872B014',
        ]);

        $params = array(
            "url" => $url
        );

        $response     = $http->post($api_url, $params);
        $ret          = $response->json(true);
        $sites_seo    = $ret;
        $ret['cache'] = 1;
        wp_cache_set($cache_key, $ret, 'sites_seo', DAY_IN_SECONDS);
    }
    io_error($sites_seo);
}
add_action('wp_ajax_nopriv_get_sites_seo', 'io_ajax_get_sites_seo_data');  
add_action('wp_ajax_get_sites_seo', 'io_ajax_get_sites_seo_data');
