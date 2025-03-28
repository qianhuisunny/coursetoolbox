<?php 
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-03-01 10:19:06
 * @LastEditors: iowen
 * @LastEditTime: 2024-04-11 17:00:55
 * @FilePath: /onenav/inc/theme-start.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * 注册边栏.
 *
 * @since   2.0.0
 */
function io_register_sidebars(){
    $sidebars = array(
        array(
            'id'            => 'sidebar-index',
            'name'          => '首页侧边栏',
            'description'   => '显示在首页的侧边栏，留空不显示。',
        ),
        array(
            'id'            => 'sidebar-h',
            'name'          => '博客布局侧边栏',
            'description'   => '显示在博客模板侧边栏，留空不显示。',
        ),
        array(
            'id'            => 'sidebar-s',
            'name'          => '文章正文侧边栏',
            'description'   => '显示在文章正文侧边栏',
        ),
        array(
            'id'            => 'sidebar-page',
            'name'          => '页面侧边栏',
            'description'   => '显示在页面侧边栏',
        ),
        array(
            'id'            => 'sidebar-a',
            'name'          => '分类归档侧边栏',
            'description'   => '显示在分类归档页、搜索、404页侧边栏',
        ),
        array(
            'id'            => 'sidebar-sites-t',
            'name'          => '网站详情页顶部小工具',
            'description'   => '注意：只能使用“★ 随机内容 ★”小工具。',
        ),
        array(
            'id'            => 'sidebar-sites-r',
            'name'          => '网站详情页侧边栏',
            'description'   => '显示在网站详情页侧边栏',
        ),
        array(
            'id'            => 'sidebar-app-r',
            'name'          => '软件&资源详情页侧边栏',
            'description'   => '显示在软件&资源详情页侧边栏',
        ),
        array(
            'id'            => 'sidebar-book-r',
            'name'          => '书籍&影视详情页侧边栏',
            'description'   => '显示在书籍&影视详情页侧边栏',
        ),
        array(
            'id'            => 'sidebar-bulletin-r',
            'name'          => '公告详情页侧边栏',
            'description'   => '显示在公告详情页侧边栏',
        ),
        array(
            'id'            => 'sidebar-bull',
            'name'          => '公告归档页侧边栏',
            'description'   => '显示在公告归档页侧边栏',
        ),
        array(
            'id'            => 'sidebar-tab-sites',
            'name'          => '首页[Tab 工具]模块侧边栏',
            'description'   => '显示在首页[Tab 工具]模块的侧边栏',
        ),
    );
    /*
     * HOOK 过滤钩子
     * io_sidebar_list_filters
     * 
     * 自定义文章侧边栏ID规则 sidebar-{post_type}-r
     */
    $sidebars = apply_filters('io_sidebar_list_filters', $sidebars); 
    foreach ($sidebars as $value) {
        register_sidebar(
            array(
                'name'          => $value['name'],
                'id'            => $value['id'],
                'description'   => $value['description'],
                'before_widget' => '<div id="%1$s" class="card io-sidebar-widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<div class="card-header widget-header"><h3 class="text-md mb-0">',
                'after_title'   => '</h3></div>',
            )
        );
    }
}
add_action('widgets_init', 'io_register_sidebars');

# 注册菜单
# --------------------------------------------------------------------
io_register_menus();
function io_register_menus(){
    $navs=array(
        'nav_menu'    => '侧栏主菜单' ,
        'nav_main'    => '侧栏底部菜单',
        'main_menu'   => '顶部菜单',
        'search_menu' => '搜索推荐',
    );
    /*
     * HOOK 过滤钩子
     * io_nav_list_filters
     */
    $navs = apply_filters('io_nav_list_filters', $navs);
    register_nav_menus($navs);
}
function io_theme_languages_setup(){
    if ('zh_CN' != get_locale()) {
        load_theme_textdomain('i_theme', get_template_directory() . '/languages');
        if (is_admin()) {
            load_theme_textdomain('io_setting', get_template_directory() . '/languages');
        }
    }
}
add_action('after_setup_theme', 'io_theme_languages_setup');

