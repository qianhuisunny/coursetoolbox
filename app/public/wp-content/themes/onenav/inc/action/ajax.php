<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:55:58
 * @LastEditors: iowen
 * @LastEditTime: 2024-02-20 23:50:52
 * @FilePath: /onenav/inc/action/ajax.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$functions = array(
    'ajax-post',
    'ajax-sites',
    'ajax-app',
    'ajax-user',
    'ajax-admin'
);

foreach ($functions as $function) {
    $path = 'inc/action/' . $function . '.php';
    require get_theme_file_path($path);
}
/**
 * 获取图片验证码
 * @return void
 */
function get_img_captcha_callback(){
    require get_theme_file_path('/inc/classes/CaptchaBuilder.php');
    
    $code_id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : 'code';
    ob_start();
    $captch = new CaptchaBuilder(); 
    $captch->create();
    $captch->output();
    $data = ob_get_contents();
    ob_end_clean();
    @session_start();
    $_SESSION['captcha_img_code_' . $code_id] = strtolower($captch->getText());
    $_SESSION['captcha_img_time_' . $code_id] = time();
    $imageString = base64_encode($data);
    io_error(array('img' => 'data:image/jpeg;base64,' . $imageString));
}
add_action('wp_ajax_nopriv_get_img_captcha', 'get_img_captcha_callback'); 
add_action('wp_ajax_get_img_captcha', 'get_img_captcha_callback');

function get_slider_captcha_callback(){
    ob_clean();
    @session_start();
    $randstr                             = !empty($_REQUEST['randstr']) ? $_REQUEST['randstr'] : '';
    $_a                                  = (int) substr($randstr, 0, 2);
    $_b                                  = (int) substr($randstr, -2);
    $_x                                  = (int) substr($randstr, $_a + 2, $_b - 2);
    $rand_str                            = md5(time());
    $_SESSION['captcha_slider_x']        = $_x;
    $_SESSION['captcha_slider_rand_str'] = $rand_str;

    $index   = rand(11, 60);
    $token   = IOTOOLS::getKm($index);
    $token .= $_x;
    $index_2 = rand(11, 60);
    $token .= IOTOOLS::getKm($index_2);
    $token .= $index_2;

    $data = array(
        'token'    => $token,
        'rand_str' => $rand_str,
        'check'    => md5(date("Y-m-d H:i:s", time())),
        'time'     => time()
    );
    io_error($data);
}
add_action('wp_ajax_nopriv_get_slider_captcha', 'get_slider_captcha_callback'); 
add_action('wp_ajax_get_slider_captcha', 'get_slider_captcha_callback');

//提交评论
add_action('wp_ajax_nopriv_ajax_comment', 'fa_ajax_comment_callback');
add_action('wp_ajax_ajax_comment', 'fa_ajax_comment_callback');
if(!function_exists('fa_ajax_comment_callback')){
function fa_ajax_comment_callback(){
    if (!wp_verify_nonce($_POST['_wpnonce'],"comment_ticket")){
        io_error('{"status":4,"msg":"'.__('对不起!您没有通过安全检查','i_theme').'"}',true);
    }
	io_ajax_is_robots();
    $comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
    if ( is_wp_error( $comment ) ) {
        $data = $comment->get_error_data();
        if ( ! empty( $data ) ) {
            io_error('{"status":4,"msg":"'.$comment->get_error_message().'"}', true);
        } else {
            exit;
        }
    }
    $user = wp_get_current_user();
    do_action('set_comment_cookies', $comment, $user);
    $GLOBALS['comment'] = $comment; //根据你的评论结构自行修改，如使用默认主题则无需修改
    $comment_id = $comment->comment_ID;
    $html = '<li '. comment_class('comment', $comment, null, false).' id="li-comment-'. $comment_id .'" style="position: relative;">
        <div id="comment-'. $comment_id .'" class="comment_body d-flex flex-fill">    
            <div class="profile mr-2 mr-md-3"> 
                '.get_avatar( $comment, 96, '', get_comment_author() ).'
            </div>                    
            <section class="comment-text d-flex flex-fill flex-column">
                <div class="comment-info d-flex align-items-center mb-1">
                    <div class="comment-author text-sm w-100">'. get_comment_author_link(). is_master( $comment->comment_author_email ) . site_rank( $comment->comment_author_email, $comment->user_id ) .'
                    </div>                                        
                </div>
                <div class="comment-content d-inline-block text-sm">
                    '. get_comment_text($comment) .
                    ($comment->comment_approved == '0'?'<span class="cl-approved">('.__('您的评论需要审核后才能显示！','i_theme').')</span><br />':'').'
                </div>
                <div class="d-flex flex-fill text-xs text-muted pt-2">
                    <div class="comment-meta">
                        <div class="info"><time itemprop="datePublished" datetime="'. get_comment_date( 'c' ).'">'. timeago(get_comment_date('Y-m-d G:i:s')).'</time></div>
                    </div>
                </div>
            </section>
        </div>
        <div class="new-comment" style="background: #4bbbff;position: absolute;top: -1rem;bottom: 1rem;left: -1.25rem;right: -1.25rem;opacity: .2;"></div>
        </li>  ';  
    io_error(array('status'=>1,'msg'=>'','html'=>$html));
}
}

//提交友情链接 
add_action('wp_ajax_nopriv_io_submit_link', 'io_submit_link');
add_action('wp_ajax_io_submit_link', 'io_submit_link');
function io_submit_link()
{
    if (isset($_COOKIE['io_links_submit_time'])) {
        io_error('{"status":3,"msg":"操作过于频繁，请稍候再试"}'); 
    }
    if (empty($_POST['link_name'])) { 
        io_error('{"status":3,"msg":"请填写链接名称"}'); 
    }
    if (empty($_POST['link_url'])) {
        io_error('{"status":3,"msg":"请填写链接地址"}'); 
    }
	io_ajax_is_robots();
    $linkdata = array(
        'link_name'   => esc_attr($_POST['link_name']),
        'link_url'    => esc_url($_POST['link_url']),
        'link_description' => !empty($_POST['link_description']) ? esc_attr($_POST['link_description']) : '',
        'link_image' => !empty($_POST['link_image']) ? esc_attr($_POST['link_image']) : '',
        'link_target' => "_blank",
        'link_visible' => 'N'// 表示链接默认不可见
    );
    $links_id = wp_insert_link($linkdata);
    if (is_wp_error($links_id)) {
        io_error('{"status":4,"msg":"'.$links_id->get_error_message().'"}');
    }
    //设置浏览器缓存限制提交的间隔时间
    $expire = time() + 30;
    setcookie('io_links_submit_time', time(), $expire, '/', '', false);

    /**添加执行挂钩 */
    do_action('io_ajax_add_links_submit_success', $_POST);
    io_error('{"status":1,"msg":"提交成功，等待管理员处理"}'); 
    exit();
}


