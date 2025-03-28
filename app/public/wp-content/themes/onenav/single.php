<?php
/*
 * @Theme Name:OneNav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:00
 * @LastEditors: iowen
 * @LastEditTime: 2023-11-01 03:20:02
 * @FilePath: \onenav\single.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
<div id="content" class="container my-3 my-md-4">
    <?php 
    $is_hide = false;
    $header  = io_single_post_header($is_hide);
    iopay_get_auto_ad_html('page', 'mb-3');
    if (!$is_hide){
        get_template_part('templates/breadcrumb');
    } 
    ?>
    <main class="content" role="main">
    <div class="content-wrap">
        <div class="content-layout">
            <?php 
            while( have_posts() ): the_post();
            if($is_hide){
                echo $header;
            }else{
                io_single_post_content();
            }
            endwhile;
            if (io_get_option('post_related', true)) {
            ?>
            <h4 class="text-gray text-lg my-4"><i class="site-tag iconfont icon-book icon-lg mr-1" ></i><?php _e('相关文章', 'i_theme') ?></h4>
            <div class="row mb-n4"> 
                <?php get_template_part('templates/related', 'post'); ?>
            </div>
            <?php
            }
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif; 
            ?>
        </div> 
    </div> 
    <?php get_sidebar(); ?>
    </main>
</div>
<?php get_footer(); ?>