<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2023-03-23 22:09:11
 * @LastEditors: iowen
 * @LastEditTime: 2023-06-28 01:18:05
 * @FilePath: \onenav\inc\functions\io-search.php
 * @Description: 
 */

function io_get_search_type_name($key){
    $name = array(
        'sites' => __('网站','i_theme'),
        'post'  => __('文章','i_theme'),
        'app'   => __('软件','i_theme'),
        'book'  => __('书籍','i_theme'),
    );
    $name = apply_filters('io_search_type_name_filters', $name); 
    return $name[$key];
}