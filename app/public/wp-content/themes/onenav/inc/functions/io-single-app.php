<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2023-02-28 00:39:46
 * @LastEditors: iowen
 * @LastEditTime: 2024-01-28 23:26:25
 * @FilePath: \onenav\inc\functions\io-single-app.php
 * @Description: 
 */

/**
 * app 头部
 * @param mixed $is_hide
 * @return string
 */
function io_app_header(&$is_hide){
    global $post,$app_down_list,$app_name,$app_screen;
    $post_id        = $post->ID;
    $app_down_list  = io_get_app_down_by_index($post_id);
    $app_screen     = get_post_meta($post_id, '_screenshot', true); 
    $app_name       = get_post_meta($post_id, '_app_name', true)?:get_the_title(); 

    $level_d        = get_user_level_directions_html('app');
    if($level_d){
        $is_hide = true;
        return $level_d;
    }

    $html = '<div class="row app-content py-5 mb-xl-5 mb-0 mx-xxxl-n5">';
    $html .= '<!-- app信息 -->';
    $html .= '<div class="col">';
    $html .= io_app_header_info();
    $html .= '</div>';
    $html .= '<!-- app信息 END -->';
    $html .= '<!-- 截图幻灯片 -->';
    $html .= io_app_screenshot_slide($app_name, $app_screen);
    $html .= '<!-- 截图幻灯片 END -->';
    $html .= '</div>';

    $is_hide = false;
    return $html;
}
/**
 * app 正文
 * @return void
 */
function io_app_content(){ 
    global $post,$app_down_list;
    $post_id = $post->ID;
    
    do_action('io_single_content_before', $post_id, 'app');
    ?>
    <div class="panel site-content card transparent"> 
        <div class="card-body p-0">
            <div class="apd-bg">
                <?php show_ad('ad_app_content_top',false, '<div class="apd apd-right">' , '</div>'); ?>
            </div> 
            <div class="panel-body single my-4 ">
            <?php 
            do_action('io_single_before', 'app');
            the_content();
            thePostPage();
            do_action('io_single_after', 'app');
            ?>
            </div>
            <?php
            if ($formal_url = get_post_meta($post_id, '_down_formal', true))
                echo '<div class="text-center"><a href="' . go_to($formal_url) . '" target="_blank" class="btn btn-lg btn-outline-primary custom_btn-outline  text-lg radius-50 py-3 px-5 my-3">' . __('去官方网站了解更多', 'i_theme') . '</a></div>';
            ?>
        </div>
    </div>
    <?php
    io_app_historic_version_list();
    do_action('io_single_content_after', $post_id, 'app');
}

/**
 * app 历史版本列表
 * @return void
 */
function io_app_historic_version_list(){
    global $post,$app_down_list;
    $post_id = $post->ID;

    if(count($app_down_list) > 1) { ?>
    <!-- 历史版本 -->
    <h2 class="text-gray text-lg my-4"><i class="iconfont icon-version icon-lg mr-1" id="historic"></i><?php _e('历史版本','i_theme') ?></h2>
    <div class="card historic"> 
        <div class="card-body" id="accordionExample">
            <div class="row row-sm text-center ">  
                <div class="col text-left"><?php _e('版本','i_theme') ?></div>
                <div class="col "><?php _e('日期','i_theme') ?></div>
                <div class="col  d-none d-md-block"><?php _e('大小','i_theme') ?></div>
                <div class="col  d-none d-lg-block"><?php _e('状态','i_theme') ?></div>
                <div class="col  d-none d-lg-block"><?php _e('语言','i_theme') ?></div>
                <div class="col text-right"><?php _e('下载','i_theme') ?></div> 
                <div class="col-12 line-thead my-3" style="height:1px"></div> 
            </div>  
            <?php 
            $i=0; 
            foreach($app_down_list as $down) { 
                if($i>6){ //最多显示5个历史版本
                    break;
                }
            ?>  
            <div class="row row-sm text-center cursor-pointer align-items-center" data-toggle="collapse" data-target="#collapse<?php echo $i ?>" aria-expanded="true" aria-controls="collapse<?php echo $i ?>">  
                <div class="col text-left"><?php echo($down['app_version']);if($i==0)echo'<span class="badge vc-theme ml-1">'.__('最新','i_theme').'</span>'; ?></div>
                <div class="col "><?php echo io_date_time($down['app_date'],false) ?></div>
                <div class="col  d-none d-md-block"><?php echo($down['app_size']) ?></div>
                <div class="col  d-none d-lg-block"><?php echo $down['app_status']=="official"?__('官方版','i_theme'):__('开心版','i_theme') ?></div>
                <div class="col  d-none d-lg-block"><?php echo($down['app_language']) ?></div>
                <div class="col text-right">
                    <?php echo io_app_down_btn($post_id, $down, 'list', $is_pay); ?>
                </div>
                <div class="col-12 line-tbody my-3" style="height:1px"> </div> 
            </div>  
            <?php if( isset($down['version_describe']) && !empty($down['version_describe']) ) { ?>
            <div id="collapse<?php echo $i ?>" class="collapse <?php echo $i==0?'show':'' ?>" aria-labelledby="headingOne" data-parent="#accordionExample">
                <div class="px-3">
                    <?php echo($down['version_describe']) ?>
                </div>
                <div class="col-12 line-tbody my-3" style="height:1px"> </div> 
            </div>
            <?php 
                }
                $i++; 
            } 
            ?>  
        </div>
    </div>
    <!-- 历史版本 end -->
    <?php }
}

