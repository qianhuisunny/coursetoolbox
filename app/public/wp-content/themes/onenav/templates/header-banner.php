<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:02
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-04 01:30:08
 * @FilePath: \onenav\templates\header-banner.php
 * @Description: 顶部导航
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 
$big_header = "";
if(is_404() || is_single() || is_search() || (is_page() && !is_mininav()) || get_query_var('custom_page')=="hotnews"){ //不显示搜索工具的页面
    $big_header = "";
}else if(is_mininav() && !get_post_meta( get_the_ID(), 'search_box', true )){ //次级导航未开启搜索工具
    $big_header = "";
}else if(is_author() || get_query_var("is_user_route")){ //顶部菜单需透明
    $big_header = "big-header-banner";
}else{
    $search_big = io_get_option('search_skin','') ? io_get_option('search_skin','') : false;
    if (io_get_option('search_position',array('home')) && in_array("home", io_get_option('search_position',array('home'))) && $search_big && $search_big['search_big']=='1') {
        $big_header = $search_big['big_skin']!="no-bg"?'big-header-banner':'no-big-header';
    }
}

$header_logo        = "";
$mobile_menu_left   = "";
$mobile_menu_right  = '<li class="nav-item d-md-none mobile-menu ml-3 ml-md-4"><a href="javascript:" id="sidebar-switch" data-toggle="modal" data-target="#sidebar"><i class="iconfont icon-classification icon-lg"></i></a></li>';
if(io_get_option('mobile_header_layout','header-center')=='header-center'){
    $header_logo        = "position-absolute w-100 text-center";
    $mobile_menu_left   = '<div class="nav-item d-md-none mobile-menu py-2 position-relative"><a href="javascript:" id="sidebar-switch" data-toggle="modal" data-target="#sidebar"><i class="iconfont icon-classification icon-lg"></i></a></div>';
    $mobile_menu_right  = '';
}
?>
    <div class="<?php echo $big_header ?> header-nav">
        <div id="header" class="page-header sticky">
            <div class="navbar navbar-expand-md">
                <div class="container-fluid p-0 position-relative">
                    <div class="<?php echo $header_logo ?>">
                        <a href="<?php echo esc_url( home_url() ) ?>" class="navbar-brand d-md-none m-0" title="<?php bloginfo('name') ?>">
                            <img src="<?php echo io_get_option('logo_normal_light','') ?>" class="logo-light" alt="<?php bloginfo('name') ?>" height="30">
                            <img src="<?php echo io_get_option('logo_normal','') ?>" class="logo-dark d-none" alt="<?php bloginfo('name') ?>" height="30">
                        </a>
                    </div>
                    <?php echo $mobile_menu_left ?>
                    <div class="collapse navbar-collapse order-2 order-md-1">
                        <div class="header-mini-btn">
                            <label>
                                <input id="mini-button" type="checkbox" <?php echo io_get_option('min_nav',false)?'':'checked="checked"'?>>
                                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"> 
                                    <path class="line--1" d="M0 40h62c18 0 18-20-17 5L31 55"></path>
                                    <path class="line--2" d="M0 50h80"></path>
                                    <path class="line--3" d="M0 60h62c18 0 18 20-17-5L31 45"></path>
                                </svg>
                            </label>
                        
                        </div>
                        <?php if(io_get_option('weather',false) && io_get_option('weather_location','header')=='header'){ ?>
                        <!-- 天气 -->
                        <div class="weather">
                            <?php io_get_weather_widget() ?>
                        </div>
                        <!-- 天气 end -->
                        <?php } ?>
                        <ul class="navbar-nav navbar-top site-menu mr-4">
                            <?php wp_menu('main_menu'); ?> 
                        </ul>
                    </div>
                    <ul class="nav navbar-menu text-xs order-1 order-md-2 position-relative">
                        <?php if(io_get_option('hitokoto',false)){ ?>
                        <!-- 一言 -->
                        <li class="nav-item mr-3 mr-lg-0 d-none d-lg-block">
                            <div class="text-sm overflowClip_1">
                                <?php echo io_get_option('hitokoto_code','') ?>
                            </div>
                        </li>
                        <!-- 一言 end -->
                        <?php } ?>
                        <?php if( io_get_option('nav_login',false) ){ 
                            global $user_ID; 
                            if(!$user_ID) {?>
                            <li class="nav-login ml-3 ml-md-4">
                                <a href="<?php echo esc_url(wp_login_url( io_get_current_url() )) ?>" title="登录"><i class="iconfont icon-user icon-lg"></i></a>
                            </li>
                        <?php }else{
                                get_template_part( 'templates/widget/header', 'user' );
                            }
                        } ?>
                        <?php if( io_get_option('search_position',array('home')) && in_array("top",io_get_option('search_position',array('home'))) ){ ?>
                        <li class="nav-search ml-3 ml-md-4">
                            <a href="javascript:" data-toggle="modal" data-target="#search-modal"><i class="iconfont icon-search icon-lg"></i></a>
                        </li>
                        <?php } ?>
                        <?php echo $mobile_menu_right ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="placeholder"></div>
        <?php 
        /**
         * -----------------------------------------------------------------------
         * HOOK : ACTION HOOK
         * io_content_header_after_code
         * 
         * 在内容顶部菜单下后挂载其他内容
         * @since   
         * -----------------------------------------------------------------------
         */
        do_action( 'io_content_header_after_code' , get_queried_object_id() ); 
        ?>
    </div>