add_action('wp_ajax_save_links_img', 'io_save_links_img_callback');
function io_save_links_img_callback(){ 
    $img_link = isset( $_POST['img_link'] ) ? trim(htmlspecialchars($_POST['img_link'])) : '';
    if(!empty($img_link)){
        $ico_source = io_get_option('ico-source',array("url_format"=>true,'ico_url'=>'https://api.iowen.cn/favicon/','ico_png'=>'.png'));
        $img = ($ico_source['ico_url'] .format_url($img_link) . $ico_source['ico_png']);
        echo json_encode(io_save_img($img,$ico_source['ico_png']?'':''));
    } else {
        io_error('{"status":0,"msg":"请填写地址！"}', true); 
    }
    exit;  
}

add_action('wp_ajax_save_letter_img', 'io_save_letter_img_callback');
function io_save_letter_img_callback(){ 
    $text = isset( $_POST['text'] ) ? trim(htmlspecialchars($_POST['text'])) : '';
    if(!empty($text)){ 
        $return_data =  array(
            'status' => true,
            'url'    => io_letter_ico($text),
            'msg'    => '获取成功！',
        );
        echo json_encode($return_data);
    } else {
        io_error('{"status":0,"msg":"请填写地址！"}', true); 
    }
    exit;  
}

// 查重
add_action('wp_ajax_nopriv_check_duplicate', 'io_check_duplicate');  
add_action('wp_ajax_check_duplicate', 'io_check_duplicate');
function io_check_duplicate(){ 
    $sites_link = isset( $_POST['sites_link'] ) ? trim(htmlspecialchars($_POST['sites_link'])) : '';
    if(!empty($sites_link)){
        if(link_exists($sites_link)) {
            echo __('存在相同的链接地址，请不要重复提交哦！','i_theme') ;
        }
        else{
            echo __('没有重复地址，可以提交！','i_theme') ;
        }  
    } else {
        echo __('请填写地址！','i_theme') ;
    }
    exit;  
}

//点赞
add_action('wp_ajax_nopriv_post_like', 'io_like_ajax_handler');  
add_action('wp_ajax_post_like', 'io_like_ajax_handler');
function io_like_ajax_handler(){
    global $wpdb, $post;  
    if($post_id = sanitize_key($_POST["post_id"])){
        
        if($post_id <= 0)
            return;

        $like_count = get_post_meta($post_id, '_like_count', true);  

        $expire = time() + 99999999;  
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; // make cookies work with localhost  

        setcookie('liked_' . $post_id, $post_id, $expire, '/', $domain, false);  
        if (!$like_count || !is_numeric($like_count)){
            update_post_meta($post_id, '_like_count', 1);
        }else{
            update_post_meta($post_id, '_like_count', ($like_count + 1));
        }

        echo get_post_meta($post_id, '_like_count', true); 
    }
    exit;  
}
//设置链接失败
add_action('wp_ajax_nopriv_link_failed', 'io_link_failed');  
add_action('wp_ajax_link_failed', 'io_link_failed');
function io_link_failed(){  
    global $wpdb, $post;  
    if($post_id = (int) sanitize_key( $_POST["post_id"]) ){
        $is_inv = $_POST["is_inv"];
        if( $post_id > 0 ){
            $invalid_count = get_post_meta($post_id, 'invalid', true);  
            if( $is_inv=="false" ){
                if ( !$invalid_count || !is_numeric($invalid_count) ){
                    update_post_meta($post_id, 'invalid', 1);
                }else{
                    update_post_meta($post_id, 'invalid', ($invalid_count + 1));
                }
            } else {
                if ( ($invalid_count || is_numeric($invalid_count)) && $invalid_count > 0){ 
                    update_post_meta($post_id, 'invalid', ($invalid_count - 1));
                }
            }
            echo __("反馈成功",'i_theme').$is_inv; 
        }
    }
    exit;  
}

// 增加文章浏览统计
add_action( 'wp_ajax_io_postviews', 'io_n_increment_views' );
add_action( 'wp_ajax_nopriv_io_postviews', 'io_n_increment_views' );
function io_n_increment_views() {
    if( empty( $_GET['postviews_id'] ) )
        return;

    $post_id =  (int) sanitize_key( $_GET['postviews_id'] );
    if( $post_id > 0 && is_views_execution(io_get_option( 'views_options',array() )) ) {
        $views_count = get_post_meta($post_id, 'views', true);  
        if (!$views_count || !is_numeric($views_count)){
            $views_count = 0;
        }
        update_post_meta($post_id, 'views', ($views_count + 1));
        if (io_get_option('leader_board',false)&&!is_page()) io_add_post_view($post_id,get_post_type( $post_id ),wp_is_mobile());
        echo $views_count+1;
        exit();
    }
}


// 获取文章浏览数据
add_action( 'wp_ajax_get_post_ranking_data', 'io_get_post_ranking_data' );
add_action( 'wp_ajax_nopriv_get_post_ranking_data', 'io_get_post_ranking_data' );
function io_get_post_ranking_data() {
    //if (!wp_verify_nonce($_POST['data']['nonce'],"post_ranking_data")){
    //    error('{"status":4,"msg":"'.__('对不起!您没有通过安全检查','i_theme').'"}');
    //}
    if( empty( $_POST['data']['post_id'] ) )
        io_error('{"status":0,"msg":"'.__('错误！','i_theme').'"}');
    $post_id =  (int) sanitize_key( $_POST['data']['post_id'] );
    $type = trim(htmlspecialchars($_POST['data']['type']));
    if( $post_id > 0 ) {
        global $ioview;
        $views_data = $ioview->getPostViewsData($post_id);
        if($views_data){
            $post_data=[];
            foreach($views_data as $v){
                $post_data[$v->time] =  get_object_vars($v );
            } 
            $desktop    = [];
            $mobile     = [];
            $download   = [];
            $count      = [];
            $x_axis     = [];
            if($type == "down")
            $series     = ['pc', __('移动端','i_theme'), __('合计','i_theme'),__('下载量','i_theme')];
            else
            $series     = ['pc', __('移动端','i_theme'), __('合计','i_theme')];
            $day = (int)io_get_option('how_long',30)-1;
            if($day>29)$day=29;
            for($i=$day;$i>=0;$i--){
                $time = date("Y-m-d", strtotime('-'. $i . 'day',current_time( 'timestamp' )));
                $x_axis[] = $time;
                if(array_key_exists($time,$post_data)){
                    $desktop[]  = (int)$post_data[$time]['desktop'];
                    $mobile[]   = (int)$post_data[$time]['mobile'];
                    $download[] = (int)$post_data[$time]['download'];
                    $count[]    = (int)$post_data[$time]['count'];
                }else{
                    $desktop[]  = 0;
                    $mobile[]   = 0;
                    $download[] = 0;
                    $count[]    = 0;
                }
            }
            $_data = array(
                'series'    => $series,
                'x_axis'    => $x_axis,
                'desktop'   => $desktop,
                'mobile'    => $mobile,
                'download'  => $download,
                'count'     => $count,
            );
            unset($post_data,$series, $x_axis,$desktop,$mobile,$download,$count);
            io_error(json_encode(array(
                'status' => 1,
                'msg'    => '成功',
                'type'   => $type,
                'data'   => $_data,
            )));
        }
    }
    io_error('{"status":0,"msg":"错误！"}');
}

