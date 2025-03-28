<?php
/*
Name: WP-PostViews
Author: Lester 'GaMerZ' Chan
*/

// 统计文章浏览
add_action( 'wp_head', 'ioo_process_postviews' );
function ioo_process_postviews() {
	global $post;
	if( is_int( $post ) ) {
		$post = get_post( $post );
	}
	if( ! wp_is_post_revision( $post ) && ! is_preview() ) {
		if( is_single() || is_page() ) {
			$id = (int) $post->ID ;
			$default = array(
				'count'        => '0',
				'exclude_bots' => true,
				'template'     => '0',
				'use_ajax'     => true,
			);
			$views_options = io_get_option( 'views_options', $default );
			if ( !$post_views = get_post_meta( $post->ID, 'views', true ) ) {
				if(io_get_option('views_n',0)>0)
					$post_views = mt_rand(0, 10)*io_get_option('views_n',0);
				else
					$post_views = 0;
			}
			$should_count = is_views_execution($views_options);
			if( $should_count && ( ( isset( $views_options['use_ajax'] ) && (int) $views_options['use_ajax'] === 0 ) || ( !defined( 'WP_CACHE' ) || !WP_CACHE ) ) ) {
				if (io_get_option('views_r',0)>0) {
					$view = round( mt_rand(1,10) * io_get_option('views_r',0));
				} else {
					$view = 1;
				}
				update_post_meta( $id, 'views', ( $post_views + $view ) );
				do_action( 'postviews_increment_views', ( $post_views + $view ) );
				if (io_get_option('leader_board',true)&&!is_page()) io_add_post_view($id,get_post_type( $id ),wp_is_mobile(),$view);
			}
		}
	}
}

function is_views_execution($views_options){
	global $user_ID;
	$should_count = false;
	switch( (int) $views_options['count'] )  {
		case 0:
			$should_count = true;
			break;
		case 1:
			if(empty( $_COOKIE[USER_COOKIE] ) && (int) $user_ID  === 0) {
				$should_count = true;
			}
			break;
		case 2:
			if( (int) $user_ID  > 0 ) {
				$should_count = true;
			}
			break;
	}
	if( isset( $views_options['exclude_bots'] ) && (int) $views_options['exclude_bots']  === 1 ) {
		$bots = array
		(
			'Google Bot' => 'google'
			, 'MSN' => 'msnbot'
			, 'Alex' => 'ia_archiver'
			, 'Lycos' => 'lycos'
			, 'Ask Jeeves' => 'jeeves'
			, 'Altavista' => 'scooter'
			, 'AllTheWeb' => 'fast-webcrawler'
			, 'Inktomi' => 'slurp@inktomi'
			, 'Turnitin.com' => 'turnitinbot'
			, 'Technorati' => 'technorati'
			, 'Yahoo' => 'yahoo'
			, 'Findexa' => 'findexa'
			, 'NextLinks' => 'findlinks'
			, 'Gais' => 'gaisbo'
			, 'WiseNut' => 'zyborg'
			, 'WhoisSource' => 'surveybot'
			, 'Bloglines' => 'bloglines'
			, 'BlogSearch' => 'blogsearch'
			, 'PubSub' => 'pubsub'
			, 'Syndic8' => 'syndic8'
			, 'RadioUserland' => 'userland'
			, 'Gigabot' => 'gigabot'
			, 'Become.com' => 'become.com'
			, 'Baidu' => 'baiduspider'
			, 'so.com' => '360spider'
			, 'Sogou' => 'spider'
			, 'soso.com' => 'sosospider'
			, 'Yandex' => 'yandex'
		);
		$useragent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		foreach ( $bots as $name => $lookfor ) {
			if ( ! empty( $useragent ) && ( stristr( $useragent, $lookfor ) !== false ) ) {
				$should_count = false;
				break;
			}
		}
	}
	return $should_count;
}
// 在启用WP_CACHE的情况下统计浏览
add_action('wp_enqueue_scripts', 'ioo_postview_cache_count_enqueue');
function ioo_postview_cache_count_enqueue() {
	global $user_ID, $post;

	if( !defined( 'WP_CACHE' ) || !WP_CACHE )
		return;

	$default = array(
		'count'        => '0',
		'exclude_bots' => true,
		'template'     => '0',
		'use_ajax'     => true,
	);
	$views_options = io_get_option( 'views_options', $default );

	if( isset( $views_options['use_ajax'] ) && (int) $views_options['use_ajax'] === 0 )
		return;

	if ( !wp_is_post_revision( $post ) && ( is_single() || is_page() ) ) {
		$should_count = false;
		switch( (int) $views_options['count'] ) {
			case 0:
				$should_count = true;
				break;
			case 1:
				if ( empty( $_COOKIE[USER_COOKIE] ) && (int) $user_ID === 0) {
					$should_count = true;
				}
				break;
			case 2:
				if ( (int) $user_ID > 0 ) {
					$should_count = true;
				}
				break;
		}
		if ( $should_count ) {
			wp_enqueue_script( 'wp-postviews-cache', get_theme_file_uri('/inc/postviews/postviews-cache.js'), array(), '', true );
			wp_localize_script( 'wp-postviews-cache', 'viewsCacheL10n', array( 'admin_ajax_url' => admin_url( 'admin-ajax.php' ), 'post_id' => (int) $post->ID ) );
		}
	}
}