function io_theme_locale($locale){
    switch ($locale) {
        case 'en_AU':
        case 'en_GB':
        case 'en_US':
            $locale = 'en';
            break;
    }
    return $locale;
}
//add_action('determine_locale', 'io_theme_locale',10 ,1);

/**
 * 启用主题后进仪表盘 
 */
add_action('load-themes.php', 'Init_theme');
function Init_theme(){
    //强制启用伪静态
    if (!get_option('permalink_structure')) {
        update_option('permalink_structure', '/%post_id%.html');
    }
    global $pagenow;
    if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
        initialization();
        update_option( 'thumbnail_size_w',0 );
        update_option( 'thumbnail_size_h', 0 );
        update_option( 'thumbnail_crop', 0 );
        update_option( 'medium_size_w',0 );
        update_option( 'medium_size_h', 0 );
        update_option( 'large_size_w',0 );
        update_option( 'large_size_h', 0 );
        wp_redirect( admin_url( '/admin.php?page=theme_settings' ) );
        exit;
    }
}
# 支持自定义功能
# ------------------------------------------------------------------------------
if(!get_option('permalink_structure'))
add_action( 'admin_notices', 'webstacks_init_check' );
function webstacks_init_check(){
    $html = '<div id="notice-warning-tgmpa" class="notice notice-warning is-dismissible" style="padding: 20px 12px;background-color: #ffeacf;">
                <p>
                    <b>警告：</b> 站点固定链接没有设置，请前往设置为非第一项的选项，推荐 “/%post_id%.html”。 
                    <a href="'.admin_url( 'options-permalink.php' ).'"> 立即前往设置</a>
                </p>
            </div>';
    echo $html;
}
//add_action( 'after_switch_theme', 'active_webstacks_notice');
function active_webstacks_notice() {
    $notice = '<div id="setting-error-tgmpa" class="notice notice-info is-dismissible"> 
				<p>
					<b>通知：</b> onenav 主题已激活，鉴于之前很多用户使用时都遇到了问题，请您先去 
                    <a href="'.admin_url( 'index.php' ).'">仪表盘</a>仔细阅读使用说明，谢谢！ 
                </p> 
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">忽略此通知。</span></button> 
        </div>';
    echo $notice;
}
function get_root_host($url){
    $url = strtolower($url);
    $hosts = parse_url($url);
    $host = isset($hosts['host'])?:$url;
    $data = explode('.', $host);
    $n = count($data);
    $preg = '/[\w].+\.(com|net|org|gov|edu)\.cn$/';
    if(($n > 2) && preg_match($preg,$host)){
        $host = $data[$n-3].'.'.$data[$n-2].'.'.$data[$n-1];
    }else{
        $host = $data[$n-2].'.'.$data[$n-1];
    }
    return $host;
}

global $wpdb;
$wpdb->iomessages   = $wpdb->prefix . 'io_messages';
$wpdb->iocustomurl  = $wpdb->prefix . 'io_custom_url';
$wpdb->iocustomterm = $wpdb->prefix . 'io_custom_term';
$wpdb->ioviews      = $wpdb->prefix . 'io_views';
$wpdb->iopayorder   = $wpdb->prefix . 'io_pay_order';

require get_theme_file_path('/inc/primary.php'); 
require get_theme_file_path('/inc/classes/sms.class.php'); 
require get_theme_file_path('/vendor/autoload.php'); 
require get_theme_file_path('/inc/classes/iodb.class.php'); 
require get_theme_file_path('/inc/classes/io.view.class.php');
require get_theme_file_path('/inc/theme-update.php'); 
require get_theme_file_path('/inc/classes/menuico.class.php'); 
require get_theme_file_path('/inc/inc.php'); 
require get_theme_file_path('/iopay/functions.php'); 
require get_theme_file_path('/inc/functions/functions.php'); 
require get_theme_file_path('/inc/admin/functions.php'); 
require get_theme_file_path('/inc/meta-menu.php'); 
require get_theme_file_path('/inc/hot-search-option.php'); 
if(io_get_option('custom_search',false)) require get_theme_file_path('/inc/search-settings.php'); 
if(io_get_option('site_map',false))      require get_theme_file_path('/inc/classes/do.sitemap.class.php'); 