// 解除绑定
add_action( 'wp_ajax_unbound_open_id', 'unbound_open_id' );
function unbound_open_id() {
    $user = wp_get_current_user();
    if(!$user->ID) {
        io_error('{"status":2,"msg":"'.__('请先登录!','i_theme').'"}'); 
    }  
    if (empty($_POST['user_id']) || empty($_POST['type'])) {
        io_error('{"status":3,"msg":"'.__('参数错误!','i_theme').'"}');  
    }
    if ($user->ID != $_POST['user_id']) {
        io_error('{"status":3,"msg":"'.__('权限不足!','i_theme').'"}');   
    }
    if(!$user->user_email){
        io_error('{"status":4,"msg":"'.__('请先绑定邮箱！','i_theme').'"}');   
    }

    $type = esc_sql($_POST['type']);
    if ('weixin_gzh' == $type) {
        $type = 'wechat_'.io_get_option('open_weixin_gzh_key', 'gzh', 'type');
    }
    if('weibo'==$type){
        $type = 'sina';
    }
    delete_user_meta($_POST['user_id'],  $type . '_openid');  
    delete_user_meta($_POST['user_id'],  $type . '_getUserInfo'); 
    delete_user_meta($_POST['user_id'],  $type . '_avatar'); 
    if(get_user_meta( $_POST['user_id'], 'avatar_type', true )==$type){
        update_user_meta( $_POST['user_id'], 'avatar_type', 'letter');
    }
    io_error('{"status":1,"msg":"'.__('已解除绑定','i_theme').'"}'); 

    exit();
}
// 增加国家数据，临时方法
add_action( 'wp_ajax_io_set_country', 'io_set_country' );
add_action( 'wp_ajax_nopriv_io_set_country', 'io_set_country' );
function io_set_country() {
    if( empty( $_POST['id'] ) )
        return;
    $country = $_POST['country'];
    $post_id =  (int) sanitize_key( $_POST['id'] );
    if( $post_id > 0 ) { 
        update_post_meta($post_id, '_sites_country', $country); 
        exit();
    }
}
//显示模式切换
add_action('wp_ajax_nopriv_switch_dark_mode', 'io_switch_dark_mode');  
add_action('wp_ajax_switch_dark_mode', 'io_switch_dark_mode');
function io_switch_dark_mode(){    
    $mode = $_POST["mode_toggle"];
    $expire = time() + 99999999;  
    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; // make cookies work with localhost  
    setcookie('io_night_mode', $mode, $expire, '/', $domain, false);  
    exit; 
}

// 增加app下载量
add_action( 'wp_ajax_down_count', 'io_add_down_count' );
add_action( 'wp_ajax_nopriv_down_count', 'io_add_down_count' );
function io_add_down_count() {
    if( empty( $_POST['id'] ) )
        return;

    $post_id =  (int) sanitize_key( $_POST['id'] );
    if( $post_id > 0 ) {
        if (io_get_option('leader_board',false)&&!is_page()) io_add_post_view($post_id,get_post_type( $post_id ),0,1,'down');
        $down_count = get_post_meta($post_id, '_down_count', true);  
        if (!$down_count || !is_numeric($down_count)){
            $down_count = 0;
        }
        update_post_meta($post_id, '_down_count', ($down_count + 1));
        echo $down_count+1;
        exit();
    }
}

// 加载热门内容
add_action( 'wp_ajax_load_hot_post' , 'load_hot_post_callback' );
add_action( 'wp_ajax_nopriv_load_hot_post' , 'load_hot_post_callback' );
function load_hot_post_callback(){
    $data = $_REQUEST['data'];
    if(is_array($data))
        echo get_home_hot_card($data);
    exit();
}

// 加载今日热榜列表
add_action( 'wp_ajax_load_hot_list' , 'load_hot_list_callback' );
add_action( 'wp_ajax_nopriv_load_hot_list' , 'load_hot_list_callback' );
function load_hot_list_callback(){
    echo(json_encode(array(
        'state' =>1,
        'data'=>all_topnew_list()
    )));
    die();
}

// 前台内容举报&反馈
add_action( 'wp_ajax_report_site_content' , 'report_site_content_callback' );
add_action( 'wp_ajax_nopriv_report_site_content' , 'report_site_content_callback' );
function report_site_content_callback(){
    $post_id    = sanitize_key($_REQUEST['post_id']); 
    $reason     = sanitize_text_field($_REQUEST['reason']);
    $redirect   = sanitize_text_field($_REQUEST['redirect']);
    if($post_id=='' || $reason=='' || ($reason=='2' && $redirect=='')){
        io_error('{"status":3,"msg":"数据错误！"}'); 
    }
    if('666'===$reason){
        update_post_meta($post_id, "_revive_url_m", $reason); //地址复活了
    } else {
        if ($count = get_post_meta($post_id, 'report', true)) {
            $msg = get_post_meta($post_id, '_invalid_reason', true);
        } else {
            $count = 0;
            $msg = array();
        }
        $msg[] = $reason;
        update_post_meta($post_id, "report", $count + 1); //反馈次数
        update_post_meta($post_id, "_invalid_reason", array_unique($msg)); //反馈理由
        if ($reason == "1")
            update_post_meta($post_id, "_dead_link", 1); //确定是死链接
        if ($redirect)
            update_post_meta($post_id, "_redirect_url", $redirect); //重定向地址
    }
    io_error(array(
        'status' =>1,
        'msg'    =>__("反馈成功",'i_theme')
    ));
}

