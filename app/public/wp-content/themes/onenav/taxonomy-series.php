<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-03-01 10:19:07
 * @LastEditors: iowen
 * @LastEditTime: 2022-06-25 22:12:20
 * @FilePath: \onenav\taxonomy-series.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
    <div id="content" class="container container-lg is_category">
        <div class="content-wrap">
            <div class="content-layout">
                <h4 class="text-gray text-lg mb-4">
                    <i class="site-tag iconfont icon-book icon-lg mr-1" id="<?php single_cat_title() ?>"></i><?php single_cat_title() ?>
                </h4>
                <div class="row">  
                    <?php 
                    if ( !have_posts() ){
                        echo '<div class="col-lg-12"><div class="nothing">'.__("没有内容","i_theme").'</div></div>';
                    }
                    if ( have_posts() ) : while ( have_posts() ) : the_post(); 
                    ?>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-4 col-xl-5a">
                        <?php include( get_theme_file_path('/templates/card-book.php') ); ?>
                        </div>
                    <?php  
                    endwhile; endif;?>
                </div>  
                <div class="posts-nav mb-4">
                    <?php echo paginate_links(array(
                        'prev_next'          => 0,
                        'before_page_number' => '',
                        'mid_size'           => 2,
                    ));?>
                </div>
            </div> 
        </div>
        <?php get_sidebar(); ?>
    </div>
<?php get_footer(); ?>
