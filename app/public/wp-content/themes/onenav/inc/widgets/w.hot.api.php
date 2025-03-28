<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-11-26 23:51:17
 * @LastEditors: iowen
 * @LastEditTime: 2024-03-27 00:45:34
 * @FilePath: /onenav/inc/widgets/w.hot.api.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$default_data = array(
    'name'      => '百度热点',
    'hot_type'  => 'api',
    'rule_id'   => 100000,
    'icon'      => get_theme_file_uri('/images/hotico/baidu.png'),
    'is_iframe' => 0,
);

CSF::createWidget( 'hot_api', array(
    'title'       => '今日热榜api',
    'classname'   => 'io-widget-hot-api',
    'description' => '按条件显示热门网址，可选“浏览数”“点赞收藏数”“评论量”',
    'fields'      => get_hot_list_option($default_data),
) );
if ( ! function_exists( 'hot_api' ) ) {
    function hot_api( $args, $instance ) {
        echo $args['before_widget'];
        hot_search($instance); 
        echo $args['after_widget'];
    }
}