// 首页TAB模式ajax加载内容     
add_action( 'wp_ajax_load_home_tab' , 'load_home_tab_post' );
add_action( 'wp_ajax_nopriv_load_home_tab' , 'load_home_tab_post' );
function load_home_tab_post(){
    if(!isset($_REQUEST['id']) || !isset($_REQUEST['taxonomy']) || !isset($_REQUEST['post_id']) ){
        exit();
    }
    $meta_id   = sanitize_key($_REQUEST['id']); 
    $taxonomy  = sanitize_text_field($_REQUEST['taxonomy']);
    $post_id   = sanitize_key($_REQUEST['post_id']); 

    $quantity = get_card_num(); 
    global $post, $queried_object_id, $is_sidebar;

    $is_sidebar        = isset($_REQUEST['sidebar']) ? intval($_REQUEST['sidebar']) : 0; 
    $queried_object_id = $post_id;
    $site_n            = $quantity[get_type_name($taxonomy)];
    show_card($site_n,$meta_id,$taxonomy,'ajax-url');

    die();
}
// 网址管理ajax加载站点内容     
add_action( 'wp_ajax_load_sites_manager' , 'load_sites_manager_post' );
add_action( 'wp_ajax_nopriv_load_sites_manager' , 'load_sites_manager_post' );
function load_sites_manager_post(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"请登录！"}');
    $term_id   = sanitize_key($_POST['term_id']); 

    global $post;
    $args = array(   
        'post_type'           => 'sites',
        //'ignore_sticky_posts' => 1,              
        'posts_per_page'      => -1,    
        'post_status'         => array( 'publish' ),
        'orderby'             => 'menu_order',
        'order'               => 'ASC',
        'tax_query'           => array(
            array(
                'taxonomy' => 'favorites',       
                'field'    => 'id',            
                'terms'    => $term_id,    
            )
        ),
    );
    $myposts = new WP_Query( $args );
    if ($myposts->have_posts()): while ($myposts->have_posts()): $myposts->the_post(); 
    if(get_post_meta($post->ID, '_sites_type', true)=='sites'):
        $isAdd = in_array(get_current_user_id(), array_unique(get_post_meta($post->ID, 'io_post_add_custom_users',false)));
    ?>
    <div id="url-<?php echo $post->ID ?>" class="col-12 col-md-4 col-lg-3 mb-2 sites-li admin-li" data-sites_id="<?php echo $post->ID ?>" title="<?php the_title() ?>">
        <div class="rounded sites-card position-relative">
            <div class="d-flex align-items-center">
                <div class=" rounded-circle mr-2 d-flex align-items-center justify-content-center">
                    <i class="iconfont icon-globe"></i>
                </div>
                <div class="flex-fill overflow-hidden">
                    <span class="sites-name overflowClip_1"><?php the_title() ?></span>
                    <span class="sites-name overflowClip_1 text-xs text-muted"><?php echo get_post_meta($post->ID, '_sites_sescribe', true)?:"..." ?></span>
                </div>
            </div>
            <div class="sites-setting">
                <a href="javascript:;" id="admin-sites-id-<?php echo $post->ID ?>" class="text-center add-admin-site <?php echo $isAdd?'add':'' ?>" data-action="add_custom_url" data-_wpnonce="<?=wp_create_nonce('add_custom_site_form') ?>" data-post_id="<?=$post->ID ?>" data-url_name="<?php the_title() ?>" data-url="<?php echo get_post_meta($post->ID, '_sites_link', true) ?>" data-url_summary="<?php echo get_post_meta($post->ID, '_sites_sescribe', true)?:"" ?>" data-url_ico="<?php echo get_post_meta($post->ID, '_thumbnail', true)?:"" ?>" style="" title="<?php echo $isAdd?__('已添加','i_theme'):__('添加','i_theme') ?>"><i class="iconfont <?php echo $isAdd?'text-danger icon-subtract':'icon-add' ?>"></i></a>
            </div>
        </div>
    </div>
    <?php
    endif; endwhile; endif;
    wp_reset_postdata();
    die();
}
// 加载随机网址
add_action( 'wp_ajax_load_random_sites' , 'load_random_sites_callback' );
add_action( 'wp_ajax_nopriv_load_random_sites' , 'load_random_sites_callback' );
function load_random_sites_callback(){
    $instance = $_POST['data'];
    if (!is_array($instance) || count($instance) < 1)
        io_error('{"status":3,"msg":"数据错误！"}'); 

    $i          = 0;
    $html       = '';
    $post_num   = $instance['number'];
    $post_type  = $instance['type'];

    $taxonomy_tag   = 'sitetag';
    $taxonomy_cat   = 'favorites';
    switch ($post_type) {
        case 'post':
            $taxonomy_tag   = 'post_tag';
            $taxonomy_cat   = 'category';
            $before_div     = '<div class="list-grid list-rounded my-n2">';
            $after_div      = '</div>';
            break;
        case 'app':
            $taxonomy_tag   = 'apptag';
            $taxonomy_cat   = 'apps';
            $before_div     = '<div class="row row-sm">';
            $after_div      = '</div>';
            break;
        case 'book':
            $taxonomy_tag   = 'booktag';
            $taxonomy_cat   = 'books';
            $before_div     = '<div class="row row-sm">';
            $after_div      = '</div>';
            break;
        
        case 'sites':
        default:
            $taxonomy_tag   = 'sitetag';
            $taxonomy_cat   = 'favorites';
            $before_div     = '<div class="row row-sm my-n1">';
            $after_div      = '</div>';
            break;
    }
    
    if(isset($instance['post_id']) && $instance['post_id']!='' && $instance['post_type']==$post_type){
        $post_id        = $instance['post_id'];
        $exclude        = array ($instance['post_id']);
        $posttags       = get_the_terms($post_id, $taxonomy_tag);  
        if($posttags){
            $tags = []; foreach ( $posttags as $tag ) $tags[]= $tag->term_id ;
            $args = array(
                'post_type'         => $post_type, 
                'post_status'       => 'publish',
                'posts_per_page'    => $post_num, 
                'orderby'           => 'rand', 
                'tax_query'         => array(
                    array(
                        'taxonomy'  => $taxonomy_tag, 
                        'field'     => 'id',
                        'terms'     => $tags
                    )
                ),
                'post__not_in'      => $exclude, 
            );
            $myposts = new WP_Query( $args ); 
            if($myposts->have_posts()){
                switch ($post_type) {
                    case 'post':
                        $data = load_widgets_min_post_html($myposts,$instance,$i);
                        break;
                    case 'app':
                        $data = load_widgets_min_app_html($myposts,$instance,$i);
                        break;
                    case 'book':
                        $data = load_widgets_min_book_html($myposts,$instance,$i);
                        break;
                    case 'sites':
                    default:
                        $data = load_widgets_min_sites_html($myposts,$instance,$i);
                        break;
                }
                $html .= $data['html'];
                $i    = $data['index'];
            }
            wp_reset_postdata();
        }
        if($i < $post_num){
            $custom_taxterms = get_the_terms( $post_id,$taxonomy_cat);
            if(is_array($custom_taxterms)){
                $terms = []; 
                foreach ( $custom_taxterms as $term ) $terms[]= $term->term_id ;
                $args = array(
                    'post_type'         => $post_type, 
                    'post_status'       => 'publish',
                    'posts_per_page'    => $post_num-$i, 
                    'orderby'           => 'rand', 
                    'tax_query'         => array(
                        array(
                            'taxonomy'  => $taxonomy_cat, 
                            'field'     => 'id',
                            'terms'     => $terms
                        )
                    ),
                    'post__not_in'      => $exclude, 
                );
                $myposts = new WP_Query( $args ); 
                if($myposts->have_posts()){
                    switch ($post_type) {
                        case 'post':
                            $data = load_widgets_min_post_html($myposts,$instance,$i);
                            break;
                        case 'app':
                            $data = load_widgets_min_app_html($myposts,$instance,$i);
                            break;
                        case 'book':
                            $data = load_widgets_min_book_html($myposts,$instance,$i);
                            break;
                        case 'sites':
                        default:
                            $data = load_widgets_min_sites_html($myposts,$instance,$i);
                            break;
                    }
                    $html .= $data['html'];
                    $i    = $data['index'];
                }
                wp_reset_postdata();
            }
        }
    }
    if($i < $post_num){
        $p_args = array(
            'post_type'           => $post_type, 
            'post_status'         => array( 'publish', 'private' ),//'publish',
            'perm'                => 'readable',
            'ignore_sticky_posts' => 1,              
            'posts_per_page'      => $post_num-$i,    
            'orderby'             => 'rand',          
        ); 
        $myposts = new WP_Query( $p_args );
        switch ($post_type) {
            case 'post':
                $data = load_widgets_min_post_html($myposts,$instance,$i);
                break;
            case 'app':
                $data = load_widgets_min_app_html($myposts,$instance,$i);
                break;
            case 'book':
                $data = load_widgets_min_book_html($myposts,$instance,$i);
                break;
            case 'sites':
            default:
                $data = load_widgets_min_sites_html($myposts,$instance,$i);
                break;
        }
        $html .= $data['html'];
        wp_reset_postdata();
    }
    exit($before_div.$html.$after_div);
}
// load_home_customize_tab  
add_action( 'wp_ajax_load_home_customize_tab' , 'load_home_customize_tab' );
function load_home_customize_tab(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');

    $user_id   = isset($_POST['user_id'])?sanitize_key($_POST['user_id']):get_current_user_id(); 
    $term_id   = sanitize_key($_POST['term_id']); 

    global $iodb;
    $i_u = 0;
    $c_urls = $iodb->getUrlWhereTerm($user_id,$term_id);
    if($c_urls){ 
        $default_ico = get_theme_file_uri('/images/favicon.png');
        foreach($c_urls as $c_url){ 
            $ico = $c_url->url_ico?:(io_get_option('ico-source','https://api.iowen.cn/favicon/','ico_url') .format_url($c_url->url) . io_get_option('ico-source','.png','ico_png'));
        ?> 
        <div id="url-<?php echo $c_url->id ?>" class="url-card sortable col-6 <?php get_columns('sites',$term_id) ?> col-xxl-10a">
            <div class="url-body mini">
                <a href="<?php echo go_to($c_url->url) ?>" target="_blank" class="card new-site mb-3 site-<?php echo $c_url->id ?>" data-id="<?php echo $c_url->id ?>" data-url="<?php echo $c_url->url ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $c_url->url_name ?>" <?php echo  nofollow($c_url->url,false,true) ?>>
                    <div class="card-body" style="padding:0.4rem 0.5rem;">
                        <div class="url-content d-flex align-items-center">
                            <div class="url-img rounded-circle mr-2 d-flex align-items-center justify-content-center">
                                <?php if(io_get_option('lazyload',false)): ?>
                                <img class="lazy" src="<?php echo $default_ico; ?>" data-src="<?php echo $ico ?>" alt="<?php echo $c_url->url_name ?>">
                                <?php else: ?>
                                <img class="" src="<?php echo $ico ?>" alt="<?php echo $c_url->url_name ?>">
                                <?php endif ?>
                            </div>
                            <div class="url-info flex-fill">
                                <div class="text-sm overflowClip_1">
                                    <strong><?php echo $c_url->url_name ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <a href="javascript:;" class="text-center remove-cm-site" data-action="delete_custom_url" data-id="<?php echo $c_url->id ?>" data-name="<?php echo $c_url->url_name ?>" style="display: none"><i class="iconfont icon-close-circle"></i></a>
        </div> 
        <?php } ?>
        <div class="url-card col-6 <?php get_columns('sites',$term_id) ?> col-xxl-10a add-custom-site" data-term_id="<?php echo $term_id ?>" style="display: none">
            <a class="btn p-0 rounded mb-3" data-toggle="modal" data-target="#addSite" style="background: rgba(136, 136, 136, 0.1);width: 100%;text-align: center;border: 2px dashed rgba(136, 136, 136, 0.5);">
                <div class="text-lg"  style="padding:0.22rem 0.5rem;">
                    +
                </div>
            </a>
        </div> 
    <?php 
    }else{ ?>
        <div class="col-lg-12 customize_nothing">
            <div class="nothing mb-4"><?php _e('没有数据！点右上角编辑添加网址', 'i_theme' ); ?></div>
        </div>
        <div class="url-card col-6 <?php get_columns('sites',$term_id) ?> col-xxl-10a add-custom-site" data-term_id="<?php echo $term_id ?>" style="display: none">
            <a class="btn p-0 rounded mb-3" data-toggle="modal" data-target="#addSite" style="background: rgba(136, 136, 136, 0.1);width: 100%;text-align: center;border: 2px dashed rgba(136, 136, 136, 0.5);">
                <div class="text-lg"  style="padding:0.22rem 0.5rem;">
                    +
                </div>
            </a>
        </div> 
    <?php }
    die();
} 
add_action('wp_ajax_add_custom_url', 'add_custom_url_callback'); 
function add_custom_url_callback(){ 
    if (!wp_verify_nonce($_POST['_wpnonce'],"add_custom_site_form")){
        io_error('{"status":4,"msg":"'.__('对不起!您没有通过安全检查','i_theme').'"}');
    }
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])?sanitize_key($_POST['user_id']):get_current_user_id(); 
    $term_id        = isset($_POST['term_id'])?sanitize_key($_POST['term_id']):-1; 
    $term_name      = isset($_POST['term_name'])?trim(chack_name($_POST['term_name'])):''; 
    $url            = trim(esc_url_raw($_POST['url'])); 
    $url_name       = trim(esc_attr($_POST['url_name'])); 
    $summary        = isset($_POST['url_summary'])?trim(esc_attr($_POST['url_summary'])):'';
    $post_id        = isset($_POST['post_id'])?sanitize_key($_POST['post_id']):''; 
    $url_ico        = isset($_POST['url_ico'])?trim(htmlspecialchars($_POST['url_ico'])):'';

    if ($term_id == -1 &&  $term_name=='' )
        io_error('{"status":2,"msg":"'.__('内容错误！','i_theme').'"}'); 
    if ($url == '' ||  $url_name=='')
        io_error('{"status":4,"msg":"'.__('网址内容不能为空！','i_theme').'"}');

    global $iodb,$wpdb; 
    if($term_id == -1 &&  $term_name!=""){
        if($iodb->term_exists($user_id,$term_name))
            io_error('{"status":3,"msg":"'.__('分类名称已经存在，不能再新建！','i_theme').'"}'); 
        if($max_order = $iodb->getTermMaxOrder($user_id)){
            $order = $max_order->order+1; 
        }else{
            $order = 1;
        }
        $term_id = $iodb->addTerm($user_id,$term_name,0,$order,true);
    }
    if($term_id == -1)
        io_error('{"status":3,"msg":"'.__('内容错误！','i_theme').'"}'); 

    if($iodb->url_exists($user_id,$term_id,$url)) 
        io_error('{"status":3,"msg":"'.__('当前分类下已经存在同样的 URL 地址！','i_theme').'"}'); 

    if($iodb->urlname_exists($user_id,$term_id,$url_name)) 
        io_error('{"status":3,"msg":"'.__('当前分类下已经存在同样名称的网址！','i_theme').'"}'); 

    $order = $iodb->getUrlTermOrder($user_id,$term_id)->order+1 ;
    if($iodb->addUrl($user_id,$url,$url_name,$term_id,$order,$summary,$url_ico,$post_id)){
        $url_id = $wpdb->insert_id;
        if($post_id!=''){
            add_post_meta($post_id, 'io_post_add_custom_users', $user_id);
        }
        io_error('{"status":1,"id":'.$url_id.',"msg":"'.__('添加成功！','i_theme').'"}');
    }
    else
        io_error('{"status":4,"msg":"'.__('添加失败！','i_theme').'"}');
}