global $iodb, $ioview; 
$iodb = new IODB();
$ioview = new IOVIEW();

if (!defined('IO_PRO') || !function_exists('isActive') ){
    wp_die('禁止破解！否则冻结订单，享受完整功能与专属服务请<a href="https://www.iotheme.cn" target="_blank">购买正版</a>！', '禁止破解！', array('response'=>403));
}
getAlibabaIco('ico');
function get_assets_path(){
    global $assets_path;
    if(!$assets_path){
        $_min = (WP_DEBUG === true?'':'.min');
        $assets = array(
            'font-awesome'      => '//lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/font-awesome/5.15.4/css/all.min.css',
            'font-awesome4'     => '//lf6-cdn-tos.bytecdntp.com/cdn/expire-1-M/font-awesome/5.15.4/css/v4-shims.min.css',
            //移除本地fa图标
            //'font-awesome'      => get_theme_file_uri('/css/all.min.css'),
            //'font-awesome4'     => get_theme_file_uri('/css/v4-shims.min.css'),
            'iconfont'          => get_theme_file_uri('/css/iconfont.css'),
            'style'             => get_theme_file_uri('/css/style'.$_min.'.css'),
            'bootstrap'         => get_theme_file_uri('/css/bootstrap.min.css'),                  //cdn
            'swiper'            => get_theme_file_uri('/css/swiper-bundle.min.css'),                      //cdn
            'lightbox'          => get_theme_file_uri('/css/jquery.fancybox.min.css'),                    //cdn

            'jquery'            => get_theme_file_uri('/js/jquery.min.js'),
            //'clipboard-mini'    => get_theme_file_uri('/js/clipboard.min.js'),                      //cdn
            'popper'            => get_theme_file_uri('/js/popper.min.js'),                 //cdn
            'bootstrap-js'      => get_theme_file_uri('/js/bootstrap.min.js'),              //cdn
            'swiper-js'         => get_theme_file_uri('/js/swiper-bundle.min.js'),                  //cdn
            'sidebar'           => get_theme_file_uri('/js/theia-sticky-sidebar.js'),       //cdn
            'lightbox-js'       => get_theme_file_uri('/js/jquery.fancybox.min.js'),        //cdn
            'comments-ajax'     => get_theme_file_uri('/js/comments-ajax.js'),
            'color-thief'       => get_theme_file_uri('/js/color-thief.umd.js'),
            'appjs'             => get_theme_file_uri('/js/app'.$_min.'.js'),
            'bookmark'          => get_theme_file_uri('/js/bookmark.js'),
            'lazyload'          => get_theme_file_uri('/js/lazyload.min.js'),
            'echarts'           => get_theme_file_uri('/js/echarts.min.js'),
            'sites-chart'       => get_theme_file_uri('/js/sites-chart.js'),

            'captcha'           => get_theme_file_uri('/js/captcha.js'), //验证码
            'new-post'          => get_theme_file_uri('/js/new-post.js'), //投稿

            'jquery-ui'         => '',
            'jquery-touch'      => '',
        );
        switch(io_get_option('cdn_resources','bytecdntp')){
            case 'jsdelivr':
                $jsdelivr_cdn = io_get_option('jsdelivr-cdn','')?:'cdn.jsdelivr.net';
                $js_v = '1.5811';
                $assets[ 'font-awesome' ]   = '//'.$jsdelivr_cdn.'/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css';
                $assets[ 'font-awesome4' ]  = '//'.$jsdelivr_cdn.'/npm/@fortawesome/fontawesome-free@5.15.4/css/v4-shims.min.css';
                $assets[ 'iconfont' ]       = '//'.$jsdelivr_cdn.'/gh/owen0o0/ioStaticResources@'.$js_v.'/onenav/css/iconfont.css';
                $assets[ 'style' ]          = '//'.$jsdelivr_cdn.'/gh/owen0o0/ioStaticResources@'.$js_v.'/onenav/css/style.min.css';
                $assets[ 'bootstrap' ]      = '//'.$jsdelivr_cdn.'/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css';
                $assets[ 'swiper' ]         = '//'.$jsdelivr_cdn.'/npm/swiper@7.3.0/swiper-bundle.min.css';
                $assets[ 'lightbox' ]       = '//'.$jsdelivr_cdn.'/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css';

                $assets[ 'jquery' ]         = '//'.$jsdelivr_cdn.'/npm/jquery@3.5.1/dist/jquery.min.js';
                //$assets[ 'clipboard-mini' ] = '//'.$jsdelivr_cdn.'/npm/clipboard@2.0.10/dist/clipboard.min.js';
                $assets[ 'popper' ]         = '//'.$jsdelivr_cdn.'/npm/popper.js@1.16.0/dist/umd/popper.min.js';
                $assets[ 'bootstrap-js' ]   = '//'.$jsdelivr_cdn.'/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js';
                $assets[ 'swiper-js' ]      = '//'.$jsdelivr_cdn.'/npm/swiper@7.3.0/swiper-bundle.min.js';
                $assets[ 'sidebar' ]        = '//'.$jsdelivr_cdn.'/npm/theia-sticky-sidebar@1.7.0/dist/theia-sticky-sidebar.min.js';
                $assets[ 'lightbox-js' ]    = '//'.$jsdelivr_cdn.'/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js';
                $assets[ 'comments-ajax' ]  = '//'.$jsdelivr_cdn.'/gh/owen0o0/ioStaticResources@'.$js_v.'/onenav/js/comments-ajax.js';
                $assets[ 'color-thief' ]    = '//'.$jsdelivr_cdn.'/gh/owen0o0/ioStaticResources@'.$js_v.'/onenav/js/color-thief.umd.js';
                $assets[ 'appjs' ]          = '//'.$jsdelivr_cdn.'/gh/owen0o0/ioStaticResources@'.$js_v.'/onenav/js/app.min.js';
                $assets[ 'bookmark' ]       = '//'.$jsdelivr_cdn.'/gh/owen0o0/ioStaticResources@'.$js_v.'/onenav/js/bookmark.js';
                $assets[ 'lazyload' ]       = '//'.$jsdelivr_cdn.'/gh/owen0o0/ioStaticResources@'.$js_v.'/onenav/js/lazyload.min.js';
                $assets[ 'echarts' ]        = '//'.$jsdelivr_cdn.'/npm/echarts@5/dist/echarts.min.js';
    
                $assets[ 'jquery-ui' ]      = '//'.$jsdelivr_cdn.'/npm/jquery-ui-dist@1.12.1/jquery-ui.min.js';
                $assets[ 'jquery-touch' ]   = '//'.$jsdelivr_cdn.'/npm/jquery-ui-touch-punch@0.2.3/jquery.ui.touch-punch.min.js';
                break;
            case 'bytecdntp':
                $assets[ 'font-awesome' ]   = '//lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/font-awesome/5.15.4/css/all.min.css';
                $assets[ 'font-awesome4' ]  = '//lf6-cdn-tos.bytecdntp.com/cdn/expire-1-M/font-awesome/5.15.4/css/v4-shims.min.css';
                $assets[ 'bootstrap' ]      = '//lf6-cdn-tos.bytecdntp.com/cdn/expire-1-ms/bootstrap/4.6.1/css/bootstrap.min.css';
                $assets[ 'swiper' ]         = '//lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/Swiper/7.4.1/swiper-bundle.min.css';
                $assets[ 'lightbox' ]       = '//lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/fancybox/3.5.7/jquery.fancybox.min.css';
                
                $assets[ 'jquery' ]         = '//lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/jquery/3.5.1/jquery.min.js';
                //$assets[ 'clipboard-mini' ] = '//lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/clipboard.js/2.0.10/clipboard.min.js';
                $assets[ 'popper' ]         = '//lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/popper.js/1.16.0/umd/popper.min.js';
                $assets[ 'bootstrap-js' ]   = '//lf26-cdn-tos.bytecdntp.com/cdn/expire-1-ms/bootstrap/4.6.1/js/bootstrap.min.js';
                $assets[ 'swiper-js' ]      = '//lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/Swiper/7.4.1/swiper-bundle.min.js';
                $assets[ 'lightbox-js' ]    = '//lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/fancybox/3.5.7/jquery.fancybox.min.js';
                $assets[ 'echarts' ]        = '//lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/echarts/5.3.0/echarts.min.js';
    
                $assets[ 'jquery-ui' ]      = '//lf6-cdn-tos.bytecdntp.com/cdn/expire-1-M/jqueryui/1.12.1/jquery-ui.min.js';
                $assets[ 'jquery-touch' ]   = '//lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js';
                break;
            case 'staticfile':
                $assets[ 'font-awesome' ]   = '//cdn.bootcdn.net/ajax/libs/font-awesome/5.15.4/css/all.min.css';
                $assets[ 'font-awesome4' ]  = '//cdn.bootcdn.net/ajax/libs/font-awesome/5.15.4/css/v4-shims.min.css';
                $assets[ 'bootstrap' ]      = '//cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.1/css/bootstrap.min.css';
                $assets[ 'swiper' ]         = '//cdn.bootcdn.net/ajax/libs/Swiper/7.4.1/swiper-bundle.min.css';
                $assets[ 'lightbox' ]       = '//cdn.bootcdn.net/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css';
            
                $assets[ 'jquery' ]         = '//cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js';
                //$assets[ 'clipboard-mini' ] = '//cdn.bootcdn.net/ajax/libs/clipboard.js/2.0.10/clipboard.min.js';
                $assets[ 'popper' ]         = '//cdn.bootcdn.net/ajax/libs/popper.js/1.16.0/umd/popper.min.js';
                $assets[ 'bootstrap-js' ]   = '//cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.min.js';
                $assets[ 'swiper-js' ]      = '//cdn.bootcdn.net/ajax/libs/Swiper/7.4.1/swiper-bundle.min.js';
                $assets[ 'lightbox-js' ]    = '//cdn.bootcdn.net/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js';
                $assets[ 'echarts' ]        = '//cdn.bootcdn.net/ajax/libs/echarts/5.3.0/echarts.min.js';
    
                $assets[ 'jquery-ui' ]      = '//cdn.bootcdn.net/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js';
                $assets[ 'jquery-touch' ]   = '//cdn.bootcdn.net/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js';
                break;
        }
        $assets_path = $assets;
    }
    return $assets_path;
}
/**
 * 获取静态文件版本
 * @param string $path 
 * @param bool $v 是否添加查询key
 * @return mixed 
 */
