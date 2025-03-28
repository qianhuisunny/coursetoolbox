<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2023-03-11 17:11:50
 * @LastEditors: iowen
 * @LastEditTime: 2023-04-21 21:11:13
 * @FilePath: \onenav\iopay\widgets\w-buy-sidebar.php
 * @Description: 
 */

/**
 * 付费小窗口
 * 
 * @param bool   $echo
 * @return mixed
 */
function iopay_buy_sidebar_html($echo = true){
    global $post;
    $post_id   = $post->ID;
    $post_type = $post->post_type;

    $html='';
    $user_level = get_post_meta($post_id, '_user_purview_level', true);
    if (!$user_level) {
        update_post_meta($post_id, '_user_purview_level', 'all');
        return $html;
    }

    if ($user_level && 'buy' === $user_level) {
        $buy_option = get_post_meta($post_id, 'buy_option', true);
    }
    if (isset($buy_option)) {
        if ('annex' === $buy_option['buy_type']) {
            $html = '<div class=" col-12 col-md-12 col-lg-4 mt-4 mt-lg-0">';
            $html .= iopay_buy_sidebar_widgets();
            $html .= '</div>';
        }
    }

    if($echo){
        echo $html;
    } else {
        return $html;
    }
}

/**
 * widgets
 * 
 * @param mixed $post
 * @return string
 */
function iopay_buy_sidebar_widgets($post = '', $args='', $instance=''){
    global $is_one_show;
    if( $is_one_show){
        //只显示一次
        return '';
    }
    $is_one_show = true;

    if(!$post){
        global $post;
    }
    $post_id   = $post->ID;
    $post_type = $post->post_type;
    if(!$post_id){
        return '';
    }

    if (!in_array($post_type, array('post', 'sites'))) {
        return '';
    }

    $buy_option = get_post_meta($post_id, 'buy_option', true);

    if(!$buy_option || 'annex' !== $buy_option['buy_type']){
        return '';
    }
    switch ($buy_option['price_type']) {
        case 'multi':
            return iopay_get_pay_sidebar_widgets_box($buy_option, $args, $instance);

        default:
            $order = iopay_is_buy($post_id);
            if($order){
                return iopay_get_show_pay_sidebar_widgets_box($buy_option, $order, $args, $instance);
            } else{
                return iopay_get_pay_sidebar_widgets_box($buy_option, $args, $instance);
            }
    }
}
/**
 * 侧边栏购买窗口
 * 
 * @param mixed $buy_option
 * @param mixed $args
 * @param mixed $instance
 * @return string
 */
function iopay_get_pay_sidebar_widgets_box($buy_option, $args='', $instance=''){
    $unit      = '<span class="text-xs">' . io_get_option('pay_unit', '￥') . '</span>';
    $btn_data  = iopay_get_post_annex_buy_btn($buy_option,'btn-block');
    $pay_price = $btn_data['pay_price'];
    $org_price = $btn_data['org_price'];
    $org_price = $org_price && $org_price > $pay_price ? '<span class="original-price d-inline-block">' . $unit . $org_price . '</span>' : '';

    $_c = '<div class="bg-blur-20 io-radius shadow mb-3">';
    $_c .= '<div class="p-2 text-center"><span class="text-64 font-weight-bold">' . $unit . $pay_price . '</span> ' . $org_price . '</div>';
    $_c .= '</div>';
    $_c .= $btn_data['btn'];

    if(!$btn_data['is_login'] && empty($pay_price)){
        return iopay_get_show_pay_sidebar_widgets_box($buy_option, '', $args, $instance);
    }
    $class = 'fx-blue';
    $html = '<div class="io-pay-box modal-header-bg semi-white overflow-hidden position-relative shadow io-radius px-3 pb-3 pt-2 ' . $class . '">';
    $html .= '<div class="mb-2">'.iopay_get_buy_type_name($buy_option['buy_type'],true).'</div>';
    $html .= '<div class="pay-box-body">';
    $html .= $_c;
    $html .= iopay_pay_tips_box('');
    $html .= '</div>';
    $html .= '</div>';

    if (empty($args)) {
        return $html;
    }else{
        return $args['before_widget'] . $html . $args['after_widget'];
    }
}

/**
 * 已支付内容显示资源地址
 * 
 * @param mixed $buy_option
 * @param mixed $order
 * @return string
 */
function iopay_get_show_pay_sidebar_widgets_box($buy_option, $order, $args='', $instance=''){
    $btn_name =__('下载地址', 'i_theme');
    $icon = '<i class="iconfont icon-down"></i> ';
    $class = 'fx-blue';
    $html = '<div class="io-pay-box modal-header-bg semi-white overflow-hidden position-relative shadow io-radius px-3 pb-3 pt-2 ' . $class . '">';
    $html .= '<div class="mb-2">'.iopay_get_buy_type_name($buy_option['buy_type'],true).'</div>';
    $html .= '<div class="pay-box-body">';
    $html .= '<div class="text-center text-white mb-4">
                <div><i class="iconfont icon-adopt icon-3x"></i></div>
                <span>'.__('已购买','i_theme').'</span>
            </div>
            
            <a href="#posts_pay_box" class="smooth-n position-relative btn vc-blue btn-block btn-shadow nofx no-c mb-3"  title="' . $btn_name . '">' . $icon . $btn_name . '</a>';
    $html .= iopay_pay_tips_box('');
    $html .= '</div>';
    $html .= '</div>';

    if (empty($args)) {
        return $html;
    }else{
        return $args['before_widget'] . $html . $args['after_widget'];
    }
}