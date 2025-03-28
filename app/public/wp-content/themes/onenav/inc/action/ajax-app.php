<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-07-04 21:42:55
 * @LastEditors: iowen
 * @LastEditTime: 2023-03-11 03:00:50
 * @FilePath: \onenav\inc\action\ajax-app.php
 * @Description: 
 */

// TODO:待完善
//提交网址
function io_ajax_new_app(){  
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
    $_tougao_ico = isset($_FILES['tougao_ico'])?$_FILES['tougao_ico']:[];
    $_wechat_qr  = isset($_FILES['wechat_qr'])?$_FILES['wechat_qr']:[];
    //表单变量初始化
    $sites_type         = isset( $_POST['tougao_type'] ) ? trim(htmlspecialchars($_POST['tougao_type'])) : '';
    $sites_link         = isset( $_POST['tougao_sites_link'] ) ? trim(htmlspecialchars($_POST['tougao_sites_link'])) : '';
    $sites_sescribe     = isset( $_POST['tougao_sites_sescribe'] ) ? trim(htmlspecialchars($_POST['tougao_sites_sescribe'])) : '';
    $title              = isset( $_POST['tougao_title'] ) ? trim(htmlspecialchars($_POST['tougao_title'])) : '';
    $category           = isset( $_POST['tougao_cat'] ) ? sanitize_key($_POST['tougao_cat']) : '0';
    $sites_ico          = isset( $_POST['tougao_sites_ico'] ) ? trim(htmlspecialchars($_POST['tougao_sites_ico'])) : '';
    $wechat_id          = isset( $_POST['tougao_wechat_id'] ) ? trim(htmlspecialchars($_POST['tougao_wechat_id'])) : '';
    $content            = isset( $_POST['tougao_content'] ) ? trim(htmlspecialchars($_POST['tougao_content'])) : '';
    $keywords           = isset( $_POST['tougao_sites_keywords'] ) ? trim(htmlspecialchars($_POST['tougao_sites_keywords'])) : '';

    
    $down_version       = isset( $_POST['tougao_down_version'] ) ? trim(htmlspecialchars($_POST['tougao_down_version'])) : '';//资源版本
    $down_formal        = isset( $_POST['tougao_down_formal'] ) ? trim(htmlspecialchars($_POST['tougao_down_formal'])) : '';//官网链接
    $sites_down         = isset( $_POST['tougao_sites_down'] ) ? trim(htmlspecialchars($_POST['tougao_sites_down'])) : '';//网盘链接
    $down_preview       = isset( $_POST['tougao_down_preview'] ) ? trim(htmlspecialchars($_POST['tougao_down_preview'])) : '';//演示链接
    $sites_password     = isset( $_POST['tougao_sites_password'] ) ? trim(htmlspecialchars($_POST['tougao_sites_password'])) : '';//网盘密码
    $down_decompression = isset( $_POST['tougao_down_decompression'] ) ? trim(htmlspecialchars($_POST['tougao_down_decompression'])) : '';//解压密码

    $typename = __('网站','i_theme');
    if( $sites_type == 'down' )
    $typename = __('资源','i_theme');
    if( $sites_type == 'wechat' )
    $typename = __('公众号','i_theme');

    $post_status = 'pending';
    $tg_option   = io_get_option('sites_tg_opt',array(
        'is_publish'    => false,
        'auto_category' => '',
        'tag_limit'     => 5,
        'img_size'      => 64
    ));
    if($tg_option['is_publish']){
        if($tg_option['auto_category'])
            $category = $tg_option['auto_category'];
        $post_status = 'publish';
    }

    // 表单项数据验证
    if ( empty($title) || mb_strlen($title) > 30 ) {
        io_error('{"status":4,"msg":"'.$typename.__('名称必须填写，且长度不得超过30字。','i_theme').'"}');
    }
    global $wpdb; 
    $titles = "SELECT post_title FROM $wpdb->posts WHERE post_status IN ('pending','publish') AND post_type = 'sites' AND post_title = '{$title}'";
    if($wpdb->get_row($titles)) {
        io_error('{"status":4,"msg":"'.__('存在相同的名称，请不要重复提交哦！','i_theme').'"}');
    }

    if ( $sites_type=='sites' && empty($sites_link) || (!empty($sites_link) && !preg_match("/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,8}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/", $sites_link))){
        io_error('{"status":3,"msg":"'.$typename.__('链接必须填写，且必须符合URL格式。','i_theme').'"}');
    }
    $meta_value = "SELECT meta_value FROM $wpdb->postmeta WHERE meta_value = '{$sites_link}' AND meta_key='_sites_link'";
    if( $sites_type=='sites' && $wpdb->get_row($meta_value)) {
        io_error('{"status":4,"msg":"'.__('存在相同的链接地址，请不要重复提交哦！','i_theme').'"}');
    }

    if ( empty($sites_sescribe) || mb_strlen($sites_sescribe) > 50 ) {
        io_error('{"status":4,"msg":"'.$typename.__('描述必须填写，且长度不得超过50字。','i_theme').'"}');
    }
    if ( $category == "0" ){
        io_error('{"status":4,"msg":"'.__('请选择分类。','i_theme').'"}');
    }
    if ( !empty(get_term_children($category, 'favorites'))){
        io_error('{"status":4,"msg":"'.__('不能选用父级分类目录。','i_theme').'"}');
    }
    if (!empty($keywords)){
        if( count(preg_split("/,|，|\s|\n/", $keywords)) > $tg_option['tag_limit'] ) {
            io_error('{"status":4,"msg":"'.sprintf(__('标签不能超过%s个！','i_theme'), $tg_option['tag_limit']).'"}');
        }
    }
    //if ( empty($content) || mb_strlen($content) > 10000 || mb_strlen($content) < 6) {
    //    error('{"status":4,"msg":"内容必须填写，且长度不得超过10000字，不得少于6字。"}');
    //}
    if( $sites_type == 'down'){
        if ( empty($down_formal) && empty($sites_down) ) {
            io_error('{"status":4,"msg":"'.__('“官网地址”和“网盘地址”怎么地也待填一个把。','i_theme').'"}');
        }
    }
    //if(!empty($sites_ico)){
    //    $sites_ico = array(
    //        'url'       => $sites_ico,  
    //        'thumbnail' => $sites_ico, 
    //    );
    //}
    //if(!empty($wechat_qr)){
    //    $wechat_qr = array(
    //        'url'       => $wechat_qr,  
    //        'thumbnail' => $wechat_qr, 
    //    );
    //}

    //执行人机验证
    io_ajax_is_robots();
    
    //上传图片
    $oldimg_id = 0;
    if(!empty($_tougao_ico) && $_tougao_ico['error']=="0"){
        if ($_tougao_ico['size'] > ($tg_option['img_size'] * 1024)) {
            echo '{"status":3,"msg":"'.sprintf(__('图片大小不能超过 %s kb','i_theme'),$tg_option['img_size']).'"}'; 
            exit();
        }  
        $_img = IOTOOLS::addImg($_tougao_ico,'tougao_ico');
        $sites_ico = $_img["src"];
        $oldimg_id = $_img["id"];
    }
    //if(!empty($_wechat_qr) && $_wechat_qr['error']==0){
    //    $_img = IOTOOLS::addImg($_wechat_qr,'wechat_qr',$oldimg_id);
    //    $wechat_qr = $_img["src"];
    //}  //根据微信号通过api生成二维码
    if ( $sites_type=='wechat' && empty($wechat_id)) {
        io_error('{"status":4,"msg":"'.__('必须添加微信号。','i_theme').'"}');
    }

    $down_list = array();
    if(!empty($sites_down)){ 
            $down_list['down_btn_name'] = '网盘下载';
            $down_list['down_btn_url'] = $sites_down;
            $down_list['down_btn_tqm'] = $sites_password;
            $down_list['down_btn_info'] = '';
    }
    if(!empty($keywords) && !$tg_option['is_publish']) {
        $content = '<span style="color:red">&lt;删除&gt;</span><h1>剪切下方关键字到标签：</h1>'.PHP_EOL. $keywords.PHP_EOL.'<h1>正文：</h1><span style="color:red">&lt;/删除&gt;</span>'.PHP_EOL . $content;
    }
    $tougao = array(
        'comment_status'   => 'open',
        'ping_status'      => 'closed',
        'post_author'      => get_current_user_id(),//用于投稿的用户ID
        'post_title'       => $title,
        'post_content'     => $content,
        'post_status'      => $post_status,
        'post_type'        => 'sites',
        //'tax_input'        => array( 'favorites' => array($category) ) //游客不可用
    );
    //if(!empty($keywords) && $tg_option['is_publish']){
    //    $tougao['tags_input'] = preg_split("/,|，|\s|\n/", $keywords);//设置文章tag
    //} 
    // 将文章插入数据库
    $in_id = wp_insert_post( $tougao );
    if ($in_id != 0){
        global $wpdb;
        add_post_meta($in_id, '_sites_type', $sites_type);
        add_post_meta($in_id, '_sites_sescribe', $sites_sescribe);
        add_post_meta($in_id, '_sites_link', $sites_link);
        add_post_meta($in_id, '_down_version', $down_version);
        add_post_meta($in_id, '_down_formal', $down_formal);
        //add_post_meta($in_id, '_sites_down', $sites_down);
        add_post_meta($in_id, '_down_preview', $down_preview);
        //add_post_meta($in_id, '_sites_password', $sites_password);
        add_post_meta($in_id, '_down_url_list', array($down_list));//----
        add_post_meta($in_id, '_dec_password', $down_decompression);
        add_post_meta($in_id, '_sites_order', '0');
        if( !empty($sites_ico))
            add_post_meta($in_id, '_thumbnail', $sites_ico); 
        if( !empty($wechat_id))
            add_post_meta($in_id, '_wechat_id', $wechat_id); 
        wp_set_post_terms( $in_id, array($category), 'favorites'); //设置文章分类
        if(!empty($keywords) && $tg_option['is_publish']) wp_set_post_terms( $in_id, preg_split("/,|，|\s|\n/", $keywords), 'sitetag'); //设置文章tag
        setcookie("tougao", time(), time()+$delay+10, '/', '', false);// 如果是直接发布的
        if ($post_status != 'publish') {
            do_action('io_contribute_to_publish', get_post($in_id));
        }
        io_error('{"status":1,"msg":"'.__('投稿成功！','i_theme').'"}');
    }else{
        io_error('{"status":4,"msg":"'.__('投稿失败！','i_theme').'"}');
    }
}
add_action('wp_ajax_nopriv_io_app_submit', 'io_ajax_new_app');  
add_action('wp_ajax_io_app_submit', 'io_ajax_new_app');

