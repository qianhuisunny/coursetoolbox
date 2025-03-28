<?php
/*!
 * Theme Name:One Nav
 * Theme URI:https://www.iotheme.cn/
 * Author:iowen
 * Author URI:https://www.iowen.cn/
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * 禁止自动生成 768px 缩略图
 */
function shapeSpace_customize_image_sizes($sizes) {
    unset($sizes['medium_large']);
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'shapeSpace_customize_image_sizes');
/**
 * wordpress禁用图片属性srcset和sizes
 */
add_filter( 'add_image_size', function(){return 1;} );
add_filter( 'wp_calculate_image_srcset_meta', '__return_false' );
add_filter( 'big_image_size_threshold', '__return_false' );

/**
 * 禁止WordPress自动生成缩略图
 */
function ztmao_remove_image_size($sizes) {
    unset( $sizes['small'] );
    unset( $sizes['medium'] );
    unset( $sizes['large'] );
    return $sizes;
}
add_filter('image_size_names_choose', 'ztmao_remove_image_size');
/**
 * 古腾堡编辑器样式
 */
function block_editor_styles() {
    wp_enqueue_style( 'block-editor-style', get_theme_file_uri( '/css/editor-blocks.css' ), array(), IO_VERSION );
}
function initialization(){
    io_add_db_table();
} 
function io_add_db_table() {
    global $wpdb;
    //if($wpdb->has_cap('collation')) {
    //    if(!empty($wpdb->charset)) {
    //        $table_charset = "DEFAULT CHARACTER SET $wpdb->charset";
    //    }
    //    if(!empty($wpdb->collate)) {
    //        $table_charset .= " COLLATE $wpdb->collate";
    //    }
    //}
    $charset_collate = $wpdb->get_charset_collate();
    // TODO `meta` text DEFAULT NULL,
    if(!io_is_table($wpdb->iomessages)) {
        $sql = "CREATE TABLE $wpdb->iomessages (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) DEFAULT NULL COMMENT '收件人',
            `sender_id` bigint(20) DEFAULT NULL COMMENT '发件人',
            `sender` varchar(50) DEFAULT NULL COMMENT '发件人名称',
            `msg_type` varchar(20) DEFAULT NULL,
            `msg_date` datetime DEFAULT NULL,
            `msg_title` text,
            `msg_content` text,
            `meta` text DEFAULT NULL,
            `msg_read` text DEFAULT NULL,
            `msg_status` varchar(20) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY uid_index (`user_id`),
            KEY sid_index (`sender_id`)
        )$charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    if(!io_is_table($wpdb->iocustomurl)) {
        $sql = "CREATE TABLE $wpdb->iocustomurl (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) DEFAULT NULL,
            `term_id` bigint(20) NOT NULL DEFAULT 0,
            `post_id` bigint(20) DEFAULT NULL,
            `url` text DEFAULT NULL,
            `url_name` varchar(50) DEFAULT NULL,
            `url_ico` text DEFAULT NULL,
            `summary` varchar(255) DEFAULT NULL,
            `date` datetime DEFAULT NULL,
            `order` int(11) NOT NULL DEFAULT 0,
            `status` int(11) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`),
            KEY `term_id` (`term_id`),
            KEY `url_name` (`url_name`)
        )$charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    if(!io_is_table($wpdb->iocustomterm)) {
        $sql = "CREATE TABLE $wpdb->iocustomterm (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) DEFAULT NULL,
            `ico` varchar(255) DEFAULT NULL,
            `user_id` bigint(20) DEFAULT NULL,
            `parent` bigint(20) NOT NULL DEFAULT 0,
            `order` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`)
        )$charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    if(!io_is_table($wpdb->ioviews)) {
        $sql = "CREATE TABLE $wpdb->ioviews (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `time` date NOT NULL,
            `post_id` bigint(20) NOT NULL,
            `type` varchar(50) NOT NULL,
            `desktop` int(11) NOT NULL,
            `mobile` int(11) NOT NULL,
            `download` int(11) NOT NULL,
            `count` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `post_id` (`post_id`),
            KEY `type` (`type`),
            KEY `time` (`time`)
        )$charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    if(!column_in_db_table($wpdb->users,'io_id')){
        $wpdb->query("ALTER TABLE $wpdb->users ADD io_id varchar(100)");
    }
    update_option('io_add_db_tables', IO_VERSION );
}
/**
 * 全屏加载效果html
 */
function get_loading_fx(){
    if(io_get_option('loading_fx',false)) { 
        echo '<div id="loading">'.loading_type().'</div>';
    }
}
# 激活友情链接模块
# --------------------------------------------------------------------
if(io_get_option('show_friendlink',false))add_filter( 'pre_option_link_manager_enabled', '__return_true' );
require_once get_theme_file_path('/inc/post-type.php');
if(io_get_option('save_image',false)) require_once get_theme_file_path('/inc/save-image.php');
if( io_get_option('post_views',false) ) require_once get_theme_file_path('/inc/postviews/postviews.php');

# 获取CSF框架图片
# --------------------------------------------------------------------
function get_post_meta_img($post_id, $key, $single){
    $metas = get_post_meta($post_id, $key, $single);
    if(is_array($metas)){
        return $metas['url'];
    } else {
        return $metas;
    }
}
function get_search_list(){
    if(io_get_option('custom_search', false)){
        /**
         * 次级导航自定义搜索
         */
        $custom_list = get_option('io_search_list'); 
        $args = array(
            'id'   => 'home',
            'list' => $custom_list['search_list']
        );
        if( is_mininav() &&  $search_id = get_post_meta( get_queried_object_id(), '_search_id', true ) ){
            if( isset($custom_list['custom_search_list'][$search_id-1]['search_list']) ){
                $args = array(
                    'id'   => $search_id-1,
                    'list' => $custom_list['custom_search_list'][$search_id-1]['search_list']
                );
            }
        }
        return $args;
    }else{
        include( get_theme_file_path('/inc/search-list.php') ); 
        return array(
            'id'   => 'home',
            'list' => $search_list
        );
    }
}/**
 * 获取自定义搜索列表序号
 * @return array 
 */
function get_search_min_list(){
    $search_min_list  = get_option('io_search_list',false);
    $is_custom_search = io_get_option('custom_search',false);
    if(!$is_custom_search || !$search_min_list){
        return array('没有开启自定义搜索');
    }
    if(!isset($search_min_list['custom_search_list'])){
        return array('没有添加搜索项');
    }
    $list = array('没添加自定义搜索列表');
    if (isset($search_min_list['custom_search_list']) && !empty($search_min_list['custom_search_list'])) {
        $list = array('选择搜索列表');
        foreach ($search_min_list['custom_search_list'] as $v) {
            $list[] = $v['search_list_id'];
        }
    }
    return $list;
}
function get_site_type_name($type){
    switch($type){
        case "sites":
            $name = __('网址','i_theme');
            break;
        case "wechat":
            $name = __('公众号','i_theme');
            break;
        case "down":
            $name = __('资源','i_theme');
            break;
        default:
            $name = __('网址','i_theme');
            break;
    }
    return $name;
}
function get_book_type_name($type){
    switch($type){
        case "books":
            $name = __('图书','i_theme');
            break;
        case "periodical":
            $name = __('期刊','i_theme');
            break;
        case "movie":
            $name = __('电影','i_theme');
            break;
        case "tv":
            $name = __('电视剧','i_theme');
            break;
        case "video":
            $name = __('小视频','i_theme');
            break;
        default:
            $name = __('图书','i_theme');
            break;
    }
    return $name;
}
# 网站块类型（兼容1.0）
# --------------------------------------------------------------------
function before_class($post_id){
    $metas      = get_post_meta_img($post_id, '_wechat_qr', true);
    $sites_type = get_post_meta($post_id, '_sites_type', true);
    if($metas != '' || $sites_type == "wechat"){
        return 'wechat';
    } elseif($sites_type == "down") {
        return 'down';
    } else {
        return '';
    }
}
# 添加菜单
# --------------------------------------------------------------------
function wp_menu($location){
    if ( function_exists( 'wp_nav_menu' ) && has_nav_menu($location) ) {
        $nav_id = get_nav_menu_locations()[$location];
        $cache_key = 'io_menu_list_'.$nav_id;
        $nav_menu = wp_cache_get( $cache_key );
        //$nav_menu = get_transient( $cache_key );
        if ( false === $nav_menu ) { 
            ob_start();
            wp_nav_menu( array( 'container' => false, 'items_wrap' => '%3$s', 'theme_location' => $location ) );
            $nav_menu = ob_get_contents();
            ob_end_clean();
            wp_cache_set( $cache_key, $nav_menu ); 
            //set_transient( $cache_key, $nav_menu, 24 * HOUR_IN_SECONDS ); 
        }
        echo $nav_menu;
    } else {
        if (current_user_can('manage_options')) { 
            if($location == 'search_menu')
                echo '<li><a href="'.get_option('siteurl').'/wp-admin/nav-menus.php">'.__('请到[后台->外观->菜单]中添加“搜索推荐”菜单。','i_theme').'</a></li>';
            else
                echo '<li><a href="'.get_option('siteurl').'/wp-admin/nav-menus.php">'.__('请到[后台->外观->菜单]中设置菜单。','i_theme').'</a></li>';
        }
    }
}
/**
 * 添加统计数据
 * 
 * @param int $post_id
 * @param string $type
 * @param bool $is_mobile
 * @param int $count 增加的值
 * @param string $action 数据类型 view down
 * @param string $time
 * @return object
 */
function io_add_post_view($post_id,$type,$is_mobile,$count =1,$action='view',$time=''){
    global $ioview;
    if($time==''){ 
        $time = date('Y-m-d',current_time( 'timestamp' ));
    }
    if($action=='down')
        return $ioview->addViews( $post_id, $type, $time, 0, 0, 1, 0 ); 
    $desktop = 0;
    $mobile  = 0;
    if($is_mobile)
        $mobile  = $count;
    else 
        $desktop = $count;
    return $ioview->addViews( $post_id, $type, $time, $desktop, $mobile, 0, $count ); 
}

/**
 * 获取排行榜
 * 
 * @param string $time 时间 today yesterday month all
 * @param string $type 类型 sites app book post
 * @param int $count 数量，默认10
 * @param int|string|array $term 分类id
 * @param bool $is_post 返回$post
 * @return array|object|bool
 */
function io_get_post_rankings($time, $type, $count = 10, $term = '', $is_post = false){
    global $ioview;
    if(is_rankings() || !$is_post){
        $_is_go = get_post_meta( get_the_ID(), '_url_go', true ); 
    }
    switch($time){
        case "today":
            if(empty($term)){
                $sql = $ioview->getDayRankings(date('Y-m-d',current_time( 'timestamp' )),$type,$count);
            }else{
                $sql = $ioview->getDayRankingsByTerm(date('Y-m-d',current_time( 'timestamp' )),$type,$term,$count);
            }
            break;
        case "yesterday":
            if(empty($term)){
                $sql = $ioview->getDayRankings(date("Y-m-d",strtotime("-1 day",current_time( 'timestamp' ))),$type,$count);
            }else{
                $sql = $ioview->getDayRankingsByTerm(date("Y-m-d",strtotime("-1 day",current_time( 'timestamp' ))),$type,$term,$count);
            }
            break;

        case "week":
        case "last_week":
        case "month":
            if(empty($term)){
                $sql = $ioview->getRangeRankings($time,$type,$count);
            }else{
                $sql = $ioview->getRangeRankingsByTerm($time,$type,$term,$count);
            }
            break;
        case "all":
        default:
            $sql = 'all';
            break;
    }
    if( $sql!='all'){
        $m_post = $sql;
        if($m_post){
            $_post_ids  = array();
            $post_view  = array();
            foreach($m_post as $post){
                $_post_ids[]                = $post->post_id;
                $post_view[$post->post_id]  = $post->count;
            }
            $args = array(   
                'post_type'           => $type,             
                'post__in'            => $_post_ids,
                'orderby'             => 'post__in', 
                'ignore_sticky_posts' => 1,
                //'nopaging'            => true,
                'posts_per_page'      => $count, 
            );
            $myposts = new WP_Query( $args );
            $_post = $is_post ? $myposts : get_rankings_data($myposts,$type,$_is_go,$post_view);
            wp_reset_postdata();
            return $_post;
        }else{
            return false;
        }
    }else{
        $args = array(   
            'post_type'           => $type,             
            'posts_per_page'      => $count,  
            'ignore_sticky_posts' => 1,
            'meta_key'            => 'views',
            'orderby'             => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ),
        );
        $myposts = new WP_Query( $args );
        $_post = $is_post ? $myposts : get_rankings_data($myposts,$type,$_is_go);
        wp_reset_postdata();
        return $_post;
    }
}
/**
 * 返回排行榜文章元数据数组
 * @param mixed $myposts 
 * @param mixed $type 
 * @param mixed $_is_go 是否直达（主要是网址类型）
 * @param int|array $post_view 查看次数
 * @return array 
 */
function get_rankings_data($myposts,$type,$_is_go,$post_view=0){
    $index = 1;
    $_post = array();
    if($myposts->have_posts()):while ($myposts->have_posts()): $myposts->the_post(); 
        $url = get_permalink();
        $post_id = get_the_ID();
        $is_go = false;
        if($type=='sites'){
            $sites_type = get_post_meta($post_id, '_sites_type', true);
            if($sites_type == 'sites'){
                if(!io_get_option('details_page',false) || (io_get_option('details_page',false)&&$_is_go)){
                    $url   = get_post_meta($post_id, '_sites_link', true);
                    $is_go = true;
                }
            }
        }
        if($post_view==0)
            $views = get_post_meta( $post_id, 'views', true );
        else 
            $views = $post_view[$post_id];
        $_post[] = array(
            "index"     => $index,
            "id"        => $post_id,
            "title"     => get_the_title(),
            "url"       => $url,
            "is_go"     => $is_go,
            "views"     => io_number_format($views),
        );
        $index++;
    endwhile;endif;
    return $_post;
}
/**
 * 加密内容
 * @param string $input 需加密的内容
 * @return string
 */
function base64_io_encode($input, $key=''){
    $url = htmlspecialchars_decode($input); 
    if ($key == '' && !$key = get_option('iotheme_encode_key')) {
        $key = IOTOOLS::getKm();
        update_option('iotheme_encode_key', $key);
    }
    $url = str_rot_pass($url, $key);
    return rtrim(strtr(base64_encode($url), '+/', '-_'), '=');
}
/**
 * 解密内容 
 * @param string $input 需解密的内容
 * @return string
 */
function base64_io_decode($input, $key=''){
    $url = base64_decode(str_pad(strtr($input, '-_', '+/'), strlen($input) % 4, '=', STR_PAD_RIGHT));
    if ($key == '') {
        $key = get_option('iotheme_encode_key');
    }
    return str_rot_pass($url, $key, true);
}
/**
 * 根据 KEY 相应的ascii值旋转每个字符串 
 * @param string $str
 * @param string $key 
 * @param bool $decrypt 
 * @return string
 */
function str_rot_pass($str, $key, $decrypt = false){
    // if key happens to be shorter than the data
    $key_len = strlen($key);
    $result = str_repeat(' ', strlen($str));
    for($i=0; $i<strlen($str); $i++){
        if($decrypt){
            $ascii = ord($str[$i]) - ord($key[$i % $key_len]);
        } else {
            $ascii = ord($str[$i]) + ord($key[$i % $key_len]);
        }
    
        $result[$i] = chr($ascii);
    }
    return $result;
}
/**
 * 对于部分链接，拒绝搜索引擎索引.
 * 
 * @param string $output Robots.txt内容
 * @param bool   $public
 * @return string
 */
function io_robots_modification($output, $public)
{
    $site_url = parse_url( home_url() );
    $path     = ( ! empty( $site_url['path'] ) ) ? $site_url['path'] : '';
    $output  .= "Disallow: $path/bookmark/\n";
    $output  .= "Disallow: $path/go/\n";
    $output  .= "Disallow: $path/user\n";
    $output  .= "Disallow: $path/hotnews\n";
    return $output;
}
add_filter('robots_txt', 'io_robots_modification', 10, 2);

/**
 * 过滤公告帖子地址
 * 
 * @description: post_type_link 自定义帖子，post_link post帖子
 * @param * $permalink
 * @param * $post
 * @return *
 */
function io_suppress_post_link( $permalink, $post ) {
    if($post->post_type=='bulletin'){
        if($goto = get_post_meta($post->ID,'_goto',true)){
            if(get_post_meta($post->ID,'_is_go',true)){
                $permalink = go_to($goto);
            }else{
                $permalink = $goto;
            }
        }
    }else{
        global $queried_object_id; 
        if(is_mininav() || ($queried_object_id && defined( 'DOING_AJAX' ) && DOING_AJAX) ){
            $post_id = get_queried_object_id()?:$queried_object_id;
            $permalink = $permalink.'?menu-id='.get_post_meta( $post_id, 'nav-id', true ).'&mininav-id='.$post_id;
        }
    }
    return $permalink;
}
add_filter( 'post_type_link', 'io_suppress_post_link', 10, 2 );
add_filter( 'post_link', 'io_suppress_post_link', 10, 2 );

