<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2023-04-01 00:07:44
 * @LastEditors: iowen
 * @LastEditTime: 2024-04-11 16:59:13
 * @FilePath: /onenav/inc/functions/io-post.php
 * @Description: 
 */

/**
 * 获取文章头图
 * 
 * @param WP_Post $post
 * @return mixed
 */
function io_get_post_thumbnail($post){
    $post_id   = $post->ID;
    $post_type = $post->post_type;
    $name      = $post->post_title;

    switch ($post_type) {
        case 'sites':
            $sites_type = get_post_meta($post_id, '_sites_type', true);
            $link_url   = get_post_meta($post_id, '_sites_link', true);
            $thumbnail  = get_site_thumbnail($name, $link_url, $sites_type, false);
            break;
        case 'app':
            $thumbnail = get_post_meta_img($post_id, '_app_ico', true);
            break;
        case 'book':
            $thumbnail = get_post_meta_img($post_id, '_thumbnail', true);
            break;
        default:
            $thumbnail = io_theme_get_thumb();
    }
    return $thumbnail;
}
function io_custom_favorites_posts_orderby($orderby, $wp_query){
    if (!io_get_option('sites_archive_order', true)) {
        return $orderby;
    }
    if (is_tax(['favorites', 'sitetag']) && !is_admin() && $wp_query->is_main_query()) {
        global $wpdb;
        // 添加一个CASE语句来调整排序逻辑
        $orderby = "CASE WHEN (SELECT COUNT(1)
                    FROM {$wpdb->postmeta}
                    WHERE {$wpdb->postmeta}.meta_key = '_affirm_dead_url'
                    AND {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID) > 0 THEN 1 ELSE 0 END ASC, 
                    {$wpdb->posts}.post_date DESC";
    }
    return $orderby;
}
add_filter('posts_orderby', 'io_custom_favorites_posts_orderby', 10, 2);
