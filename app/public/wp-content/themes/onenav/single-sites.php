<?php
/*
 * @Theme Name:OneNav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:00
 * @LastEditors: iowen
 * @LastEditTime: 2023-11-01 03:20:38
 * @FilePath: \onenav\single-sites.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); 
while( have_posts() ): the_post();
global $sites_type;
$sites_type = get_post_meta(get_the_ID(), '_sites_type', true);
?>
<div id="content" class="container my-4 my-md-5">
    <?php 
    echo io_header_fx();
    $is_hide = false;
    $header  = io_site_header($is_hide);
    if ($is_hide){
        iopay_get_auto_ad_html('page', 'mb-3 mt-n3 mt-md-n4');
    }else{
        iopay_get_auto_ad_html('page', 'my-n3 my-md-n4');
        echo $header;
    }
    ?>
    <main class="content" role="main">
        <div class="content-wrap">
            <div class="content-layout">
                <?php  
                if($is_hide){
                    echo $header;
                }else{
                    io_site_content();
                }
                if (io_get_option('sites_related', true)) {
                ?>
                <h2 class="text-gray text-lg my-4"><i class="site-tag iconfont icon-tag icon-lg mr-1" ></i><?php _e('相关导航', 'i_theme') ?></h2>
                <div class="row mb-n4"> 
                    <?php get_template_part('templates/related', 'sites'); ?>
                </div>
                <?php
                }
                if (comments_open() || get_comments_number()) {
                    comments_template();
                }
                ?>
            </div><!-- content-layout end -->
        </div><!-- content-wrap end -->
    <?php get_sidebar('sites');  ?>
    </main>
</div><!-- container end -->
<?php endwhile; ?>
<?php 
if( !$is_hide && io_get_option('leader_board',true) && io_get_option('details_chart',true)){ 
    wp_enqueue_script('echarts');
    wp_enqueue_script('sites-chart');
}
get_footer();
