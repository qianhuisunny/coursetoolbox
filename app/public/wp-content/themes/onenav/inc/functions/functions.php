<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-02-09 21:11:15
 * @LastEditors: iowen
 * @LastEditTime: 2024-04-10 17:29:43
 * @FilePath: /onenav/inc/functions/functions.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$functions = array(
    'io-check-link',
    'io-widget-tab',
    'io-widgets',
    'io-login',
    'io-user',
    'io-admin',
    'io-tools-hotcontent',
    'io-post',
    'io-single-post',
    'io-single-site',
    'io-single-app',
    'io-single-book',
    'io-letter-ico',
    'io-tool',
    'io-footer',
    'io-oauth',
    'io-meta',
    'io-search'
);

foreach ($functions as $function) {
    $path = 'inc/functions/' . $function . '.php';
    require get_theme_file_path($path);
}
/**
 * 获取分类排序规则
 * @param string $_order
 * @return array
 */
function get_term_order_args($_order){
    switch ($_order) {
        case 'views':
            $args = array(      
                'meta_key' => 'views',
                'orderby'  => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ),
            );
            break;
        case '_sites_order': 
            if ( io_get_option('sites_sortable',false)){
                $args = array(      
                    'orderby' => array( 'menu_order' => 'ASC', 'ID' => 'DESC' ),
                );
            }else{
                $args = array(      
                    'meta_key' => '_sites_order',
                    'orderby'  => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ),
                );
            }
            break;
        case '_down_count':
            $args = array(      
                'meta_key' => '_down_count',
                'orderby'  => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ),
            );
        case 'ID':
            $args = array(      
                'orderby' => $_order,
                'order'   => 'DESC',
            );
            break;
        default:
            $args = array(      
                'orderby' => array( $_order => 'DESC', 'ID' => 'DESC' ),
            );
            break;
    }
    return apply_filters('io_term_order_args_filters', $args, $_order);
}

/**
 * 排序
 * @description: 
 * @param string $db 数据库
 * @param object $origins 源数据
 * @param array $data 排序数据
 * @param string $origin_key 排序数据源key
 * @param string $order_key 数据库排序字段
 * @param string $where_key 判断条件
 * @return array
 */
function io_update_obj_order($db,$origins,$data,$origin_key,$order_key,$where_key='id'){
    $results = array(
        'status' => 0,
        'msg'    => '',
    );
    if (!is_array($origins) || count($origins) < 1){
        $results['msg'] = __('数据错误！','i_theme');
        return $results; 
    }
    //创建ID列表
    $objects_ids    = array();
    foreach($origins as $origin)
    {
        $objects_ids[] = (int)$origin->id;   
    }
    $index = 0;
    for($i = 0; $i < count($origins); $i++){
        if(!isset($objects_ids[$i]))
            break;
            
        $objects_ids[$i] = (int)$data[$origin_key][$index];//替换列表id为排序id
        $index++;
    }
    global $wpdb;
    //更新数据库中的菜单顺序
    foreach( $objects_ids as $order => $id ) 
    {
        $update = array(
            $order_key => $order
        );
        $wpdb->update( $db , $update, array($where_key => $id) ); 
    } 
    $results = array(
        'status' => 1,
        'msg'    => __('排序成功！','i_theme'),
    );
    return $results;
}
/**
 * 搜索
 * @return string
 */
function search_results(){   
    global $wp_query;    
    return get_search_query() . '<i class="text-danger px-1">•</i>' . sprintf(__('找到 %s 个相关内容', 'i_theme'), $wp_query->found_posts);
}

/**
 * 发送验证码
 * @param mixed $to 邮箱或者电话号码
 * @param string $type 类型
 * @return mixed
 */
function io_send_captcha($to, $type = 'email'){
    if(!session_id()) session_start();
    if (!empty($_SESSION['code_time'])) {
        $time_x = strtotime(current_time('mysql')) - strtotime($_SESSION['code_time']);
        if ($time_x < 60) {
            //剩余时间
            return array('status' => 2, 'msg' => (60 - $time_x) . '秒后可重新发送');
        }
    }
    $code = io_get_captcha();
    
    $_SESSION['reg_mail_token'] = $code;
    $_SESSION['new_mail']       = $to;
    $_SESSION['code_time']      = current_time('mysql');
    session_write_close();

    switch ($type) {
        case 'email':
            $result = io_mail($to, sprintf(__('「%s」邮箱验证码', 'i_theme'), get_bloginfo('name')), io_templet_verification_code(array('date' => date("Y-m-d H:i:s", current_time('timestamp')), 'code' => $code)));
            if (is_array($result)) {
                return array('status' => 3, 'msg' => $result['msg']);
            } elseif ($result) {
                return array('status' => 1, 'msg' => __('发送成功，请前往邮箱查看！', 'i_theme'));
            } else {
                return array('status' => 3, 'msg' => __('发送验证码失败，请稍后再尝试。', 'i_theme'));
            }
        case 'phone':
            $result = IOSMS::send($to, $code);
            if (!empty($result['result'])) {
                $result['error'] = 0;
                $result['msg'] = __('短信已发送', 'i_theme');
            }
            $ret = array('status' => 1, 'msg' => $result['msg']);
            if ($result['error'] == 1) {
                $ret['status'] = 3;
            }
            return $ret;
    }
}

/**
 * 验证码判断
 * @param mixed $type
 * @param mixed $to
 * @param mixed $code_name
 * @return bool
 */
