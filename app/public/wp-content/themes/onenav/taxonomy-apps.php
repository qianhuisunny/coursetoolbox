<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-03-01 10:19:07
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-04 00:14:04
 * @FilePath: \onenav\taxonomy-apps.php
 * @Description: APP 分类归档
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
    <div id="content" class="container container-lg is_category">
		<div class="content-wrap">
			<div class="content-layout">
                <h4 class="text-gray text-lg mb-4">
                    <i class="site-tag iconfont icon-app icon-lg mr-1" id="<?php single_cat_title() ?>"></i><?php single_cat_title() ?>
                </h4>
                <div class="row">  
                    <?php 
					if ( !have_posts() ){
						echo '<div class="col-lg-12"><div class="nothing">'.__("没有内容","i_theme").'</div></div>';
					}
					if ( have_posts() ) : while ( have_posts() ) : the_post();  
                        if(io_get_option('app_card_mode','card') == 'card'){
                            echo'<div class="col-1a col-md-2a col-lg-3a col-xl-4a">';
                            include( get_theme_file_path('/templates/card-appcard.php') ); 
                            echo'</div>';
                        }else{
                            echo'<div class="col-3a col-md-4a col-lg-5a col-xl-6a col-xxl-7a pb-1">';
                            include( get_theme_file_path('/templates/card-app.php') ); 
                            echo'</div>';
                        }
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
