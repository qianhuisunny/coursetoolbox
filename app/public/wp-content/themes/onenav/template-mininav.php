<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-28 16:29:55
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-04 00:21:20
 * @FilePath: \onenav\template-mininav.php
 * @Description: 
 */
/*
Template Name: 次级导航
*/
?>
<?php get_header();?>
<div id="content" class="container container-fluid customize-width">
<?php
 
    // 加载文章轮播模块
    get_template_part( 'templates/widget/home','widget' );

    // 加载热搜模块
    get_template_part( 'templates/tools','hotsearch' ); 

    // 加载自定义模块
    //if(is_user_logged_in() && io_get_option('user_center',false)){
    //    get_template_part( 'templates/tools','customizeforuser' ); 
    //}else{
    //    get_template_part( 'templates/tools','customize' ); 
    //}
    // 加载热门模块
    if (get_post_meta( get_the_ID(), 'hot_box', true )){
        get_template_part( 'templates/tools','hotcontent' ); 
    }

    // 加载广告模块second
    show_ad('ad_home_card_top');
    //get_template_part( 'templates/ads','homesecond' );

    // 加载菜单内容卡片
    get_template_part( 'templates/tools','cardcontent' );
    //add_menu_content_card();
    
    // 加载广告模块link
    show_ad('ad_home_link_top');
    //get_template_part( 'templates/ads','homelink' );
    // 加载友链模块
    //get_template_part( 'templates/friendlink' ); 
?>   
</div> 
<?php
get_footer();