function io_ajax_is_captcha($type, $to = '', $code_name = 'verification_code'){
    if (empty($to)) {
        io_error('{"status":2,"msg":"'.__('参数错误!','i_theme').'"}'); 
    } 
    $name = array(
        'email' => __('邮箱', 'i_theme'),
        'phone' => __('手机号', 'i_theme'),
    );
    if (empty($_REQUEST[$code_name])) {
        io_error('{"status":2,"msg":"'.sprintf(__('请输入%s验证码','i_theme'),$name[$type]).'"}'); 
    } 
    $is_captcha = io_is_captcha($type, $to, $_REQUEST[$code_name]);
    if ($is_captcha['error']) {
        io_error('{"status":3,"msg":"' . $is_captcha['msg'] . '"}');
    }

    return true;
}
/**
 * 获取二维码图片url
 * @param mixed $data
 * @param mixed $size
 * @param mixed $margin
 * @return string
 */
function get_qr_url($data, $size, $margin = 10){
    if (io_get_option('qr_api','local') === 'local') {
        //$cache_key = 'qr'.io_md5($data);
        //if(!get_transient($cache_key)){
        //    $_d = array(
        //        'u' => $data,
        //        's' => $size,
        //        'm' => $margin,
        //    );
        //    set_transient($cache_key, maybe_serialize($_d),YEAR_IN_SECONDS);
        //}
        //return esc_url(home_url()."/qr/{$cache_key}.png");
        return esc_url(home_url()."/qr/?text={$data}&size={$size}&margin={$margin}");
    } else {
        return str_ireplace(array('$size', '$url'), array($size, $data), io_get_option('qr_url',''));
    }
}

/**
 * 16位 md5
 * 
 * @param mixed $data
 * @return string
 */
function io_md5($data){
    $hash = md5($data);
    $short_hash = substr($hash, 8, 16);
    return $short_hash;
}

/**
 * 获取验证码input
 * @param mixed $id
 * @return mixed
 */
function get_captcha_input_html($id = '', $class = 'form-control'){
    if(!LOGIN_007) return true;
    $captcha_type = io_get_option('captcha_type','null');
    $input = '';
    switch ($captcha_type) {
        case 'image':
            $input = '<div class="image-captcha-group'.( in_array($id,array('io_submit_link','ajax_comment'))?'':' mb-2').'">';
            $input .= '<input captcha-type="image" type="text" size="6" name="image_captcha" class="'.$class.'" placeholder="'.__('图形验证码','i_theme').'" autocomplete="off">';
            $input .= '<input type="hidden" name="image_id" value="' . $id . '">';
            $input .= '<span class="image-captcha" data-id="' . $id . '" data-toggle="tooltip" title="'.__('点击刷新','i_theme').'"></span>';
            $input .= '</div>';
            break;
        case 'slider':
            $input = '<input captcha-type="slider" type="hidden" name="captcha_type" value="slider" slider-id="">';
            break;
        case 'tcaptcha':
            $option = io_get_option('tcaptcha_option');
            if (!empty($option['appid']) && !empty($option['secret_key'])) {
                $input = '<input captcha-type="tcaptcha" type="hidden" name="captcha_type" value="tcaptcha" data-appid="' . $option['appid'] . '" data-isfree="'.(empty($option['api_secret_id'])?'true':'false').'">';
            }
            break;
        case 'geetest':
            $option = io_get_option('geetest_option');
            if (!empty($option['id']) && !empty($option['key'])) {
                $input = '<input captcha-type="geetest" type="hidden" name="captcha_type" value="geetest" data-appid="' . $option['id'] . '">';
            }
            break;
        case 'vaptcha':
            $option = io_get_option('vaptcha_option');
            if (!empty($option['id']) && !empty($option['key'])) {
                $input = '<input captcha-type="vaptcha" type="hidden" name="captcha_type" value="vaptcha" data-appid="' . $option['id'] . '" data-scene="' . (char_to_num($id)%5) . '">';
            }
            break;
    }
    io_add_captcha_js_html($captcha_type);
    return $input;
}
function io_add_captcha_js_html($status = ''){
    $status = $status ?: io_get_option('captcha_type', 'null');
    if ($status != 'null') {
        add_captcha_js();
        wp_enqueue_script('captcha');
    }
}

/**
 * 验证码是否有效
 * @param mixed $type
 * @param mixed $to
 * @param mixed $code
 * @return array
 */
function io_is_captcha($type, $to, $code){
    $name = array(
        'email' => __('邮箱', 'i_theme'),
        'phone' => __('手机号', 'i_theme'),
    );
    if(!session_id()) session_start(); 

    if (empty($_SESSION['reg_mail_token']) || $_SESSION['reg_mail_token'] != $code || empty($_SESSION['new_mail']) || $_SESSION['new_mail'] != $to) {
        return array('error' => 1, 'msg' => sprintf( __('%s验证码错误！', 'i_theme'),$name[$type]));
    } else {
        if (!empty($_SESSION['code_time'])) {
            $time_x = strtotime(current_time('mysql')) - strtotime($_SESSION['code_time']);
            if ($time_x > 1800) {//30分钟有效
                return array('error' => 1, 'msg' => sprintf( __('%s验证码已过期', 'i_theme'),$name[$type]));
            }
        }
        return array('error' => 0, 'msg' => sprintf( __('%s验证码效验成功', 'i_theme'),$name[$type]));
    }
}
/**
 * 删除验证码
 * @return void
 */
function io_remove_captcha(){
    if(!session_id()) session_start(); 
    unset($_SESSION['new_mail']);
    unset($_SESSION['reg_mail_token']);
    unset($_SESSION['code_time']);
}
/**
 * 人机验证
 * @param mixed $id
 * @return bool
 */