add_action('wp_ajax_add_custom_urls', 'add_custom_urls_callback'); 
function add_custom_urls_callback(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])? sanitize_key($_POST['user_id']):get_current_user_id(); 
    $term_id        = isset($_POST['term_id'])? sanitize_key($_POST['term_id']):-1; 
    $term_name      = trim(chack_name($_POST['term_name']));   

    $urlDatas = json_decode(base64_decode($_POST['urls']),true); 
    if (!is_array($urlDatas) || count($urlDatas)<1)
        io_error('{"status":2,"msg":"'.__('内容错误！','i_theme').'"}'); 
    global $iodb;
    if($term_id == -1){
        if($iodb->term_exists($user_id,$term_name))
            io_error('{"status":3,"msg":"'.__('分类名称已经存在，不能再新建！','i_theme').'"}'); 
        if($max_order = $iodb->getTermMaxOrder($user_id)){
            $order = $max_order->order+1; 
        }else{
            $order = 1;
        }
        $term_id = $iodb->addTerm($user_id,$term_name,0,$order,true);
    }
    if($term_id == -1)
        io_error('{"status":3,"msg":"'.__('内容错误！','i_theme').'"}'); 
        
	$date = date('Y-m-d H:i:s',current_time( 'timestamp' ));
    $arr_key = array('user_id','url','url_name','term_id','date');
    $data_urls=[]; 
    $url_i = 0;
    foreach($urlDatas as $urlData){ 
        $url = array( $user_id, esc_url_raw($urlData["url"]), esc_attr($urlData["name"]), $term_id, $date );
        $data_urls[] = $url;
        $url_i++;
    }
    if(count($data_urls)!=0){
        $iodb->addUrls($user_id,$data_urls,$arr_key);
        io_error('{"status":1,"msg":"'.sprintf(__('成功添加 %s 个网址。','i_theme'), $url_i).'"}');
    }else{
        io_error('{"status":4,"msg":"'.__('添加失败！','i_theme').'"}');
    }
}