/**
 * 显示置顶标签
 * ******************************************************************************************************
 */
function show_sticky_tag($isSticky){
    $span = '';
    $default = array(
        'switcher' => false,
        'name'     => 'T',
    );
    $sticky = io_get_option('sticky_tag',$default);
    if($isSticky && $sticky['switcher'])
        $span = '<span class="badge vc-red text-ss mr-1" title="'.__('置顶','i_theme').'">'.$sticky['name'].'</span>';
    echo $span;
}
/**
 * 显示 NEW 标签
 * ******************************************************************************************************
 */
function show_new_tag($post_date){
    $span = '';
    $default = array(
        'switcher' => false,
        'name'     => 'N',
        'date'     => 7,
    );
    $new = io_get_option('new_tag',$default);
    if($new['switcher']){
        $t2=date("Y-m-d H:i:s",current_time( 'timestamp' ));
        $t3=$new['date']*24;
        $diff=(strtotime($t2)-strtotime($post_date))/3600;
        if( $diff < $t3 ){ 
            $span = '<span class="badge vc-red text-ss mr-1" title="'.__('新','i_theme').'">'.$new['name'].'</span>'; 
        }
    }
    echo $span;
}
/**
 * 编辑器增强
 * ******************************************************************************************************
 */
add_action('init','io_tinymce_button');
function io_tinymce_button() {
    add_filter( 'mce_external_plugins', 'io_add_tinymce_button' );
    add_filter( 'mce_buttons', 'io_register_tinymce_button' );
    add_filter( 'mce_buttons_2', 'io_register_tinymce_button2' );
}
add_filter( 'mce_css', 'io_plugin_mce_css' );
function io_plugin_mce_css( $mce_css ) {
    if ( ! empty( $mce_css ) )
        $mce_css .= ',';
    $mce_css .= get_theme_file_uri('css/editor-style.css');
    return $mce_css;
}
function io_register_tinymce_button( $buttons ) {
    $buttons = ['formatselect', 'bold', 'bullist', 'numlist', 'blockquote', 'alignleft', 'aligncenter', 'alignright', 'link', 'spellchecker','wp_page'];
    if (!is_admin() && wp_is_mobile()) {
        $buttons = ['io_h2', 'io_h3', 'bold', 'bullist', 'link', 'spellchecker'];
    }
    if (!is_admin()) {
        $buttons[] = 'io_img';
    }
    
    if(is_admin()){
        $buttons[] = 'io_ad';
        $buttons[] = 'io_hide';
        $buttons[] = 'io_post_card';
    }
    $buttons[] = 'wp_adv';
    $buttons[] = 'dfw';
    if(!is_admin()){
        $buttons[] = 'fullscreen';
    }

    return $buttons;
}
function io_register_tinymce_button2($buttons) { 
    if(!is_admin() && wp_is_mobile()){
        $buttons = ['styleselect', 'fontsizeselect', 'forecolor', 'removeformat', 'undo', 'redo'];
        return $buttons;
    }
    $io_btn = array('styleselect'); 
    if(is_admin()){
        $io_btn[] = 'fontselect';
        $io_btn[] = 'fontsizeselect';
    }
    return array_merge($io_btn, $buttons);
}
function io_add_tinymce_button( $plugin_array ) {
    $plugin_array['io_button_script'] = get_theme_file_uri('/js/mce-buttons.js');
    return $plugin_array;
}    
//为编辑器加入body class
function io_tiny_mce_before_init_filter($settings, $editor_id)
{
    if ('post_content' === $editor_id) {
        $settings['body_class'] .= ' front-edit ' . theme_mode();
    }
    return $settings;
}
add_filter('tiny_mce_before_init', 'io_tiny_mce_before_init_filter', 10, 2);
/**
 * 主题切换
 * ******************************************************************************************************
 */
function theme_mode(){
    $default_c = io_get_option('theme_mode','io-grey-mode');
    if (io_get_option('theme_auto_mode', 'manual-theme') != 'null') {
        if ($default_c == 'io-black-mode')
            $default_c = '';
        if (isset($_COOKIE['io_night_mode']) && $_COOKIE['io_night_mode'] != '') {
            $default_c = (trim($_COOKIE['io_night_mode']) == '0' ? 'io-black-mode' : $default_c);
        } else {
            $time      = current_time('G');
            $time_auto = io_get_option('time_auto', array('from' => '07', 'to' => '18'));
            if ('time-auto' == io_get_option('theme_auto_mode', 'manual-theme')) {
                if ($time > $time_auto['to'] || $time < $time_auto['from']) {
                    $default_c = 'io-black-mode';
                }
            }
        }
    }
    return apply_filters('io_theme_mode_class', $default_c);
}

function io_body_class(){
    if(get_query_var('bookmark_id')){
        return '';
    }
    $class = '';

    //$class .= theme_mode();

    $class .= io_is_show_sidebar(); 
    if(io_get_option('min_nav',false)) $class .= ' mini-sidebar'; 
    if ((is_single() || is_page()) && get_post_format()) {
        $class .= ' postformat-' . get_post_format();
    }
    return apply_filters('io_add_body_class', trim($class));
}
function io_html_class(){
    if(get_query_var('bookmark_id')){
        return '';
    }
    echo 'class="'.theme_mode().'"';
}
function io_auto_theme_mode(){
    if(get_query_var('bookmark_id')){
        return '';
    }
    $auto_mode = io_get_option('theme_auto_mode', 'manual-theme');
    if ($auto_mode=='auto-system' || (defined( 'WP_CACHE' ) && WP_CACHE && $auto_mode!='null')) {
        $ars = '';
        if($auto_mode=='auto-system')
            $ars = ' || (!night && window.matchMedia("(prefers-color-scheme: dark)").matches)';
        echo '<script>
    var default_c = "'. io_get_option('theme_mode','') .'";
    var night = document.cookie.replace(/(?:(?:^|.*;\s*)io_night_mode\s*\=\s*([^;]*).*$)|^.*$/, "$1"); 
    try {
        if (night === "0"'.$ars.') {
            document.documentElement.classList.add("io-black-mode");
            document.documentElement.classList.remove(default_c);
        } else {
            document.documentElement.classList.remove("io-black-mode");
            document.documentElement.classList.add(default_c);
        }
    } catch (_) {}
</script>';
    }
}
/**
 * 侧边栏显示判断
 * 
 * sidebar_no sidebar_left sidebar_right
 * @return string 
 */
function io_is_show_sidebar(){
    global $sidebar_class, $is_sidebar;
    $is_sidebar = false;
    if( !$sidebar_class ){
        $sidebar_class = '';
        if(is_io_user()){
            return $sidebar_class;
        }
        // 
        if(apply_filters('io_show_sidebar', false)){
            $sidebar_class = ' sidebar_right';
            $is_sidebar = true;
            return $sidebar_class;
        }
        if(wp_is_mobile()){ // 移动端不显示侧边栏
            $sidebar_class = ' sidebar_no';
            if( is_active_sidebar( 'sidebar-index' ) ){
                $is_sidebar = true;
            }
            return $sidebar_class;
        }
        $class = io_get_option( 'sidebar_layout','sidebar_right');
        if(is_single() || is_page()){ 
            $post_id        = get_queried_object_id();
            $post_type      = get_post_type();
            $show_layout    = get_post_meta($post_id, 'sidebar_layout', true);
            $page_template  = str_replace('.php', '', get_page_template_slug($post_id));

            if($show_layout){
                $class = $show_layout=='default' ? $class : $show_layout;
            }
            if ( is_blog() ) {
                if(is_active_sidebar('sidebar-h')){
                    $sidebar_class = " ".$class; 
                    $is_sidebar = true;
                }else{
                    $sidebar_class = " sidebar_no";
                }
                return $sidebar_class;
            }
            if( is_mininav() ){ // 次级导航
                if(is_active_sidebar('sidebar-page-'.$post_id)){
                    $sidebar_class = " ".$class; 
                    $is_sidebar = true;
                }else{
                    $sidebar_class = " sidebar_no";
                }
                return $sidebar_class;
            }
            if(is_page() && is_page_template()){
                if(is_active_sidebar('sidebar-'.$page_template)){
                    $sidebar_class = " ".$class; 
                    $is_sidebar = true;
                }else{
                    if (is_active_sidebar('sidebar-s')) {
                        $sidebar_class =  " ".$class; 
                        $is_sidebar = true;
                    }else {
                        $sidebar_class = " sidebar_no";
                    }
                }
                return $sidebar_class;
            }
            switch ($post_type){
                case "page":
                case "post":
                    if(!is_active_sidebar( 'sidebar-s' ))
                        $class = 'sidebar_no';
                    break;
                default: 
                    if(!is_active_sidebar( 'sidebar-'.$post_type.'-r' ))
                        $class = 'sidebar_no';
                    break;
            }
            if('sidebar_no'!==$class){
                $is_sidebar = true;
            }
            $sidebar_class = " " . $class . " " . $post_type;
            return $sidebar_class;
        }
        if(is_author() || is_io_user()){
            $sidebar_class = '';
            return $sidebar_class;
        }
        if( is_home() || is_front_page() ){
            if( is_active_sidebar( 'sidebar-index' ) ){
                $sidebar_class = " ".$class;
                $is_sidebar = true;
            }else{
                $sidebar_class = " sidebar_no";
            }
            return $sidebar_class;
        } 
        if( (is_archive() || is_search() || is_404()) ){
            if( is_active_sidebar( 'sidebar-a' ) ) { 
                $sidebar_class = " ".$class;
                $is_sidebar = true;
            }else{
                $sidebar_class = " sidebar_no";
            }
            return $sidebar_class;
        }
        if('sidebar_no'!==$class){
            $is_sidebar = true;
        }
        $sidebar_class = " ".$class;
    }
    return $sidebar_class;
}

/**
 * 获取作者签名
 */
function get_user_desc($user_id)
{
    $des = get_user_meta($user_id, 'description', true);
    if (!$des) {
        $des = __('帅气的我简直无法用语言描述！', 'i_theme');
    }
    return esc_attr($des);
}
function get_ref_url($args, $url, $raw){
    if($raw) return $url;
    if(is_array($args)&& count($args)>0){
        $temp = array();
        foreach($args as $v){
            $temp[$v['key']] = $v['value'];
        }
        return add_query_arg( $temp, $url );
    }else{
        return $url;
    }
}
/**
 * 在启用WP_CACHE的情况下切换主题状态
 */
function dark_mode_js(){
    if( !defined( 'WP_CACHE' ) || !WP_CACHE )
        return; 
    echo '<script type="text/javascript">
    var default_c = "'.io_get_option('theme_mode','').'";
    var night = document.cookie.replace(/(?:(?:^|.*;\s*)io_night_mode\s*\=\s*([^;]*).*$)|^.*$/, "$1"); 
    if(night == "1"){
        document.body.classList.remove("io-black-mode");
        document.body.classList.add(default_c);
    }else if(night == "0"){
        document.body.classList.remove(default_c);
        document.body.classList.add("io-black-mode");
    }
    </script> '; 
}
/**
 * 获取自定义菜单列表
 * ******************************************************************************************************
 */
function get_menu_list( $theme_location ) {
    if ( is_numeric($theme_location) || (has_nav_menu($theme_location) && ($theme_location) && ($locations = get_nav_menu_locations()) && isset($locations[$theme_location])) ) {
        $nav_id = is_numeric($theme_location)?$theme_location:$locations[$theme_location];
        $cache_key = 'io_menu_list_main_'. $nav_id;
        $io_menu_list = wp_cache_get( $cache_key );
        //$io_menu_list = get_transient( $cache_key );
        if ( false === $io_menu_list ) { 
            $io_menu_list = array();
            $menu_items = wp_get_nav_menu_items($nav_id);
            foreach( $menu_items as $menu_item ) {
                if( $menu_item->menu_item_parent == 0 ) {
                    $parent = $menu_item->ID;
                    $my_parent = array();
                    foreach($menu_item as $k=>$v)
                        $my_parent[$k] = $v ;
                    $menu_array = array();
                    $bool = false;
                    foreach( $menu_items as $submenu ) {
                        if( $submenu->menu_item_parent == $parent ) {
                            $bool = true;
                            $my_submenu = array();
                            foreach($submenu as $k=>$v)
                                $my_submenu[$k] = $v ;
                            $menu_array[] = $my_submenu;
                        }
                    }
                    if( $bool == true && count( $menu_array ) > 0 ) {
                        $my_parent['submenu'] = $menu_array;
                    } else { 
                        $my_parent['submenu'] = array();
                    }
                    $io_menu_list[] = $my_parent;
                } 
            }
            wp_cache_set( $cache_key, $io_menu_list ); 
            //set_transient( $cache_key, $io_menu_list, 24 * HOUR_IN_SECONDS ); 
        }
        return $io_menu_list;
    }else{
        return array();
    }
}
/**
 * 新窗口访问
 * ******************************************************************************************************
 */
function new_window($forced=false){
    if(io_get_option('new_window',false) || $forced)
        return 'target="_blank"';
    else
        return '';
}
/**
 * 网址块添加 nofollow
 * noopener external nofollow
 * $details 忽略设置
 * ******************************************************************************************************
 */
function nofollow($url, $details = false, $is_blank = false){
    $ret = '';
    if($details)
        return $ret;

    if(io_get_option('is_nofollow',false) && !is_go_exclude($url))
        $ret .= 'external nofollow';

    if(io_get_option('new_window',false) ||  $is_blank)
        $ret .= ' noopener';

    if($ret == '')
        return $ret;
    else
        return 'rel="'.$ret.'"';
}
/**
 * 网址块 go 跳转
 * @param string $url 外链地址
 * @param bool $forced 强制转换
 * @return string
 */
function go_to($url, $forced=false){
    if($forced)
        return esc_url(home_url()).'/go/?url='.urlencode(base64_encode($url)) ;
    if(io_get_option('is_go',false)){
        if(is_go_exclude($url))
            return $url;
        else
            return esc_url(home_url()).'/go/?url='.urlencode(base64_encode($url)) ;
    }
    else
        return $url;
}
/**
 * 添加go跳转，排除白名单
 * ******************************************************************************************************
 */
function is_go_exclude($url){ 
    $exclude_links = array();
    $site = esc_url(home_url());
    if (!$site)
        $site = get_option('siteurl');
    $site = str_replace(array("http://", "https://"), '', $site);
    $p = strpos($site, '/');
    if ($p !== FALSE)
        $site = substr($site, 0, $p);/*网站根目录被排除在屏蔽之外，不仅仅是博客网址*/
    $exclude_links[] = "http://" . $site;
    $exclude_links[] = "https://" . $site;
    $exclude_links[] = 'javascript';
    $exclude_links[] = 'mailto';
    $exclude_links[] = 'skype';
    $exclude_links[] = '/';/* 有关相对链接*/
    $exclude_links[] = '#';/*用于内部链接*/

    if(io_get_option('exclude_links',false)){
        $a = explode(PHP_EOL , io_get_option('exclude_links',false));
        $exclude_links = array_merge($exclude_links, $a);
    }
    foreach ($exclude_links as $val){
        if (stripos(trim($url), trim($val)) === 0) {
            return true;
        }
    }
    return false;
}
/**
 * app下载js地址预处理
 * @see io_ajax_get_app_down_btn()
 * @deprecated 4.0 将被弃用
 * @param array $metadata 下载数据json
 * @return string
 */
function io_js_down_goto_pretreatment($metadata){
    $data = array();
    foreach($metadata as $m){
        $data[] = array(
            'app_version' => $m['app_version'],
            'down_url'    => $m['down_url']
        );
    }
    $meta_string = json_encode($data);
    if( io_get_option('is_go',false) && !io_get_option('is_app_down_nogo',false)){
        //"down_btn_url":"https://www.iowen.cn/"
        $regexp = 'down_btn_url":"([^"]+)';
        if(preg_match_all("/$regexp/i", $meta_string, $matches, PREG_SET_ORDER)) { // s 匹配换行
            if( !empty($matches) ) {
                $srcUrl = get_option('siteurl'); 
                for ($i=0; $i < count($matches); $i++)
                { 
                    $url = $matches[$i][1];
                    $url_goto = go_to(stripslashes($matches[$i][1]));
                    $meta_string = str_replace($url,$url_goto,$meta_string);  
                }
            }
        }
    }
    return $meta_string;
}
add_filter( 'query_vars',  'wp_link_pages_all_parameter_queryvars'  );
add_action( 'the_post',  'wp_link_pages_all_the_post'  , 0 );
function wp_link_pages_all_parameter_queryvars( $queryvars ) {
    $queryvars[] = 'view';
    return( $queryvars );
}
function wp_link_pages_all_the_post( $post ) {
    global $pages, $multipage, $wp_query;
    if ( isset( $wp_query->query_vars[ 'view' ] ) && ( 'all' === $wp_query->query_vars[ 'view' ] ) ) {
        $multipage = true;
        $post->post_content = str_replace( '<!--nextpage-->', '', $post->post_content );
        $pages = array( $post->post_content );
    }
}