function io_ajax_is_robots($id=''){
    if(!LOGIN_007) return true;
    $captcha_type = io_get_option('captcha_type','null');
    switch ($captcha_type) {
        case 'image':
            $id = isset($_REQUEST['image_id']) ? esc_sql($_REQUEST['image_id']) : '';
            $id = $id ?: (!empty($_REQUEST['action']) ? $_REQUEST['action'] : 'code');
            if(!session_id()) session_start();
            if (empty($_REQUEST['image_captcha']) || strlen($_REQUEST['image_captcha']) < 4) {
                echo (json_encode(array('status' => 2, 'msg' => '请输入图形验证码')));
                exit();
            }
            if (empty($_SESSION['captcha_img_code_' . $id]) || empty($_SESSION['captcha_img_time_' . $id])) {
                echo (json_encode(array('status' => 3, 'msg' => '环境异常，请刷新后重试')));
                exit();
            }
            if ($_SESSION['captcha_img_code_' . $id] !== strtolower($_REQUEST['image_captcha'])) {
                echo (json_encode(array('status' => 3, 'msg' => '图形验证码错误')));
                exit();
            }
            if (($_SESSION['captcha_img_time_' . $id] + 300) < time()) {
                echo (json_encode(array('status' => 3, 'msg' => '图形验证码已过期')));
                unset($_SESSION['captcha_img_code_' . $id]);
                unset($_SESSION['captcha_img_time_' . $id]);
                exit();
            }
            break;
        case 'slider':
            if (empty($_REQUEST['captcha']['ticket']) || empty($_REQUEST['captcha']['randstr']) || empty($_REQUEST['captcha']['spliced']) || empty($_REQUEST['captcha']['check'])) {
                echo (json_encode(array('status' => 2, 'msg' => '人机验证失败!')));
                exit();
            }
            if (!io_slider_captcha_verification($_REQUEST['captcha']['ticket'], $_REQUEST['captcha']['randstr'])) {
                echo (json_encode(array('status' => 2, 'msg' => '人机验证失败!')));
                exit();
            }
            break;
        case 'tcaptcha':
            if (empty($_REQUEST['captcha']['ticket']) || empty($_REQUEST['captcha']['randstr'])) {
                echo (json_encode(array('status' => 2, 'msg' => '人机验证失败!')));
                exit();
            }
            $tencent007 = io_tcaptcha_verification($_REQUEST['captcha']['ticket'], $_REQUEST['captcha']['randstr']);
            if($tencent007['error']){
                echo (json_encode(array('status' => 2, 'msg' => $tencent007['msg'])));
                exit();
            }
            break;
        case 'geetest':
            if (empty($_REQUEST['captcha']['ticket']) || empty($_REQUEST['captcha']['lot_number'])) {
                echo (json_encode(array('status' => 2, 'msg' => '人机验证失败!')));
                exit();
            }
            $verification = io_geetest_verification($_REQUEST['captcha']);
            if ($verification['error']) {
                echo (json_encode(array('status' => 2, 'msg' => $verification['msg'])));
                exit();
            }
            break;
        case 'vaptcha':
            if (empty($_REQUEST['captcha']['ticket']) || empty($_REQUEST['captcha']['server'])) {
                echo (json_encode(array('status' => 2, 'msg' => '人机验证失败!')));
                exit();
            }
            $verification = io_vaptcha_verification($_REQUEST['captcha']);
            if ($verification['error']) {
                echo (json_encode(array('status' => 2, 'msg' => $verification['msg'])));
                exit();
            }
            break;
    }
    return true;
}

/**
 * 腾讯请求服务器验证
 */
function io_tcaptcha_verification($Ticket,$Randstr){
    $option         = io_get_option('tcaptcha_option');
    $AppSecretKey   = $option['secret_key'];  
    $appid          = $option['appid'];  
    $UserIP         = IOTOOLS::get_ip();
    $http           = new Yurun\Util\HttpRequest;
    if(!empty($option['api_secret_id'])){
        $url = "https://captcha.tencentcloudapi.com";
        $params = array(
            "Action"       => 'DescribeCaptchaResult',
            "Version"      => '2019-07-22',
            "CaptchaType"  => 9,
            "Ticket"       => $Ticket,
            "UserIp"       => $UserIP,
            "Randstr"      => $Randstr,
            "CaptchaAppId" => (int)$appid,
            "AppSecretKey" => $AppSecretKey,
            "Timestamp"    => time(),
            "Nonce"        => rand(),
            "SecretId"     => $option['api_secret_id'],
        );
        $params["Signature"] = tcaptcha_calculate_sig($params,$option['api_secret_key']);

        $result = [];
        $result['response'] = 0;

        $response = $http->post($url, $params);
        $ret      = $response->json(true);

        if(!isset($ret['Response'])){
            $result['err_msg'] = $ret;
        } else {
            $resp = $ret['Response'];
            if (!empty($resp['Error']['Message'])) {
                $result['err_msg'] = $resp['Error']['Message'];
            } elseif (isset($resp['CaptchaMsg'])) {
                if ($resp['CaptchaCode'] === 1 || strtolower($resp['CaptchaMsg']) === 'ok') {
                    $result['response'] = 1;
                } elseif ($resp['CaptchaMsg']) {
                    $result['err_msg'] = $resp['CaptchaMsg'];
                    $result['captcha_code'] = $resp['CaptchaCode'];
                }
            } else {
                $result['err_msg'] = $ret;
            }
        }
    } else {
        $url = "https://ssl.captcha.qq.com/ticket/verify";
        $params = array(
            "aid"          => $appid,
            "AppSecretKey" => $AppSecretKey,
            "Ticket"       => $Ticket,
            "Randstr"      => $Randstr,
            "UserIP"       => $UserIP
        );
        $response = $http->get($url, $params);
        $result   = $response->json(true);
    }
    if($result){
        if($result['response'] == 1){
            
            return array(
                'error'=>0,
                'msg'  => ''
            );
        }else{
            return array(
                'error'=>1,
                'msg'  => (isset($result['captcha_code'])?$result['captcha_code'].': ':'').$result['err_msg']
            );
        }
    }else{
        return array(
            'error'=>1,
            'msg'  => __('请求失败,请再试一次！','i_theme')
        );
    }
}
/**
 * 腾讯验证码签名
 * @param mixed $param
 * @param mixed $secretKey
 * @return string
 */