//删除自定义网址
add_action('wp_ajax_delete_custom_url', 'delete_custom_url_callback'); 
function delete_custom_url_callback(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])? sanitize_key($_POST['user_id']):get_current_user_id(); 
    $id             = isset($_POST['id'])? sanitize_key($_POST['id']):-1; 
    $name           = isset($_POST['name'])? trim(esc_attr($_POST['name'])):'';
    if($id<=0){
        io_error('{"status":2,"msg":"'.__('数据错误！','i_theme').'"}');
    }
    global $iodb;  
    $res = $iodb->getUrlWhereID($user_id, $id);
    if($res && $res->post_id>0){
        delete_post_meta($res->post_id, 'io_post_add_custom_users', $user_id);
    }
    if($iodb->deleteUrl($user_id, $id))
        io_error('{"status":1,"msg":"'.__('删除成功！','i_theme').'"}');
    else
        io_error('{"status":4,"msg":"'.__('操作失败，稍后再试！','i_theme').'"}');
}

//搜索自定义网址
add_action('wp_ajax_nopriv_search_custom_url', 'search_custom_url_callback');  
add_action('wp_ajax_search_custom_url', 'search_custom_url_callback'); 
function search_custom_url_callback(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])? sanitize_key($_POST['user_id']):get_current_user_id(); 
    $key_word       = isset($_POST['key_word'])? trim(esc_attr($_POST['key_word'])):'';
    if($key_word==''){
        io_error('{"status":2,"msg":"'.__('数据错误！','i_theme').'"}');
    }
    global $iodb;  
    $res = $iodb->getUrlByKeyWord($user_id, $key_word,0,10);
    if($res){
        io_error(($res));
    }
    else
        io_error('{"status":4,"msg":"'.__('操作失败，稍后再试！','i_theme').'"}');
}
//edit_custom_url 
add_action('wp_ajax_edit_custom_url', 'edit_custom_url_callback'); 
function edit_custom_url_callback(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])? sanitize_key($_POST['user_id']):get_current_user_id(); 
    $id             = isset($_POST['url_id'])? sanitize_key($_POST['url_id']):-1; 
    $term_id        = isset($_POST['term_id'])? sanitize_key($_POST['term_id']):-1; 
    $name           = isset($_POST['url_name'])? trim(esc_attr($_POST['url_name'])):'';
    $summary        = isset($_POST['url_summary'])? trim(esc_attr($_POST['url_summary'])):'';
    $url            = isset($_POST['url'])? trim(esc_url_raw($_POST['url'])):'';
    if($name == '' || $url == '' || $id <0 || $term_id <0){
        io_error('{"status":2,"msg":"'.__('数据错误！','i_theme').'"}');
    }
    global $iodb; 
    $results = $iodb->getUrlWhereID($user_id,$id);
    if(!$results){
        io_error('{"status":3,"msg":"'.__('数据错误！','i_theme').'"}');
    }else if( $results->url_name == $name && $results->url == $url && $results->summary == $summary ){
        io_error('{"status":4,"msg":"'.__('网址没有变化！','i_theme').'"}');
    }
    
    if($results->url != $url && $iodb->url_exists($user_id,$term_id,$url)) 
        io_error('{"status":3,"msg":"'.__('当前分类下已经存在同样的 URL 地址！','i_theme').'"}'); 

    if($results->url_name != $name && $iodb->urlname_exists($user_id,$term_id,$name)) 
        io_error('{"status":3,"msg":"'.__('当前分类下已经存在同样名称的网址！','i_theme').'"}'); 

    if($iodb->updateUrl($user_id, $id, $url, $name, $summary))
        io_error('{"status":1,"msg":"'.__('修改成功！','i_theme').'"}');
    else
        io_error('{"status":4,"msg":"'.__('操作失败，稍后再试！','i_theme').'"}');
}
//upload_bookmark 
add_action('wp_ajax_upload_bookmark', 'upload_bookmark_callback'); 
function upload_bookmark_callback(){
    if (!wp_verify_nonce($_POST['ubnonce'],"upload_bookmark_cb")){
        io_error('{"status":4,"msg":"'.__('对不起!您没有通过安全检查','i_theme').'"}');
    }
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])? sanitize_key($_POST['user_id']):get_current_user_id(); 
    $folders = json_decode(base64_decode($_POST['bookmark']),true);
    
	$date = date('Y-m-d H:i:s',current_time( 'timestamp' ));
    $i = 0;
    $data_urls=[]; 

    if($folders=="")
        io_error('{"status":4,"msg":"'.__('数据错误！','i_theme').'"}');
    
    global $iodb;
    if($max_order = $iodb->getTermMaxOrder($user_id)){
        $order = $max_order->order+1; 
    }else{
        $order = 1;
    }
    $arr_key = array('user_id','term_id','url','url_name','summary','date');
    $items_i = 0;
    $url_i = 0;
	foreach($folders['folders'] as $folder)
    {
        if(count($folder['items'])!=0){
            $term_id = $iodb->addTerm($user_id,trim(chack_name($folder['title'])),0,$order,true);
            foreach($folder['items'] as $link){
                $intex = iostrpos( $link['title'], array('-','_','|',',','－','–') );
                $_title = trim($link['title']);
                $summary = $_title;
                if( $intex > 0 ){
                    $_title = trim(substr($_title, 0, $intex));
                }
                $url = array( $user_id,$term_id,esc_url_raw($link['href']),esc_attr($_title),esc_attr($summary),$date );
                $data_urls[] = $url;
                $url_i++;
            }
            if($i>10){
                $i=0;
                $iodb->addUrls($user_id,$data_urls,$arr_key);
                $data_urls=[]; 
            }else{
                $i++;
            }
            $order++;
            $items_i++;
        }
    }
    if(count($data_urls)!=0)
        $iodb->addUrls($user_id,$data_urls,$arr_key);
    if($items_i > 0)
        io_error('{"status":1,"msg":"'.sprintf(__('成功添加 %s 个分类，%s 个网址。','i_theme'),$items_i,$url_i).'"}');
    else
        io_error('{"status":4,"msg":"'.__('添加失败！','i_theme').'"}');
}
//add_custom_terms 
add_action('wp_ajax_add_custom_terms', 'add_custom_terms_callback'); 
function add_custom_terms_callback(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])? sanitize_key($_POST['user_id']):get_current_user_id(); 
    $name           = isset($_POST['name'])? trim(chack_name($_POST['name'])):'';
    if($name == ''){
        io_error('{"status":2,"msg":"'.__('数据错误！','i_theme').'"}');
    }
    global $iodb; 
    if($iodb->term_exists($user_id,$name))
        io_error('{"status":3,"msg":"'.__('分类名称已经存在，不能再新建！','i_theme').'"}'); 
    if($max_order = $iodb->getTermMaxOrder($user_id)){
        $order = $max_order->order+1; 
    }else{
        $order = 1;
    }
    if($iodb->addTerm($user_id, $name,0, $order))
        io_error('{"status":1,"msg":"'.__('添加成功！','i_theme').'"}');
    else
        io_error('{"status":4,"msg":"'.__('操作失败，稍后再试！','i_theme').'"}');
}
//edit_custom_terms 
add_action('wp_ajax_edit_custom_terms', 'edit_custom_terms_callback'); 
function edit_custom_terms_callback(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])? sanitize_key($_POST['user_id']):get_current_user_id(); 
    $id             = isset($_POST['id'])? sanitize_key($_POST['id']):-1; 
    $name           = isset($_POST['name'])? trim(chack_name($_POST['name'])):'';
    $old_name       = isset($_POST['old_name'])? trim(chack_name($_POST['old_name'])):'';
    if($name == '' || $old_name == '' || $id < 0 ){
        io_error('{"status":2,"msg":"'.__('数据错误！','i_theme').'"}');
    }
    global $iodb;  
    if($iodb->term_exists($user_id,$name))
        io_error('{"status":3,"msg":"'.__('分类名称已经存在！','i_theme').'"}'); 
    if($iodb->updateTerm($user_id,$id,$name))
        io_error('{"status":1,"msg":"'.__('修改成功！','i_theme').'"}');
    else
        io_error('{"status":4,"msg":"'.__('操作失败，稍后再试！','i_theme').'"}');
}
//delete_custom_terms 
add_action('wp_ajax_delete_custom_terms', 'delete_custom_terms_callback'); 
function delete_custom_terms_callback(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])? sanitize_key($_POST['user_id']):get_current_user_id(); 
    $id             = isset($_POST['id'])? sanitize_key($_POST['id']):-1; 
    $name           = isset($_POST['name'])? trim(chack_name($_POST['name'])):'';
    $clean          = isset($_POST['clean'])? sanitize_key($_POST['clean']):0;
    if($id<=0 || $name==''){
        io_error('{"status":2,"msg":"'.__('数据错误！','i_theme').'"}');
    }
    global $iodb;
    if($urls = $iodb->getUrlWhereTerm($user_id,$id)){
        if($clean){
            $data_urls = [];
            foreach($urls as $url){
                $data_urls[] = $url->id;
            }
            $iodb->deleteUrls($user_id,$data_urls,'id');
        }else{
            io_error('{"status":4,"msg":"'.__('此分类内包含网址，无法删除！','i_theme').'"}');
        }
    }
    if($iodb->deleteTerm($user_id, $id))
        io_error('{"status":1,"msg":"'.__('删除成功！','i_theme').'"}');
    else
        io_error('{"status":4,"msg":"'.__('操作失败，稍后再试！','i_theme').'"}');
}
//move_sites_to_terms 
add_action('wp_ajax_sites_to_terms', 'sites_to_terms_callback'); 
function sites_to_terms_callback(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])? sanitize_key($_POST['user_id']):get_current_user_id(); 
    $id             = isset($_POST['sites_id'])? sanitize_key($_POST['sites_id']):-1; 
    $terms_id           = isset($_POST['terms_id'])? sanitize_key($_POST['terms_id']):-1;
    if($id<=0 || $terms_id<=0){
        io_error('{"status":2,"msg":"'.__('数据错误！','i_theme').'"}');
    }
    
    //error('{"status":2,"msg":"'.$user_id.'删除成功！'.$id.'"}');
    global $iodb,$wpdb; 
    
    $results = $iodb->getUrlWhereID($user_id,$id);
    if(!$results){
        io_error('{"status":3,"msg":"'.__('数据错误！','i_theme').'"}');
    }
    if($iodb->setUrlTerm($user_id, $id, $terms_id))
        io_error('{"status":1,"msg":"'.__('移动成功','i_theme').'"}');
    else
        io_error('{"status":4,"msg":"'.__('操作失败，稍后再试！','i_theme').'"}');
}

