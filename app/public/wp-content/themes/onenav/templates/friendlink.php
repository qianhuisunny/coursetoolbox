<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-03-01 10:19:07
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-11 15:25:42
 * @FilePath: \onenav\templates\friendlink.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

        <?php if( io_get_option('show_friendlink',false) && io_get_option('links',false)) : ?>
        <h4 class="text-gray text-lg mb-4">
            <i class="iconfont icon-book-mark-line icon-lg mr-2" id="friendlink"></i><?php _e('友情链接','i_theme') ?>
        </h4>
        <div class="friendlink text-xs card">
            <div class="card-body"> 
                <?php 
                $args = array(
                    'orderby'          => 'rating',
                    'order'            => 'DESC', 
                    'category'         =>  io_get_option('home_links',false)?implode(',',io_get_option('home_links',false)):'', 
                    'categorize'       => 0,
                    'title_li'         => '', 
                    'before'           => '',
                    'after'            => '',
                    'show_images'      => 0
                );
                wp_list_bookmarks( $args ); ?>
                <a href="<?php echo io_get_template_page_url('template-links.php') ?>" target="_blank" title="<?php _e('更多链接','i_theme') ?>"><?php _e('更多链接','i_theme') ?></a>
            </div> 
        </div> 
        <?php endif; ?> 
        