# 后台检测投稿状态
# --------------------------------------------------------------------
add_action('admin_bar_menu', 'pending_prompt_menu', 2000);
function pending_prompt_menu() {
    if( ! is_admin() ) { return; }
    global $wp_admin_bar;
    $menu_id = 'pending';
    $args = array(
        'post_type' => 'sites',// 文章类型
        'post_status' => 'pending',
    );
    $pending_items = new WP_Query( $args ); 
    if ($pending_items->have_posts()) : 
        $wp_admin_bar->add_menu(array(
            'id' => $menu_id,  
            'title' => '<span class="update-plugins count-2" style="display: inline-block;background-color: #d54e21;color: #fff;font-size: 9px;font-weight: 600;border-radius: 10px;z-index: 26;height: 18px;margin-right: 5px;"><span class="update-count" style="display: block;padding: 0 6px;line-height: 17px;">'.$pending_items->found_posts.'</span></span>个网址待审核', 
            'href' => get_option('siteurl')."/wp-admin/edit.php?post_status=pending&post_type=sites"
        ));     
    endif; 
    wp_reset_postdata();
}
# 格式化 url
# --------------------------------------------------------------------
function format_url($url,$is_format=false){
    if($url == '')
    return;
    $url = rtrim($url,"/");
    if(io_get_option('ico-source', true, 'url_format') || $is_format){
        $pattern = '@^(?:https?://)?([^/]+)@i';
        $result = preg_match($pattern, $url, $matches);
        if ($result) {
            return $matches[1];
        }
        return $url;
    } else {
        return $url;
    }
} 
# 格式化数字 $precision int 精度
# --------------------------------------------------------------------
function format_number($n, $precision = 2)
{
    return $n;
    if ($n < 1e+3) {
        $out = number_format($n);
    } else {
        $out = number_format($n / 1e+3, $precision) . 'k';
    }
    return $out;
}
# 获取点赞数
# --------------------------------------------------------------------
function get_like($post_id ,$post_type = "sites"){
    if(io_get_option('user_center',false) && function_exists('io_get_post_star_count')){
        $type         = $post_type;
        if($post_type == "sites-down")
            $type     = "sites";
        $like_data    = io_get_post_star_count($post_id,$type);
        $like_count   = $like_data['count'];
    }else{
        if ( !$like_count = get_post_meta( $post_id, '_like_count', true ) ) {
            if(io_get_option('like_n',0)>0){
                $like_count = mt_rand(0, 10)*io_get_option('like_n',0);
                update_post_meta( $post_id, '_like_count', $like_count );
            }
            else
                $like_count = 0;
        }
    }
    return format_number($like_count);
} 
/**
 * 查找字符是否存在
 * 
 * @description:
 * @param string $str
 * @param array $array_sou
 * @return string
 */
function iostrpos($str , $array_sou){
    $intex = 0;
    foreach($array_sou as $value){
        if(strstr($str , $value)!==false){
            if( $intex == 0)
                $intex = strpos($str , $value);
            if( $intex>strpos($str , $value) )
                $intex = strpos($str , $value);
        }
    }
    return $intex;
}
/**
 * 文章浏览数
 * 
 * @param string $author_id 用户id, all 为所有文章
 * @param bool $display
 * @return string|null
 */
function author_posts_views($author_id = 'all',$display = true){
    if(empty($author_id)) return '0';
    global $wpdb;
    if($author_id == 'all')
        $sql = "SELECT sum(meta_value) FROM $wpdb->postmeta WHERE meta_key='views'";
    else
        $sql = "SELECT SUM(meta_value+0) FROM $wpdb->posts left join $wpdb->postmeta on ($wpdb->posts.ID = $wpdb->postmeta.post_id) WHERE meta_key = 'views' AND post_author =$author_id";    
    $comment_views = intval($wpdb->get_var($sql));
    if($display) {
        echo io_number_format($comment_views);
    } else {
        return $comment_views;
    }
}
/**
 * 获取作者所有文章点赞数
 * 
 * @param * $author_id 用户id, all 为所有文章
 * @param * $display
 * @return *
 */
function author_posts_likes($author_id = 'all' ,$display = true) {
    if(empty($author_id)) return '0';
    global $wpdb;
    if($author_id == 'all')
        $sql = "SELECT sum(meta_value) FROM $wpdb->postmeta WHERE meta_key='_like_count'";
    else
        $sql = "SELECT SUM(meta_value+0) FROM $wpdb->posts left join $wpdb->postmeta on ($wpdb->posts.ID = $wpdb->postmeta.post_id) WHERE meta_key = '_like_count' AND post_author = $author_id ";
        
    $posts_likes = intval($wpdb->get_var($sql));    
    if($display) {
        echo io_number_format($posts_likes);
    } else {
        return $posts_likes;
    }
}


//用户资料
function io_author_con_datas($user_id = '', $class = 'col-sm-6 p-2', $title_class = 'text-muted', $value_class = '')
{
    if (!$user_id) return;
    $current_id = get_current_user_id();
    $udata = get_userdata($user_id);
    if (!$udata) return;
    $privacy = get_user_meta($user_id, 'privacy', true);

    $datas = array(
        array(
            'title' => '昵称',
            'value' => esc_attr($udata->display_name),
            'show' => false,
        ),
        array(
            'title' => '签名',
            'value' => get_user_desc($user_id),
            'show' => false,
        ), array(
            'title' => '注册时间',
            'value' => get_date_from_gmt($udata->user_registered),
            'spare' => '未知',
            'show' => false,
        ), array(
            'title' => '最后登录',
            'value' => get_user_meta($user_id, 'last_login', true),
            'spare' => '未知',
            'show' => false,
        ), array(
            'title' => '邮箱',
            'value' => esc_attr($udata->user_email),
            'spare' => '未知',
            'show' => true,
        ), array(
            'title' => '个人网站',
            'value' => io_get_url_link($user_id),
            'spare' => '未知',
            'show' => true,
        )
    );
    foreach ($datas as $data) {
        if (!is_super_admin() && $data['show'] && $privacy != 'public' && $current_id != $user_id) {
            if (($privacy == 'just_logged' && !$current_id) || $privacy != 'just_logged') {
                $data['value'] = '用户未公开';
            }
        }
        echo '<div class="' . $class . '">';
        echo '<ul class="list-inline list-author-data">';
        echo '<li class="author-set-left ' . $title_class . '">' . $data['title'] . '</li>';
        echo '<li class="author-set-right ' . $value_class . '">' . ($data['value'] ? $data['value'] : $data['spare']) . '</li>';
        echo '</ul>';
        echo '</div>';
    }
}
function io_get_url_link($user_id, $class = 'focus-color'){
    $user_url =  get_userdata($user_id)->user_url;
    $url_name = get_user_meta($user_id, 'url_name', true) ? get_user_meta($user_id, 'url_name', true) : $user_url;
    $user_url =  go_to($user_url);
    return $user_url ? '<a class="' . $class . '" href="' . esc_url($user_url) . '" target="_blank">' . esc_attr($url_name) . '</a>' : 0;
}

/**
 * 将数字四舍五入为K（千），M（百万）或B（十亿）
 * @param mixed $number 
 * @param int $min_value 
 * @param int $decimal 
 * @return string|void 
 */
function io_number_format( $number, $min_value = 1000, $decimal = 1 ) {
    if( $number < $min_value ) {
        return number_format_i18n( $number );
    }
    $alphabets = array( 1000000000 => 'B', 1000000 => 'M', 1000 => 'K' );
    foreach( $alphabets as $key => $value )
        if( $number >= $key ) {
            return round( $number / $key, $decimal ) . '' . $value;
        }
}
/**
 * 菜单允许的类型
 * 
 * @description:
 * @param 
 * @return array
 */
function get_menu_category_list(){
    $terms = apply_filters( 'io_category_list', array('favorites','apps','category','books',"series","apptag","sitetag","booktag","post_tag") );
    return $terms;
}
# 获取分类下文章数量
# --------------------------------------------------------------------
function io_get_category_count($cat_ID = '',$taxonomy = '') {
    if($cat_ID == '' || $taxonomy == '' ){
        global $wp_query;
        $cat_ID = get_query_var('cat');
        $category = get_category($cat_ID);
    }else{
        $category = get_term( $cat_ID, $taxonomy );
    }
    return $category->count;
}

//add_action('publish_sites', 'io_add_post_data_fields');
//add_action('publish_book', 'io_add_post_data_fields');
//add_action('publish_app', 'io_add_post_data_fields');
//add_action('publish_post', 'io_add_post_data_fields');
//add_action('publish_page', 'io_add_post_data_fields');//wp_insert_post
add_action('save_post_sites', 'io_add_post_data_fields');
add_action('save_post_book', 'io_add_post_data_fields');
add_action('save_post_app', 'io_add_post_data_fields');
add_action('save_post_post', 'io_add_post_data_fields');
add_action('save_post_page', 'io_add_post_data_fields');
function io_add_post_data_fields($post_ID) {
    // 检查是否为新帖子或更新帖子
    if ( wp_is_post_autosave( $post_ID ) || wp_is_post_revision( $post_ID ) ) {
        return;
    }
    add_post_meta($post_ID, 'views', 0, true);
    add_post_meta($post_ID, '_down_count', 0, true);
    add_post_meta($post_ID, '_like_count', 0, true);
    add_post_meta($post_ID, '_star_count', 0, true);
    add_post_meta($post_ID, '_user_purview_level', 'all', true);
}
function like_button($post_id,$post_type="sites",$display = true){
    if(io_get_option('user_center',false) && function_exists('io_get_post_star_count')){
        $type         = $post_type;
        if($post_type == "sites-down")
            $type     = "sites";
        $like_data    = io_get_post_star_count($post_id,$type);
        $like_count   = $like_data['count'];
        $liked        = $like_data['status']; 
        switch($post_type){
            case "sites":
                $button = '
                <a href="javascript:;" data-action="post_star" data-post_type="'.$type.'" data-id="'.$post_id.'" data-ticket="'.wp_create_nonce('post_star_nonce').'" class=" btn btn-like btn-icon btn-light rounded-circle p-2 mx-3 mx-md-2 '. ($liked?'liked':'') .'" data-toggle="tooltip" data-placement="top" title="'. __('收藏','i_theme') .'">
                <span class="flex-column text-height-xs">
                    <i class="star-ico icon-lg iconfont icon-collection'. ($liked?'':'-line') .'"></i>
                    <small class="star-count-'.$post_id.' text-xs mt-1">'. $like_count .'</small>
                </span>
                </a>';
                break;
            case "book":
                $button = '
                <a href="javascript:;" data-action="post_star" data-post_type="'.$type.'" data-id="'.$post_id.'" data-ticket="'.wp_create_nonce('post_star_nonce').'" class=" btn btn-like btn-icon btn-light rounded-circle p-2 mx-3 mx-md-2 '. ($liked?'liked':'') .'" data-toggle="tooltip" data-placement="top" title="'. __('收藏','i_theme') .'">
                <span class="flex-column text-height-xs">
                    <i class="star-ico icon-lg iconfont icon-collection'. ($liked?'':'-line') .'"></i>
                    <small class="star-count-'.$post_id.' text-xs mt-1">'. $like_count .'</small>
                </span>
                </a>';
                break;
            case "app":
            case "sites-down":
                $button = '
                <button type="button" data-action="post_star" data-post_type="'.$type.'" data-id="'.$post_id.'" data-ticket="'.wp_create_nonce('post_star_nonce').'" class="btn btn-lg px-4 text-lg radius-50 btn-outline-danger custom_btn-outline mb-2 btn-like '.($liked?'liked':'').'">
                    <i class="star-ico iconfont icon-collection'. ($liked?'':'-line') .' mr-2"></i> '. __('收藏','i_theme') .' <span class="star-count-'.$post_id.'">'.$like_count.'</span>
                </button>';
                break;
            case "post":
                $button = '
                <span class="mr-3"><a class="btn-like btn-link-like '.($liked?'liked':'').'" href="javascript:;" data-action="post_star" data-post_type="'.$type.'" data-id="'.$post_id.'" data-ticket="'.wp_create_nonce('post_star_nonce').'"><i class="star-ico iconfont icon-collection'. ($liked?'':'-line') .'"></i> <span class="star-count-'.$post_id.'">'.$like_count.'</span></a></span>';
                break;
        }
    }else{
        $like_count    = get_like($post_id);
        $liked        = isset($_COOKIE['liked_' . $post_id]) ? 'liked' : ''; 
        switch($post_type){
            case "sites":
                $button = '
                <a href="javascript:;" data-action="post_like" data-id="'.$post_id.'" class=" btn btn-like btn-icon btn-light rounded-circle p-2 mx-3 mx-md-2 '.$liked.'" data-toggle="tooltip" data-placement="top" title="'. __('点赞','i_theme').'">
                <span class="flex-column text-height-xs">
                    <i class="icon-lg iconfont icon-like"></i>
                    <small class="like-count text-xs mt-1">'.$like_count.'</small>
                </span>
                </a>';
                break;
            case "book":
                $button = '
                <a href="javascript:;" data-action="post_like" data-id="'.$post_id.'" class=" btn btn-like btn-icon btn-light rounded-circle p-2 mx-3 mx-md-2 '.$liked.'" data-toggle="tooltip" data-placement="top" title="'. __('点赞','i_theme').'">
                <span class="flex-column text-height-xs">
                    <i class="icon-lg iconfont icon-like"></i>
                    <small class="like-count text-xs mt-1">'.$like_count.'</small>
                </span>
                </a>';
                break;
            case "app":
            case "sites-down":
                $button = '
                <button type="button" data-action="post_like" data-id="'.$post_id.'" class="btn btn-lg px-4 text-lg radius-50 btn-outline-danger custom_btn-outline mb-2 btn-like '.$liked.'">
                    <i class="iconfont icon-like mr-2"></i> '. __('赞','i_theme') .' <span class="like-count ">'.$like_count.'</span>
                </button>';
                break;
            case "post":
                $button = '
                <span class="mr-3"><a class="btn-like btn-link-like '.$liked.'" href="javascript:;" data-action="post_like" data-id="'.$post_id.'"><i class="iconfont icon-like"></i> <span class="like-count">'.$like_count.'</span></a></span>';
                break;
        }
    }
    if($display)
        echo $button;
    else
        return $button;
}
function like_home_button($post_id,$post_type="sites",$display = true){
    if(io_get_option('user_center',false) && function_exists('io_get_post_star_count')){
        $like_data    = io_get_post_star_count($post_id,$post_type);
        $like_count   = $like_data['count'];
        $liked        = $like_data['status']; 
        $button = '<span class="btn-like pl-2 '. ($liked?'liked':'') .'" data-action="post_star" data-post_type="'.$post_type.'" data-id="'.$post_id.'" data-ticket="'.wp_create_nonce('post_star_nonce').'" ><i class="star-ico iconfont icon-collection'. ($liked?'':'-line') .'"></i> <span class="star-count-'.$post_id.'">'.$like_count.'</span></span>';
    }else{
        $like_count    = get_like($post_id);
        $liked        = isset($_COOKIE['liked_' . $post_id]) ? 'liked' : ''; 
        $button = '<span class="home-like pl-2 '. $liked .'" data-action="post_like" data-id="'.$post_id.'" ><i class="iconfont icon-heart"></i> <span class="home-like-'.$post_id.'">'.$like_count.'</span></span>';
    }
    if($display)
        echo $button;
    else
        return $button;
}
/**
 * 获取用户权限等级
 * @param int $user_id
 * @return int
 */
function io_get_user_level($user_id = -1)
{
    if($user_id == -1){
        global $current_user;
        $user_id = $current_user->ID;
    }
    if (user_can($user_id, 'manage_options')) {
        return 10;
    }
    if (user_can($user_id, 'edit_others_posts')) {
        return 7;
    }
    if (user_can($user_id, 'publish_posts')) {
        return 2;
    }
    if (user_can($user_id, 'edit_posts')) {
        return 1;
    }
    return 0;
}
/**
 * 加载主页菜单内容对应卡片
 * @param 
 * @return null
 */