// 显示文章浏览统计
if ( ! function_exists( 'the_views' ) ) {
function the_views($display = true, $prefix = '', $postfix = '', $always = false) {
	$post_views = (int) get_post_meta( get_the_ID(), 'views', true );
	$default = array(
		'count'        => '0',
		'exclude_bots' => true,
		'template'     => '0',
		'use_ajax'     => true,
	);
	$views_options = io_get_option('views_options',$default);
	$views_template = $views_options['template']==0?'%VIEW_COUNT%': '%VIEW_COUNT_ROUNDED%';
	$output = $prefix.str_replace( array( '%VIEW_COUNT%', '%VIEW_COUNT_ROUNDED%' ), array( number_format_i18n( $post_views ), ioo_postviews_round_number( $post_views) ), stripslashes( $views_template ) ).$postfix;
	if($display) {
		echo apply_filters('the_views', $output);
	} else {
		return apply_filters('the_views', $output);
	}
}
}
// 添加视图自定义栏目
add_action('publish_post', 'ioo_add_views_fields');
add_action('publish_page', 'ioo_add_views_fields');
function ioo_add_views_fields($post_ID) {
	global $wpdb;
	if(!wp_is_post_revision($post_ID)) {
		add_post_meta($post_ID, 'views', 0, true);
	}
}


// 公共变量
add_filter('query_vars', 'ioo_views_variables');
function ioo_views_variables($public_query_vars) {
	$public_query_vars[] = 'v_sortby';
	$public_query_vars[] = 'v_orderby';
	return $public_query_vars;
}

// 增加文章浏览统计
add_action( 'wp_ajax_postviews', 'ioo_increment_views' );
add_action( 'wp_ajax_nopriv_postviews', 'ioo_increment_views' );
function ioo_increment_views() {
	if( empty( $_GET['postviews_id'] ) )
		return;

	if( !defined( 'WP_CACHE' ) || !WP_CACHE )
		return;

	$default = array(
		'count'        => '0',
		'exclude_bots' => true,
		'template'     => '0',
		'use_ajax'     => true,
	);
	$views_options = io_get_option( 'views_options', $default );

	if( isset( $views_options['use_ajax'] ) && (int) $views_options['use_ajax']  === 0 )
		return;

	$post_id =  (int) sanitize_key( $_GET['postviews_id'] );
	if( $post_id > 0 ) {
		if (io_get_option('leader_board',true)&&!is_page()) io_add_post_view($post_id,get_post_type( $post_id ),wp_is_mobile());

		$post_views = get_post_custom( $post_id );
		$post_views = (int) $post_views['views'][0] ;
		update_post_meta( $post_id, 'views', ( $post_views + 1 ) );
		do_action( 'postviews_increment_views_ajax', ( $post_views + 1 ) );
		echo ( $post_views + 1 );
		exit();
	}
}

// 后台文章列表添加浏览计数
add_action('manage_posts_custom_column', 'ioo_add_postviews_column_content');
add_filter('manage_posts_columns', 'ioo_add_postviews_column');
add_action('manage_pages_custom_column', 'ioo_add_postviews_column_content');
add_filter('manage_pages_columns', 'ioo_add_postviews_column');
function ioo_add_postviews_column($defaults) {
	$defaults['views'] = __( '浏览', 'i_theme' );
	return $defaults;
}


// 浏览次数
function ioo_add_postviews_column_content($column_name) {
	if($column_name == 'views') {
		if(function_exists('the_views')) { the_views(true, '', '', true); }
	}
}

// 将数字四舍五入为K（千），M（百万）或B（十亿）
function ioo_postviews_round_number( $number, $min_value = 1000, $decimal = 1 ) {
	if( $number < $min_value ) {
		return number_format_i18n( $number );
	}
	$alphabets = array( 1000000000 => 'B', 1000000 => 'M', 1000 => 'K' );
	foreach( $alphabets as $key => $value )
		if( $number >= $key ) {
			return round( $number / $key, $decimal ) . '' . $value;
		}
}
