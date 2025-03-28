<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:55:58
 * @LastEditors: iowen
 * @LastEditTime: 2024-03-01 21:47:19
 * @FilePath: /onenav/header.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<!DOCTYPE html>
<html <?php language_attributes() ?> <?php io_html_class() ?>>
<head> 
<?php io_auto_theme_mode() ?>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="renderer" content="webkit">
<meta name="force-rendering" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=0.0, viewport-fit=cover">
<?php get_template_part( 'templates/title' ) ?>
<link rel="shortcut icon" href="<?php echo io_get_option('favicon','') ?>">
<link rel="apple-touch-icon" href="<?php echo io_get_option('apple_icon','') ?>">
<!--[if IE]><script src="<?php echo get_theme_file_uri('/js/html5.min.js') ?>"></script><![endif]-->
<?php wp_head(); ?>
<?php custom_color() ?>

<!-- 自定义代码 -->
<?php echo io_get_option('code_2_header','');?>
<!-- end 自定义代码 -->
</head> 
<body <?php body_class(io_body_class()) ?>>
<?php if(io_get_option('loading_fx',false)) { ?><div id="loading"><?php loading_type() ?></div><?php } ?>
<?php 
if(!get_query_var('bookmark_id')){
    if (!is_404()) {
        get_template_part('templates/sidebar', 'nav');
    }
    echo '<div class="main-content flex-fill">';  //需在 footer 闭合</div>
    get_template_part( 'templates/tools','header' ); 
 }
?>  