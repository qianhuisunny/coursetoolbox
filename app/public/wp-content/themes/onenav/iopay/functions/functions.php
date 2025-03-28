<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2023-02-25 22:40:30
 * @LastEditors: iowen
 * @LastEditTime: 2023-06-01 10:47:39
 * @FilePath: \onenav\iopay\functions\functions.php
 * @Description: 
 */

$functions = array(
    'iopay-options',
    'iopay-ajax',
    'iopay-order',
    'iopay-post',
    'iopay-pay',
);

foreach ($functions as $function) {
    $path = 'iopay/functions/' . $function . '.php';
    require get_theme_file_path($path);
}

/**
 * 判断是否已付费
 * 
 * @param int $post_id      文章ID
 * @param int $index        商品序号
 * @param string $post_type 文章类型
 * @param int $user_id      用户ID
 * @return array|bool       增加缓存？？？？？
 */
function iopay_is_buy($post_id, $index = 0, $post_type='', $user_id = 0){
    if (!$post_id) {
        return false;
    }

    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    $buy_option = get_post_meta($post_id, 'buy_option', true);

    if ('single' === $buy_option['price_type']) { // 如果是总价（单一价格）
        if (empty($buy_option['pay_price'])) {
            $pay_order = array('pay_state' => 'free');
            return $pay_order;
        }
    } elseif (0 !== $index) {
        $index = (int)$index;
        if (empty($post_type)) {
            $post_type = get_post_type($post_id);
        }
        switch ($post_type) {
            case 'app':
                global $app_down_list;
                if (!$app_down_list) {
                    $app_down_list = io_get_app_down_by_index($post_id);
                }
                if ( isset($app_down_list[$index]['pay_price']) && empty($app_down_list[$index]['pay_price'])) {
                    $pay_order = array('pay_state' => 'free');
                    return $pay_order;
                }
                break;
        }
    }

    global $wpdb;

    if ($user_id) {
        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->iopayorder} WHERE `user_id`=%d AND `post_id`=%d AND `merch_index`=%d AND `status`=1", $user_id, $post_id, $index);
        if (0 == $index) {
            $sql = $wpdb->prepare("SELECT * FROM {$wpdb->iopayorder} WHERE `user_id`=%d AND `post_id`=%d AND `status`=1", $user_id, $post_id);
        }
        $pay_order = $wpdb->get_row($sql);
        if ($pay_order) {
            $pay_order              = (array) $pay_order;
            $pay_order['pay_state'] = 'buy';
            return $pay_order;
        }
    }

    if (isset($_COOKIE['iopay_' . $post_id .'_'. $index])) {
        $sql       = $wpdb->prepare("SELECT * FROM {$wpdb->iopayorder} WHERE `order_num` = %s AND `post_id`=%d AND `merch_index`=%d AND `status`=1", $_COOKIE['iopay_' . $post_id . '_' . $index], $post_id, $index);
        $pay_order = $wpdb->get_row($sql);
        if ($pay_order) {
            $pay_order              = (array) $pay_order;
            $pay_order['pay_state'] = 'buy';
            return $pay_order;
        }
    }

    return false;
}

/**
 * 判断文章支付是不是积分支付
 * 
 * @param mixed $pay_type 支付方式
 * @param mixed $post_id
 * @return bool
 */
function iopay_post_is_points($pay_type = array(), $post_id = 0){
    if (!isset($pay_type['pay_type'])) {
        $pay_type = get_post_meta($post_id, 'posts_iopay', true);
    }

    if (!isset($pay_type['pay_type'])) {
        return false;
    }

    return isset($pay_type['pay_type']) && 'points' === $pay_type['pay_type'];
}


/**
 * 获取支持的支付方式
 * 
 * @param mixed $pay_type
 * @return mixed
 */
function iopay_get_pay_methods($pay_type = ''){
    $pay_names = array(
        'wechat' => array(
            'name' => __('微信','i_theme'),
            'img'  => '<img src="' . get_theme_file_uri('/iopay/assets/img/wechat.svg') . '" alt="wechat-logo">',
        ),
        'alipay' => array(
            'name' => __('支付宝','i_theme'),
            'img'  => '<img src="' . get_theme_file_uri('/iopay/assets/img/alipay.svg') . '" alt="alipay-logo">',
        ),
        'points' => array(
            'name' => __('积分','i_theme'),
            'img'  => '<img src="' . get_theme_file_uri('/iopay/assets/img/points.svg') . '" alt="points-logo">',
        ),
        'paypal' => array(
            'name' => 'PayPal',
            'img'  => '<img src="' . get_theme_file_uri('/iopay/assets/img/paypal.svg') . '" alt="paypal-logo">',
        ),
    );
    $methods        = array();
    $pay_wechat_sdk = io_get_option('pay_wechat_sdk','null');
    $pay_alipay_sdk = io_get_option('pay_alipay_sdk','null');
    if ('null' != $pay_wechat_sdk) {
        $methods['wechat'] = $pay_names['wechat'];
    }

    if ('null' != $pay_alipay_sdk) {
        $methods['alipay'] = $pay_names['alipay'];
    }

    if (io_get_option('paypal','','user')) {
        $methods['paypal'] = $pay_names['paypal'];
    }


    return apply_filters('iopay_pay_methods', $methods, $pay_type);
}