function get_assets_version($path, $v=false){
    if (preg_match('/'. str_replace('/', '\/', get_url_root(home_url())) .'/i', $path) && !strstr($path, 'cdn.iocdn.cc')) {
        if($v){
            return '?ver='. IO_VERSION;
        }
        return IO_VERSION;
    }
    return null;
}
function theme_load_scripts() {
    $assets_path = get_assets_path(); 

    if (io_get_option('disable_gutenberg',false)) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('wc-block-style'); // 移除WOO插件区块样式
        wp_dequeue_style('global-styles'); // 移除 THEME.JSON
    }
    wp_deregister_script( 'jquery' );

    wp_register_style( 'font-awesome',              $assets_path['font-awesome'], array(), get_assets_version($assets_path['font-awesome']), 'all'  );
    wp_register_style( 'font-awesome4',             $assets_path['font-awesome4'], array(), get_assets_version($assets_path['font-awesome4']), 'all'  );
    wp_register_style( 'iconfont',                  $assets_path['iconfont'], array(), get_assets_version($assets_path['iconfont']), 'all'  );
    wp_register_style( 'style',                     $assets_path['style'], array(), get_assets_version($assets_path['style']) );
    wp_register_style( 'bootstrap',                 $assets_path['bootstrap'], array(), get_assets_version($assets_path['bootstrap']), 'all'  );                 //cdn
    wp_register_style( 'swiper',                    $assets_path['swiper'], array(), get_assets_version($assets_path['swiper']) );                               //cdn
    wp_register_style( 'lightbox',                  $assets_path['lightbox'], array(), get_assets_version($assets_path['lightbox']) );                           //cdn
    
    wp_register_script( 'jquery',                   $assets_path['jquery'], array(), get_assets_version($assets_path['jquery']) ,false);
    //wp_register_script( 'clipboard-mini',           $assets_path['clipboard-mini'], array(), get_assets_version($assets_path['clipboard-mini']), true );         //cdn
    wp_register_script( 'popper',                   $assets_path['popper'], array('jquery'), get_assets_version($assets_path['popper']), true );                 //cdn
    wp_register_script( 'bootstrap',                $assets_path['bootstrap-js'], array('jquery'), get_assets_version($assets_path['bootstrap-js']), true );     //cdn
    wp_register_script( 'swiper',                   $assets_path['swiper-js'], array(), get_assets_version($assets_path['swiper-js']), true );                   //cdn
    wp_register_script( 'sidebar',                  $assets_path['sidebar'], array('jquery'), get_assets_version($assets_path['sidebar']), true );               //cdn
    wp_register_script( 'lightbox-js',              $assets_path['lightbox-js'], array('jquery'), get_assets_version($assets_path['lightbox-js']), true );       //cdn
    wp_register_script( 'comments-ajax',            $assets_path['comments-ajax'], array('jquery'), get_assets_version($assets_path['comments-ajax']), true );
    wp_register_script( 'color-thief',              $assets_path['color-thief'], array(), get_assets_version($assets_path['color-thief']), true );
    wp_register_script( 'appjs',                    $assets_path['appjs'], array('jquery'), get_assets_version($assets_path['appjs']), true );
    wp_register_script( 'bookmark',                 $assets_path['bookmark'], array('jquery'), get_assets_version($assets_path['bookmark']), true );
    wp_register_script( 'lazyload',                 $assets_path['lazyload'], array('jquery'), get_assets_version($assets_path['lazyload']), true );
    wp_register_script( 'echarts',                  $assets_path['echarts'], array(), get_assets_version($assets_path['echarts']), true );
    wp_register_script( 'sites-chart',              $assets_path['sites-chart'], array('echarts'), get_assets_version($assets_path['sites-chart']), true );

    wp_register_script( 'captcha',                  $assets_path['captcha'], array(), get_assets_version($assets_path['captcha']), true );
    wp_register_script( 'new-post',                 $assets_path['new-post'], array('jquery'), get_assets_version($assets_path['new-post']), true );

    if( 'local'!==io_get_option('cdn_resources','local') ){ //本地调用wp自带js代码
        wp_register_script( 'jquery-ui',            $assets_path['jquery-ui'], array('jquery'), get_assets_version($assets_path['jquery-ui']), true );
        wp_register_script( 'jquery-touch',         $assets_path['jquery-touch'], array('jquery'), get_assets_version($assets_path['jquery-touch']), true );
    }
    if( !is_admin() ){ 
        wp_enqueue_style('iconfont');
        if ( !is_bookmark() && !is_io_login()) {
            if ( io_get_option('is_iconfont',false)) {
                $urls = io_get_option('iconfont_url','');
                $urls = explode(PHP_EOL , $urls);
                $index = 1;
                if(!empty($urls)&&is_array($urls)){
                    foreach($urls as $url){
                        wp_enqueue_style( 'iconfont-io-'.$index,  $url, array(), get_assets_version($url) );
                        $index++;
                    }
                }else{
                    wp_enqueue_style( 'iconfont-io',  $urls, array(), get_assets_version($urls) );
                }
            }else{
                wp_enqueue_style('font-awesome');
                wp_enqueue_style('font-awesome4');
            }
        }
        wp_enqueue_style('bootstrap');
        //add_filter('io_add_lightbox_js', '__return_true');
        if( apply_filters('io_add_lightbox_js', false) || 
            ( io_get_option('hot_iframe',false) && (is_home() || is_front_page() || is_mininav() || get_query_var('custom_page')=='hotnews') ) ||  
            is_single() 
        ){
            wp_enqueue_style('lightbox'); 
        }
        

        wp_enqueue_script('jquery');
        wp_add_inline_script( 'jquery', '/* <![CDATA[ */ 
        function loadFunc(func) {if (document.all){window.attachEvent("onload",func);}else{window.addEventListener("load",func,false);}}   
        /* ]]> */');
        if( ( is_home() ||  is_front_page() || is_io_user() ) && is_user_logged_in() ){
            if( 'local'===io_get_option('cdn_resources','local') ){
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_script('jquery-ui-droppable');
                wp_enqueue_script('jquery-touch-punch');
            }else{
                wp_enqueue_script('jquery-ui');
                wp_enqueue_script('jquery-touch');
            }
        }

        if( apply_filters('io_add_swiper_js', false) || is_home() ||  is_front_page() || is_mininav() ||  is_blog()){
            wp_enqueue_style('swiper');
            wp_enqueue_script('swiper');
        }

        //if(is_single() || is_bookmark()) wp_enqueue_script('clipboard-mini');

        wp_enqueue_script('popper');
        wp_enqueue_script('bootstrap');
        if (!is_io_login()) {
            wp_enqueue_script('sidebar');
            if(io_get_option('lazyload',false)) wp_enqueue_script('lazyload'); 
        }
        if( ( io_get_option('hot_iframe',false) && (is_home() || is_front_page() || is_mininav() || get_query_var('custom_page')=='hotnews') ) ||  is_single() ) wp_enqueue_script('lightbox-js'); 
        
        wp_enqueue_style('style'); 
        wp_enqueue_script('appjs'); 

        if( is_bookmark() ) {
            //wp_enqueue_script('color-thief'); 
            wp_enqueue_script('bookmark'); 
        }

        if( is_singular() && comments_open() ) {
            wp_enqueue_script( 'comment-reply' );
            wp_enqueue_script( 'comments-ajax' );
        }
    }
    $ico_source = io_get_option( 'ico-source', array( "url_format"=>true, "ico_url"=>"https://api.iowen.cn/favicon/", "ico_png"=>".png" ) );
    
    wp_add_inline_script('appjs', '/* <![CDATA[ */ 
    $(document).ready(function(){if($("#search-text")[0]){$("#search-text").focus();}});
    /* ]]> */');
    wp_localize_script('appjs', 'theme' , array(
        'ajaxurl'      => admin_url( 'admin-ajax.php' ),
        'uri'          => get_template_directory_uri(),
        'loginurl'     => esc_url(wp_login_url( io_get_current_url() )),
        'sitesName'    => get_bloginfo('name'),
        'addico'       => get_theme_file_uri('/images/add.png'),
        'order'        => get_option('comment_order'),
        'formpostion'  => 'top', 
        'defaultclass' => io_get_option('theme_mode','io-black-mode')=="io-black-mode"?'':io_get_option('theme_mode','io-black-mode'), 
        'isCustomize'  => io_get_option('customize_card',false),
        'icourl'       => $ico_source['ico_url'],
        'icopng'       => $ico_source['ico_png'],
        'urlformat'    => $ico_source['url_format'],
        'customizemax' => io_get_option('customize_n',10),
        'newWindow'    => io_get_option('new_window',false),
        'lazyload'     => io_get_option('lazyload',false),
        'minNav'       => io_get_option('min_nav',false),
        'loading'      => io_get_option('loading_fx',false),
        'hotWords'     => io_get_option('baidu_hot_words','baidu'),
        'classColumns' => get_columns('sites','',false),
        'apikey'       => ioThemeKey(),
        'isHome'       => ( is_home() || is_front_page() || is_mininav() ),
        'version'      => IO_VERSION,
    )); 
    wp_localize_script('appjs', 'localize' , array(
        'liked'             => __('您已经赞过了!','i_theme'),
        'like'              => __('谢谢点赞!','i_theme'),
        'networkerror'      => __('网络错误 --.','i_theme'),
        'selectCategory'    => __('为什么不选分类。','i_theme'),
        'addSuccess'        => __('添加成功。','i_theme'),
        'timeout'           => __('访问超时，请再试试，或者手动填写。','i_theme'),
        'lightMode'         => __('日间模式','i_theme'),
        'nightMode'         => __('夜间模式','i_theme'),
        'editBtn'           => __('编辑','i_theme'),
        'okBtn'             => __('确定','i_theme'),
        'urlExist'          => __('该网址已经存在了 --.','i_theme'),
        'cancelBtn'         => __('取消','i_theme'),
        'successAlert'      => __('成功','i_theme'),
        'infoAlert'         => __('信息','i_theme'),
        'warningAlert'      => __('警告','i_theme'),
        'errorAlert'        => __('错误','i_theme'),
        'extractionCode'    => __('网盘提取码已复制，点“确定”进入下载页面。','i_theme'),
        'wait'              => __('请稍候','i_theme'),
        'loading'           => __('正在处理请稍后...','i_theme'),
        'userAgreement'     => __('请先阅读并同意用户协议','i_theme'),
        'reSend'            => __('秒后重新发送','i_theme'),
        'weChatPay'         => __('微信支付','i_theme'),
        'alipay'            => __('支付宝','i_theme'),
        'scanQRPay'         => __('请扫码支付','i_theme'),
        'payGoto'           => __('支付成功，页面跳转中','i_theme'),
    ));
}

function add_captcha_js(){
    $captcha = io_get_option('captcha_type','null');
    switch ($captcha) {
        case 'tcaptcha':
            //wp_enqueue_script( 'captcha-007','//ssl.captcha.qq.com/TCaptcha.js',array(),null,true ); //通过js加载
            break;
        case 'geetest':
            wp_enqueue_script('captcha-007', '//static.geetest.com/v4/gt4.js', array(), null, true);
            break;
        case 'vaptcha':
            wp_enqueue_script('captcha-007', '//v-cn.vaptcha.com/v3.js', array(), null, true);
            break;
        case 'slider':
            wp_localize_script('appjs', 'slidercaptcha', array(
                'loading' => __('加载中...', 'i_theme'),
                'retry'   => __('再试一次', 'i_theme'),
                'slider'  => __('向右滑动填充拼图', 'i_theme'),
                'failed'  => __('加载失败', 'i_theme'),
            ));
            wp_enqueue_script('captcha-007', get_theme_file_uri('/js/longbow.slidercaptcha.min.js'), array('jquery'), IO_VERSION, true);
            break;
    }
}
function io_admin_load_scripts($hook) {
    if( !is_admin() )return;
	if( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'toplevel_page_theme_settings' ) {
        wp_register_style( 'add-hot',  get_theme_file_uri('/css/add-hot.css'), array(), IO_VERSION );
        wp_register_script( 'add-hot', get_theme_file_uri('/js/add-hot.js'), array('jquery'), IO_VERSION, true );
        wp_enqueue_style('add-hot'); 
        wp_enqueue_script('add-hot');
        wp_localize_script('add-hot', 'io_news' , array(
            'ajaxurl'      => admin_url( 'admin-ajax.php' ),
            'apikey'       => iowenKey(),
        )); 
    }
}
add_action('admin_enqueue_scripts', 'io_admin_load_scripts');

function io_csf_enqueue(){
    if(io_get_option('is_iconfont',false)){
        //wp_register_style( 'iconfont-io',  io_get_option('iconfont_url',''), array(), '' );
        //wp_enqueue_style('iconfont-io'); 
        $urls = io_get_option('iconfont_url','');
        $urls = explode(PHP_EOL , $urls);
        $index = 1;
        if(!empty($urls)&&is_array($urls)){
            foreach($urls as $url){
                wp_enqueue_style( 'iconfont-io-'.$index,  $url, array(), get_assets_version($url) );
                $index++;
            }
        }else{
            wp_enqueue_style( 'iconfont-io',  $urls, array(), get_assets_version($urls) );
        }
    }
    wp_enqueue_style( 'iconfont', get_theme_file_uri('/css/iconfont.css') , array(), IO_VERSION );
}
add_action('csf_enqueue', 'io_csf_enqueue');

//为编辑器添加全局变量
add_action('wp_enqueue_editor', function () {
    echo '<script type="text/javascript">var mce = {
            is_admin:"' . is_admin() . '",
            ajax_url:"' . esc_url(admin_url('admin-ajax.php')) . '",
            post_img_allow_upload:"' . apply_filters('io_tinymce_upload_img', false) . '",
            post_img_max:"' . io_get_option('post_tg_opt', 1024, 'img_size') . '",
            upload_nonce:"' . wp_create_nonce('edit_file_upload') . '",
            local : {
                post_img_max_msg:"' . sprintf(__('图片大小不能超过 %s kb','i_theme'),io_get_option('post_tg_opt',1024,"img_size")) . '",
            }
        }</script>';
});