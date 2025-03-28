<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-03-01 10:19:07
 * @LastEditors: iowen
 * @LastEditTime: 2024-04-10 18:05:29
 * @FilePath: /onenav/templates/tools-header.php
 * @Description: 搜索模块加顶部导航
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 
$search_big      = io_get_option('search_skin','') ?: false;
$search_position = io_get_option('search_position', array('home'));

if(is_404() || is_author() || is_single() || is_search() || (is_page() && !is_mininav()) || get_query_var('is_user_route') || get_query_var('custom_page')=="hotnews")://不显示搜索框的页面
    get_template_part('templates/header', 'banner');
else:
if( $search_position && in_array("home",$search_position) && $search_big && $search_big['search_big']=='1' ){
    if (is_mininav() && !get_post_meta( get_the_ID(), 'search_box', true )){
        get_template_part( 'templates/header','banner' );
        echo '<div class="my-2"></div>';  
    }else{ 
        get_template_part('templates/header', 'banner'); 

        // padding-bottom
        $padding = '';
        if ( $search_big['post_top'] &&
            (((is_home() || is_front_page()) && (count(io_get_option('home_widget',array(),'enabled'))>0 || (io_get_option('is_show_hot',true)&&io_get_option('hot_new',''))) ) ||
            (is_mininav() && (isset(get_post_meta(get_the_ID(),'widget',true)['enabled'])||(io_get_option('is_show_hot',true)&&get_post_meta(get_the_ID(), 'hot_new', true)))))
        ) {
            $padding .= 'post-top ';
        }
        
        // gradual
        $gradual = '';
        if ($search_big['big_skin']!="no-bg" && $search_big['bg_gradual']) {
            $gradual = 'bg-gradual ';
        }

        $style='';
        $gradient = '';
        if ($search_big['big_skin']=="css-color") {
            $style = 'style="background-image: linear-gradient(45deg, '.$search_big['search_color']['color-1'].' 0%, '.$search_big['search_color']['color-2'].' 50%, '.$search_big['search_color']['color-3'].' 100%);"';
        }
        if ($search_big['big_skin']=="css-img") {
            $style = 'style="background-image: url('.$search_big['search_img'].')"';
        }
        if ($search_big['big_skin']=="css-bing") {
            $style = 'style="background-image: url('.get_bing_img_cache(rand(0, 5), 'full').')"';
            if (!$search_big['bg_gradual']) {
                $gradient = '<div class="gradient-linear" style="top:0"></div>';
            }
        }
    

        echo '<div class="header-big '.($search_big['changed_bg']?'':'unchanged').' '. $padding . $gradual . $search_big['big_skin'] .' mb-4" '. $style .'>';
        echo $gradient;

        if ($search_big['big_skin']=="canvas-fx") {
            if ($search_big['canvas_id']=='custom') {
                echo '<iframe class="canvas-bg" scrolling="no" sandbox="allow-scripts allow-same-origin" src="'.$search_big['custom_canvas'].'"></iframe>';
            } else {
                echo '<iframe class="canvas-bg" scrolling="no" sandbox="allow-scripts allow-same-origin" src="'.get_theme_file_uri('/fx/io-fx'.sprintf("%02d", ($search_big['canvas_id']==0?rand(1, 17):$search_big['canvas_id'])).'.html').'"></iframe>';
            }
        }
        // 加载搜索模块
        if ( in_array("home", $search_position)) {
            get_template_part('templates/search/big');
        } else {
            echo '<div class="no-search my-2 p-1"></div>';
        }
        // 加载公告模块
        if (is_home() || is_front_page()) {
            echo '<div class="bulletin-big mx-3 mx-md-0">';
            get_template_part('templates/bulletin');
            echo '</div>';
        }

        iopay_get_auto_ad_html((is_mininav()?'page':'home'), 'my-4 mx-2 mb-n4 mb-md-n5 position-relative z-index-1', 'search');

        echo '</div>';  
    }
} else { 
    get_template_part( 'templates/header','banner' );

    echo '<div class="container container-fluid customize-width">';

    if (is_mininav() && !get_post_meta( get_the_ID(), 'search_box', true )){
        echo '<div class="my-2"></div>';
        goto DEF_END;
    }
    // 加载公告模块
    if(is_home() || is_front_page())
        get_template_part( 'templates/bulletin' );  

    // 加载搜索模块 
    if( $search_position && in_array("home",$search_position) ){
        get_template_part( 'templates/search/default' );
    } else {
        echo '<div class="no-search my-2 p-1"></div>';
    }

    DEF_END:
    // 加载广告模块
    show_ad('ad_home_top');

    echo '</div>';
}
endif;