/**
 * app 头部信息数据
 * @return string
 */
function io_app_header_info(){
    global $post, $app_down_list, $app_type;
    $post_id     = $post->ID;
    $app_down    = io_get_app_down_by_index($post_id,true);
    $default_ico = get_theme_file_uri('/images/t.png');
    $app_name    = get_post_meta($post_id, '_app_name', true)?:get_the_title(); 
    $app_ico     = get_post_meta_img($post_id, '_app_ico', true);
    $platform    = get_post_meta($post_id, '_app_platform', true);

    $html = '<div class="d-md-flex mt-n3 mb-5 my-xl-0">';
            
    $html .= '<div class="app-ico text-center mr-0 mr-md-2 mb-3 mb-md-0">';
    $html .= get_lazy_img($app_ico, $app_name, array(128,'auto'), 'app-rounded mr-0 mr-md-3', $default_ico);
    $html .= '</div>';

    $html .= '<div class="app-info">';

    $html .= '<h1 class="h3 text-center text-md-left mb-0">' .  $app_name;
    $html .= '<span class="text-md">' . ($app_down['app_version']) . '</span>';
    $html .= io_get_post_edit_link($post_id);
    $html .= '</h1>'; 

    $html .= '<p class="text-xs text-center text-md-left my-1">' .  get_post_meta($post_id, '_app_sescribe', true) . '</p>';
    if ($app_type === 'app') {
        $html .= '<div class="app-nature text-center text-md-left mb-5 mb-md-4">';
        $html .= '<span class="badge badge-pill vc-black mr-1"><i class="iconfont icon-version-Line mr-2"></i>' . ($app_down['app_status'] == "official" ? __('官方版', 'i_theme') : __('开心版', 'i_theme')) . '</span>';
        $html .= '<span class="badge badge-pill vc-black mr-1"><i class="iconfont icon-ad-line mr-2"></i>' . ($app_down['app_ad'] ? __('有广告', 'i_theme') : __('无广告', 'i_theme')) . '</span>';
        $html .= '<span class="badge badge-pill vc-black mr-1"><i class="iconfont icon-chakan-line mr-2"></i>' . (function_exists('the_views') ? the_views(false) : '0') . '</span>';
        $html .= '</div>';
    }

    $html .= '<p class="text-muted mb-4">';
    $html .= '<span class="info-term mr-3">' . __('更新日期：','i_theme') . io_date_time($app_down['app_date'],false) . '</span>';
    $html .= '<span class="info-term mr-3">' . __('分类标签：','i_theme') . io_get_post_tags($post_id,array('apps','apptag')) . '</span>';
    $html .= '<span class="info-term mr-3">' . __('语言：','i_theme') . ($app_down['app_language']) . '</span>';
    $html .= '<span class="info-term mr-3">' . __('平台：','i_theme') . io_app_platform_list($platform) .'</span>';
    $html .= '</p>';

    $html .= '<div class="mb-2 app-button">';
    $html .= io_app_down_btn($post_id, $app_down, 'top', $is_pay);
    $html .= like_button($post_id, 'app', false);
    $html .= $is_pay ? iopay_pay_tips_box() : '';
    $html .= '</div>';


    $html .= '<p class="mb-0 text-muted text-sm">';
    $html .= io_app_other_info($post_id, $app_down_list);
    $html .= '</p>';
    $html .= '</div>';

    $html .= '</div>';
    return $html;
}
/**
 * app 头部截图数据
 * @return string
 */
