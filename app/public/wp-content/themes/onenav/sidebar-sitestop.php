<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-07-15 00:43:42
 * @LastEditors: iowen
 * @LastEditTime: 2022-06-25 22:17:04
 * @FilePath: \onenav\sidebar-sitestop.php
 * @Description: 网址详情页上方小工具
 */ 

if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<?php if ( is_active_sidebar( 'sidebar-sites-t' ) ) : ?> 
	<div class="sidebar sidebar-border col-12 col-md-12 col-lg-4 mt-4 mt-lg-0">
		<?php dynamic_sidebar( 'sidebar-sites-t' ) ?> 
	</div>
<?php else: 
    show_ad('ad_site_right',false,'<div class="col-12 col-md-12 col-lg-4 mt-4 mt-lg-0"><div class="apd apd-right">','</div></div>');
endif;