function add_menu_content_card(){
    $menu_categories = io_get_menu_categories($is_min_nav);
    foreach($menu_categories as $category) {
        if(get_post_meta( $category['ID'], 'purview', true )<=io_get_user_level()):
        if($category['menu_item_parent'] == 0){
            if(empty($category['submenu'])){ 
                $terms = get_menu_category_list();
                if($category['type'] != 'taxonomy') {
                    $url = trim($category['url']);
                    if( strlen($url)>1 ) {
                        if(substr( $url, 0, 1 ) == '#' || substr( $url, 0, 4 ) == 'http' )
                            continue;
                        echo "<div class='card py-3 px-4'><p style='color:#f00'>“{$category['title']}”不是分类，请到菜单重新添加</p></div>";
                    }
                } elseif ( $category['type'] == 'taxonomy' && in_array( $category['object'],$terms ) ){
                    fav_con_a($category);
                } else {
                    echo "<div class='card py-3 px-4'><p style='color:#f00'>“{$category['title']}”不是分类，请到菜单重新添加</p></div>";
                }
            }else{
                $is_null=true; //如果菜单内没有有效的项目，则不显示在正文中。
                foreach($category['submenu'] as $mid){
                    if($mid['type'] != 'taxonomy' ){
                        continue;
                    }
                    $is_null=false;
                }
                if($is_null) continue;
                if(io_get_option("tab_type",false)) {
                    fav_con_tab($category['submenu'],$category,io_get_option("tab_ajax",true));
                }else{
                    echo '<span id="term-'.$category['object_id'].'"></span>'; // 添加菜单描点
                    foreach($category['submenu'] as $mid) {
                        if($mid['type'] != 'taxonomy' ){
                            $url = trim($mid['url']);
                            if( strlen($url)>1 ) {
                                if(substr( $url, 0, 1 ) == '#' || substr( $url, 0, 4 ) == 'http' )
                                    continue;
                            }
                        }
                        fav_con_a($mid,$category);
                    }
                }
            }
        }
        endif;
    } 
}
/**
 * 获取bing图片
 * https://cn.bing.com/th?id=OHR.YoshinoyamaSpring_ZH-CN5545606722_UHD.jpg&pid=hp&w=2880&h=1620&rs=1&c=4&r=0
 * https://cn.bing.com/th?id=OHR.YoshinoyamaSpring_ZH-CN5545606722_1920x1080.jpg&rf=LaDigue_1920x1080.jpg&pid=hp"
 * set_url_scheme
 * 
 * @param  int      $idx 序号
 * @param  string   $size 尺寸 full 1080p uhd 2880x1620 ro 4476x2518
 * 
 * @return string
 */
function get_bing_img_cache($idx=0,$size='uhd'){ 
    $today = strtotime(date("Y-m-d",current_time( 'timestamp' )));// mktime(0,0,0,date('m'),date('d'),date('Y'));
    $yesterday = strtotime(date("Y-m-d",strtotime("-1 day",current_time( 'timestamp' ))));
    if($size=='full'){
        $suffix = '_1920x1080.jpg';
        $url_add = "_1920x1080.jpg";
    }else{
        $suffix = '_UHD.jpg';
        $url_add = "_UHD.jpg&pid=hp&w=2880&h=1620&rs=1&c=4&r=0";
    }
    if(io_get_option('bing_cache',false)){
        $imgDir = wp_upload_dir();
        $bingDir = $imgDir['basedir'].'/bing';
        if (!file_exists($bingDir)) {
            if(!mkdir($bingDir, 0755)){
                wp_die('创建必应图片缓存文件夹失败，请检测文件夹权限！', '创建文件夹失败', array('response'=>403));
            }
        }
        if (!file_exists($bingDir.'/'.$today.$suffix)) {
            $bing_url = 'http:'.bing_img_url($idx).$url_add;
            //$content = file_get_contents($bing_url, false, stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 5))));

            $response = wp_remote_get($bing_url);
            $content = wp_remote_retrieve_body($response);

            file_put_contents($bingDir.'/'.$today.$suffix, $content); // 写入今天的
            $yesterdayimg = $bingDir.'/'.$yesterday.$suffix;
            if (file_exists($yesterdayimg)) {
                unlink($yesterdayimg); //删除昨天的 
            }
            $src = $imgDir['baseurl'].'/bing/'.$today.$suffix;
        } else {
            $src = $imgDir['baseurl'].'/bing/'.$today.$suffix;
        }
    }else{
        $src = bing_img_url($idx).$url_add;
    }
    return $src;
}
function bing_img_url($idx=0,$n=1){
    //$res = file_get_contents('http://cn.bing.com/HPImageArchive.aspx?format=js&idx='.$idx.'&n='.$n, false, stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 5))));
    $response = wp_remote_get('http://cn.bing.com/HPImageArchive.aspx?format=js&idx='.$idx.'&n='.$n);
    $res = wp_remote_retrieve_body($response);
    $bingArr = json_decode($res, true);
    $bing_url = "//cn.bing.com{$bingArr['images'][0]['urlbase']}";
    return $bing_url;
}
/**
 * 获取简介 
 * @param int $count
 * @param string $meta_key
 * @param string $trimmarker
 * @return string
 */
function io_get_excerpt($count = 90,$meta_key = '_seo_desc', $trimmarker = '...', $post=''){
    if(''===$post){
        global $post;
    }
    $excerpt = '';
    if (!($excerpt = get_post_meta($post->ID, $meta_key, true))) { 
        if (!empty($post->post_excerpt)) {
            $excerpt = $post->post_excerpt;
        } else {
            $excerpt = $post->post_content;
        }
    }
    $excerpt = trim(str_replace(array("\r\n", "\r", "\n", "　", " "), " ", str_replace("\"", "'", strip_tags(strip_shortcodes($excerpt)))));
    $excerpt = mb_strimwidth(strip_tags($excerpt), 0, $count, $trimmarker);
    return $excerpt;
}
/**
 * 截取内容
 * @param string $excerpt
 * @param int $count
 * @param string $trimmarker
 * @return string
 */
function io_strimwidth($excerpt, $count = 90, $trimmarker = '...'){
    $excerpt = trim(str_replace(array("\r\n", "\r", "\n", "　", " "), " ", str_replace("\"", "'", strip_tags(strip_shortcodes($excerpt)))));
    $excerpt = mb_strimwidth(strip_tags($excerpt), 0, $count, $trimmarker);
    return $excerpt;
}
/**
 * 保存外链图片到本地
 * @param string $src
 * @return array
 */
function io_save_img($src, $ext='') { 
    // 本地上传路径信息(数组)，用来构造url
    $wp_upload_dir = wp_upload_dir();

    
    $return_data =  array(
        'status' => false,
        'url'    => '',
        'msg'    => '',
    );

    // 脚本执行不限制时间
    set_time_limit(0);

    if (isset($src) && unexclude_image($src)) {// 如果图片域名是外链

        // 检查src中的url有无扩展名，没有则重新给定文件名
        // 注意：如果url中有扩展名但格式为webp，那么返回的file_info数组为 ['ext' =>'','type' =>'']
        $file_info = wp_check_filetype(basename($src), null);
        if ($file_info['ext'] == false) {
            // 无扩展名和webp格式的图片会被作为无扩展名文件处理 
            $file_name = date('YmdHis-',current_time( 'timestamp' )).dechex(mt_rand(100000, 999999)).'.tmp';
        } else {
            // 有扩展名的图片重新给定文件名防止与本地文件名冲突
            //判断是不是后缀是不是 .html，如果是就替换成 .png
            if(in_array($file_info['ext'],['png','jpg','jpeg','gif','webp'])){
                $file_name = dechex(mt_rand(100000, 999999)) . '-' . basename($src);
            }else{
                $file_name = date('YmdHis-',current_time( 'timestamp' )).dechex(mt_rand(100000, 999999)).'.png';
            }
        }
        // 抓取图片, 将图片写入本地文件
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS,20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $src);
        $file_path = $wp_upload_dir['path'] . '/' . $file_name;
        $img = fopen($file_path, 'wb');

        // curl写入$img
        curl_setopt($ch, CURLOPT_FILE, $img);
        $img_data  = curl_exec($ch);
        // 将扩展名为tmp和webp的图片转换为jpeg文件并重命名
        $t   = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        fclose($img);

        if (file_exists($file_path) && filesize($file_path) > 0) {
            $arr = explode('/', $t);
            // 对url地址中没有扩展名或扩展名为webp的图片进行处理
            if (pathinfo($file_path, PATHINFO_EXTENSION) == 'tmp') {
                $file_path = io_handle_ext($file_path, $arr[1], $wp_upload_dir['path'], $file_name, 'tmp');
            } elseif (pathinfo($file_path, PATHINFO_EXTENSION) == 'webp') {
                $file_path = io_handle_ext($file_path, $arr[1], $wp_upload_dir['path'], $file_name, 'webp');
            }

            // 本地src
            $url = $wp_upload_dir['url'] . '/' . basename($file_path);
            // 构造附件post参数并插入媒体库(作为一个post插入到数据库)
            $attachment = io_get_attachment_post(basename($file_path), $url);
            // 生成并更新图片的metadata信息
            $attach_id = wp_insert_attachment($attachment, ltrim($wp_upload_dir['subdir'] . '/' . basename($file_path), '/'), 0);
            $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
            // 直接调用wordpress函数，将metadata信息写入数据库
            $ss = wp_update_attachment_metadata($attach_id, $attach_data);

            $return_data['status'] = true;
            $return_data['url'] = $url;
            $return_data['msg'] = '获取成功！';
            return $return_data;
        }else{
            $return_data['msg'] = '图片获取失败！';
            return $return_data;
        }
    }else{
        $return_data['msg'] = '已经是本地图片了';
        return $return_data;
    }
}
/**
 * 图片白名单
 * @param string $url
 * @return bool
 */
function unexclude_image($url){
    if(io_get_option('exclude_image','')){
        $exclude = explode(PHP_EOL , io_get_option('exclude_image',''));
        $exclude[] = $_SERVER['HTTP_HOST']; 
        foreach($exclude as $v){
            if(strpos($url, $v) !== false){
                return false;
            }
        }
        return true;
    }
    return true;
}
/**
 * 处理没有扩展名的图片:转换格式或更改扩展名
 *
 * @param string $file 图片本地绝对路径
 * @param string $type 图片mimetype
 * @param string $file_dir 图片在本地的文件夹
 * @param string $file_name 图片名称
 * @param string $ext 图片扩展名
 * @return string 处理后的本地图片绝对路径
 */
function io_handle_ext($file, $type, $file_dir, $file_name, $ext) {
    switch ($ext) {
        case 'tmp':
            if('x-icon' == $type){
                if (rename($file, str_replace('tmp', 'png', $file))) {
                    return $file_dir . '/' . str_replace('tmp', 'png', $file_name);
                }
            }else{
                if (rename($file, str_replace('tmp', $type, $file))) {
                    if ('webp' == $type) {
                        // 将webp格式的图片转换为jpeg格式
                        return io_image_convert('webp', 'jpeg', $file_dir . '/' . str_replace('tmp', $type, $file_name));
                    }
                    return $file_dir . '/' . str_replace('tmp', $type, $file_name);
                }
            }
        case 'webp':
            if ('webp' == $type) {
                // 将webp格式的图片转换为jpeg格式
                return io_image_convert('webp', 'jpeg', $file);
            } else {
                if (rename($file, str_replace('webp', $type, $file))) {
                    return $file_dir . '/' . str_replace('webp', $type, $file_name);
                }
            }
        default:
            return $file;
    }
}
/**
 * 图片格式转换，暂只能从webp转换为jpeg
 *
 * @param string $from
 * @param string $to
 * @param string $image 图片本地绝对路径
 * @return string 转换后的图片绝对路径
 */
function io_image_convert($from='webp', $to='jpeg', $image='') {
    // 加载 WebP 文件
    $im = imagecreatefromwebp($image);
    // 以 100% 的质量转换成 jpeg 格式并将原webp格式文件删除
    if (imagejpeg($im, str_replace('webp', 'jpeg', $image), 100)) {
        try {
            unlink($image);
        } catch (Exception $e) {
            $error_msg = sprintf('Error removing local file %s: %s', $image,
                $e->getMessage());
            error_log($error_msg);
        }
    }
    imagedestroy($im);

    return str_replace('webp', 'jpeg', $image);
}
/**
 * 构造图片post参数
 *
 * @param string $filename
 * @param string $url
 * @return array 图片post参数数组
 */
function io_get_attachment_post($filename, $url) {
    $file_info  = wp_check_filetype($filename, null);
    return array(
        'guid'           => $url,
        'post_type'      => 'attachement',
        'post_mime_type' => $file_info['type'],
        'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );
} 

function io_head_favicon(){
    if (io_get_option('favicon','')) {
        echo "<link rel='shortcut icon' href='" . io_get_option('favicon','') . "'>";
    } else {
        echo "<link rel='shortcut icon' href='" . home_url('/favicon.ico') . "'>";
    }
    if (io_get_option('apple_icon','')) {
        echo "<link rel='apple-touch-icon' href='" . io_get_option('apple_icon','') . "'>";
    }
}
add_action('admin_head', 'io_head_favicon');


/**
 * 输出lazy图片
 * 
 * @param  string   $src       图片地址
 * @param  string   $alt       名称
 * @param  int|string|array $size      大小
 * @param  string   $class     class
 * @param  string   $def_src   默认图片
 * @param  boolean  $is_error  是否触发错误输出
 * @param  string   $error_src error图片
 * @return string 
 */
function get_lazy_img($src, $alt, $size, $class='', $def_src='', $is_error=false, $error_src=''){
    if ($def_src=='') {
        $def_src=get_theme_file_uri('/images/t.png');
    }
    $onerror = '';
    if ($is_error) {
        $onerror = $error_src?:'onerror="javascript:this.src=\''.$def_src.'\'"';
    }

    if(is_array($size)){
        $_size = 'height="'.$size[1].'" width="'.$size[0].'"';
    }else{
        $_size = 'height="'.$size.'" width="'.$size.'"';
    }
    if (io_get_option('lazyload',false)) {
        return '<img class="'.$class.' lazy unfancybox" src="'.$def_src.'" data-src="'.$src.'" '.$onerror.' '.$_size.'  alt="'.$alt.'">';
    }else{
        return '<img class="'.$class.' unfancybox" src="'.$src.'" '.$onerror.' '.$_size.' alt="'.$alt.'">';
    }
}
/**
 * 输出lazy图片BG
 * 
 * @param  string $src   图片地址
 * @param  string $style 其他样式
 * @return string 
 */
function get_lazy_img_bg($src, $style=''){ 
    if (io_get_option('lazyload',false)) {
        return 'data-bg="url('.$src.')"'.($style==''?'':' style="'.$style.'"');
    }else{
        return 'style="background-image: url('.$src.')'.';'.$style.'"';
    }
}
if(!function_exists('get_columns')):
/**
 * 首页卡片一行个数样式
 * 
 * @param  string $type    文章类型
 * @param  string $cat_id  分类id
 * @param  bool   $display
 * @param  bool   $is_sidebar  有侧边栏，自动减1
 * @param  string $mode    特殊模块样式
 * @return string|null
 */
function get_columns($type='sites', $cat_id='', $display=true, $is_sidebar=false, $mode=''){
    $columns = array(
        'sm'=>2,
        'md'=>2,
        'lg'=>3,
        'xl'=>5,
        'xxl'=>6
    );
    if($mode=='mini'){
        $class = " col-2a col-sm-2a col-md-4a col-lg-5a col-xl-6a col-xxl-10a ";
    }else{
        if ($cat_id!='' && get_term_meta($cat_id, 'columns_type', true)=="custom") {
            $columns = get_term_meta($cat_id, 'columns', true);
        } else {
            $columns = io_get_option($type.'_columns', $columns);
        }
        if (is_array($columns) && isset($columns['xl'])) {
            if ($is_sidebar) {
                $columns['xxl'] -= 1;
                $columns['xl'] -= 1;
                $columns['lg'] -= 1;
            }
            if ($mode == 'max') {
                $columns['sm'] = 1;
            }
            $class = " col-{$columns['sm']}a col-sm-{$columns['sm']}a col-md-{$columns['md']}a col-lg-{$columns['lg']}a col-xl-{$columns['xl']}a col-xxl-{$columns['xxl']}a ";
        }else{
            $class = " col-2a col-sm-2a col-md-2a col-lg-3a col-xl-5a col-xxl-6a ";
        }
    }
    if($display)
        echo $class;
    else
        return $class;
}
endif;
# 时间格式转化
# --------------------------------------------------------------------
function timeago( $ptime ) {
    if (!is_numeric($ptime)) {
        $ptime = strtotime($ptime);
    }
    $etime = current_time( 'timestamp' ) - $ptime;
    if($etime < 1) return __('刚刚', 'i_theme');
    $interval = array (
        12 * 30 * 24 * 60 * 60  =>  __('年前', 'i_theme').' ('.date('Y', $ptime).')',
        30 * 24 * 60 * 60       =>  __('个月前', 'i_theme'),
        7  * 24 * 60 * 60       =>  __('周前', 'i_theme'),
        24 * 60 * 60            =>  __('天前', 'i_theme'),
        60 * 60                 =>  __('小时前', 'i_theme'),
        60                      =>  __('分钟前', 'i_theme'),
        1                       =>  __('秒前', 'i_theme')
    );
    foreach ($interval as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . $str;
        }
    };
}
/**
 * 根据WP设置显示日期时间。
 *
 * @param  integer|string   $datetime   DateTime或UNIX时间戳。
 * @param  boolean          $time       如果要显示时间部分，则为True。
 * @return string                       格式化的日期时间。
 * --------------------------------------------------------------------------
 */