function io_app_screenshot_slide($app_name, $app_screen){
    $html = '';
    if(!empty($app_screen)) {
        $html .= '<div class="col-12 col-xl-5">';
        $html .= '<div class="mx-auto screenshot-carousel rounded-lg" >';
        $html .= '<div id="carousel" class="carousel slide" data-ride="carousel">';
        $html .= '<div class="carousel-inner" role="listbox">';
        for($i=0;$i<count($app_screen);$i++) { 
            $screen_img = $app_screen[$i]['img'];
            $html .= '<div class="carousel-item ' . ($i==0?'active':'') . '">';
            $html .= '<div class="img_wrapper"> ';
            $html .= '<a href="' . $screen_img . '" class="text-center" data-fancybox="screen" data-caption="' .  sprintf( __('%s的使用截图', 'i_theme'), $app_name ) . '[' . ($i+1) . ']' . '">';
            $html .= get_lazy_img($screen_img, sprintf( __('%s的使用截图', 'i_theme'), $app_name ).'['.($i+1).']' , 'auto', 'img-fluid', $screen_img);
            $html .= '</a>';
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';

        if(count($app_screen)>1) {
            $html .= '<ol class="carousel-indicators">';
            for($i=0;$i<count($app_screen);$i++) {
                $html .= '<li data-target="#carousel" data-slide-to="' .  $i . '" class="' .  ($i==0?'active':'') . '"></li>';
            }
            $html .= '</ol>';
            $html .= '<a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev"><i class="iconfont icon-arrow-l icon-lg" aria-hidden="true"></i><span class="sr-only">Previous</span></a>';
            $html .= '<a class="carousel-control-next" href="#carousel" role="button" data-slide="next"><i class="iconfont icon-arrow-r icon-lg" aria-hidden="true"></i><span class="sr-only">Next</span></a>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    return $html;
}
/**
 * app 支持平台列表
 * @param mixed $platform
 * @return string
 */
function io_app_platform_list($platform){
    $html = '';
    if($platform){
        foreach($platform as $pl){  
            $html .= '<i class="iconfont '.$pl.' mr-1"></i>';
        }
    }else{
        $html .= __('没限制','i_theme');
    }
    return $html;
}
/**
 * app 其他数据
 * @param mixed $post_id
 * @param mixed $down_list
 * @return string
 */
function io_app_other_info($post_id, $down_list){
    $html = '';
    
    if(count($down_list) > 1) {
        $html .= '<a class="mr-2 smooth-n" href="#historic"><i class="iconfont icon-version"></i> <span>'.__('历史版本','i_theme').'('.count($down_list).')</span><i class="iconfont icon-jt-line-r"></i></a>';
    }
    $html .= '<span class="mr-2"><i class="iconfont icon-zip"></i> <span>' . (array_values($down_list)[0]['app_size']) . '</span></span> ';
    $html .= '<span class="mr-2"><i class="iconfont icon-qushitubiao"></i> <span class="down-count-text count-a">' . (get_post_meta($post_id, '_down_count', true)?:0) . '</span> ' . __('人已下载','i_theme') .'</span>';
    
    if(!wp_is_mobile()){
        $width = 150;
        $qrurl = "<img src='" . get_qr_url(get_permalink($post_id), $width) . "' width='{$width}'>"; 
        $html .= '<span class="mr-2 cursor-pointer" data-toggle="tooltip" data-placement="bottom" data-html="true" title="' .  $qrurl . '"><i class="iconfont icon-phone"></i> ' . __('手机查看','i_theme') .'</span>';
    }

    return $html;
}

/**
 * 获取按钮
 * 
 * @param mixed $post_id
 * @param array $data 商品数据
 * @param mixed $type top 头部   list 历史列表
 * @param bool $is_pay
 * @return string 下载按钮或者购买按钮
 */
function io_app_down_btn($post_id, $data, $type, &$is_pay){
    $is_pay     = false;
    $index      = $data['index'];
    if ('top' == $type) {
        $class = 'btn btn-lg px-4 text-lg radius-50 btn-danger custom_btn-d btn_down mr-3 mb-2';
        $icon  = '<i class="iconfont icon-down mr-2"></i>';
        $name  = array(__('立即下载', 'i_theme'), __('立即购买', 'i_theme'));
    } else {
        $class = 'btn btn-sm btn-danger custom_btn-d btn_down my-n1';
        $icon  = '';
        $name  = array(__('下载', 'i_theme'), __('购买', 'i_theme'));
    }
    $btn        = '<button type="button" class="' . $class . ' io-ajax-modal" data-modal_size="modal-lg" data-modal_type="overflow-hidden" data-action="get_app_down_btn" data-post_id="' . $post_id . '" data-id="' . $index . '">' . $icon . $name[0] . ' </button>';
    $user_level = get_post_meta($post_id, '_user_purview_level', true);
    if (!$user_level) {
        update_post_meta($post_id, '_user_purview_level', 'all');
        return $btn;
    }

    if ($user_level && 'buy' === $user_level) {
        $buy_option = get_post_meta($post_id, 'buy_option', true);
    }
    if (isset($buy_option)) {
        if ('annex' === $buy_option['buy_type']) { // 附件模式
            if('single'==$buy_option['price_type']){
                $index = 0;
            }
            $is_buy = iopay_is_buy($post_id, $index, 'app');
        }
    }
    if (isset($is_buy) && !$is_buy) {
        $pay_price      = '';
        $original_price = '';
        $_name          = $name[1];
        $_class         = $class . ' io-ajax-modal-get nofx';
        $unit           = '<span class="text-xs">' . io_get_option('pay_unit', '￥') . '</span>';
        if ('single' == $buy_option['price_type']) { //总价模式
            if ('top' == $type) {
                $pay_price      = $buy_option['pay_price'];
                $original_price = $buy_option['price'];
            } else {
                $_class = $class . ' go-up';
                $_name  = __('去购买', 'i_theme');
                $unit   = '';
            }
        } else {
            $pay_price      = $data['pay_price'];
            $original_price = $data['price'];
        }
        if ($icon) {
            $icon = '<i class="iconfont icon-buy_car mr-2"></i>';
        }
        $is_pay         = true;
        $original_price = $original_price && $original_price > $pay_price ? '<div class="original-price d-inline-block text-xs">' . $unit . $original_price . '</span></div>' : '';

        $args = array(
            'class' => 'btn-lg px-4 text-lg radius-50 mr-3 mb-2',
            'price' => ' ' . $unit . $pay_price . $original_price,
        );
        if ('list' == $type) {
            $args = array(
                'class' => 'btn-sm',
                'text'  => '登录',
                'icon'  => '',
                'price' => ' ' . $unit . $pay_price . $original_price,
            );
        }
        $btn    = apply_filters('iopay_buy_btn_before', 'app', $buy_option, $args);
        if (empty($btn)) {
            $url            = esc_url(add_query_arg(array('action' => 'pay_cashier_modal', 'id' => $post_id, 'index' => strval($index)), admin_url('admin-ajax.php')));
            $btn            = '<button type="button" class="' . $_class . '" data-href="' . $url . '">' . $icon . $_name . ' ' . $unit . $pay_price . $original_price . '</button>';
        }
    }
    return $btn;
}
/**
 * 根据序号排序资源
 * 
 * @param mixed $post_id
 * @param bool $first 取第一个值
 * @return array
 */
function io_get_app_down_by_index($post_id, $first = false){
    $app_down_list = get_post_meta($post_id, 'app_down_list', true);
    $up_data       = array();
    $data          = array();
    $index         = 1;
    foreach ($app_down_list as $val) {
        if (isset($val['index']) && '' !== $val['index']) {
            $data[$val['index']] = $val;
        } else {
            $val['index'] = $index;
            $data[$index] = $val;
            $up_data[]    = $val;
        }
        $index++;
    }
    if (!empty($up_data)) {
        // 更新旧数据序号
        update_post_meta($post_id, 'app_down_list', $up_data);
    }
    if ($first) {
        return $app_down_list[0];
    }
    return $data;
}
/**
 * app 类型名称
 * 
 * @param mixed $type
 * @return string
 */
function get_app_type_name($type){
    switch($type){
        case "app":
            $name = __('软件','i_theme');
            break;
        case "down":
            $name = __('资源','i_theme');
            break;
        default:
            $name = __('资源','i_theme');
            break;
    }
    return $name;
}
?>