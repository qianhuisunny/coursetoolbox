<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-02-05 16:03:25
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-11 15:32:34
 * @FilePath: \onenav\templates\widget\widget-carousel-post.php
 * @Description: 图片、文章轮播
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }  
?>
<div class="slice-article mb-4"> 
<div class="row no-gutters">
    <?php  get_template_part( 'templates/slide','blog' ); ?>
    <div class="col-12 col-md-5 col-lg-4 col-xl-3 mt-4 mt-md-0">
        <div class="card new-news mb-0 overflow-hidden rounded-xl">
            <h3 class="h6 news_title"><i class="iconfont icon-category"></i>&nbsp;&nbsp;<?php _e('最新资讯','i_theme') ?></h3>
            <a class="news_all_btn text-xs" href="<?php echo io_get_template_page_url('template-blog.php') ?>" <?php echo new_window() ?> title="<?php _e('最新资讯','i_theme') ?>"><?php _e('所有','i_theme') ?></a>
            <ul>
            <?php 
            $args = array(
                'category__not_in' => explode(',', io_get_option('article_not_in','')),
                'ignore_sticky_posts' => 1,
            );
            $the_query = new WP_Query( $args );
            
            if ( $the_query->have_posts() ) : while (  $the_query->have_posts() ) :  $the_query->the_post();?> 
                <li>
                    <i class="iconfont icon-point"></i> 
                    <a class="text-sm" href="<?php the_permalink(); ?>" <?php echo new_window() ?>><span><?php the_title(); ?></span></a>
                    <div class="d-flex flex-fill text-xs text-muted">
                        <?php 
                        $category = get_the_category();
                        if($category[0]){?>
                        <a class="mr-2" href="<?php echo get_category_link($category[0]->term_id ) ?>" <?php echo new_window() ?>><?php echo $category[0]->cat_name ?></a>
                        <?php } ?>
                        <div class="flex-fill"></div>
                        <?php echo get_the_date() ?>
                    </div>
                </li> 
            <?php endwhile; endif; wp_reset_postdata(); ?>
            </ul>
        </div>
    </div>
</div>
</div>