function io_date_time( $datetime, $time = true ) {
    if( ! is_numeric($datetime) ) {
        $datetime = strtotime($datetime);
    }
    $date_time_format = get_option( 'date_format' );
    if( $time ) {
        $date_time_format .= ' ';
        $date_time_format .= get_option( 'time_format' );
    }
    return date( $date_time_format, $datetime );
}
# 评论高亮作者
# --------------------------------------------------------------------
function is_master($email = '') {
    if( empty($email) ) return;
    $handsome = array( '1' => ' ', );
    $adminEmail = get_option( 'admin_email' );
    if( $email == $adminEmail ||  in_array( $email, $handsome )  )
    return '<span class="is-author"  data-toggle="tooltip" data-placement="right" title="'.__('博主','i_theme').'"><i class="iconfont icon-user icon-fw"></i></span>';
}
/**
 * 首页标签图标,菜单图标
 * @description: 
 * @param string $terms   分类法
 * @param array $mid      分类对象
 * @param string $default 默认图标
 * @return string
 */
function get_tag_ico($terms, $mid, $default='iconfont icon-tag'){
    $icon = $default; 
    if(!is_array($mid))
        return $icon; 
    if(!io_get_option('same_ico',false) && $terms!='' ){
        if($terms == "favorites") { 
            $icon = 'iconfont icon-tag'; 
        } elseif($terms == "apps") { 
            $icon = 'iconfont icon-app'; 
        } elseif($terms == "books") { 
            $icon = 'iconfont icon-book'; 
        } elseif($terms == "category") {
            $icon = 'iconfont icon-publish';
        } else { 
            $icon = $default;
        }
    }else{
        if( isset($mid['ID']) || (isset($mid['classes'])&&is_array($mid['classes'])) ){
            if(!$icon = get_post_meta( $mid['ID'], 'menu_ico', true )){
                $classes = preg_grep( '/^(fa[b|s]?|io)(-\S+)?$/i', $mid['classes'] );
                if( !empty( $classes ) ){
                    $icon = implode(" ",$mid['classes']);
                }else{
                    $icon = $default;
                }
            }
        }
    }
    return $icon;
}
# 评论头衔
# --------------------------------------------------------------------
function site_rank( $comment_author_email, $user_id ) {
    $adminEmail = get_option( 'admin_email' );
    if($comment_author_email ==$adminEmail) 
        return;

    $rank = io_get_user_cap_string($user_id);
    return $rank = '<span class="rank" title="'.__('头衔：','i_theme') . $rank .'">'. $rank .'</span>';

    //$v1 = 'Vip1';
    //$v2 = 'Vip2';
    //$v3 = 'Vip3';
    //$v4 = 'Vip4';
    //$v5 = 'Vip5';
    //$v6 = 'Vip6'; 
    //global $wpdb;
    //$num = count( $wpdb->get_results( "SELECT comment_ID as author_count FROM $wpdb->comments WHERE comment_author_email = '$comment_author_email' " ) );
    //
    //if ( $num > 0 && $num < 6 ) {
    //    $rank = $v1;
    //}
    //elseif ( $num > 5 && $num < 11 ) {
    //    $rank = $v2;
    //}
    //elseif ( $num > 10 && $num < 16 ) {
    //    $rank = $v3;
    //}
    //elseif ($num > 15 && $num < 21) {
    //    $rank = $v4;
    //}
    //elseif ( $num > 20 && $num < 26 ) {
    //    $rank = $v5;
    //}
    //elseif ( $num > 25 ) {
    //    $rank = $v6;
    //}

    //if( $comment_author_email != $adminEmail )
    //    return $rank = '<span class="rank" data-toggle="tooltip" data-placement="right" title="'.__('头衔：','i_theme') . $rank .'，'.__('累计评论：','i_theme') . $num .'">'. $rank .'</span>';
}
# 评论格式
# --------------------------------------------------------------------
if(!function_exists('io_comment_default_format')){
    function io_comment_default_format($comment, $args, $depth){
        $GLOBALS['comment'] = $comment;
        ?>
        <li <?php comment_class('comment'); ?> id="li-comment-<?php comment_ID() ?>">
            <div id="comment-<?php comment_ID(); ?>" class="comment_body d-flex flex-fill">    
                <div class="profile mr-2 mr-md-3"> 
                    <?php 
                    echo  get_avatar( $comment, 96, '', get_comment_author() );
                    ?>
                </div>                    
                <section class="comment-text d-flex flex-fill flex-column">
                    <div class="comment-info d-flex align-items-center mb-1">
                        <div class="comment-author text-sm w-100"><?php comment_author_link(); ?>
                        <?php echo is_master( $comment->comment_author_email ); echo site_rank( $comment->comment_author_email, $comment->user_id ); ?>
                        </div>                                        
                    </div>
                    <div class="comment-content d-inline-block text-sm">
                        <?php comment_text(); ?> 
                        <?php
                        if ($comment->comment_approved == '0'){
                            echo '<span class="cl-approved">('.__('您的评论需要审核后才能显示！','i_theme').')</span><br />';
                        } 
                        ?>
                    </div>
                    <div class="d-flex flex-fill text-xs text-muted pt-2">
                        <div class="comment-meta">
                            <span class="info mr-2"><i class="iconfont icon-time mr-1"></i><time itemprop="datePublished" datetime="<?php echo get_comment_date( 'c' );?>"><?php echo timeago(get_comment_date('Y-m-d G:i:s'));?></time></span>
                            <?php if(io_get_option('ip_location',false,'comment')){ ?>
                            <span class="info-location"><i class="iconfont icon-location mr-1"></i><?php echo io_get_ip_location(get_comment_author_ip()) ?></span>
                            <?php } ?>
                        </div>
                        <div class="flex-fill"></div>
                        <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                    </div>
                </section>
            </div>
        <?php
    }
}
/**
 * 禁止冒充管理员评论
 * ******************************************************************************************************
 */
function usercheck($incoming_comment) {
    $isSpam = false;
    $administrator = io_get_option( 'io_administrator', array( 'admin_name'=>'', 'admin_email'=>'') );
    if (trim($incoming_comment['comment_author']) == $administrator['admin_name'] )
        $isSpam = true;
    if (trim($incoming_comment['comment_author_email']) == $administrator['admin_email'] )
        $isSpam = true;
    if(!$isSpam)
        return $incoming_comment;
    io_error('{"status":3,"msg":"'.__('请勿冒充管理员发表评论！' , 'i_theme' ).'"}', true);
}
if (!is_user_logged_in()) { add_filter('preprocess_comment', 'usercheck'); }
/**
 * 过滤纯英文、日文和一些其他内容
 * ******************************************************************************************************
 */
function io_refused_spam_comments($comment_data) {
    $pattern = '/[一-龥]/u';
    $jpattern = '/[ぁ-ん]+|[ァ-ヴ]+/u';
    $links = '/http:\/\/|https:\/\/|www\./u';
    $commentset = io_get_option('io_comment_set',array('no_url' => true, 'no_chinese' => false,));
    if ($commentset['no_url'] && (preg_match($links, $comment_data['comment_author']) || preg_match($links, $comment_data['comment_content']))) {
        io_error('{"status":3,"msg":"'.__('别啊，昵称和评论里面添加链接会怀孕的哟！！' , 'i_theme').'"}', true);
    }
    if($commentset['no_chinese']){
        if (!preg_match($pattern, $comment_data['comment_content'])) {
            io_error('{"status":3,"msg":"'.__('评论必须含中文！' , 'i_theme' ).'"}', true);
        }
        if (preg_match($jpattern, $comment_data['comment_content'])) {
            io_error('{"status":3,"msg":"'.__('评论必须含中文！' , 'i_theme' ).'"}', true);
        }
    }
    if (wp_check_comment_disallowed_list($comment_data['comment_author'], $comment_data['comment_author_email'], $comment_data['comment_author_url'], $comment_data['comment_content'], isset($comment_data['comment_author_IP']), isset($comment_data['comment_agent']))) {
        header("Content-type: text/html; charset=utf-8");
        io_error('{"status":3,"msg":"'.sprintf(__('不好意思，您的评论违反了%s博客评论规则' , 'i_theme'), get_option('blogname')).'"}', true);
    }
    return ($comment_data);
}
add_filter('preprocess_comment', 'io_refused_spam_comments');
/**
 * 禁止评论自动超链接
 * ******************************************************************************************************
 */
remove_filter('comment_text', 'make_clickable', 9);   
/**
 * 屏蔽长链接转垃圾评论
 * ******************************************************************************************************
 */
function lang_url_spamcheck($approved, $commentdata) {
    return (strlen($commentdata['comment_author_url']) > 50) ? 'spam' : $approved;
}
add_filter('pre_comment_approved', 'lang_url_spamcheck', 99, 2);


if ( ! function_exists( 'filter_pre_get_posts' ) ):
# 归档页显示数量单独设置
# --------------------------------------------------------------------
function filter_pre_get_posts( $query ){
    if ( $query->is_main_query() ){
        $num = '';  
        $meta = '';  
        $home_sort = io_get_option('home_sort',array(
            'favorites'   => '_sites_order',
            'apps'        => 'modified',
            'books'       => 'modified',
            'category'    => 'date'
        ));
        if ( is_tax('favorites') ){ $num = io_get_option('site_archive_n',12)?:''; $meta = $home_sort['favorites']; } 
        if ( is_tax('sitetag') ){ $num = io_get_option('site_archive_n',12)?:''; $meta = $home_sort['favorites']; } 
        if ( is_tax('apps') ){ $num = io_get_option('app_archive_n',12)?:''; $meta = $home_sort['apps']; } 
        if ( is_tax('apptag') ){ $num = io_get_option('app_archive_n',12)?:''; $meta = $home_sort['apps']; } 
        if ( is_tax('books') ){ $num = io_get_option('book_archive_n',12)?:''; $meta = $home_sort['books']; } 
        if ( is_tax('booktag') ){ $num = io_get_option('book_archive_n',12)?:''; $meta = $home_sort['books']; } 
        if ( is_tax('series') ){ $num = io_get_option('book_archive_n',12)?:''; $meta = $home_sort['books']; } 
        
        if ( '' != $num ){ $query->set( 'posts_per_page', $num ); }

        if( '' != $meta ){
            if( $meta=="views" || $meta=="_sites_order" || $meta=="_down_count" ){
                if($meta=="_sites_order"&& io_get_option('sites_sortable',false)){
                    $query->set( 'orderby',  array( 'menu_order' => 'ASC', 'ID' => 'DESC' )  ); 
                }else{
                    $query->set( 'meta_key', $meta );
                    $query->set( 'orderby', array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ) );
                }
            }else{
                $query->set( 'orderby', $meta );
                $query->set( 'order', 'DESC' );
            }
        }
    }
}
endif;
/**
 * 首页置顶靠前
 * 
 * @param * $myposts
 * @param * $post_type
 * @param * $taxonomy
 * @param * $terms
 * @return {*}
 */
function sticky_posts_to_top($myposts,$post_type,$taxonomy,$terms){
    $sticky_posts = get_option( 'sticky_posts' );
    if (is_array( $sticky_posts ) && ! empty( $sticky_posts ) ) {
        $num_posts     = count( $myposts->posts );
        $sticky_offset = 0;
        // 循环文章，将置顶文章移到最前面。
        for ( $i = 0; $i < $num_posts; $i++ ) {
            if ( in_array( $myposts->posts[$i]->ID, $sticky_posts, true ) ) {
                $sticky_post = $myposts->posts[$i];
                // 从当前位置移除置顶文章。
                array_splice( $myposts->posts, $i, 1 );
                // 移到前面，在其他置顶文章之后。
                array_splice( $myposts->posts, $sticky_offset, 0, array( $sticky_post ) );
                // 增加置顶文章偏移量。下一个置顶文章将被放置在此偏移处。
                $sticky_offset++;
                // 从置顶文章数组中删除文章。
                $offset = array_search( $sticky_post->ID, $sticky_posts, true );
                unset( $sticky_posts[$offset] );
            }
        }
    }
    // 获取查询结果中没有的置顶文章
    if ( !empty($sticky_posts) ) { 
        $stickies = get_posts( array(
            'post__in' => $sticky_posts, 
            'post_status' => 'publish',
            'post_type' => $post_type,
            'nopaging' => true,
            'tax_query'           => array(
                array(
                    'taxonomy' => $taxonomy,       
                    'field'    => 'id',            
                    'terms'    => $terms,    
                )
            ),
        ) );
        foreach ( $stickies as $sticky_post ) {
            array_splice( $myposts->posts, $sticky_offset, 0, array( $sticky_post ) );
            $sticky_offset++;
        }
    }

    return $myposts;
}
# 归档页置顶靠前
# --------------------------------------------------------------------
if( io_get_option('show_sticky',false) && io_get_option('category_sticky',false)) add_filter('the_posts',  'category_sticky_to_top' );
function category_sticky_to_top( $posts ) {
    if(is_admin() || is_home() || is_front_page() || !is_main_query() || !is_archive() )
        return $posts; 
    global $wp_query;
    
    if($wp_query->post_count>0)
        return $posts; 
    // 获取所有置顶文章
    $sticky_posts = get_option('sticky_posts');
    if ( $wp_query->query_vars['paged'] <= 1 && !empty($sticky_posts) && is_array($sticky_posts) && !get_query_var('ignore_sticky_posts') ) {
        $stickies1 = get_posts( array( 'post__in' => $sticky_posts ) );
        foreach ( $stickies1 as $sticky_post1 ) { 
            // 判断当前是否分类页 
            if($wp_query->is_category == 1 && !has_category($wp_query->query_vars['cat'], $sticky_post1->ID)) {
              // 去除不属于本分类的置顶文章
                $offset1 = array_search($sticky_post1->ID, $sticky_posts);
                unset( $sticky_posts[$offset1] );
            } 
            // 判断当前是否自定义分类页 
            if($wp_query->is_tax == 1 && !has_term((isset($wp_query->query_vars['term'])?$wp_query->query_vars['term']:$wp_query->query_vars['term_id']),$wp_query->query_vars['taxonomy'], $sticky_post1->ID)) {
                // 去除不属于本分类的置顶文章
                $offset1 = array_search($sticky_post1->ID, $sticky_posts);
                unset( $sticky_posts[$offset1] );
            }  
            if($wp_query->is_tag == 1 && !has_tag($wp_query->query_vars['tag'], $sticky_post1->ID)) {
                // 去除不属于本标签的文章
                $offset1 = array_search($sticky_post1->ID, $sticky_posts);
                unset( $sticky_posts[$offset1] );
            }
            if($wp_query->is_year == 1 && date_i18n('Y', strtotime($sticky_post1->post_date))!=$wp_query->query['m']) {
                // 去除不属于本年份的文章
                $offset1 = array_search($sticky_post1->ID, $sticky_posts);
                unset( $sticky_posts[$offset1] );
            }
            if($wp_query->is_month == 1 && date_i18n('Ym', strtotime($sticky_post1->post_date))!=$wp_query->query['m']) {
                // 去除不属于本月份的文章
                $offset1 = array_search($sticky_post1->ID, $sticky_posts);
                unset( $sticky_posts[$offset1] );
            }
            if($wp_query->is_day == 1 && date_i18n('Ymd', strtotime($sticky_post1->post_date))!=$wp_query->query['m']) {
                // 去除不属于本日期的文章
                $offset1 = array_search($sticky_post1->ID, $sticky_posts);
                unset( $sticky_posts[$offset1] );
            }
            if($wp_query->is_author == 1 && $sticky_post1->post_author != $wp_query->query_vars['author']) {
                // 去除不属于本作者的文章
                $offset1 = array_search($sticky_post1->ID, $sticky_posts);
                unset( $sticky_posts[$offset1] );
            }
        }
        $num_posts = count($posts);
        $sticky_offset = 0;
        // 循环文章，将置顶文章移到最前面。
        for ( $i = 0; $i < $num_posts; $i++ ) {
            if ( in_array($posts[$i]->ID, $sticky_posts) ) {
                $sticky_post = $posts[$i];
                // 从当前位置移除置顶文章。
                array_splice($posts, $i, 1);
                // 移到前面，在其他置顶文章之后。
                array_splice($posts, $sticky_offset, 0, array($sticky_post));
                // 增加置顶文章偏移量。下一个置顶文章将被放置在此偏移处。
                $sticky_offset++;
                // 从置顶文章数组中删除文章。
                $offset = array_search($sticky_post->ID, $sticky_posts);
                unset( $sticky_posts[$offset] );
            }
        }
        // 删除被排除的文章
        if ( !empty($sticky_posts) && !empty($wp_query->query_vars['post__not_in'] ) )
            $sticky_posts = array_diff($sticky_posts, $wp_query->query_vars['post__not_in']);
        // 获取查询结果中没有的置顶文章
        if ( !empty($sticky_posts) ) {
            if( is_tax() ){
                $args = array(
                    'post__in'    => $sticky_posts, 
                    'post_status' => 'publish',
                    'post_type'   => $wp_query->query_vars['post_type'],
                    'nopaging'    => true
                );
                if(isset($wp_query->query_vars['term_id'])){
                    $args['tax_query'] = array(
                            array(
                                'taxonomy' => $wp_query->query_vars['taxonomy'],       
                                'field'    => 'term_id',            
                                'terms'    => $wp_query->query_vars['term_id'],    
                            )
                    );
                }else{
                    $args['tax_query'] = array(
                            array(
                                'taxonomy' => $wp_query->query_vars['taxonomy'],       
                                'field'    => 'slug',            
                                'terms'    => $wp_query->query_vars['term'],    
                            )
                    );
                }
            }else{
                $args = array(
                    'post__in'    => $sticky_posts,
                    'post_status' => 'publish',
                    'post_type'   => $wp_query->query_vars['post_type'],
                    'nopaging'    => true
                );
            }
            $args = apply_filters('io_archive_query_var_filters', $args);
            $stickies = get_posts($args);
            foreach ( $stickies as $sticky_post ) {
                array_splice( $posts, $sticky_offset, 0, array( $sticky_post ) );
                $sticky_offset++;
            }
        }
    }
    return $posts;
}
# 编辑菜单后删除相应菜单缓存
# --------------------------------------------------------------------
add_action( 'wp_update_nav_menu', 'io_delete_menu_cache', 10, 1 );
function io_delete_menu_cache($menu_id) {  
    if (wp_using_ext_object_cache()){
        //$_menu = wp_get_nav_menu_object( $menu_id );
        wp_cache_delete('io_menu_list_'.$menu_id);
        wp_cache_delete('io_menu_list_main_'.$menu_id);
    }
    delete_transient('io_menu_list_'.$menu_id);
    delete_transient('io_menu_list_main_'.$menu_id);
}
# 主题设置项变更排序相关选项后删除缓存
# --------------------------------------------------------------------
add_action( 'csf_io_get_option_saved', 'io_delete_home_post_cache', 10, 2 );
function io_delete_home_post_cache($data,$_this) {  
    if( io_get_option('user_center')                            != $data['user_center']                             || 
        io_get_option('rewrites_types')                         != $data['rewrites_types']                          || 
        io_get_option('rewrites_end')                           != $data['rewrites_end']                            || 
        io_get_option('sites_rewrite')['post']                  != $data['sites_rewrite']['post']                   || 
        io_get_option('sites_rewrite')['taxonomy']              != $data['sites_rewrite']['taxonomy']               || 
        io_get_option('sites_rewrite')['tag']                   != $data['sites_rewrite']['tag']                    || 
        io_get_option('app_rewrite')['post']                    != $data['app_rewrite']['post']                     || 
        io_get_option('app_rewrite')['taxonomy']                != $data['app_rewrite']['taxonomy']                 || 
        io_get_option('app_rewrite')['tag']                     != $data['app_rewrite']['tag']                      || 
        io_get_option('book_rewrite')['post']                   != $data['book_rewrite']['post']                    || 
        io_get_option('book_rewrite')['taxonomy']               != $data['book_rewrite']['taxonomy']                || 
        io_get_option('book_rewrite')['tag']                    != $data['book_rewrite']['tag']                     || 
        io_get_option('ioc_category')                           != $data['ioc_category']                            || 
        io_get_option('rewrites_category_types')['rewrites']    != $data['rewrites_category_types']['rewrites']     || 
        io_get_option('rewrites_category_types')['types']       != $data['rewrites_category_types']['types'])
    {
        //wp_safe_redirect( admin_url( 'options-permalink.php?settings-updated=true' ) );
        io_refresh_rewrite();
        flush_rewrite_rules();
    }
    if (wp_using_ext_object_cache()){
        //添加判断条件
        if( io_get_option('home_sort')['favorites'] != $data['home_sort']['favorites']  || 
            io_get_option('home_sort')['apps']      != $data['home_sort']['apps']       || 
            io_get_option('home_sort')['books']     != $data['home_sort']['books']      || 
            io_get_option('home_sort')['category']  != $data['home_sort']['category']   || 
            io_get_option('show_sticky')            != $data['show_sticky']             || 
            io_get_option('category_sticky')        != $data['category_sticky']         || 
            io_get_option('sites_sortable')         != $data['sites_sortable'])
        {
            wp_cache_flush();
        }else{
            wp_cache_delete('io_options_cache', 'options');
        }
    }
}
/* 
 * 编辑文章排序后删除对应缓存 id
 * --------------------------------------------------------------------
 */