function io_ajax_get_app_down_btn(){
    $post_id   = esc_sql($_REQUEST['post_id']);
    $index     = (int)esc_sql($_REQUEST['id']);
    $down_list = io_get_app_down_by_index($post_id);
    $app_name  = get_post_meta($post_id, '_app_name', true)?:get_the_title($post_id); 
    $down      = $down_list[$index];
    $html      = io_get_modal_header_simple('', 'icon-down', $app_name . ' - ' . $down['app_version']);
    $html      .= '<div class="p-4">
    <div class="row">
        <div class="col-6 col-md-7">'.__('描述','i_theme').'</div>
        <div class="col-2 col-md-2" style="white-space: nowrap;">'.__('提取码','i_theme').'</div>
        <div class="col-4 col-md-3 text-right">'.__('下载','i_theme').'</div>
    </div>
    <div class="col-12 line-thead my-2" style="height:1px;background: rgba(136, 136, 136, 0.4);"></div>';
    $list = '';
    if (!empty($down['down_url'])) {
        $i = 0;
        foreach ($down['down_url'] as $d) {
            $url = $d['down_btn_url'] == "" ? "javascript:" : $d['down_btn_url'];
            if (io_get_option('is_go', false) && !io_get_option('is_app_down_nogo', false)) {
                $url = go_to($url);
            }
            $target = $d['down_btn_url'] == "" ? '' : ' target="_blank"';
            $list .= '<div class="row">';
            $list .= '<div class="col-6 col-md-7">' . ($d['down_btn_info'] ?: __('无', 'i_theme')) . '</div>';
            $list .= '<div class="col-2 col-md-2" style="white-space: nowrap;">' . ($d['down_btn_tqm'] ?: __('无', 'i_theme')) . '</div>';
            $list .= '<div class="col-4 col-md-3 text-right"><a class="btn btn-danger custom_btn-d py-0 px-1 mx-auto down_count copy-data text-sm" href="' . $url . '" ' . $target . ' data-clipboard-text="' . $d['down_btn_tqm'] . '" data-id="' . $post_id . '" data-action="down_count" data-mmid="down-mm-' . $i . '">' . $d['down_btn_name'] . '</a></div>';
            $list .= '</div>';
            $list .= '<div class="col-12 line-thead my-2" style="height:1px;background: rgba(136, 136, 136, 0.2);"></div>';
            $i++;
        }
    }else{
        $list = '<div class="tips-box btn-block">'.__('没有内容','i_theme').'</div>';
    }
    $html .= '<div class="down_btn_list mb-4">';
    $html .= $list;
    $html .= '</div>';
    $html .= show_ad('ad_res_down_popup', false, '<div class="apd apd-footer d-none d-md-block mb-4">', '</div>', false);
    $html .= '<div class="io-alert border-2w text-sm" role="alert"><i class="iconfont icon-statement mr-2" ></i><strong>' . __('声明：', 'i_theme') . '</strong>' . io_get_option('down_statement', '') . '</div>';
    $html .= '</div>';
    exit($html);
}
add_action('wp_ajax_nopriv_get_app_down_btn', 'io_ajax_get_app_down_btn');  
add_action('wp_ajax_get_app_down_btn', 'io_ajax_get_app_down_btn');