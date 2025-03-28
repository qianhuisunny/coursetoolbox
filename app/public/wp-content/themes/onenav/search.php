<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-03-01 10:19:07
 * @LastEditors: iowen
 * @LastEditTime: 2023-06-28 01:29:24
 * @FilePath: \onenav\search.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); 
?> 
	<div id="content" class="container mb-4 mb-md-5">
		<?php get_template_part( 'templates/search/mini' ) ?>
		<main class="content" role="main">
		<div class="content-wrap">
			<div class="content-layout">
				<div class="mb-4"> 
					<?php 
					$search_page = io_get_option('search_page_post',array('enabled'=>array()))['enabled'];
					$search_page = apply_filters('io_search_page_post_list_filters', $search_page);
					if(count($search_page)>1){
					foreach($search_page as $k => $v){
						//$title = sprintf( __('有关“%s”的网站', 'i_theme'), htmlspecialchars($s) ) ;
						echo '<a class="btn btn-search mr-2 text-gray '. ($post_type==$k?'current':'') .'" href="'. esc_url(home_url()) .'?s='. htmlspecialchars($s) .'&post_type='.$k.'" title="'. sprintf( __('有关“%s”的%s', 'i_theme'), htmlspecialchars($s), io_get_search_type_name($k) ) .'">'. io_get_search_type_name($k) .'</a>';
					}}
					?>
				</div>
				<h4 class="text-gray text-lg mb-4"><i class="iconfont icon-search mr-1"></i><?php echo sprintf( __('“%s”的搜索结果', 'i_theme'), htmlspecialchars($s) ) ?></h4>
				<?php 
				if(isset($_GET['post_type'] )){
					$post_type=$_GET['post_type']; 
					if (locate_template('templates/search-' . $post_type . '.php') != '') {
						get_template_part( 'templates/search', $post_type );
					}
					else{
						get_template_part( 'templates/search', 'sites' );
					}
				}
				else{
					get_template_part( 'templates/search', 'sites' );
				}
				?>
				<div class="posts-nav mb-4">
					<?php echo paginate_links(array(
						'prev_next'				=> 0,
						'before_page_number'	=> '',
						'mid_size'				=> 2,
					));?>
				</div>
			</div> 
		</div>
		<?php get_sidebar(); ?> 
		</main> 
	</div> 
<?php
get_footer(); 