function io_edit_post_delete_home_cache( $terms, $taxonomy='favorites' )
{
    if (wp_using_ext_object_cache()){
        $site_n= io_get_option('card_n',16,$taxonomy);
        $ajax = 'ajax-url';
        //$slug = get_term_by( 'id', $terms, 'favorites')->slug;
        $cache_key      = 'io_home_posts_'.$terms.'_'.$taxonomy.'_'. $site_n.'_';
        $cache_ajax_key = 'io_home_posts_'.$terms.'_'.$taxonomy.'_'. $site_n.'_'.$ajax;
        wp_cache_delete($cache_key,'home-card');
        wp_cache_delete($cache_ajax_key,'home-card');
    }
} 
add_action( "csf_sites_post_meta_save_before", 'io_meta_saved_delete_home_cache_article',10,2 );
function io_meta_saved_delete_home_cache_article( $data, $post_id )
{
    if (wp_using_ext_object_cache()){
        //添加判断条件
        if( get_post_meta($post_id, '_sites_order', true) != $data['_sites_order']){
            // 删除缓存
            $terms = get_the_terms($post_id,'favorites');
            foreach($terms as $term){
                io_edit_post_delete_home_cache($term->term_id,'favorites');
            } 
        }
    }
}
//删除分类后删除对应缓存
add_action("delete_term", "io_delete_term_delete_cache",10,5);
function io_delete_term_delete_cache($term, $tt_id, $taxonomy, $deleted_term, $object_ids){
    io_edit_post_delete_home_cache($tt_id, $taxonomy);
}

# 替换用户链接
# --------------------------------------------------------------------
add_filter('author_link', 'author_link', 10, 2);
function author_link( $link, $author_id) {
    global $wp_rewrite;
    $author_id = (int) $author_id;
    $link = $wp_rewrite->get_author_permastruct();
    if ( empty($link) ) {
        $link = home_url() . '/?author=' . $author_id;
    } else {
        $link = str_replace('%author%', $author_id, $link);
        $link = home_url() . user_trailingslashit($link);
    }
    return $link;
}
add_filter('request', 'author_link_request');
function author_link_request( $query_vars ) {
    if ( array_key_exists( 'author_name', $query_vars ) ) {
        global $wpdb;
        $author_id=$query_vars['author_name'];
        if ( $author_id ) {
            $query_vars['author'] = $author_id;
            unset( $query_vars['author_name'] );    
        }
    }
    return $query_vars;
}
# 屏蔽用户名称类
# --------------------------------------------------------------------
add_filter('comment_class','remove_comment_body_author_class');
add_filter('body_class','remove_comment_body_author_class');
function remove_comment_body_author_class( $classes ) {
    foreach( $classes as $key => $class ) {
    if(strstr($class, "comment-author-")||strstr($class, "author-")) {
            unset( $classes[$key] );
        }
    }
    return $classes;
}
function chack_name($filename){
    $filename     = remove_accents( $filename );
    $special_chars = array( '?', '[', ']', '/', '\\', '=', '<', '>', ':', ';', ',', "'", '"', '$', '#', '*', '(', ')', '~', '`', '!', '{', '}', '%', '+', '’', '«', '»', '”', '“', chr( 0 ) );
    static $utf8_pcre = null;
    if ( ! isset( $utf8_pcre ) ) {
        $utf8_pcre = @preg_match( '/^./u', 'a' );
    }
    if ( !seems_utf8( $filename ) ) {
        $_ext     = pathinfo( $filename, PATHINFO_EXTENSION );
        $_name    = pathinfo( $filename, PATHINFO_FILENAME );
        $filename = sanitize_title_with_dashes( $_name ) . '.' . $_ext;
    }
    if ( $utf8_pcre ) {
        $filename = preg_replace( "#\x{00a0}#siu", ' ', $filename );
    }
    $filename = str_replace( $special_chars, '', $filename );
    $filename = str_replace( array( '%20', '+' ), '', $filename );
    $filename = preg_replace( '/[\r\n\t -]+/', '', $filename );
    return esc_attr($filename);
}
function loading_type($id=0){
    if($id!=0){
        $type = $id;
    }else{
        $type = io_get_option('loading_type','1')?:'rand';
        if($type == 'rand')
            $type = wp_rand(1,7);
    }
    include( get_theme_file_path("/templates/loadfx/loading-{$type}.php") );
}
# 禁止谷歌字体
# --------------------------------------------------------------------
add_action( 'init', 'remove_open_sans' );
function remove_open_sans() {
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );
    wp_enqueue_style('open-sans','');
}
# 字体增加
# --------------------------------------------------------------------
add_filter('tiny_mce_before_init', 'custum_fontfamily');
function custum_fontfamily($initArray){  
    $initArray['font_formats'] = "微软雅黑='微软雅黑';宋体='宋体';黑体='黑体';仿宋='仿宋';楷体='楷体';隶书='隶书';幼圆='幼圆';";  
    return $initArray;  
} 
/**
 * 轮播HTML
 * ******************************************************************************************************
 */
function io_get_swiper( $swiper_data ){
    $output ='<div class="swiper swiper-post-module rounded-xl">
        <div class="swiper-wrapper">';
    foreach ( $swiper_data as $v ) { 
        $output .= '<div class="swiper-slide media media-21x9">';
        $output .= '<a class="media-content media-title-bg" href="'.$v['url'].'" '. new_window($v['is_ad']) .' '.($v['is_ad']?'rel="noopener"':'rel="external noopener nofollow"').' '.get_lazy_img_bg($v['img']).'><span class="carousel-caption d-none d-md-block">'.$v['title'].'</span></a>';
        $output .= '</div>';
    }
        $output .='</div>
        <div class="swiper-pagination carousel-blog"></div>
    </div>';
    return $output;
}
/**
 * 关键词加链接
 * ******************************************************************************************************
 */
if (io_get_option('tag_c', false, 'switcher')) {
    add_filter('the_content','tag_link',8);
    function tag_link($content){
        $option = io_get_option('tag_c',array());
        global $post_type;
        $match_num_from = 1;        //配置：一个关键字少于多少不替换  
        $match_num_to = $option['chain_n'];        //配置：一个关键字最多替换，建议不大于2  
        $tax = array('post_tag','apptag','sitetag','booktag');
        //$post_tags = get_terms( 
        //    array(
        //        'taxonomy'      => $tax, 
        //        'number'        => 256, 
        //        'orderby'       => 'count', 
        //        'order'         => 'DESC', 
        //        'hide_empty'    => true,
        //    )
        //);
        if ( isset($option['tags']) && $custom_tag = $option['tags']) {
            $case = false ? "i" : ""; //配置：忽略大小写 true是开，false是关  
            foreach($custom_tag as $tag) {
                $link = $tag['url'];
                $keyword = $tag['tag'];
                $cleankeyword = stripslashes($keyword);
                $describe = $tag['describe']?:str_replace('%s',addcslashes($cleankeyword, '$'),__('查看与 %s 相关的文章', 'i_theme' ));
                $limit = rand($match_num_from,$match_num_to);
                $content = tag_to_url($link,$cleankeyword,$describe,$limit,$content,$case);
            }
        }
        if($option['auto']){
            $post_tags = get_the_tags(); 
            if ($post_tags) {
                $sort_func = function($a, $b){
                    if ( $a->name == $b->name ) return 0;
                    return ( strlen($a->name) > strlen($b->name) ) ? -1 : 1;
                };
                usort($post_tags, $sort_func);//重新排序
                $case = false ? "i" : ""; //配置：忽略大小写 true是开，false是关  
                foreach($post_tags as $tag) {
                    $link = get_tag_link($tag->term_id);
                    $keyword = $tag->name;
                    $cleankeyword = stripslashes($keyword);
                    $describe = str_replace('%s',addcslashes($cleankeyword, '$'),__('查看与 %s 相关的文章', 'i_theme' ));
                    $limit = rand($match_num_from,$match_num_to);
                    $content = tag_to_url($link,$cleankeyword,$describe,$limit,$content,$case);
                }
            }
        }
        return $content;
    }
}
function tag_to_url($link,$cleankeyword,$describe,$limit,$content,$case,$ex_word=''){
    $url = "<a class=\"external\" href=\"$link\" title=\"".$describe."\"";
    $url .= ' target="_blank"';
    $url .= ">".addcslashes($cleankeyword, '$')."</a>"; 
    $ex_word = preg_quote($cleankeyword, '\''); 
    $content = preg_replace( '|(<a[^>]+>)(.*)<pre.*?>('.$ex_word.')(.*)<\/pre>(</a[^>]*>)|U'.$case, '$1$2%&&&&&%$4$5', $content);//a标签，免混淆处理  
    $content = preg_replace( '|(<img)(.*?)('.$ex_word.')(.*?)(>)|U'.$case, '$1$2%&&&&&%$4$5', $content);//img标签
    $content = preg_replace( '|(\[)(.*?)('.$ex_word.')(.*?)(\])|U'.$case, '$1$2%&&&&&%$4$5', $content);//短代码标签
    $cleankeyword = preg_quote($cleankeyword,'\'');
    $regEx = '\'(?!((<.*?)|(<a.*?)))('. $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
    $content = preg_replace($regEx,$url,$content,$limit);
    $content = str_replace( '%&&&&&%', stripslashes($ex_word), $content);//免混淆还原处理  
    return $content;
}
# 移除 WordPress 文章标题前的“私密/密码保护”提示文字
# --------------------------------------------------------------------
add_filter('private_title_format', 'remove_title_prefix');//私密
add_filter('protected_title_format', 'remove_title_prefix');//密码保护
function remove_title_prefix($content) {
    return '%s';
}
# FancyBox图片灯箱
# --------------------------------------------------------------------
//add_filter('the_content', 'io_fancybox');
function io_fancybox($content){ 
    global $post;
    $title = $post->post_title;
    $pattern = array("/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>(.*?)<\/a>/i","/<img(.*?)src=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>/i");
    $replacement = array('<a$1href=$2$3.$4$5 data-fancybox="images"$6 data-caption="'.$title.'">$7</a>','<a$1href=$2$3.$4$5 data-fancybox="images" data-caption="'.$title.'"><img$1src=$2$3.$4$5$6></a>');
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}
/**
 * 去掉正文图片外围标签p、自动添加 a 标签和 data-original
 * ******************************************************************************************************
 */
