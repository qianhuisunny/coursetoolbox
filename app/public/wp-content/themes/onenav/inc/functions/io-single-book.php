<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2023-03-11 01:49:11
 * @LastEditors: iowen
 * @LastEditTime: 2023-04-21 22:13:11
 * @FilePath: \onenav\inc\functions\io-single-book.php
 * @Description: 
 */

/**
 * book 头部
 * @param mixed $is_hide
 * @return string
 */
function io_book_header(&$is_hide){
    global $post, $down_list;
    $post_id = $post->ID; 
    $level_d = get_user_level_directions_html('book');
    if($level_d){
        $is_hide = true;
        return $level_d;
    }
    $is_hide = false;

    $down_list  = get_post_meta($post_id, '_down_list', true);  
    $imgurl     = get_post_meta_img($post_id, '_thumbnail', true);
    $booktitle  = get_the_title();

    $html = '<div class="row site-content py-4 py-md-5 mb-xl-5 mb-0 mx-xxxl-n5">';
    $html .= '<!-- book封面 -->';
    $html .= '<div class="col-12 col-sm-5 col-md-4 col-lg-3">';
    $html .= io_book_header_img($imgurl, $booktitle);
    $html .= '</div>';
    $html .= '<!-- book封面 END -->';
    $html .= '<!-- book信息 -->';
    $html .= '<div class="col mt-4 mt-sm-0">';
    $html .= io_book_header_info( $booktitle );
    $html .= '</div>';
    $html .= '<!-- book信息 END -->';
    $html .= '</div>';

    return $html;
}
/**
 * book 正文
 * @return void
 */
function io_book_content(){ 
    global $post;
    $post_id = $post->ID;
    
    do_action('io_single_content_before', $post_id, 'book');
    ?>
    <div class="panel site-content card transparent"> 
        <div class="card-body p-0">
            <div class="apd-bg">
                <?php show_ad('ad_app_content_top',false, '<div class="apd apd-right">' , '</div>'); ?>
            </div> 
            <div class="panel-body single my-4 ">
            <?php 
            do_action('io_single_before', 'book');
            the_content();
            thePostPage();
            do_action('io_single_after', 'book');
            ?>
            </div>
        </div>
    </div>
    <?php
    io_book_content_down(get_the_title());
    do_action('io_single_content_after', $post_id, 'book');
}


/**
 * 下载资源模态框
 * @param mixed $name
 * @return void
 */
function io_book_content_down($name){
    global $post , $down_list, $is_pay;
    if($is_pay || !$down_list){
        return;
    }
    $title  = __('下载地址: ', 'i_theme') . $name;
    echo io_get_down_modal($title, $down_list, 'book', '', 10);
}
/**
 * 期刊名字
 * @return string
 */
function io_get_journal_name(){
    global $post, $book_type;

    $journal = '';
    if($book_type=='periodical'){
        $j = "";
        switch (get_post_meta($post->ID, '_journal', true)){
            case 1: 
                $j = __('季刊','i_theme');
                break;
            case 2: 
                $j = __('双月刊','i_theme');
                break;
            case 3: 
                $j = __('月刊','i_theme');
                break;
            case 6: 
                $j = __('半月刊','i_theme');
                break;
            case 9: 
                $j = __('旬刊','i_theme');
                break;
            case 12: 
                $j = __('周刊','i_theme');
                break;
            default: 
                $j = __('月刊','i_theme');
        }
        $journal = '<span class="badge vc-violet2 text-xs font-weight-normal ml-2 journal">'. $j .'</span>';
    }
    return $journal;
}

/**
 * book 头部名称
 * @param mixed $imgurl
 * @param mixed $booktitle
 * @return string
 */
