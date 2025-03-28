<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-03-01 10:19:06
 * @LastEditors: iowen
 * @LastEditTime: 2024-01-29 20:11:16
 * @FilePath: \onenav\index.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }?>
<?php get_header();?>
<?php echo io_header_fx();?>
<div id="content" class="container container-fluid customize-width">
<?php
    // 加载文章轮播模块
    get_template_part( 'templates/widget/home','widget' );
    get_template_part( 'templates/article','list' ); 

    // 加载热搜模块
    get_template_part( 'templates/tools','hotsearch' );

    iopay_get_auto_ad_html('home', 'my-4', 'content');

    // 加载自定义模块
    if(is_user_logged_in() && io_get_option('user_center',false)){
        get_template_part( 'templates/tools','customizeforuser' ); 
    }else{
        get_template_part( 'templates/tools','customize' ); 
    }
    // 加载热门模块
    get_template_part( 'templates/tools','hotcontent' ); 

    // 加载广告模块second
    show_ad('ad_home_card_top');
    //get_template_part( 'templates/ads','homesecond' );

    // 加载菜单内容卡片
    get_template_part( 'templates/tools','cardcontent' );

    // 加载广告模块link
    show_ad('ad_home_link_top');
    //get_template_part( 'templates/ads','homelink' );
    // 加载友链模块
    get_template_part( 'templates/friendlink' ); 
?>   
</div><!-- content end -->
<?php
get_footer();
