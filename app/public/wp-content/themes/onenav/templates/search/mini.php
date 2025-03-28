<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-03-01 10:19:07
 * @LastEditors: iowen
 * @LastEditTime: 2022-01-20 23:46:55
 * @FilePath: \onenav\templates\search\mini.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 
// 站内搜索框，mini搜索框
?> 
<div id="search" class="s-search mx-auto my-4">
	<form name="formsearch" method="get" action="<?php echo esc_url( home_url() ) ?>" id="super-search-fm">
			<input type="hidden" name="post_type"  value="<?php echo (isset($_GET['post_type'])?$_GET['post_type']:'sites') ?>"/> 
			<input type="text" id="search-text" required="required" name="s" class="form-control search-keyword" placeholder="<?php _e( '输入关键字搜索', 'i_theme' ); ?>" style="outline:0"/> 
			<button type="submit"><i class="iconfont icon-search "></i></button>
    </form>
</div>