//更新书签分类排序
add_action('wp_ajax_update_custom_terms_order', 'update_custom_terms_order_callback'); 
function update_custom_terms_order_callback(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])? sanitize_key($_POST['user_id']):get_current_user_id();   

    if($user_id<=0){
        io_error('{"status":2,"msg":"'.__('数据错误！','i_theme').'"}');
    }
    parse_str($_POST['order'], $data);
                    
    if (!is_array($data)    ||  count($data)    <   1)
        io_error('{"status":3,"msg":"'.__('数据错误！','i_theme').'"}'); 

    global $iodb,$wpdb; 
    $origins = $iodb->getTerm($user_id);

    $result  = io_update_obj_order($wpdb->iocustomterm,$origins,$data,'termsli','order','id');
    io_error($result);  
}


//更新书签网址排序
add_action('wp_ajax_update_custom_url_order', 'update_custom_url_order_callback'); 
function update_custom_url_order_callback(){
    if(!is_user_logged_in())
        io_error('{"status":3,"msg":"'.__('请登录！','i_theme').'"}');
    $user_id        = isset($_POST['user_id'])? sanitize_key($_POST['user_id']):get_current_user_id(); 
    $term_id        = isset($_POST['term_id'])? sanitize_key($_POST['term_id']):get_current_user_id();  

    if($term_id<=0){
        io_error('{"status":2,"msg":"'.__('数据错误！','i_theme').'"}');
    }
    parse_str($_POST['order'], $data);
                    
    if (!is_array($data)    ||  count($data)    <   1)
        io_error('{"status":3,"msg":"'.__('数据错误！','i_theme').'"}'); 

    global $iodb,$wpdb; 
    $origins = $iodb->getUrlWhereTerm($user_id,$term_id);
    $result  = io_update_obj_order($wpdb->iocustomurl,$origins,$data,'url','order','id');
    io_error($result);
}

