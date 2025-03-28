<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-11-16 19:54:57
 * @LastEditors: iowen
 * @LastEditTime: 2022-06-25 22:07:05
 * @FilePath: \onenav\templates\tools-cardcontent.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }  ?>

<div class="content">
    <div class="content-wrap">
        <div class="content-layout">
        <?php add_menu_content_card();// 加载菜单内容卡片 ?>
        </div>
    </div>
    <?php get_sidebar(); ?>
</div>