/**
 * 根据商品类型判断是否允许积分抵扣
 * 
 * @param mixed $pay_type
 * @return bool
 */
function iopay_is_points_pay($pay_type){
    // 待添加限制
    return false;
}
/**
 * 获取form中使用的支付按钮
 * 
 * @param mixed $pay_type 商品类型：阅读 资源
 * @param mixed $pay_price 金额
 * @param mixed $text
 * @return string
 */
function iopay_get_pay_input($pay_type, $pay_price = 0, $text = ''){
    $text          = !empty($text) ? $text : __('立即支付', 'i_theme');
    $user_id       = get_current_user_id();
    $pay_methods   = iopay_get_pay_methods($pay_type);
    $methods_lists = '';
    $methods_html  = '';

    if (!$pay_methods) {
        if (is_super_admin()) {
            return '<a href="' . io_get_admin_csf_url('商城设置/支付接口') . '" class="btn vc-red btn-outline btn-block">请先配置支付接口</a>';
        } else {
            return '<span class="tips-box vc-l-yellow btn-block">'.__('暂时无法支付，请与客服联系！','i_theme').'</span>';
        }
    }

    $i = 1;
    foreach ($pay_methods as $key => $val) {
        if ($i === 1) {
            $method_default = $key;
        }
        $methods_lists .= '<div class="d-flex align-items-center justify-content-center flex-wrap io-radio flex-fill' . ($i === 1 ? ' active' : '') . '" data-for="pay_method"  data-value="' . $key . '" >' . $val['img'];
        $methods_lists .= '<div class="text-sm">' . $val['name'] . '</div>';
        $methods_lists .= '</div>';
        $i++;
    }

    if ($methods_lists && $i > 2) {
        $methods_html = '<div class="d-flex mb-2">' . $methods_lists . '</div>';
    }

    //积分抵扣
    $points_deduction = '';
    if (iopay_is_points_pay($pay_type)) {
    }

    $html = '<div class="dependency-box">';
    $html .= $points_deduction;
    $html .= $methods_html;
    $html .= '<input type="hidden" name="pay_method" value="' . $method_default . '">';
    $html .= '<button class="btn vc-blue btn-shadow initiate-pay btn-block mt-3">' . $text . '</button>';
    $html .= '</div>';

    return $html;
}

/**
 * 获取付费类型的名称
 * @param mixed $buy_type
 * @param mixed $show_icon
 * @return string
 */
function iopay_get_buy_type_name($buy_type, $show_icon = false)
{
    $name = array(
        'view'        => __('付费内容', 'i_theme'),
        'part'        => __('付费阅读', 'i_theme'),
        'annex'       => __('付费资源', 'i_theme'),
        'auto_ad_url' => __('自动广告', 'i_theme'),
    );
    $n = isset($name[$buy_type]) ? $name[$buy_type] : '付费内容';
    if ($show_icon) {
        $icons = array(
            'view'  => 'icon-minipanel',
            'part'  => 'icon-instructions',
            'annex' => 'icon-down',
        );
        return '<i class="iconfont ' . $icons[$buy_type] . ' mr-2"></i>' . $n;
    }
    return $n;
}

/**
 * 获取支付设置
 * @param string $type
 * @return array|string
 */
function iopay_get_option($type){
    $config = io_get_option($type);
    return io_trim($config);
}

/**
 * 付成功后执行相关处理
 * 
 * @param mixed $order_id 订单号
 * @return void
 */
