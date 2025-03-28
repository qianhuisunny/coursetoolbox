<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2023-03-11 21:58:46
 * @LastEditors: iowen
 * @LastEditTime: 2023-04-11 10:52:47
 * @FilePath: \onenav\inc\widgets\w.pay.box.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

CSF::createWidget( 'w_pay_box', array(
    'title'       => '付费购买',
    'classname'   => 'io-widget-pay-box',
    'description' => '显示当前文章的付费内容，置于内容页。不支持 App 和 书籍',
    'fields'      => array(
    )
) );
if ( ! function_exists( 'w_pay_box' ) ) {
    function w_pay_box( $args, $instance ) {
        if (is_single()) {
            echo iopay_buy_sidebar_widgets('', $args, $instance);
        }
    }
}