function io_book_header_img($imgurl, $booktitle){
    global $post;
    $post_id = $post->ID;

    $html = '<div class="text-center position-relative">';
    $html .= '<img class="rounded shadow" src="'. $imgurl.'" alt="'. $booktitle.'" title="'. $booktitle.'" style="max-height: 350px;">';
    $html .= '<div class="tool-actions text-center">';
        
    $html .= like_button($post_id, 'book' , false);
            
    $html .= '<a href="javascript:;" class="btn-share-toggler btn btn-icon btn-light rounded-circle p-2 mx-3 mx-md-2" data-toggle="tooltip" data-placement="top" title="'. __('浏览','i_theme').'">';
    $html .= '<span class="flex-column text-height-xs">';
    $html .= '<i class="icon-lg iconfont icon-chakan"></i>';
    $html .= '<small class="share-count text-xs mt-1">'. (function_exists('the_views')? the_views(false) :  '0').'</small>';
    $html .= '</span>';
    $html .= '</a>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

/**
 * book 头部信息
 * @param mixed $imgurl
 * @param mixed $booktitle
 * @return string
 */
function io_book_header_info( $booktitle ){
    global $post, $down_list, $is_pay;
    $post_id = $post->ID;
    $is_pay  = false;

    $list = '';
    if ($books_data = get_post_meta(get_the_ID(), '_books_data', true)) {
        foreach ($books_data as $value) {
            $list .= '<li class="my-2"><span class="info-title mr-3">' . $value['term'] . '</span>' . $value['detail'] . '</li>';
        }

        $_c   = io_get_post_tags($post_id, array('books', 'booktag'));
        if(!empty($_c))
            $list .= '<li class="my-2"><span class="info-title mr-3">' . __('标签', 'i_theme') . '</span>' . $_c . '</li>';

        $_t   = io_get_post_tags($post_id, array('series'));
        if(!empty($_t))
            $list .= '<li class="my-2"><span class="info-title mr-3">' . __('系列', 'i_theme') . '</span>' . $_t . '</li>';

        $list = '<ul>' . $list . '</ul>';
    }

    $html = '<div class="site-body text-sm">';
    $html .= io_post_header_nav('books');
    $html .= '<h1 class="site-name h3 my-3">' . $booktitle . io_get_journal_name();
    $html .= io_get_post_edit_link($post_id);
    $html .= '</h1>';
    $html .= '<div class="mt-n2">';
    $html .= '<p>' . io_get_excerpt(170, '_summary') . '</p>';
    $html .= '<div class="book-info text-sm text-muted">';
    $html .= $list;
    $html .= '</div>';
    $html .= '<div class="site-go mt-3">';
    if ($buy_list = get_post_meta(get_the_ID(), '_buy_list', true)) {
        foreach ($buy_list as $value) {
            if ($value['price']) {
                $html .= '<a target="_blank" href="' . go_to($value['url']) . '" class="btn btn-mgs rounded-lg" data-toggle="tooltip" data-placement="top" title="' . $value['term'] . '"><span class="b-name">' . $value['term'] . '</span><span class="b-price">' . ($value['price'] ?: '0') . '</span><i class="iconfont icon-buy_car"></i></a>';
            } else {
                $html .= '<a target="_blank" href="' . go_to($value['url']) . '" class="btn btn-arrow rounded-lg" title="' . $value['term'] . '"><span class="b-name">' . $value['term'] . '</span><i class="iconfont icon-arrow-r"></i></a>';
            }
        }
    }
    if ($down_list) {
        $html .= io_book_down_btn($is_pay);
    }
    $html .= $is_pay ? iopay_pay_tips_box() : '';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}

function io_book_down_btn(&$is_pay){
    global $post, $down_list;
    $post_id = $post->ID;
    $name    = __('下载资源', 'i_theme');
    $icon    = '<i class="iconfont icon-down"></i>';
    $btn     = '<a href="javascript:" class="btn btn-arrow qr-img"  title="' . $name . '" data-id="0" data-toggle="modal" data-target="#book-down-modal"><span>' . $name . $icon . '</span></a>';

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
            $is_buy = iopay_is_buy($post_id, 0, 'app');
        }
    }
    if (isset($is_buy) && !$is_buy) {
        $name           = __('立即购买', 'i_theme');
        $pay_price      = $buy_option['pay_price'];
        $original_price = $buy_option['price'];
        $unit           = '<span class="text-xs">' . io_get_option('pay_unit', '￥') . '</span>';
        $icon           = '<i class="iconfont icon-buy_car mr-2"></i>';

        $is_pay         = true;
        $original_price = $original_price && $original_price > $pay_price ? '<div class="original-price d-inline-block text-xs">' . $unit . $original_price . '</div>' : '';
        $btn            = apply_filters('iopay_buy_btn_before', 'book', $buy_option, array('price' => ' ' . $unit . $pay_price . $original_price));
        if (empty($btn)) {
            $url = esc_url(add_query_arg(array('action' => 'pay_cashier_modal', 'id' => $post_id, 'index' => 0), admin_url('admin-ajax.php')));
            $btn = '<a href="' . $url . '" class="btn vc-blue io-ajax-modal-get nofx"  title="' . $name . '">' . $icon . $name . ' ' . $unit . $pay_price . $original_price . '</a>';
        }
    }
    return $btn;
}