//update_sites_order
add_action('wp_ajax_update_sites_order', 'update_sites_order_callback');  
function update_sites_order_callback()
{
    set_time_limit(600);
    
    global $wpdb, $userdata;
    
    $post_type  =   filter_var ( $_POST['post_type'], FILTER_SANITIZE_STRING);
    $term_id    =   filter_var ( $_POST['term_id'], FILTER_SANITIZE_STRING);
    $paged      =   filter_var ( $_POST['paged'], FILTER_SANITIZE_NUMBER_INT);
    $nonce      =   $_POST['_nonce'];
    
    //安全验证
    if (! wp_verify_nonce( $nonce, 'sortable_nonce_' . $userdata->ID ) )
        die();
    if(!is_numeric($term_id)){
        $term_id = get_term_by( 'slug', $term_id, 'favorites')->term_id;
    }
    if(!is_numeric($term_id) || $term_id==0){
        io_error('{"status":2,"msg":"'.__('请先筛选到分类再排序！','i_theme').'"}'); 
    }
    parse_str($_POST['order'], $data);
    
    if (!is_array($data)    ||  count($data)    <   1)
        io_error('{"status":3,"msg":"'.__('数据错误！','i_theme').'"}'); 
    
    //检索所有对象的列表
    $mysql_query    =   $wpdb->prepare("SELECT ID FROM $wpdb->posts 
                                            WHERE post_type = %s and post_status IN ('publish', 'pending', 'draft', 'private', 'future', 'inherit') 
                                            and ($wpdb->posts.ID IN (SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN (%d) ) ) 
                                            ORDER BY menu_order, post_date DESC", $post_type, $term_id);
    $results        =   $wpdb->get_results($mysql_query);
    
    if (!is_array($results)    ||  count($results)    <   1)
        io_error('{"status":4,"msg":"'.__('数据错误！','i_theme').'"}'); 
    
    //创建ID列表
    $objects_ids = array();
    foreach($results    as  $result)
    {
        $objects_ids[] = (int)$result->ID;   
    }
    $obj_index = min($objects_ids); //初始序号
    global $userdata;
    $objects_per_page   =   get_user_meta($userdata->ID ,'edit_' .  $post_type  .'_per_page', TRUE);//查询设置每页显示多少
    if(empty($objects_per_page))
        $objects_per_page   =   20;//默认20
    
    $edit_start_at      =   $paged  *   $objects_per_page   -   $objects_per_page;//获取开始id
    $index              =   0;
    for($i = $edit_start_at; $i < ($edit_start_at + $objects_per_page); $i++)
    {
        if(!isset($objects_ids[$i]))
            break;
        $objects_ids[$i]    =   (int)$data['post'][$index];//替换列表id为排序id
        $index++;
    }
    
    //更新数据库中的菜单顺序
    foreach( $objects_ids as $menu_order   =>  $id ) 
    {
        $data = array(
            'menu_order' => $menu_order+$obj_index
        );
        $wpdb->update( $wpdb->posts, $data, array('ID' => $id) );
        clean_post_cache( $id ); 
    }
    io_edit_post_delete_home_cache($term_id,'favorites');
    io_error('{"status":1,"msg":"排序成功！'.$objects_per_page.'"}');                
}

/**
 * 输出提示
 * @description: 
 * @param array|string $errMsg 1 success 2 info 3 warning 4 danger
 * @param bool $err 错误
 * @param int $cache 缓存时间，分钟
 * @return null
 */
function io_error($errMsg, $err=false, $cache = 0) {
    if($err){
        header('HTTP/1.0 500 Internal Server Error');
    }
    header("Content-type:application/json;character=utf-8");
    if($cache>0){
        header("Cache-Control: public"); 
        header("Pragma: cache"); 
        $offset = 60*$cache;  
        $ExpStr = "Expires: ".gmdate("D, d M Y H:i:s", time() + $offset)." GMT"; 
        header($ExpStr); 
    }
    if(is_array($errMsg))
        echo json_encode($errMsg);
    else
        echo $errMsg;
    exit;
} 
/**
 * 输出错误
 * @param mixed $msg
 * @return void
 */
function io_tips_error($msg, $err=true){
    io_error(array(
        'status' => 3,
        'msg'    => $msg,
    ),$err);
}
/**
 * 输出成功
 * @param mixed $msg
 * @return void
 */
function io_tips_success($msg){
    io_error(array(
        'status' => 1,
        'msg'    => $msg,
    ));
}