function tcaptcha_calculate_sig($param,$secretKey) { 
    $tmpParam = [];
    ksort($param);
    foreach ($param as $key => $value) {
        array_push($tmpParam, $key . "=" . $value);
    }
    $strParam  = join("&", $tmpParam);
    $signStr   = 'POSTcaptcha.tencentcloudapi.com/?' . $strParam;
    $signature = base64_encode(hash_hmac('SHA1', $signStr, $secretKey, true));
    return $signature;
}

/**
 * 滑动拼图验证
 * @param mixed $Ticket
 * @param mixed $Randstr
 * @return bool
 */
function io_slider_captcha_verification($Ticket, $Randstr){
    if(!session_id()) session_start();
    if (empty($_SESSION['captcha_slider_x']) || empty($_SESSION['captcha_slider_rand_str'])) {
        return false;
    }
    $machine_slider_x        = $_SESSION['captcha_slider_x'];
    $machine_slider_rand_str = $_SESSION['captcha_slider_rand_str'];
    
    $T_a = (int) substr($Ticket, 0, 2);
    $T_b = (int) substr($Ticket, -2);
    $T_x = (int) substr($Ticket, $T_a + 2, $T_b - 2);

    if (absint($T_x - $machine_slider_x) > 8) {
        return false;
    }

    $R_a = (int) substr($Randstr, 0, 1);
    $R_b = (int) substr($Randstr, -2);
    $R_x = substr($machine_slider_rand_str, $R_a, $R_b - $R_a);
    if ($R_a . $R_x . $R_b !== $Randstr) {
        return false;
    }

    return true;
}
/**
 * 极验行为验
 * @param mixed $data
 * @return array
 */
function io_geetest_verification($data){
    $option         = io_get_option('geetest_option');
    $api_server     = "http://gcaptcha4.geetest.com/validate?captcha_id=" . $option['id'];
    $captcha_key    = $option['key'];
    $lot_number     = $data['lot_number'];
    $captcha_output = $data['captcha_output'];
    $pass_token     = $data['ticket'];
    $gen_time       = $data['gen_time'];
    $sign_token     = hash_hmac('sha256', $lot_number, $captcha_key);

    $query = array(
        "lot_number"     => $lot_number,
        "captcha_output" => $captcha_output,
        "pass_token"     => $pass_token,
        "gen_time"       => $gen_time,
        "sign_token"     => $sign_token,
    );

    $http     = new Yurun\Util\HttpRequest;
    $response = $http->post($api_server, $query);
    $result   = $response->json(true);

    if (!isset($result['result'])) {
        return array('error' => 1, 'msg' => '验证失败');
    }

    if ($result['result'] === 'success') {
        return array('error' => 0);
    }

    return array('error' => 1, 'msg' => '验证失败' . ((!empty($result['reason']) ? '：' . $result['reason'] : '')) . ((!empty($result['msg']) ? '：' . $result['msg'] : '')));
}


/**
 * vaptcha
 * @param mixed $data
 * @return array
 */
function io_vaptcha_verification($data){
    $option    = io_get_option('vaptcha_option');
    $api_server = $data['server'];
    $token      = $data['ticket'];
    $user_ip    = IOTOOLS::get_ip(); 

    $query = array(
        "id"        => $option['id'],
        "secretkey" => $option['key'],
        "scene"     => 0,
        "token"     => $token,
        "ip"        => $user_ip,
    );

    $http     = new Yurun\Util\HttpRequest;
    $response = $http->post($api_server, $query);
    $result   = $response->json(true);

    if (!isset($result['success'])) {
        return array('error' => 1, 'msg' => '验证失败');
    }

    if ($result['success']) {
        return array('error' => 0);
    }

    return array('error' => 1, 'msg' => '验证失败' .  (!empty($result['msg']) ? '：' . $result['msg'] : ''));
}

/**
 * 获取多语言规则列表
 * 
 * @return string
 */
function io_get_lang_rules(){
    if(!io_get_option('m_language',false)){
        return '';
    }else{
        return '(' . io_get_option('lang_list', 'en') . ')/';
    }
}
/**
 * 内容可见度权限
 * 
 * @param mixed $query
 * @return void
 */
function io_posts_purview_query_var_filter( $query ){
    global $current_user, $pagenow;
    if (is_preview() || "upload.php" == $pagenow || "admin.php"== $pagenow || isset($_REQUEST['action']) && 'query-attachments' === $_REQUEST['action']) {
        return;
    }
    $post_type = $query->get('post_type');
    $types = array('sites', 'post', 'app', 'book');
    if (!empty($post_type) && is_array($post_type))
        $post_type = $post_type[0];
    if (empty($post_type) && is_single()) {
        $post_type = 'post';
    }
    if (
        (is_admin() && defined('DOING_AJAX') && DOING_AJAX) ||
        (!is_admin() && (
            ($post_type && in_array($post_type, $types)) || ($query->is_main_query() && is_archive())
        ))
    ) {
        //判断查询中是否有meta_query
        if (isset($query->query_vars['meta_query'])) {
            $meta_query = $query->query_vars['meta_query'];
            if (is_array($meta_query)) {
                $meta_query = array_merge($meta_query, get_post_user_purview_level_query_var());
            } else {
                $meta_query = get_post_user_purview_level_query_var();
            }
            $query->set('meta_query', $meta_query);
        } else {
            $query->set('meta_query', get_post_user_purview_level_query_var());
        }
    } 
}
add_action('pre_get_posts', 'io_posts_purview_query_var_filter');//pre_get_posts parse_query