function lazyload_fancybox($content) {
    global $post;
    $title = $post->post_title;
    $loadimg_url=get_template_directory_uri().'/images/t.png';
      //判断是否为文章页或者页面
    if(!is_single())
        return $content;
    if(!is_feed()||!is_robots()) {
        $content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '$1$2$3', $content);
        //添加 fancybox
        $pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>(.*?)<\/a>/i";
        $replacement = '<a$1href=$2$3.$4$5 data-fancybox="images" data-caption="'.$title.'"$6>$7</a>';
        $content = preg_replace($pattern, $replacement, $content);
        //添加懒加载
        $imgpattern   = '/<img(.*?)src=[\'|"]([^\'"]+)[\'|"](.*?)>/i';
        //$imgpattern = "/<img(.*?)src=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>/i";
        if(io_get_option('lazyload',false)){
            $imgreplacement = '<img$1data-src="$2" src="'.$loadimg_url.'" alt="'.$title.'"$3>';
        } else {
            $imgreplacement = '<img$1src="$2" alt="'.$title.'"$3>';
        }
        $content = preg_replace($imgpattern,$imgreplacement,$content);
    }
    return $content;
} 
add_filter ('the_content', 'lazyload_fancybox',10);
function find_character($string,$arr){
    preg_match_all('#('.implode('|', $arr).')#', $string, $wordsFound);
    $wordsFound = array_unique($wordsFound[0]);
    if(count($wordsFound) > 0){
        return true;
    }else{
        return false;
    }
}
# 正文外链跳转和自动nofollow
# --------------------------------------------------------------------
add_filter( 'the_content', 'ioc_seo_wl',10);
function ioc_seo_wl( $content ) {
    //$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
    $regexp = "<a(.*?)href=('|\")([^>]*?)('|\")(.*?)>(.*?)<\/a>";
    if(preg_match_all("/$regexp/i", $content, $matches, PREG_SET_ORDER)) { // s 匹配换行
        if( !empty($matches) ) {
            $srcUrl = get_option('siteurl')?:home_url(); 
            for ($i=0; $i < count($matches); $i++)
            { 
                $url = $matches[$i][3];
                if ( "#" !==substr($url, 0, 1) && false === strpos($url,$srcUrl) ) {
                    $_url=$matches[$i][3];
                    if(io_get_option('is_go',false) && is_go_exclude($_url)===false && !preg_match('/\.(jpg|jpeg|png|ico|bmp|gif|tiff)$/i',$_url) && !preg_match('/(ed2k|thunder|Flashget|flashget|qqdl):\/\//i',$_url)) {
                        $_url= go_to($_url);
                    }
                    $tag = '<a'.$matches[$i][1].'href='.$matches[$i][2].$_url.$matches[$i][4].$matches[$i][5].'>';
                    $tag2 = '<a'.$matches[$i][1].'href='.$matches[$i][2].$url.$matches[$i][4].$matches[$i][5].'>';
                    $noFollow = '';
                    $pattern = '/target\s*=\s*"\s*_blank\s*"/';
                    preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
                    if( count($match) < 1 ){
                        $noFollow .= ' target="_blank" ';
                    }
                    $pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
                    preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
                    if( count($match) < 1 ){
                        $noFollow .= ' rel="nofollow noopener" ';
                    }
                    if(strpos($matches[$i][6],'<img') === false){
                        $pattern = '/class\s*=\s*"\s*(.*?)\s*"/';  //追加class的方法-------------------------------------------------------------
                        preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE); 
                        if( count($match) > 0 ){
                            $tag = str_replace($match[1][0],'external '.$match[1][0],$tag); 
                        }else{
                            $noFollow .= ' class="external" ';
                        }
                    }
                    $tag = rtrim ($tag,'>');
                    $tag .= $noFollow.'>';
                    $content = str_replace($tag2,$tag,$content); 
                }
            }
        }
    }
    return $content;
}

# 评论作者链接跳转 or 评论作者链接新窗口打开
# --------------------------------------------------------------------
if (io_get_option('is_go',false)) {
    add_filter('get_comment_author_link', 'comment_author_link_to');
    function comment_author_link_to() {
        $encodeurl = get_comment_author_url();
        $url = go_to($encodeurl);
        $author = get_comment_author();
        if ( empty( $encodeurl ) || 'http://' == $encodeurl )
            return $author;
        else
            return "<a href='$url' target='_blank' rel='nofollow noopener noreferrer' class='url'>$author</a>";
    }
} else {
    add_filter('get_comment_author_link', 'comment_author_link_blank');
    function comment_author_link_blank() {
        $url    = get_comment_author_url();
        $author = get_comment_author();
        if ( empty( $url ) || 'http://' == $url )
            return $author;
        else
            return "<a target='_blank' href='$url' rel='nofollow noopener noreferrer' class='url'>$author</a>";
    }
}
# 定制CSS
# --------------------------------------------------------------------
function modify_css(){
    $css = '';
    if (io_get_option("custom_css",'')) {
        $css .= substr(io_get_option("custom_css",''),0);
    }
    $css .= '.customize-width{max-width:'.io_get_option('h_width',1900).'px}';
    $css .= '.sidebar-nav{width:'.io_get_option('sidebar_width',220).'px}@media (min-width: 768px){.main-content{margin-left:'.io_get_option('sidebar_width',220).'px;}.main-content .page-header{left:'.io_get_option('sidebar_width',220).'px;}}';
    if($css != '')
        echo "<style>" . $css . "</style>";
}

function add_popup(){
    if(is_404() || !io_get_option('enable_popup',false) || is_io_login()) return;
    $popup_set = io_get_option('popup_set',array());
    if( $popup_set['only_home'] && !(is_home() || is_front_page()) ) return;
    //---date_default_timezone_set(TIMEZONE);
    $update_date = $popup_set['logged_show']?strtotime($popup_set['update_date']):'1';
    if( !$popup_set['show'] || (  $popup_set['show'] && ( !isset($_COOKIE['io_popup_tips'])||( isset($_COOKIE['io_popup_tips']) && $_COOKIE['io_popup_tips'] != $update_date ) )  ) ){ 
        if(!$popup_set['valid'] ||( $popup_set['valid'] && validity_inspection($popup_set['begin_time'],$popup_set['end_time']) ) ){
        ?>
        <div id='io-popup-tips' class="io-bomb" data-date='<?php echo $update_date ?>'>
            <div class="io-bomb-overlay"></div>
            <div class="io-bomb-body" style="max-width:<?php echo $popup_set['width'] ?>px">
                <div class="io-bomb-content io-popup-tips-content rounded m-3 p-4 p-md-5 bg-white">
                    <?php if($title=$popup_set['title']){
                        echo '<h3 class="mb-4 pb-md-2 text-center">'.$title.'</h3>';
                    } ?>
                    <div>
                        <?php echo $popup_set['content'] ?>
                    </div>
                </div>
                <div class="btn-close-bomb mt-2 text-center">
                    <i class="iconfont popup-close icon-close-circle"></i>
                </div>
            </div>
            <script>
                var cookieValue='<?php echo $update_date ?>';
                var exdays = <?php echo is_user_logged_in()&&$popup_set['logged_show']?'30':'1' ?>;
                $(document).ready(function(){
                    <?php echo $popup_set['show']?"if(getCookie('io_popup_tips')!=cookieValue)":"" ?>
                    setTimeout(function(){ 
                        $('#io-popup-tips').addClass('io-bomb-open');
                    },<?php echo $popup_set['delay'].'000' ?>);  
                });
                $(document).on('click','.popup-close',function(ev) {
                    $('#io-popup-tips').removeClass('io-bomb-open').addClass('io-bomb-close');
                    <?php echo ($popup_set['show']?'setCookie("io_popup_tips",cookieValue,exdays);':'') ?>
                    setTimeout(function(){
                        $('#io-popup-tips').remove(); 
                    },600);
                });
            </script>
        </div>
    <?php }
    }
}
function io_footer_box() {
    add_popup();
}
add_action( 'wp_footer', 'io_footer_box' );
/**
 * @description: 
 * @param string $begin_time 开始时间 2021/05/20
 * @param string $end_time 结束时间 2021/06/18
 * @return bool  
 */
function validity_inspection($begin_time,$end_time){ 
    $today=date("y-m-d h:i:s",current_time( 'timestamp' ));  
    $state = true;
    if( strtotime($today)<strtotime($begin_time." 00:00:00"))
        $state = false;
    elseif( strtotime($today)>strtotime($end_time." 23:59:59"))
        $state = false;
    return $state;
}
add_action('admin_footer', 'io_win_console', 99);
add_action('wp_footer', 'io_win_console', 99);
function io_win_console(){
    if( !defined( 'WP_CACHE' ) || !WP_CACHE || is_user_logged_in() ){
?>
    <script type="text/javascript">
        console.log("数据库查询：<?php echo get_num_queries(); ?>次 | 页面生成耗时：<?php echo timer_stop(0, 6) . 's'; ?>");
    </script>
<?php
    }
}
# 重写规则
# --------------------------------------------------------------------
add_action('generate_rewrite_rules', 'io_rewrite_rules' );  
if ( ! function_exists( 'io_rewrite_rules' ) ){ 
    function io_rewrite_rules( $wp_rewrite ){
        $lang = io_get_lang_rules();
        $new_rules = array(    
            'go/?$'         => 'index.php?custom_page=go',
            'hotnews/?$'    => 'index.php?custom_page=hotnews',
            'qr/?$'         => 'index.php?custom_page=qr',
            //'login/?$'       => 'index.php?custom_page=login',
        ); //添加翻译规则   
        if($lang){
            $new_rules[$lang . 'go/?$']      = 'index.php?lang=$matches[1]&custom_page=go';
            $new_rules[$lang . 'hotnews/?$'] = 'index.php?lang=$matches[1]&custom_page=hotnews';
            $new_rules[$lang . 'qr/?$']      = 'index.php?lang=$matches[1]&custom_page=qr';
        }
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;   
        //php数组相加   
    }   
} 
add_action('query_vars', 'io_add_query_vars');  
if ( ! function_exists( 'io_add_query_vars' ) ){ 
    function io_add_query_vars($public_query_vars){
        if (!is_admin()) {
            //往数组中添加自定义查询 custom_page 
            $public_query_vars[] = 'custom_page';
        }
        return $public_query_vars;     
    }  
} 
add_action("template_redirect", 'io_template_redirect');   //模板载入规则  
if ( ! function_exists( 'io_template_redirect' ) ){ 
    function io_template_redirect(){   
        global $wp, $wp_query, $wp_rewrite;  
        if( !isset($wp_query->query_vars['custom_page']) )   
            return;    
        $reditect_page =  $wp_query->query_vars['custom_page'];   
        $wp_query->is_home = false;
        switch ($reditect_page) {
            case 'go':
                include(get_theme_file_path('/go.php'));
                die();
            case 'hotnews':
                include(get_theme_file_path('/templates/hot/hot-home.php'));
                die();
            case 'qr':
                // TODO:权限判断？
                //$key = get_query_var('qr_data');
                //if($key && get_transient($key)){
                //    $_d = maybe_unserialize(get_transient($key));
                //    io_show_qrcode($_d['u'],$_d['s'],$_d['m']);
                //}
                $text = urldecode($_GET['text']);
                $size = isset($_GET['size']) ? $_GET['size'] : 256;
                $margin = isset($_GET['margin']) ? $_GET['margin'] : 10;
                io_show_qrcode($text,$size,$margin);
                die();
            //case 'login':
            //    include(get_theme_file_path('/login.php'));
            //    die();
        }
    }
} 
# 激活主题更新重写规则
# --------------------------------------------------------------------
add_action( 'load-themes.php', 'io_flush_rewrite_rules' );   
function io_flush_rewrite_rules() {   
    global $pagenow;   
    if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) )   
        io_refresh_rewrite();   
}
function io_refresh_rewrite()
{
    // 如果启用了memcache等对象缓存，固定链接的重写规则缓存对应清除
    if (wp_using_ext_object_cache()){
        wp_cache_flush();
    }
    // 刷新固定链接
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}
# 搜索只查询文章和网址。
# --------------------------------------------------------------------
//add_filter('pre_get_posts','searchfilter');
function searchfilter($query) {
    //限定对搜索查询和非后台查询设置
    if ($query->is_search && !is_admin() ) {
        $query->set('post_type',array('sites','post','app'));
    }
    return $query;
}
# --------------------------- LEFT JOIN 方法查询自定义字段   -------------------------------------- #

# 修改搜索查询的sql代码，将postmeta表左链接进去。
# --------------------------------------------------------------------
//add_filter('posts_join', 'io_search_join',10,2 );
function io_search_join( $join, $query ) {
    global $wpdb;
    if ( is_search() && $query->is_main_query() && !empty($query->query['s']) ) {
        $join .=' LEFT JOIN '. $wpdb->postmeta . ' AS post_metas ON ' . $wpdb->posts . '.ID = post_metas.post_id ';
    }
    return $join;
}
//add_filter('posts_where', 'io_search_where',10,2);// 在wordpress查询代码中加入自定义字段值的查询。
function io_search_where( $where, $query ) {
    global $pagenow, $wpdb;
    if ( is_search() && $query->is_main_query() && !empty($query->query['s']) ) {
        $meta_key = "'_sites_link','_spare_sites_link','_seo_desc','_sites_sescribe','_app_sescribe','app_down_list','_summary','_books_data','_down_list'";
        $where = preg_replace("/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/","({$wpdb->posts}.post_title LIKE $1) OR ((post_metas.meta_value LIKE $1) AND (post_metas.meta_key IN ({$meta_key})))", $where ); 
    }
    return $where;
}
// 去重
function io_search_distinct( $where, $query) {
    global $wpdb;
    if ( is_search() && $query->is_main_query() && !empty($query->query['s']) )  {
        return 'DISTINCT';
    }
    return $where;
}
//add_filter( "posts_distinct", "io_search_distinct",10,2 );
# --------------------------- LEFT JOIN 方法查询自定义字段 END -------------------------------------- #

# --------------------------- EXISTS 方法查询自定义字段 --------------------------------------------- #
// EXISTS 方法查询自定义字段
add_action('posts_search', 'io_posts_search_where_exists',10,2);
function io_posts_search_where_exists($search, $query){
    global $wpdb; 
    if (is_search() && $query->is_main_query() && !empty($query->query['s'])) {
        $meta_key = "'_sites_link','_spare_sites_link','_seo_desc','_sites_sescribe','_app_sescribe','app_down_list','_summary','_books_data','_down_list'";
        $sql = " OR EXISTS (SELECT * FROM {$wpdb->postmeta} WHERE post_id={$wpdb->posts}.ID AND meta_key IN ({$meta_key}) AND meta_value like %s)"; 
        $like = '%' . $wpdb->esc_like($query->query['s']) . '%'; 
        $where = $wpdb->prepare($sql, $like);
        $search = preg_replace("/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/","({$wpdb->posts}.post_title LIKE $1) {$where}", $search ); 
    } 
    return $search; 
}
# --------------------------- EXISTS 方法查询自定义字段 END ------------------------------------------ #


function wp_insert_score($comment_ID,$comment_data) { 
    global $iodb;
    $post_ID = $comment_data->comment_post_ID;
    //文章作者ID
    $post_author_id = get_post($post_ID)->post_author;
    //给评论者站内通知
    if($comment_data->comment_parent != 0) { 
        //父级评论者id
        $user_id = get_comment($comment_data->comment_parent)->user_id;
        if($user_id != 0) {
            $iodb->addMessages($user_id,'comment',sprintf( __('%s在「%s」中回复了你', 'i_theme'), $comment_data->comment_author, get_post($post_ID)->post_title), $comment_data->comment_date, $comment_data->comment_content, $comment_data->user_id, $comment_data->comment_author );
        }
    }
    //给文章作者站内通知
    if($post_author_id != $comment_data->user_id){
        if($comment_data->comment_parent != 0){
            $user_id = get_comment($comment_data->comment_parent)->user_id;
            if($user_id != $post_author_id) {
                $iodb->addMessages($post_author_id,'notification',sprintf( __('%s在你的文章「%s」中发表了评论', 'i_theme'), $comment_data->comment_author, get_post($post_ID)->post_title), $comment_data->comment_date, $comment_data->comment_content );
            }
        }else{
            $iodb->addMessages($post_author_id,'notification',sprintf( __('%s在你的文章「%s」中发表了评论', 'i_theme'), $comment_data->comment_author, get_post($post_ID)->post_title), $comment_data->comment_date, $comment_data->comment_content );
        }
    }
}
add_action('wp_insert_comment','wp_insert_score',10,2);
/**
 * 判断是否在微信APP内 
 */
function io_is_wechat_app()
{
    return strripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger');
}

/**
 * 获取当前页面url.
 * TODO: cdn不按协议回源会增加端口号
 */
function io_get_current_url($method = 'wp'){
    if ($method === 'wp') {
        global $wp;
        $url = get_option('permalink_structure') == '' ? add_query_arg($wp->query_string, '', home_url($wp->request) ) : home_url(add_query_arg(array(), $wp->request));
        return $url;
    }

    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $port_str = ($_SERVER['SERVER_PORT'] == '80' && $scheme == 'http') || ($_SERVER['SERVER_PORT'] == '443' && $scheme == 'https') ? '' : ':' . $_SERVER['SERVER_PORT'];
    $url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $port_str . $_SERVER["REQUEST_URI"];
    return esc_url($url);
}

/**
 * 为链接添加重定向链接.
 * --------------------------------------------------------------------------------------
 */
function io_add_redirect($url, $redirect = '')
{
    if ($redirect) {
        return add_query_arg('redirect_to', urlencode($redirect), $url);
    } elseif (isset($_GET['redirect_to'])) {
        return add_query_arg('redirect_to', urlencode(esc_url_raw($_GET['redirect_to'])), $url);
    } elseif (isset($_GET['redirect'])) {
        return add_query_arg('redirect_to', urlencode(esc_url_raw($_GET['redirect'])), $url);
    }

    return add_query_arg('redirect_to', urlencode(home_url()), $url);
}
function is_http($url){
    $preg = "/^http(s)?:\\/\\/.+/";
    if(preg_match($preg,$url)){
        return true;
    }else{
        return false;
    }
}
// 用户注册成功后通知
add_action('user_register','io_register_msg'); 
function io_register_msg($user_id){ 
    io_create_message($user_id,0,'System','notification',sprintf( __('欢迎来到%s，请首先在个人设置中完善您的账号信息。', 'i_theme'), get_bloginfo('name') ));
}