function iopay_payment_order_success_action($order_id){
    global $wpdb;
    $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->iopayorder} WHERE `order_num` = %s AND `status` = %d", $order_id, 1));

    if($order->post_id){
        //存储文章销量数据，根据文章内商品序号
        $_data = (get_post_meta($order->post_id, 'io_pay_count', true));
        if($_data && is_array($_data)){
            $_data['p' . $order->index] = isset($_data['p' . $order->index]) ? ((int) $_data['p' . $order->index] + 1) : 1;
        }else{
            $_data = array(
                'p' . $order->index => 1,
            );
        }
        update_post_meta($order->post_id, 'io_pay_count', $_data);
    }

    switch ($order->order_type) {
        case 'auto_ad_url':
            $ad_data = maybe_unserialize($order->order_meta);
            $ad_data['order_num'] = $order->order_num;
            iopay_update_auto_ad_url($ad_data);

            //通知管理员
            $data  = $ad_data;
            $check = io_get_option('auto_ad_config', false, 'check');
            if($check){
                $data['check'] = true;
                $data['msg']   = "请在30天内完成审核，否则会自动删除。";
                $title = sprintf(__('[%s]新自动广告需审核', 'i_theme'), get_bloginfo('name'));
            }else{
                $data['check'] = false;
                $title = sprintf(__('[%s]自动广告入驻通知', 'i_theme'), get_bloginfo('name'));
            }
            io_mail_to_admin($title, io_templet_add_auto_ad_url($data));

            //待审核通知客户
            if ($check && ((isset($data['contact']) && !empty($data['contact'])) || !empty($data['user_id']))) {
                $title         = sprintf(__('您在站点「%s」申请的自动广告待审核', 'i_theme'), get_bloginfo('name'));
                $data['title'] = $title;
                $go            = !empty($data['contact']) ? $data['contact'] : '';
                if (empty($go)) {
                    $go = get_userdata($data['user_id'])->user_email;
                }
                $data['msg']   = "请等待审核通过或者联系客服，谢谢！";
                io_mail($go, $title, io_templet_auto_ad_url_check($data));
            }
            break;

        default:
            break;
    }
}
add_action('iopay_payment_order_success', 'iopay_payment_order_success_action');

/**
 * 支付提示
 * 
 * @param mixed $loc start end
 * @return string
 */
function iopay_pay_tips_box($loc = 'start'){
    $_l = 'ml-3';
    if ('start' == $loc) {
        $_l = 'mr-3';
    }
    $_t_c = '';
    if ('' == $loc) {
        $_t_c = 'text-left';
        $_l   = 'mr-3';
    }
    $tips_b   = '';
    $no_login = '';
    if (!is_user_logged_in() && io_get_option('pay_no_login',true)) {
        $no_login = io_get_option('pay_no_login_tips_multi', '') ? '<div>' . io_get_option('pay_no_login_tips_multi', '') . '</div>' : '';
    }
    $pay_service  = '';
    $pay_sev_list = io_get_option('pay_service', array());
    if (!empty($pay_sev_list) && is_array($pay_sev_list)) {
        foreach ($pay_sev_list as $s) {
            $pay_service .= '<div class="' . $_l . ' d-inline-block"><i class="' . $s['icon'] . ' mr-1"></i>' . _iol($s['value']) . '</div>';
        }
    }
    if ($no_login || $pay_service) {
        $tips_b .= '<div class="position-relative d-flex flex-column align-items-' . $loc . ' mt-1">';
    }
    if ($no_login) {
        $tips_b .= '<div class="tips-box vc-yellow text-sm ' . $_t_c . ' px-md-3 mt-2">' . $no_login . '</div>';
    }
    if ($pay_service) {
        $tips_b .= '<div class="tips-box vc-blue text-xs ' . $_t_c . ' py-1 px-md-3 mt-2">' . $pay_service . '</div>';
    }
    if ($tips_b) {
        $tips_b .= '</div>';
    }
    return $tips_b;
}


/**
 * 获取订单统计
 * 
 * @param mixed $type
 * @return array
 */
function io_get_order_stats_by_time($type = 'today'){
    $default = array(
        'count' => 0,
        'sum'   => 0,
    );

    if (!$type) {
        return $default;
    }

    switch ($type) {
        case 'today':
            $like_time = current_time('Y-m-d');
            break;
        case 'yester':
            $today     = current_time('Y-m-d');
            $like_time = date("Y-m-d", strtotime("$today -1 day"));
            break;
        case 'this_month':
            $like_time = current_time('Y-m');
            break;
        case 'last_month': 
            $this_month = current_time('Y-m');
            $like_time  = date('Y-m', strtotime("$this_month -1 month"));
            break;
        case 'this_year': 
            $like_time = current_time('Y');
            break;
        case 'all': 
            $like_time = '';
            break;
        default:
            $like_time = current_time('Y-m-d');

    }

    global $wpdb;
    $result = (array) $wpdb->get_row("SELECT COUNT(*) AS count, SUM(pay_price) AS sum FROM `{$wpdb->iopayorder}` WHERE `pay_time` LIKE '%$like_time%' AND `status` = 1 AND `pay_price` > 0");

    if (!isset($result['count'])) {
        $data = $default;
    } else {
        $data = array(
            'count' => $result['count'] ?: 0,
            'sum'   => $result['sum'] ? : 0,
        );
    }
    return $data;
}