function io_user_purview_level_query_var_filter( $args ){
    if(is_preview()){
        return $args;
    }
    //判断查询中是否有meta_query
    if (isset($args['meta_query'])) {
        $meta_query = $args['meta_query'];
        if (is_array($meta_query)) {
            $meta_query = array_merge($meta_query, get_post_user_purview_level_query_var());
        } else {
            $meta_query = get_post_user_purview_level_query_var();
        }
        $args['meta_query'] = $meta_query;
    } else {
        $args['meta_query'] = get_post_user_purview_level_query_var();
    }
    return $args;
}
add_action('io_blog_post_query_var_filters', 'io_user_purview_level_query_var_filter');
add_action('io_archive_query_var_filters', 'io_user_purview_level_query_var_filter');

function io_user_add_cap() {
    foreach (array('subscriber', 'editor', 'author', 'contributor') as $user_role) {
        $role = get_role($user_role);
        if(is_object($role)){
            $role->add_cap('edit_posts');
        }
    }
}
add_action('init', 'io_user_add_cap');

/**
 * 获取query_var
 * 
 * @return array
 */
function get_post_user_purview_level_query_var(){
    $option = io_get_option('global_remove','close');
    if ('close' === $option) {
        return array();
    }
    $args = array(
        array(
            'key'     => '_user_purview_level',
            'value'   => array('user','all','buy'),
            'compare' => 'IN'
        )
    );
    $user   = wp_get_current_user();
    if(!$user->ID && in_array($option, array('admin', 'user'))){
        $args = array(
            array(
                'key'     => '_user_purview_level',
                'value'   => 'all', //添加 buy ？？？？？？？？
                'compare' => '='
            )
        );
    } else {
        if (user_can($user->ID, 'manage_options')) {
            $args = array(
                array(
                    'key'     => '_user_purview_level',
                    'value'   => array('admin','user','all','buy'),
                    'compare' => 'IN'
                )
            );
        } else {
            // TODO 其他用户权限 VIP 等
            $args = array(
                array(
                    'key'     => '_user_purview_level',
                    'value'   => array('user','all','buy'),
                    'compare' => 'IN'
                )
            );
        }
    }
    return $args;
}

/**
 * 用户授权说明提示，操作引导
 * 
 * @param string $post_type
 * @param bool $echo
 * @return void|string
 */
function get_user_level_directions_html($post_type, $echo = false){
    global $post;
    $post_id = $post->ID;
    //$option = io_get_option('global_remove','close');
    //if ('close' === $option) {
    //    return false;
    //}
    
    $user_level = get_post_meta($post_id, '_user_purview_level', true);
    if(!$user_level){
        update_post_meta($post_id, '_user_purview_level', 'all');
        return false;
    }

    if($user_level && 'buy'===$user_level){ 
        $buy_option = get_post_meta($post_id, 'buy_option', true);
    }
    if(isset($buy_option)){
        if('view' === $buy_option['buy_type']){
            $is_buy = iopay_is_buy($post_id);
        }
    }

    $user   = wp_get_current_user();
    if (!$user->ID && $user_level && in_array($user_level, array('admin','user'))) {
        $title     = __('权限不足', 'i_theme');
        $tips      = __('此内容已隐藏，请登录后查看！', 'i_theme');
        $btn       = __('登录查看', 'i_theme');
        $ico       = 'icon-user';
        $color     = '';
        $url_class = '';
        $url       = esc_url(wp_login_url(io_get_current_url()));
        $meta      = '';
        $tips_b    = '';
    }
    if (isset($is_buy) && !$is_buy) {
        $title     = __('付费阅读', 'i_theme');
        $tips      = __('此内容已隐藏，请购买后查看！', 'i_theme');
        $btn       = __('购买查看', 'i_theme');
        $ico       = 'icon-buy_car';
        $color     = '';
        $url_class = 'io-ajax-modal-get nofx';
        $url       = esc_url(add_query_arg(array('action' => 'pay_cashier_modal', 'id' => $post_id, 'index' => 0), admin_url('admin-ajax.php'))); 
        $meta      = '';
        $buy_data  = get_post_meta($post_id, 'buy_option', true);
        $org       = '';
        $tag       = '';
        if ((float) $buy_data['pay_price'] < (float) $buy_data['price']) {
            $org = '<span class="original-price text-sm"><span class="text-xs">' . io_get_option('pay_unit', '￥') . '</span>' . $buy_data['price'] . '</span>';
            $tag = '<div class="badge vc-red"><i class="iconfont icon-time-o mr-2"></i>' . __('限时特惠', 'i_theme') . '</div>';
        }
        $meta   .= '<div class="text-32"><span class="text-xs text-danger">' . io_get_option('pay_unit', '￥') . '</span><span class="text-danger font-weight-bold">' . $buy_data['pay_price'] . '</span> ' . $org . '</div>'.$tag;
        $tips_b = iopay_pay_tips_box('end');
    }
    if(!isset($url)){
        return false;
    }
    $name      = get_the_title();
    $thumbnail = io_get_post_thumbnail($post);

    $html = '<div class="user-level-box mb-5">';
    $html .= '<div class="user-level-header io-radius modal-header-bg ' . $color . ' px-3 py-1 py-md-2">';
    $html .= '<div class="text-lg mb-5"><i class="iconfont icon-version mr-2"></i>';
    $html .= '<span>' . $title . '</span></div>';
    $html .= '</div>';

    $html .= '<div class="user-level-body d-flex io-radius shadow bg-blur p-3 mt-n5 ml-1 ml-md-3">';
    $html .= '<div class="card-thumbnail img-type-' . $post_type . ' mr-2 mr-md-3 d-none d-md-block">';
    $html .= '<div class="h-100 img-box">';
    $html .= '<img src="' . $thumbnail . '" alt="' . $name . '">';
    $html .= '</div> ';
    $html .= '</div> ';
    $html .= '<div class="d-flex flex-fill flex-column">';
    $html .= '<div class="list-body">';
    $html .= '<h1 class="h5 overflowClip_2">' . $name . '</h1>';
    $html .= '<div class="mt-2 text-xs text-muted"><i class="iconfont icon-tishi mr-1"></i>' . $tips . '</div>';
    $html .= $meta;
    $html .= '</div> ';
    $html .= '<div class="text-right">';
    $html .= '<a href="' . $url . '" class="btn vc-blue btn-outline ' . $url_class . ' btn-md-lg"><i class="iconfont ' . $ico . ' mr-2"></i>' . $btn . '</a>';
    $html .= '</div>'; 
    $html .= '</div>';
    $html .= '</div>';
    $html .= $tips_b;
    $html .= '</div>';

    if ($echo)
        echo $html;
    else
        return $html;
}