/**
 * 创建消息.
 *
 * @param int    $user_id   接收用户ID
 * @param int    $sender_id 发送者ID(可空)
 * @param string $sender    发送者
 * @param string $type      消息类型 cash|货币 comment|评论 credit|积分 notification|通知 star|收藏
 * @param string $title     消息标题
 * @param string $content   消息内容
 * @param string $status    消息状态
 * @param string $date      消息时间
 *
 * @return bool
 */
function io_create_message($user_id = 0,  $sender_id = 0, $sender = 0, $type = '', $title = '',$content = '', $date = '', $status = 'publish')
{
    $user_id = absint($user_id);
    $sender_id = absint($sender_id);
    $title = sanitize_text_field($title);

    if (!$user_id || empty($title)) {
        return false;
    }

    $type = $type ? sanitize_text_field($type) : 'notification';
    $date = $date ? $date : date('Y-m-d H:i:s',current_time( 'timestamp' ));
    
    $content = htmlspecialchars($content);

    global $iodb; 

    if ($iodb->addMessages($user_id, $type, $title,$date, $content, $sender_id, $sender,$status)) {
        return true;
    }

    return false;
}
/**
 * 添加消息
 * @param  int $user_id 消息对象
 * @param  string $msg_type 消息类型 cash|货币 comment|评论 credit|积分 notification|通知 star|收藏
 * @param  string $msg_title 消息标题
 * @param  string $msg_date 消息时间
 * @param  string $msg_content 消息内容 
 * @param  int $sender_id  发送人 ID 默认 0
 * @param  string $sender 发送人 默认 System
 * @param  string $msg_status 消息状态
 */

if( function_exists('erphp_register_extra_fields') ) {//改--
    add_action( 'register_award', 'io_register_award', 10, 2 );
    function io_register_award($user_id,$money) {  
        global $iodb;
        if($money > 0) { 
            $iodb->addMessages($user_id,'cash',sprintf( __('获得注册奖励%s', 'i_theme'), $money.get_option('ice_name_alipay') ));
        }
    }
    add_action( 'promotion_award', 'io_promotion_award', 10, 3 );
    function io_promotion_award($aff_id,$user_id,$money) {
        global $wpdb,$iodb; 
        $sql = "SELECT $wpdb->users.display_name FROM $wpdb->users WHERE ID = $user_id";
        $user_name = $wpdb->get_var($sql);
        if($money > 0) { 
            $iodb->addMessages($aff_id,'cash',sprintf( __('获得注册推广（来自%s的注册）奖励%s', 'i_theme'),$user_name, $money.get_option('ice_name_alipay') ));
        }
    }
}

function the_post_page() {  
    global $wp_query, $numpages;
    if(isset( $wp_query->query_vars[ 'view' ] ) && $wp_query->query_vars[ 'view' ] === 'all'){ 
        echo '<div class="page-nav text-center my-3"><a href="' . get_permalink() . '"><span class="all">分页阅读</span></a></div>';
    } elseif ( 1 < $numpages ) {
        echo '<div class="page-nav text-center my-3">';
        //wp_link_pages(array('before' => '', 'after' => '', 'next_or_number' => 'next', 'previouspagelink' => '<span><i class="iconfont icon-arrow-l"></i></span>', 'nextpagelink' => "")); 
        //wp_link_pages(array('before' => '', 'after' => '', 'next_or_number' => 'number', 'link_before' =>'<span>', 'link_after'=>'</span>'));
        //wp_link_pages(array('before' => '', 'after' => '', 'next_or_number' => 'next', 'previouspagelink' => '', 'nextpagelink' => ' <span><i class="iconfont icon-arrow-r"></i></span>')); 
        echo io_post_paging();
        echo ' <a href="' . get_pagenum_link( 1 ) . ( preg_match( '/\?/', get_pagenum_link( 1 ) ) ? '&' : '?' ) . 'view=all' . '"><span class="all">阅读全文</span></a></div>';
    }
}
/**
 * 文章分页
 * @return string
 */
function io_post_paging() {
    global $wp_rewrite, $wp_query, $numpages, $post;
    $max_page = $numpages;
    $paged    = $wp_query->query_vars['page'];
    $begin = 1;
    $end   = 2;
    if ( $max_page <= 1 ) return ''; 
    if ( empty( $paged ) ) $paged = 1;
    
    $html = '';
    if($paged > 1){
        $url  = io_get_post_page_link($paged - 1);
        $link = '<a href="' . esc_url( $url ) . '" class="post-page-numbers"><span><i class="iconfont icon-arrow-l"></i></span></a>';
        $html .= apply_filters( 'wp_link_pages_link', $link, $paged-1 );
    }
    if ($paged > ($begin+1)) {
        $url = io_get_post_page_link(1);
        $link = '<a href="' . esc_url( $url ) . '" class="post-page-numbers"><span>1</span></a>';
        $html .= $link;
    }
    if ( $paged > ($begin+2) ) $html .= "<span> ... </span>";
    for( $i = $paged - $begin; $i <= $paged + $end; $i++ ) {
        $link = '';
        if ( $i > 0 && $i <= $max_page ) {
            if ($i == $paged) {
                $link = "<span class='post-page-numbers current' aria-current='page'><span>{$i}</span></span>";
            } else {
                $url = io_get_post_page_link($i);
                $link = '<a href="' . esc_url($url) . '" class="post-page-numbers"><span>' . $i . '</span></a>';
            }
        }
        $html .= apply_filters( 'wp_link_pages_link', $link, $i );
    }
    if ( $paged < $max_page - ($end+1) ) $html .= "<span> ... </span>";
    if ( $paged < $max_page - $end ){
        $url = io_get_post_page_link($max_page);
        $link = '<a href="' . esc_url( $url ) . '" class="post-page-numbers"><span>'.$max_page.'</span></a>';
        $html .= apply_filters( 'wp_link_pages_link', $link, $max_page );
    }
    if($paged < $max_page){
        $url = io_get_post_page_link($paged+1);
        $link = '<a href="' . esc_url( $url ) . '" class="post-page-numbers"><span><i class="iconfont icon-arrow-r"></i></span></a>';
        $html .= apply_filters( 'wp_link_pages_link', $link, $paged+1 );
    }
    return $html;
}

function io_get_post_page_link($i){
    global $wp_rewrite, $post;
    if (1 == $i) {
        $url = get_permalink();
    } else {
        if (!get_option('permalink_structure')) {
            $url = add_query_arg('page', $i, get_permalink());
        } elseif ('page' === get_option('show_on_front') && get_option('page_on_front') == $post->ID) {
            $url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
        } else {
            $url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
        }
    }
    return $url;
}

/**
 * 查询IP地址
 * @param mixed $ip
 * @param mixed $level 1国家 2省 3市 4国家加省 5省加市 6详细
 * @return mixed
 */
function io_get_ip_location($ip, $level = ''){
    if (empty($ip))
        return '无记录';
    $option = io_get_option('ip_location', array('level' => 2, 'v4_type' => 'qqwry'));
    $level  = $level ?: (int) $option['level'];
    require_once get_theme_file_path('/inc/classes/ip/function.php');
    $isQQwry = $option['v4_type'] == 'qqwry';
    //$url     = 'http://freeapi.ipip.net/' . $ip;
    $data    = itbdw\Ip\IpLocation::getLocation($ip, $isQQwry);
    if (isset($data['error'])) {
        return '错误：' . $data['msg'];
    }
    switch ($level) {
        case 1:
            $loc = $data['country'];
            break;
        case 2:
            $loc = $data['province'];
            break;
        case 3:
            $loc = $data['city'];
            break;
        case 4:
            $loc = $data['country'] . $data['province'];
            break;
        case 5:
            $loc = $data['province'] . $data['city'];
            break;
        case 6:
            $loc = $data['area'];
            break;
        default:
            $loc = $data['province'];
            break;
    }
    if (empty($loc))
        $loc = '未知';
    return $loc;
}

/**
 * 记录用户登录时间和IP
 */
function user_last_login($user_login){
    $user = get_user_by('login', $user_login);
    $time = current_time('mysql');
    update_user_meta($user->ID, 'last_login', $time);
    $login_ip = IOTOOLS::get_ip();  
    update_user_meta( $user->ID, 'last_login_ip', $login_ip);  
}
add_action('wp_login', 'user_last_login');

/**
 * 判断是否有非法名称
 */
function is_disable_username($name)
{
    $disable_reg_keywords = io_get_option('user_nickname_stint','');
    $disable_reg_keywords = preg_split("/,|，|\s|\n/", $disable_reg_keywords);

    if (!$disable_reg_keywords || !$name) {
        return false;
    }
    foreach ($disable_reg_keywords as $keyword) {
        if (stristr($name, $keyword) || $keyword == $name) {
            return true;
        }
    }
    return false;
}
/**
 * 中文文字计数
 */
function _new_strlen($str, $charset = 'utf-8')
{
    //中文算一个，英文算半个
    return (int)((strlen($str) + mb_strlen($str, $charset)) / 4);
}
/**
 * 判断是否是重复昵称
 */
function io_nicename_exists($name)
{
    $db_name = false;
    if ($name) {
        global $wpdb;
        $db_name = $wpdb->get_var("SELECT id FROM $wpdb->users WHERE `user_nicename`='" . $name . "' OR `display_name`='" . $name . "' ");
        // 查询已登录用户
        $current_user_id = get_current_user_id();
        if($db_name && $current_user_id && $db_name == $current_user_id){
            $db_name = false;
        }
    }
    return $db_name;
}
/**
 * 判断用户名合法 
 * @param string $user_name
 * @param string $logn_in 登录流程
 * @return array
 */
function is_username_legal($user_name, $logn_in = false)
{

    if (!$user_name) {
        return array('error' => 1, 'msg' => '请输入用户名');
    }

    if (_new_strlen($user_name) < 2) {
        return array('error' => 1, 'msg' => '用户名太短');
    }
    if (_new_strlen($user_name) > 10) {
        return array('error' => 1, 'msg' => '用户名太长');
    }
    if (!$logn_in) {
        if (is_numeric($user_name)) {
            return array('error' => 1, 'msg' => '用户名不能为纯数字');
        }
        if (filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
            return array('error' => 1, 'msg' => '请勿使用邮箱帐号作为用户名');
        }
        if (is_disable_username($user_name)) {
            return array('error' => 1, 'msg' => '昵称含保留或非法字符');
        }
        //重复昵称判断
        if (io_get_option('nickname_exists', true)) {
            if (io_nicename_exists($user_name)) {
                return array('error' => 1, 'msg' => '昵称已存在，请换一个试试');
            }
        }
    }

    return array('error' => 0);
}

function my_avatar( $avatar, $id_or_email,  $size = 96, $default = '', $alt = '' ,$args=NULL){  
    if ( is_numeric( $id_or_email ) )
        $user_id = (int) $id_or_email;
    elseif ( is_string( $id_or_email ) && ( $user = get_user_by( 'email', $id_or_email ) ) )
        $user_id = $user->ID;
    elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) )
        $user_id = (int) $id_or_email->user_id;
        
    if ( empty( $user_id ) )
        return $avatar;
    $type = get_user_meta( $user_id, 'avatar_type', true );
    $author_class = is_author( $user_id ) ? ' current-author' : '' ;
    if( empty($type) || 'gravatar' === $type || 'letter' === $type){
        return $avatar;
    }else{
        return "<img alt='" . esc_attr( $alt ) . "' src='" . format_http(esc_url( get_user_meta( $user_id, "{$type}_avatar", true )) ) . "' class='{$args['class']} avatar avatar-{$size}{$author_class} photo' height='{$size}' width='{$size}' />";
    }
}

function format_http($url){  
        $pattern = '@^(?:https?:)?(.*)@i';
        $result = preg_match($pattern, $url, $matches);
        return $matches[1];
} 

/**
 * 显示广告
 * 
 * @param mixed $loc
 * @param mixed $is_tow 
 * @param mixed $begin
 * @param mixed $end
 * @param mixed $echo
 * @return mixed
 */
function show_ad($loc, $is_tow = true, $begin = '<div class="container apd apd-footer">', $end = '</div>', $echo = true){
    $ad_data = io_get_option($loc,array('switch'=>false,'tow'=>false));
    $html    = '';
    if( $ad_data['switch']&&( 
        $ad_data['loc'] === '1' ||
        ($ad_data['loc'] === '3' && !wp_is_mobile() ) || 
        ($ad_data['loc'] === '2' && wp_is_mobile() ) 
    )) {
        if(!$is_tow){
            $html = $begin . stripslashes( $ad_data['content'] ) . $end; 
        }else{
            if( isset($ad_data['tow']) && $ad_data['tow'] ) { 
                $html = '<div class="row mb-4"><div class="apd apd-home col-12 col-xl-6">'. stripslashes( $ad_data['content'] ) .'</div>
                <div class="apd apd-home col-12 col-xl-6 d-none d-xl-block">'. stripslashes( $ad_data['content2'] ) .'</div></div>';     
            } else {
                $html = '<div class="row mb-4"><div class="apd apd-home col-12">'. stripslashes( $ad_data['content'] ) .'</div></div>';
            }
        }
    }
    if ($echo)
        echo $html;
    else
        return $html;
}
/**
 * 根据页面模板获取页面链接
 * 没用就自动创建
 * @param string $template 模板文件名称
 * @param int $is_id 返回文章id
 * @param array $args
 * @return string|int|bool
 */
function io_get_template_page_url($template, $is_id = false, $args = array()){
    $cache_key = $template . ($is_id ? '_is_id' : '');
    $cache = wp_cache_get($cache_key, 'page_url', true);
    if ($cache) return $cache;
    $templates = array(
        'template-blog.php'       => array('博客', 'blog'),
        'template-bulletin.php'   => array('公告列表', 'bulletin'),
        'template-contribute.php' => array('投稿', 'contribute'),
        'template-links.php'      => array('友情链接', 'links'),
        'template-rankings'       => array('排行榜', 'rankings'),
    );
    $templates = array_merge($templates, $args);
    $pages_args = array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $template
    );
    $pages = get_pages($pages_args);
    $page_id = 0;
    if (!empty($pages[0]->ID)) {
        $page_id = $pages[0]->ID;
    } elseif (!empty($templates[$template][0])) {//创建
        $one_page = array(
            'post_title'  => $templates[$template][0],
            'post_name'   => $templates[$template][1],
            'post_status' => 'publish',
            'post_type'   => 'page',
            'post_author' => 1,
        );

        $page_id = wp_insert_post($one_page);
        update_post_meta($page_id, '_wp_page_template', $template);
    }
    if ($page_id) {
        if ($is_id) {
            wp_cache_set($cache_key, $page_id, 'page_url');
            return $page_id;
        }
        $url = esc_url(get_permalink($page_id));
        wp_cache_set($cache_key, $url, 'page_url');
        return $url;
    } else {
        return false;
    }
}

function io_add_sidebar_list_filters($sidebars){
    $pages_args = array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-mininav.php'
    );
    $pages = get_pages($pages_args); 
    foreach($pages as $page){
        $sidebars[] = array(
            'id'            => 'sidebar-page-'.$page->ID,
            'name'          => '* 次级导航侧边栏 - ['.$page->post_title.']',
            'description'   => '显示在“'.$page->post_title.'”的侧边栏。',
        );
    }
    return $sidebars;
}
add_filter('io_sidebar_list_filters', 'io_add_sidebar_list_filters'); 

//主题更新
function io_theme_update_checker(){
    if (io_get_option('update_theme', false)) {
        /**
         * @var string 子主题支持的最大版本
         * HOOK : io_sub_max_version_filters
         */
        $max_v = apply_filters('io_sub_max_version_filters', '');
        require_once get_theme_file_path('/inc/classes/theme.update.checker.class.php');
        new ThemeUpdateChecker(
            'onenav',
            'https://www.iotheme.cn/update/',
            $max_v
        );
    }
}
add_action('init', 'io_theme_update_checker');

/**
 * go跳转广告位
 * @return void
 */
function io_go_page_content_ad_html(){
    $html = show_ad('ad_go_page_content',false);
    echo $html; 
}
add_action('io_go_page_content_ad', 'io_go_page_content_ad_html');




/**
 * 刷新固定连接
 * @return void
 */
function io_admin_init(){
    if (isset($_REQUEST['page']) &&isset($_REQUEST['action']) && 'theme_settings' === $_REQUEST['page'] && 'rewrite_rules' === $_REQUEST['action']) {
        flush_rewrite_rules();
    }
}
add_action('admin_init', 'io_admin_init', 1);

/**
 * 编辑器js代码禁止压缩
 */
add_action('before_wp_tiny_mce','io_before_wp_tiny_mce');
add_action('after_wp_tiny_mce','io_after_wp_tiny_mce');
function io_before_wp_tiny_mce(){
    echo '<!--wp-compress-html--><!--wp-compress-html no compression-->';
}
function io_after_wp_tiny_mce(){
    echo '<!--wp-compress-html no compression--><!--wp-compress-html-->';
}
