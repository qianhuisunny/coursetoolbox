<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-08-05 19:58:10
 * @LastEditors: iowen
 * @LastEditTime: 2022-01-25 21:44:49
 * @FilePath: \onenav\inc\meta-menu.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if(!is_admin()) return;

if( class_exists( 'CSF' ) && IO_PRO ) {

    $prefix = '_io_one_nav_menu_options_ico';
    CSF::createNavMenuOptions( $prefix, array(
        'data_type' => 'unserialize', //   `serialize` or `unserialize`
        'depth' => 0, //作用层数
    ));
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'id'    => 'menu_ico',
                'type'  => 'icon',
                'title' => '图标',
                'default' => 'iconfont icon-category'
            ),
        )
    ));

    $prefix = '_io_one_nav_menu_options';
    CSF::createNavMenuOptions( $prefix, array(
        'data_type' => 'unserialize', //   `serialize` or `unserialize`
        'depth' => 1, //作用层数
    ));
    CSF::createSection( $prefix, array(
        'fields' => array( 
            array(
                'id'    => 'open',
                'type'  => 'switcher',
                'title' => '默认展开',
                'help'  => 'mini菜单无效',
            ),
            array(
                'id'        => 'purview',
                'type'      => 'button_set',
                'title'     => __('权限','io_setting'),
                'options'   => array(
                    '0'         => __('所有','io_setting'),
                    '1'         => __('登录','io_setting'),
                ),
                'default'   => '0',
                'help'      => '所有用户可见，或者登录后可见，但是菜单内的内容不会消失在站内搜索中。',
            ),
        )
    ));
}