/**
 * 判断是否已经评论
 * @param mixed $user_id
 * @param mixed $post_id
 * @return bool|null|string
 */
function io_user_is_commented($user_id = 0, $post_id = 0){
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $WHERE = '';
    if ($user_id) {
        $WHERE = "`user_id`={$user_id}";
    } elseif (isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {
        $email = str_replace('%40', '@', $_COOKIE['comment_author_email_' . COOKIEHASH]);
        $WHERE = "`comment_author_email`='{$email}'";
    } else {
        return false;
    }

    global $wpdb;
    $query = "SELECT `comment_ID` FROM {$wpdb->comments} WHERE `comment_post_ID`={$post_id} and `comment_approved`='1' and $WHERE LIMIT 1";
    return $wpdb->get_var($query);
}

/**
 * 获取模态框的炫彩头部
 * @param mixed $class
 * @param mixed $icon
 * @param mixed $title
 * @return string
 */
function io_get_modal_header($class = 'fx-blue', $icon = '', $title = ''){
    $class = !empty($class) ? $class : 'fx-blue';
    $html = '<div class="modal-header modal-header-bg ' . $class . '">';
    $html .= '<button type="button" class="close io-close" data-dismiss="modal" aria-label="Close"><i class="iconfont icon-close-circle text-xl" aria-hidden="true"></i></button>';
    $html .= '<div class="text-center">';
    $html .= $icon ? '<i class="iconfont ' . $icon . ' icon-2x"></i>' : '';
    $html .= $title ? '<div class="mt-2 text-lg">' . $title . '</div>' : '';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}

/**
 * 获取模态框简单头部
 * @param mixed $class
 * @param mixed $icon
 * @param mixed $title
 * @return string
 */
function io_get_modal_header_simple($class = 'vc-blue', $icon = '', $title = ''){
    $class = !empty($class) ? $class : 'vc-blue';
    $html = '<div class="modal-header py-2 modal-header-simple ' . $class . '">';
    $html .= '<span></span>';
    $html .= '<div class="text-md">';
    $html .= $icon ? '<i class="iconfont ' . $icon . ' mr-2"></i>' : '';
    $html .= $title ? '<span class="text-sm">' . $title . '</span>' : '';
    $html .= '</div>';
    $html .= '<button type="button" class="close io-close" data-dismiss="modal" aria-label="Close"><i class="iconfont icon-close-circle text-xl" aria-hidden="true"></i></button>';
    $html .= '</div>';
    return $html;
}


/**
 * ajax模态框通知
 * @param mixed $type
 * @param mixed $msg
 * @return never
 */
function io_ajax_notice_modal($type = 'warning', $msg = ''){
    $type_class = array(
        'success' => 'blue',
        'info'    => 'green',
        'warning' => 'yellow',
        'danger'  => 'red',
    );
    $icon_class = array(
        'success' => 'icon-adopt',
        'info'    => 'icon-tishi',
        'warning' => 'icon-warning',
        'danger'  => 'icon-crying-circle',
    );

    $class = isset($type_class[$type]) ? $type_class[$type] : 'yellow';
    $icon  = isset($icon_class[$type]) ? $icon_class[$type] : 'icon-warning';

    $html = io_get_modal_header('fx-' . $class, $icon);
    $html .= '<div class="modal-body bg-blur">';
    $html .= '<div class="d-flex justify-content-center align-items-center text-md p-3 c-' . $class . '" style="min-height:135px">' . $msg . '</div>';
    $html .= '</div>';
    echo $html;
    exit;
}
/**
 * 头部效果
 * @return string
 */
function io_header_fx(){
    $s = false;
    if($s){
        return '';
    }
    $html = '<div class="background-fx">';
    for ($i=1; $i < 12; $i++) { 
        $index = sprintf("%02d", $i);
        $html .= '<img src="'. get_theme_file_uri('/images/fx/shape-'.$index.'.svg') .'" class="shape-'.$index.'">';
    }
    $html .= '</div>';
    return $html;
}
/**
 * 获取编辑按钮
 * 
 * @param mixed $text
 * @param mixed $before
 * @param mixed $after
 * @param mixed $post_id
 * @param mixed $class
 * @return string|null
 */
function io_get_post_edit_link( $post_id = 0, $text = null, $before = '', $after = '', $class = 'post-edit-link' ) {
    $url = get_edit_post_link( $post_id );
    if ( ! $url ) {
        return;
    }

    $text   = $text?:'<i class="iconfont icon-modify mr-1"></i>'.__('编辑','i_theme');
    $before = $before?:'<span class="edit-link text-xs ml-2 text-muted">';
    $after  = $after?:'</span>';

    if ( null === $text ) {
        $text = __( 'Edit This' );
    }

    $link = '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' . $text . '</a>';

    return $before .  $link . $after;
}

/**
 * 获取文章分类和标签html
 * 
 * @param int    $post_id
 * @param array  $taxonomy
 * @param string $before 
 * @param string $sep    
 * @param string $after  
 * @return string
 */
function io_get_post_tags($post_id, $taxonomy, $before = '', $sep = '', $after = ''){
    $before = $before?:'<span class="mr-2">';
    $sep    = $sep?:'<i class="iconfont icon-wailian text-ss"></i></span> <span class="mr-2">';
    $after  = $after?:'<i class="iconfont icon-wailian text-ss"></i></span>';

    $html = '';
    foreach ($taxonomy as $tax) {
        $html .= get_the_term_list($post_id, $tax, $before, $sep, $after);
    }
    return $html;
}

/**
 * 获取文章分类和标签按钮html
 * 
 * @param int    $post_id
 * @param array  $taxonomy
 * @param string $before
 * @param string $after
 * @param string $count
 * @return string
 */
function io_get_cat_tags_btn($post_id, $taxonomy, $before = '', $after = '', $count = 0){
    $color = array(
        'vc-l-gray', 
        'vc-l-red', 
        'vc-l-yellow', 
        'vc-l-cyan', 
        'vc-l-blue', 
        'vc-l-violet', 
        ''
    );

    $i = 0;
    $btn = '';
    foreach ($taxonomy as $tax) {
        $datas = get_the_terms( $post_id, $tax );
        if($datas){
            foreach($datas as $tag) {
                $btn .= '<a href="'.get_tag_link($tag->term_id).'" class="btn ' . $color[mt_rand(0, count($color)-1)] . ' btn-sm text-xs text-height-xs m-1 rounded-pill"  rel="tag" title="'.__('查看更多文章','i_theme').'">' . $before . $tag->name . $after . '</a>';
                $i++;
                if ($count && $i == $count) {
                    break;
                }
            }
        }
    }
    return $btn;
}

/**
 * 获取文章时间
 * 
 * @return string
 */
function io_get_post_time(){
    global $post;
    $modified_time = get_the_modified_time('U', $post);
    $time          = get_the_time('U', $post);

    if ($modified_time > $time) {
        $time_html = '<span title="' . io_date_time($time) . __('发布','i_theme').'">' . timeago($modified_time) . __('更新','i_theme').'</span>';
    } else {
        $time_html = '<span title="' . io_date_time($time) . __('发布','i_theme').'">' . timeago($time) . __('发布','i_theme').'</span>';
    }
    return $time_html;
}
/**
 * 下载列表模态框
 * 
 * @param mixed $title 标题
 * @param mixed $down_list 资源列表
 * @param mixed $type app book
 * @param mixed $decompression
 * @return string
 */
function io_get_down_modal($title, $down_list, $type, $decompression = '', $count = 0){
    global $post;
    $post_id = $post->ID;
    $key     = '';
    if('app'===$type){
        $key = 'down_btn_';
    }
    $html = '<div class="modal fade search-modal resources-down-modal" id="'.$type.'-down-modal">';
    $html .= '<div class="modal-dialog modal-lg modal-dialog-centered">';
    $html .= '<div class="modal-content overflow-hidden">';
    $html .= io_get_modal_header_simple('', 'icon-down', $title );
    $html .= '<div class="modal-body down_body">';

    $html .= '<div class="down_btn_list mb-4">';
    
    if($down_list){
        $html .= '<div class="row no-gutters">';
        $html .= '<div class="col-6 col-md-7">'.__('描述','i_theme').'</div>';
        $html .= '<div class="col-2 col-md-2" style="white-space: nowrap;">'.__('提取码','i_theme').'</div>';
        $html .= '<div class="col-4 col-md-3 text-right">'.__('下载','i_theme').'</div>';
        $html .= '</div>';
        $html .= '<div class="col-12 line-thead my-2" style="height:1px;background: rgba(136, 136, 136, 0.4);"></div>';

        $list = '';
        for($i=0; $i<count($down_list); $i++){
            if ($count && $i > $count) {
                break;
            }
            $list .= '<div class="row no-gutters">';
            $list .= '<div class="col-6 col-md-7">'. ($down_list[$i][$key.'info']?:__('无','i_theme')) .'</div>';
            $list .= '<div class="col-2 col-md-2" style="white-space: nowrap;">'. ($down_list[$i][$key.'tqm']?:__('无','i_theme')) .'</div>';
            $list .= '<div class="col-4 col-md-3 text-right"><a class="btn btn-danger custom_btn-d py-0 px-1 mx-auto down_count text-sm" href="'. go_to($down_list[$i][$key.'url']) .'" target="_blank" data-id="'. $post_id .'" data-action="down_count" data-clipboard-text="'.($down_list[$i][$key.'tqm']?:'').'" data-mmid="down-mm-'.$i.'">'.$down_list[$i][$key.'name'].'</a></div>';
            if($down_list[$i][$key.'tqm']) 
                $list .= '<input type="text" style="width:1px;position:absolute;height:1px;background:transparent;border:0px solid transparent" name="down-mm-'.$i.'" value="'.$down_list[$i][$key.'tqm'].'" id="down-mm-'.$i.'">';
            $list .= '</div>';
            $list .= '<div class="col-12 line-thead my-2" style="height:1px;background: rgba(136, 136, 136, 0.2);"></div>';
        }
        $html .= '<div class="down_btn_row">'.$list.'</div>';

    }else{
        $html .= '<div class="tips-box btn-block">'.__('没有内容','i_theme').'</div>';
    }
    if ($decompression)
        $html .= '<div class="w-100 text-right"><p class="mt-2 tips-box text-sm py-0">' . __('解压密码：', 'i_theme') . $decompression . '</p></div>';
    $html .= '</div>';

    $html .= show_ad('ad_res_down_popup', false, '<div class="apd apd-footer d-none d-md-block mb-4">', '</div>', false);       
    $html .= '<div class="io-alert border-2w text-sm" role="alert"><i class="iconfont icon-statement mr-2" ></i><strong>' . __('声明：', 'i_theme') . '</strong>' . io_get_option('down_statement', '') . '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';  
    $html .= '</div>'; 
    
    return $html;
}
/**
 * 头部层级导航
 * @return string
 */
function io_post_header_nav($type){
    global $post;
    $html  = '';
    $terms = get_the_terms( $post->ID, $type ); 
    if(isset($_GET['mininav-id'])){
        // 加入次级导航链接
        $html .= '<a class="btn-cat custom_btn-d mr-1" href="' . esc_url( get_permalink(intval($_GET['mininav-id'])) ) . '">' . get_post( intval($_GET['mininav-id']) )->post_title . '</a>';
        $html .= '<i class="iconfont icon-arrow-r-m custom-piece_c" style="font-size:50%;color:#f1404b;vertical-align:0.075rem"></i>';
    }
    if( !empty( $terms ) ){
        foreach( $terms as $term ){
            if($term->parent != 0){
                $parent_category = get_term( $term->parent );
                $html .= '<a class="btn-cat custom_btn-d mr-1" href="' . esc_url( get_category_link($parent_category->term_id)) . '">' . esc_html($parent_category->name) . '</a>';
                $html .= '<i class="iconfont icon-arrow-r-m custom-piece_c" style="font-size:50%;color:#f1404b;vertical-align:0.075rem"></i>';
                break;
            }
        } 
        foreach( $terms as $term ){
            $name = $term->name;
            $link = esc_url( get_term_link( $term, $type ) );
            $html .= "<a class='btn-cat custom_btn-d mr-1' href='{$link}'>{$name}</a>";
        }
    }
    return $html;
}

/**
 * 返回菜单，并赋值
 * @param mixed $is_min_nav
 * @return array｜object
 */
function io_get_menu_categories(&$is_min_nav){
    global $menu_categories;

    $_min_nav = false;
    if ($nav_id = get_post_meta(get_the_ID(), 'nav-id', true)) {
        $categories = $menu_categories ?: get_menu_list($nav_id);
        $_min_nav = true;
    } elseif (isset($_GET['menu-id'])) { //次级导航菜单
        $categories = $menu_categories ?: get_menu_list($_GET['menu-id']);
        $_min_nav = true;
    } else {
        $categories = $menu_categories ?: get_menu_list('nav_menu');
    }
    if (!$menu_categories)
        $menu_categories = $categories;

    $is_min_nav = $_min_nav;

    return $menu_categories;
}

/**
 * 知心天气
 * @return void
 */
function io_get_weather_widget($html = ''){
    $locale = 'zh-chs';
    switch (get_locale()) {
        case 'zh':
        case 'zh_CN':
            $locale = 'zh-chs';
            break;
        case 'zh_TW':
        case 'zh_HK':
            $locale = 'zh-cht';
            break;
        case 'pt_PT':
        case 'pt_BR':
        case 'pt_AO':
            $locale = 'pt';
            break;
        case 'ja':
            $locale = 'ja';
            break;
        case 'en_AU':
        case 'en_GB':
        case 'en_US':
        default:
            $locale = 'en';
            break;
    }
    echo '<div id="he-plugin-simple">'.$html.'</div>';
    echo '<script>(function(T,h,i,n,k,P,a,g,e){g=function(){P=h.createElement(i);a=h.getElementsByTagName(i)[0];P.src=k;P.charset="utf-8";P.async=1;a.parentNode.insertBefore(P,a)};T["ThinkPageWeatherWidgetObject"]=n;T[n]||(T[n]=function(){(T[n].q=T[n].q||[]).push(arguments)});T[n].l=+new Date();if(T.attachEvent){T.attachEvent("onload",g)}else{T.addEventListener("load",g,false)}}(window,document,"script","tpwidget","//widget.seniverse.com/widget/chameleon.js"))</script>';
    echo '<script>tpwidget("init",{"flavor": "slim","location": "WX4FBXXFKE4F","geolocation": "enabled","language": "'.$locale.'","unit": "c","theme": "chameleon","container": "he-plugin-simple","bubble": "enabled","alarmType": "badge","color": "#999999","uid": "UD5EFC1165","hash": "2ee497836a31c599f67099ec09b0ef62"});tpwidget("show");</script>'; 
}