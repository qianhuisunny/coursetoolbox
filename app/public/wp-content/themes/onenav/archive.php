<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-03-01 10:19:00
 * @LastEditors: iowen
 * @LastEditTime: 2022-06-25 22:07:16
 * @FilePath: \onenav\archive.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
<div id="content" class="container is_category">
    <div class="content-wrap">
        <div class="content-layout">
            <?php get_template_part( 'templates/cat','list' ) ?>
        </div> 
    </div>
	<?php get_sidebar(); ?>
</div> 
<?php get_footer(); ?>
