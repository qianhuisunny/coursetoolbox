<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-02-21 12:46:57
 * @LastEditors: iowen
 * @LastEditTime: 2024-03-24 23:58:35
 * @FilePath: /onenav/inc/functions/io-single-site.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if(!function_exists('get_data_evaluation')):
/**
 * 数据评估HTML
 * @param mixed $name
 * @param mixed $views
 * @param mixed $url
 * @return void
 */
function get_data_evaluation($name,$views,$url){
    if(!io_get_option('sites_default_content',false)) return;
    global $post;
    $aizhan_data = go_to('https://www.aizhan.com/seo/'. format_url($url,true));
    $chinaz_data = go_to('https://seo.chinaz.com/?q='. format_url($url,true));
    $e5118_data  = go_to('https://seo.5118.com/'. format_url($url,true) . '?t=ydm');
?>
    <h2 class="text-gray  text-lg my-4"><i class="iconfont icon-tubiaopeizhi mr-1"></i><?php _e('数据评估','i_theme') ?></h2>
    <div class="panel site-content sites-default-content card"> 
        <div class="card-body">
            <p class="viewport">
            <?php echo $name ?>浏览人数已经达到<?php echo $views ?>，如你需要查询该站的相关权重信息，可以点击"<a class="external" href="<?php echo $e5118_data ?>" rel="nofollow" target="_blank">5118数据</a>""<a class="external" href="<?php echo $aizhan_data ?>" rel="nofollow" target="_blank">爱站数据</a>""<a class="external" href="<?php echo $chinaz_data ?>" rel="nofollow" target="_blank">Chinaz数据</a>"进入；以目前的网站数据参考，建议大家请以爱站数据为准，更多网站价值评估因素如：<?php echo $name ?>的访问速度、搜索引擎收录以及索引量、用户体验等；当然要评估一个站的价值，最主要还是需要根据您自身的需求以及需要，一些确切的数据则需要找<?php echo $name ?>的站长进行洽谈提供。如该站的IP、PV、跳出率等！</p>
            <div class="text-center my-2"><span class=" content-title"><span class="d-none">关于<?php echo $name ?></span>特别声明</span></div>
            <p class="text-muted text-sm m-0">
            本站<?php bloginfo('name'); ?>提供的<?php echo $name ?>都来源于网络，不保证外部链接的准确性和完整性，同时，对于该外部链接的指向，不由<?php bloginfo('name'); ?>实际控制，在<?php echo the_time(TIME_FORMAT) ?>收录时，该网页上的内容，都属于合规合法，后期网页的内容如出现违规，可以直接联系网站管理员进行删除，<?php bloginfo('name'); ?>不承担任何责任。</p>
        </div>
        <div class="card-footer text-muted text-xs">
            <div class="d-flex"><span><?php bloginfo('name'); ?>致力于优质、实用的网络站点资源收集与分享！</span><span class="ml-auto d-none d-md-block">本文地址<?php the_permalink() ?>转载请注明</span></div>
        </div>
    </div>
<?php
}
endif;

if(!function_exists('get_report_button')):
function get_report_button($post_id=''){
    if(!io_get_option('report_button',true))
        return;
    if($post_id==''){
        global $post;
        $post_id = get_the_ID();
    }                                            
    return '<a href="javascript:" class="btn btn-danger qr-img tooltip-toggle rounded-lg" data-post_id="'.$post_id.'" data-toggle="modal" data-placement="top" data-target="#report-sites-modal" title="'. __('反馈','i_theme') .'"><i class="iconfont icon-statement icon-lg"></i></a>';
}
endif;



/**
 * site 头部
 * @return string
 */
function io_site_header(&$is_hide){
    global $post, $sites_type, $is_hide;

    $level_d = get_user_level_directions_html('sites');
    if($level_d){
        $is_hide = true;
        return $level_d;
    }
    $is_hide = false;

    if('down' === $sites_type){
        $html = io_site_header_down();
    }else{
        $html = io_site_header_site();
    }

    return $html;
}
/**
 * site 正文
 * @return void
 */
function io_site_content(){
    global $post, $sites_type;
    $post_id    = $post->ID;
    do_action('io_single_content_before', $post_id, 'sites');
    ?>
    <div class="panel site-content card transparent"> 
        <div class="card-body p-0">
            <div class="apd-bg">
                <?php  show_ad('ad_app_content_top',false, '<div class="apd apd-right">' , '</div>');  ?>
            </div> 
            <div class="panel-body single my-4 ">
                <?php  
                do_action('io_single_before', 'sites');
                $contentinfo = get_the_content();
                if( $contentinfo ){
                    echo apply_filters('the_content', $contentinfo);
                    thePostPage();
                }else{
                    echo htmlspecialchars(get_post_meta($post_id, '_sites_sescribe', true));
                }
                if('down' === $sites_type){
                    if ($formal_url = get_post_meta($post_id, '_down_formal', true))
                        echo ('<div class="text-center"><a href="' . go_to($formal_url) . '" target="_blank" class="btn btn-lg btn-outline-primary custom_btn-outline  text-lg radius-50 py-3 px-5 my-3">' . __('去官方网站了解更多', 'i_theme') . '</a></div>');
                }
                do_action('io_single_after', 'sites');
                ?>
            </div>
        </div>
    </div>
    <?php if( io_get_option('leader_board',false) && io_get_option('details_chart',false)){ //图表统计?>
    <h2 class="text-gray text-lg my-4"><i class="iconfont icon-zouxiang mr-1"></i><?php _e('数据统计','i_theme') ?></h2>
    <div class="card io-chart"> 
        <div id="chart-container" class="" style="height:300px" data-type="<?php echo $sites_type ?>" data-post_id="<?php echo $post_id ?>" data-nonce="<?php echo wp_create_nonce( 'post_ranking_data' ) ?>">
            <div class="chart-placeholder p-4">
                <div class="legend">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="pillar">
                    <span style="height:40%"></span>
                    <span style="height:60%"></span>
                    <span style="height:30%"></span>
                    <span style="height:70%"></span>
                    <span style="height:80%"></span>
                    <span style="height:60%"></span>
                    <span style="height:90%"></span>
                    <span style="height:50%"></span>
                </div>
            </div>
        </div> 
    </div> 
    <?php 
    }
    $title = get_the_title();
    if('down' === $sites_type){
        io_site_content_down($title);
    } else {
        $views      = function_exists('the_views')? the_views(false) :  '0' ;
        $m_link_url = get_post_meta($post_id, '_sites_link', true); 
        get_data_evaluation($title, $views, $m_link_url);
    }
    do_action('io_single_content_after', $post_id, 'sites');
}

function io_site_header_site(){
    global $post, $sites_type, $tmp_post;
    $tmp_post   = $post;
    $post_id    = $post->ID;
    $m_link_url = get_post_meta($post_id, '_sites_link', true);  
    $is_dead    = get_post_meta($post_id, '_affirm_dead_url', true);
    $is_preview = false;
    $sitetitle  = get_the_title();
    $imgurl     = get_site_thumbnail($sitetitle, $m_link_url, $sites_type, true ,$is_preview);
    $views      = function_exists('the_views')? the_views(false) :  '0' ;

    $html = '<div class="row site-content py-4 py-md-5 mb-xl-5 mb-0 mx-xxxl-n5">';
    $html .= '<!-- 网址信息 -->';
    
    $html .= '<div class="col-12 col-sm-5 col-md-4 col-lg-3">';
    $html .= io_site_header_img($is_preview, $imgurl, $sitetitle, $views, $is_dead);
    $html .= '</div>';

    $html .= '<div class="col mt-4 mt-sm-0">';
    $html .= '<div class="site-body text-sm">';

    $html .= io_post_header_nav('favorites');

    // 标题
    $html .= '<h1 class="site-name h3 my-3">'.$sitetitle;
    $language = get_post_meta($post_id, '_sites_language', true); 
    if($m_link_url!="" && $language && !find_character($language,['中文','汉语','zh','cn','简体']) ){
        $html .= '<a class="text-xs" href="//fanyi.baidu.com/transpage?query='.format_url($m_link_url,true).'&from=auto&to=zh&source=url&render=1" target="_blank" rel="nofollow noopener noreferrer">'.__('翻译站点','i_theme').'<i class="iconfont icon-wailian text-ss"></i></a>';
    }
    $html .= io_get_post_edit_link( $post_id );
    $html .= '</h1>';

    $html .= io_site_header_info($m_link_url, $is_dead, $sitetitle);

    $html .= '</div>';
    $html .= '</div>';
    $html .= '<!-- 网址信息 end -->';

    // 头部侧边栏小工具
    $html .= io_site_header_sidebar();
    $html .= '</div>';
    
    // 还原主循环
    $post = $tmp_post;
    setup_postdata($post);
    return $html;
}

/**
 * 头部侧边栏小工具
 * 
 * @return string
 */
function io_site_header_sidebar(){
    global $post;
    $post_id = $post->ID;
    $html    = '';

    $user_level = get_post_meta($post_id, '_user_purview_level', true);
    if (!$user_level) {
        update_post_meta($post_id, '_user_purview_level', 'all');
        return $html;
    }
    if($user_level!='buy'){
        ob_start();
        get_sidebar('sitestop');
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
    $html = iopay_buy_sidebar_html(false);
    return $html;
}

function io_site_header_down(){
    global $post, $sites_type;
    $post_id       = $post->ID;
    $name          = get_the_title();//资源名称  
    $version       = get_post_meta($post_id, '_down_version', true);//当前版本
    $info          = htmlspecialchars(get_post_meta($post_id, '_sites_sescribe', true));//说明与描述
    $preview       = get_post_meta($post_id, '_down_preview', true);//演示地址
    $down_screen   = get_post_meta($post_id, '_screenshot', true); //资源截图

    $size          = get_post_meta($post_id, '_down_size', true);//资源大小
    $platform      = get_post_meta($post_id, '_app_platform', true);//资源大小

    $default_ico   = get_theme_file_uri('/images/t.png');
    $imgurl        = get_post_meta_img($post_id, '_thumbnail', true);
    if($imgurl == ''){
        $imgurl = get_theme_file_uri('/images/down_ico.png');
    }
    $html = '<div class="row app-content py-5 mb-xl-5 mb-0 mx-xxxl-n5">';
    $html .= '<!-- 资源信息 -->';
    $html .= '<div class="col">';
    $html .= '<div class="d-md-flex mt-n3 mb-5 my-xl-0">';
    $html .= '<div class="app-ico text-center mr-0 mr-md-2 mb-3 mb-md-0">';
    $html .= get_lazy_img($imgurl, $name, 128, 'app-rounded', $default_ico);
    $html .= '</div>';

    $html .= '<div class="app-info">';
    $html .= '<h1 class="h3 text-center text-md-left mb-0">' . $name;
    $html .= '<span class="text-md">' . $version . '</span>';
    $html .= io_get_post_edit_link( $post_id );
    $html .= '</h1>  ';
    $html .= '<p class="text-xs text-center text-md-left my-1">' . $info . '</p>';
    $html .= '<div class="app-nature text-center text-md-left mb-5 mb-md-4">';
    $html .= '<span class="badge badge-pill badge-dark mr-1"><i class="iconfont icon-chakan-line mr-2"></i>' . (function_exists('the_views')? the_views(false) :  '0') . '</span>';
    $html .= '</div>';
    $html .= '<p class="text-muted   mb-4">';
    $html .= '<span class="info-term mr-3">' . __('更新日期：','i_theme') . get_the_date() .'</span>';
    $html .= '<span class="info-term mr-3">' . __('分类标签：','i_theme') . io_get_post_tags($post_id,array('favorites', 'sitetag')) .'</span>';
    $html .= '<span class="info-term mr-3">' . __('平台：','i_theme') . io_app_platform_list($platform) . '</span>';
    $html .= '</p>';
    $html .= '<div class="mb-2 app-button">';
    $html .= '<button type="button" class="btn btn-lg px-4 text-lg radius-50 btn-danger custom_btn-d btn_down mr-3 mb-2" data-id="0" data-toggle="modal" data-target="#app-down-modal"><i class="iconfont icon-down mr-2"></i>' . __('立即下载','i_theme') . '</button> ';
    $html .=  like_button($post_id,'sites-down',false);
    $html .= '</div> ';
    $html .= '<p class="mb-0 text-muted text-sm"> ';
    $html .= '<span class="mr-2"><i class="iconfont icon-zip"></i> <span>' . ($size?:__('大小未知','i_theme')) . '</span></span> ';
    $html .= '<span class="mr-2"><i class="iconfont icon-qushitubiao"></i> <span class="down-count-text count-a">' . (get_post_meta($post_id, '_down_count', true)?:0) . '</span> ' . __('人已下载','i_theme') . '</span>';

    if(!wp_is_mobile() && io_get_option('mobile_view_btn',true)){
        $width = 150;
        $qrurl = "<img src='".get_qr_url(get_permalink($post_id), $width)."' width='{$width}'>"; 
        $html .= '<span class="mr-2" data-toggle="tooltip" data-placement="bottom" data-html="true" title="' . $qrurl . '"><i class="iconfont icon-phone"></i> ' . __('手机查看','i_theme') . '</span>';
    } 
    $html .= '</p>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<!-- 资源信息 end -->';
    $html .= '<!-- 截图幻灯片 -->';
    $html .= io_app_screenshot_slide($name, $down_screen);
    $html .= '<!-- 截图幻灯片 end -->';
    $html .= '</div> ';

    return $html;
}

/**
 * 下载资源模态框
 * @param mixed $name
 * @return void
 */
function io_site_content_down($name){
    global $post;
    $post_id       = $post->ID;
    $version       = get_post_meta($post_id, '_down_version', true);//当前版本
    $down_list     = get_post_meta($post_id, '_down_url_list', true);//下载列表
    $decompression = get_post_meta($post_id, '_dec_password', true);//解压密码
    $title         = __('下载地址: ', 'i_theme') . $name . ($version ? ' - <span class="app-v">' . $version . '</span>' : '');
    
    echo io_get_down_modal($title, $down_list, 'app', $decompression);
}
/**
 * 头部图标
 * @param mixed $is_preview
 * @param mixed $imgurl
 * @param mixed $sitetitle
 * @param mixed $views
 * @param mixed $is_dead
 * @return string
 */
function io_site_header_img($is_preview, $imgurl, $sitetitle, $views, $is_dead){
    global $post;
    $post_id = $post->ID;

    $html = '<div class="siteico">';
    if (!$is_preview) {
        $html .= '<div class="blur blur-layer" style="background: transparent url(' . $imgurl . ') no-repeat center center;-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;animation: rotate 30s linear infinite;"></div>';
        if (io_get_option('is_letter_ico', false) && io_get_option('first_api_ico', false)) {
            $html .= get_lazy_img($imgurl, $sitetitle, 'auto', 'img-cover', '', true, 'onerror=null;src=ioLetterAvatar(alt,98)');
        } else {
            $html .= get_lazy_img($imgurl, $sitetitle, 'auto', 'img-cover', '');
        }
    }
    if ($is_preview) {
        $html .= get_lazy_img($imgurl, $sitetitle, 'auto', 'img-cover');
    }
    if ($country = get_post_meta($post_id, '_sites_country', true)) {
        $html .= '<div id="country" class="text-xs custom-piece_c_b country-piece loadcountry"><i class="iconfont icon-globe mr-1"></i>' . $country . '</div>';
    } else {
        $html .= '<div id="country" class="text-xs custom-piece_c_b country-piece" style="display:none;"><i class="iconfont icon-loading icon-spin"></i></div>';
    }
    $html .= '<div class="tool-actions text-center mt-md-4">';
    $html .= like_button($post_id, 'sites', false);
    $html .= '<a href="javascript:;" class="btn-share-toggler btn btn-icon btn-light rounded-circle p-2 mx-3 mx-md-2" data-toggle="tooltip" data-placement="top" title="' . __('浏览', 'i_theme') . '">';
    $html .= '<span class="flex-column text-height-xs">';
    $html .= '<i class="icon-lg iconfont icon-chakan"></i>';
    $html .= '<small class="share-count text-xs mt-1">' . $views . '</small>';
    $html .= '</span>';
    $html .= '</a>';
    $html .= '</div>';
    if ($is_dead) {
        $html .= '<div class="link-dead"><i class="iconfont icon-subtract mr-1"></i>' . __('链接已失效', 'i_theme') . '</div>';
    }
    $html .= '</div>';

    return $html;
}

if(!function_exists('io_site_header_info')):
function io_site_header_info($m_link_url,$is_dead,$sitetitle){
    global $post, $sites_type;
    $post_id = $post->ID;

    $width = 150;
    if(get_post_meta_img($post_id, '_wechat_qr', true) || $sites_type == 'wechat'){
        $m_qrurl = get_post_meta_img($post_id, '_wechat_qr', true);
        $qrurl = "<img src='".$m_qrurl."' width='{$width}'>";
        if(get_post_meta($post_id,'_is_min_app', true) ){
            $qrname = __("小程序",'i_theme');
            if($m_qrurl == "")
                $qrurl = '<p>'.__('居然没有添加二维码','i_theme').'</p>';
        }else{
            $qrname = __("公众号",'i_theme');
            if($m_qrurl == ""){
                if($wechat_id = get_post_meta_img($post_id, '_wechat_id', true)){
                    $qrurl = "<img src='https://open.weixin.qq.com/qr/code?username=".$wechat_id."' width='{$width}'>";
                }else{
                    $qrurl = '<p>'.__('居然没有添加二维码','i_theme').'</p>';
                }
            }
        }
    }else{
        $m_post_link_url = $m_link_url ?: get_permalink($post_id);
        $qrurl = "<img src='".get_qr_url($m_post_link_url, $width)."' width='{$width}'>";
        $qrname = __("手机查看",'i_theme');
    }

    // 爱站权重
    $az_html = '';
    if($sites_type == "sites" && !$is_dead && io_get_option('url_rank',false)){
        $aizhan  = go_to('https://seo.5118.com/' . format_url($m_link_url, true) . '?t=ydm', true);
        $az_html .= '<div class="mt-2 sites-seo-load" data-url="'.format_url($m_link_url,true).'" data-go_to="'. $aizhan .'">';
        $az_html .= '<span class="sites-weight loading"></span><span class="sites-weight loading"></span><span class="sites-weight loading"></span><span class="sites-weight loading"></span><span class="sites-weight loading"></span>';
        //$az_html .= '<span class="mr-2">PC <a href="'. $aizhan .'" title="百度权重" target="_blank"><img class="" src="//baidurank.aizhan.com/api/br?domain='.format_url($m_link_url,true).'&style=images" alt="百度权重" title="百度权重" style="height:18px"></a></span>';
        //$az_html .= '<span class="mr-2">'.__('移动','i_theme') .' <a href="'. $aizhan .'" title="百度移动权重" target="_blank"><img class="" src="//baidurank.aizhan.com/api/mbr?domain='.format_url($m_link_url,true).'&style=images" alt="百度移动权重" title="百度移动权重" style="height:18px"></a></span>';
        $az_html .= '</div>';
    }

    // 目标站链接、手机查看按钮
    $btn = '<div class="site-go mt-3">';
    if ($m_link_url != "") {
        $a_class = '';
        $a_ico   = 'icon-arrow-r-m';
        if ($is_dead) {
            $m_link_url = esc_url(home_url());
            $a_class = ' disabled';
            $a_ico   = 'icon-subtract';
        }
        $btn .= '<div id="security_check_img"></div>';
        $btn .= '<span class="site-go-url">';
        $btn .= '<a href="'.go_to($m_link_url).'" title="'.$sitetitle.'" target="_blank" class="btn btn-arrow mr-2'.$a_class.'"><span>'.__('链接直达', 'i_theme').'<i class="iconfont '.$a_ico.'"></i></span></a>';
        $btn .= '</span>';
    }
    if(!$is_dead && ((io_get_option('mobile_view_btn',true) && !wp_is_mobile())|| $sites_type == "wechat")){
        $btn .= '<a href="javascript:" class="btn btn-arrow qr-img"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="'.$qrurl.'"><span>'.$qrname.'<i class="iconfont icon-qr-sweep"></i></span></a>';
    }
    $btn .= get_report_button();
    $btn .= '</div>';

    // 其他信息
    $other = '';
    if (!$is_dead && $spare_link = get_post_meta($post_id, '_spare_sites_link', true)) {
        $other .= '<div class="spare-site mb-3">';
        $other .= '<i class="iconfont icon-url"></i><span class="mr-3">' . __('其他站点:', 'i_theme') . '</span>';
        for ($i = 0; $i < count($spare_link); $i++) {
            $other .= '<a class="mb-2 mr-3" href="' . go_to($spare_link[$i]['spare_url']) . '" title="' . $spare_link[$i]['spare_note'] . '" target="_blank" style="white-space:nowrap"><span>' . $spare_link[$i]['spare_name'] . '<i class="iconfont icon-wailian"></i></span></a>';
        }
        $other .= '</div>';
    }
    if($is_dead){
        $other .= '<p class="text-xs link-dead-msg"><i class="iconfont icon-warning mr-2"></i>'.__('经过确认，此站已经关闭，故本站不再提供跳转，仅保留存档。','i_theme').'</p>';
    }

    $html = '<div class="mt-2">';
    $html .= '<p class="mb-2">'.io_get_excerpt(170,'_sites_sescribe').'</p>';
    $html .= __('标签：','i_theme') . io_get_post_tags($post_id, array('favorites', 'sitetag'));
    $html .= $az_html;
    $html .= $btn;
    $html .= $other;
    $html .= '</div>';

    return $html;
}
endif;
/**
 * 获取网址类型文章的缩略图
 * 
 * @param string $title      网址标题
 * @param string $link       网址目标地址
 * @param string $type       网址类型
 * @param bool   $show       二维码是否可见
 * @param bool   $is_preview 是否显示预览
 * @return string
 */
function get_site_thumbnail($title, $link, $type, $show, &$is_preview = ''){
    global $post;

    $img_url = get_post_meta_img($post->ID, '_thumbnail', true);
    if ($type == "down")
        return $img_url;
    if($show && $type == "wechat"){
        return get_site_wechat_qr();
    }
    if($img_url == '' || io_get_option('sites_preview','') ){
        if( $link != '' || ($type == "sites" && $link != '') ){
            if($img_url = get_post_meta($post->ID, '_sites_preview', true)){
                $is_preview = true;
            }else{
                if(!io_get_option('sites_preview',false)){
                    if(empty($img_url) && io_get_option('is_letter_ico',false) && !io_get_option('first_api_ico',false)){
                        $img_url = io_letter_ico($title, 160);
                    }else{
                        $img_url = (io_get_option('ico-source','https://api.iowen.cn/favicon/','ico_url') .format_url($link) . io_get_option('ico-source','.png','ico_png'));
                    }
                }else{
                    $img_url = '//cdn.iocdn.cc/mshots/v1/'. format_url($link,true) .'?w=383&h=328';
                    $is_preview = true;
                }
            }
        } elseif ($type == "wechat") {
            $img_url = get_theme_file_uri('/images/qr_ico.png');
        } else {
            $img_url = get_theme_file_uri('/images/favicon.png');
        }
    }
    return $img_url;
}

function get_site_wechat_qr(){
    global $post;

    $qrurl = get_post_meta_img($post->ID, '_wechat_qr', true);
    if($qrurl == "" && !get_post_meta($post->ID,'_is_min_app', true) ){
        if($wechat_id = get_post_meta_img($post->ID, '_wechat_id', true)){
            $qrurl = "https://open.weixin.qq.com/qr/code?username={$wechat_id}";
        }
    }
    return $qrurl;
}

/**
 * 获取网址正文需要的meta数据
 * 
 * @param mixed $post
 * @return array
 */
function get_sites_post_meta($post = ''){
    if ('' === $post) {
        global $post;
    }
    $link_url    = get_post_meta($post->ID, '_sites_link', true);
    $default_ico = get_theme_file_uri('/images/favicon.png');
    $title       = get_the_title();
    $is_dead     = get_post_meta($post->ID, '_affirm_dead_url', true);
    $summary     = io_get_excerpt(170, '_sites_sescribe');
    
    $sites_type = get_post_meta($post->ID, '_sites_type', true);
    $is_preview = false;
    $sitetitle  = get_the_title();
    $imgurl     = get_site_thumbnail($sitetitle, $link_url, $sites_type, true ,$is_preview);
    $views      = function_exists('the_views')? the_views(false) :  '0' ;


    // 权限
    $post_show  = true;
    $user_level = get_post_meta($post->ID, '_user_purview_level', true);
    if ((!is_user_logged_in() && $user_level && $user_level != 'all')) {
        $link_url  = get_permalink();
        $post_show = false;
    }

    $sites_card_meta = array(
        "post_id"       => $post->ID,
        "title"         => $title, // 名字
        "summary"       => $summary, // 简介
        "sites_type"    => $sites_type, // 类型 网址 公众号
        "link_url"      => $link_url, // 目标地址
        "default_ico"   => $default_ico, // 默认图标
        "is_dead"       => $is_dead, // 确认是死链
        "post_show"     => $post_show // 权限 是否可见见
    );
    return $sites_card_meta;
}
if(!function_exists('get_sites_card_meta')):
/**
 * 获取网址meta数据
 * 
 * @param mixed $post
 * @return array
 */
function get_sites_card_meta($post = ''){
    if('' === $post){
        global $post;
    }
    $link_url       = get_post_meta($post->ID, '_sites_link', true); 
    $default_ico    = get_theme_file_uri('/images/favicon.png');
    $title          = get_the_title();
    $is_dead        = get_post_meta($post->ID, '_affirm_dead_url', true);

    $summary=htmlspecialchars(get_post_meta($post->ID, '_sites_sescribe', true));
    if( $summary=='' ){
        $summary = io_get_excerpt(30);
        update_post_meta($post->ID, '_sites_sescribe',$summary);
    } 
    $sites_type = get_post_meta($post->ID, '_sites_type', true);
    if($post->post_type != 'sites')
        $link_url = get_permalink($post->ID);
    $tip_title = $link_url;
    $is_html = '';
    $width = 128;
    $tooltip = 'data-toggle="tooltip" data-placement="bottom"';
    if($wechat_qr = get_post_meta_img($post->ID, '_wechat_qr', true)){
        $tip_title="<img src='" . $wechat_qr . "' width='{$width}'>";
        $is_html = 'data-html="true"';
    } elseif(($wechat_id = get_post_meta_img($post->ID, '_wechat_id', true)) && !get_post_meta_img($post->ID, '_is_min_app', true)){
        $tip_title="<img src='https://open.weixin.qq.com/qr/code?username=" . $wechat_id . "' width='{$width}'>";
        $is_html = 'data-html="true"';
    } else {
        switch(io_get_option('po_prompt','null')) {
            case 'null':  
                $tip_title = $title;
                $tooltip = '';
                break;
            case 'url': 
                if($link_url==""){
                    if($sites_type == "down")
                        $tip_title = __('下载','i_theme').'“'.$title.'”';
                    elseif ($sites_type == "wechat") 
                        $tip_title = __('居然没有添加二维码','i_theme');
                    else
                        $tip_title = __('没有 url','i_theme');
                }
                break;
            case 'summary':
                if($sites_type == "down")
                    $tip_title = __('下载','i_theme').'“'.$title.'”';
                else
                    $tip_title = $summary;
                break;
            case 'qr':
                if($link_url==""){
                    if($sites_type == "down")
                        $tip_title = __('下载','i_theme').'“'.$title.'”';
                    elseif ($sites_type == "wechat") 
                        $tip_title = __('居然没有添加二维码','i_theme');
                    else
                        $tip_title = __('没有 url','i_theme');
                }
                else{
                    $tip_title = "<img src='".get_qr_url($link_url, $width)."' width='{$width}' height='{$width}'>";
                    $is_html = 'data-html="true"';
                }
                break;
            default:  
        } 
    } 
    
    $url = '';
    $blank = new_window() ;
    $is_views = '';
    //($sites_meta['sites_type'] == "sites" && get_post_meta($post->ID, '_goto', true))?$sites_meta['link_url']:go_to($sites_meta['link_url'])
    if($sites_type == "sites" && get_post_meta($post->ID, '_goto', true)){
        $is_views = 'is-views';
        $blank = 'target="_blank"' ;
        $url = $link_url;
    }else{
        if(io_get_option('details_page',false)){
            $url=get_permalink();
        }else{ 
            if($sites_type && $sites_type != "sites"){
                $url=get_permalink();
            }
            elseif($link_url==""){
                $url = 'javascript:';
                $blank = '';
            }else{
                $is_views = 'is-views';
                $blank = 'target="_blank"' ;
                $url = go_to($link_url);
            }
        }
    }
    $ico            = '';
    $first_api_ico  = false;
    //if( !io_get_option('no_ico','') ){
        if($post->post_type != 'sites'){
            $ico = io_theme_get_thumb();
        }else{
            $ico = get_post_meta_img($post->ID, '_thumbnail', true);
            if(empty($ico) && io_get_option('is_letter_ico',false) && !io_get_option('first_api_ico',false)){
                $ico = io_letter_ico($title);
            }elseif(empty($ico) && io_get_option('is_letter_ico',false) && io_get_option('first_api_ico',false)){
                $first_api_ico = true;
            }
        }
        if($ico == ''){
            if( $link_url != '' || ($sites_type == "sites" && $link_url != '') ){
                $source = io_get_option( 'ico-source', array( "url_format"=>true, "ico_url"=>"https://api.iowen.cn/favicon/", "ico_png"=>".png" ) );
                $ico = ($source['ico_url'] .format_url($link_url) . $source['ico_png']);
            }elseif($sites_type == "wechat"){
                $ico = get_theme_file_uri('/images/qr_ico.png');
            }elseif($sites_type == "down"){
                $ico = get_theme_file_uri('/images/down_ico.png');
            }else{
                $ico = $default_ico;
            }
        }
    //}
    if ($is_dead )
        $link_url = get_permalink();

    // 权限
    $post_show  = true;
    $user_level = get_post_meta($post->ID, '_user_purview_level', true);
    if ( (!is_user_logged_in() && $user_level && $user_level != 'all') ) {
        $link_url = get_permalink();
        $post_show = false;
    }

    $sites_card_meta = array(
        "post_id"       => $post->ID,
        "title"         => $title,         // 名字
        "ico"           => $ico,           // 图标
        "url"           => $url,           // 详情页
        "is_views"      => $is_views,      // 启用点击增加点击量
        "is_html"       => $is_html,       // tooltip 类型
        "tooltip"       => $tooltip,       // tooltip 开关
        "tip_title"     => $tip_title,     // tooltip 内容
        "blank"         => $blank,         // 新窗口
        "summary"       => $summary,       // 简介
        "sites_type"    => $sites_type,    // 类型 网址 公众号
        "link_url"      => $link_url,      // 目标地址
        "default_ico"   => $default_ico,   // 默认图标
        "first_api_ico" => $first_api_ico, // 使用js首字母图标
        "is_dead"       => $is_dead,       // 确认是死链
        "post_show"     => $post_show      // 权限 是否可见见
    );
    return $sites_card_meta;
}
endif;


function get_report_reason(){
    $reasons = array(
        '1' => __('已失效','i_theme'),
        '2' => __('重定向&变更','i_theme'),//必须为 2
        '3' => __('已屏蔽','i_theme'),
        '4' => __('敏感内容','i_theme'),
        '0' => __('其他','i_theme'), //必须为 0
    );
    return apply_filters('io_sites_report_reason', $reasons);
}

/**
 * 举报模态框
 * @return void
 */
function report_model_body(){
    global $post, $post_type, $tmp_post; 
    // 还原主循环
    $post = $tmp_post;
    setup_postdata($post);
    if ($post_type != 'sites' || ($post_type == 'sites' && get_post_meta( get_the_ID(), '_sites_type', true )=='down')) return;
    ?>
    <div class="modal fade add_new_sites_modal" id="report-sites-modal" tabindex="-1" role="dialog" aria-labelledby="report-sites-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-md" id="report-sites-title"><?php _e('反馈','i_theme') ?></h5>
                    <button type="button" id="close-sites-modal" class="close io-close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="iconfont icon-close-circle text-xl"></i>
                    </button>
                </div>
                <div class="modal-body"> 
                    <div class="alert alert-info" role="alert">
                    <i class="iconfont icon-statement "></i> <?php _e('让我们一起共建文明社区！您的反馈至关重要！','i_theme') ?>
                    </div>
                    <form id="report-form" method="post"> 
                        <input type="hidden" name="post_id" value="<?php echo get_the_ID() ?>">
                        <input type="hidden" name="action" value="report_site_content">
                        <div class="form-row">
                            <?php
                            $option = get_report_reason();
                            if(get_post_meta(get_the_ID(), '_affirm_dead_url', true)){
                                $option = array('666' => __('已可访问','i_theme'));
                            }
                            foreach ($option as $key => $reason) {
                                echo '<div class="col-6 py-1">
                                <label><input type="radio" name="reason" class="reason-type-' . $key . '" value="' . $key . '" ' . (in_array($key,array(1,666)) ? 'checked' : '') . '> ' . $reason . '</label>
                            </div>';
                            }
                            ?>
                        </div>
                        <div class="form-group other-reason-input" style="display: none;">
                            <input type="text" class="form-control other-reason" value="" placeholder="<?php _e('其它信息，可选','i_theme') ?>">
                        </div>  
                        <div class="form-group redirect-url-input" style="display: none;">
                            <input type="text" class="form-control redirect-url" value="" placeholder="<?php _e('重定向&变更后的地址','i_theme') ?>">
                        </div> 
                        <div class=" text-center">
                            <button type="submit" class="btn btn-danger"><?php _e('提交反馈','i_theme') ?></button>
                        </div> 
                    </form>
                </div> 
            </div>
        </div>
        <script>
        $(function () {
            $('.tooltip-toggle').tooltip();
            $('input[type=radio][name=reason]').change(function() {
                var t = $(this); 
                var reason = $('.other-reason-input');
                var url = $('.redirect-url-input');
                reason.hide();
                url.hide();
                if(t.val()==='0'){
                    reason.show();
                }else if(t.val()==='2'){
                    url.show();
                }
            }); 
            $(document).on("submit",'#report-form', function(event){
                event.preventDefault(); 
                var t = $(this); 
                var reason = t.find('input[name="reason"]:checked').val();
                if(reason === "0"){
                    reason = t.find('.other-reason').val();
                    if(reason==""){
                        showAlert(JSON.parse('{"status":4,"msg":"<?php _e('信息不能为空！','i_theme') ?>"}'));
                        return false;
                    }
                }
                if(reason === "2"){
                    if(t.find('.redirect-url').val()==""){
                        showAlert(JSON.parse('{"status":4,"msg":"<?php _e('信息不能为空！','i_theme') ?>"}'));
                        return false;
                    }
                }
                $.ajax({
                    url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
                    type: 'POST', 
                    dataType: 'json',
                    data: {
                        action : t.find('input[name="action"]').val(),
                        post_id : t.find('input[name="post_id"]').val(),
                        reason : reason,
                        redirect : t.find('.redirect-url').val(),
                    },
                })
                .done(function(response) {   
                    if(response.status == 1){
                        $('#report-sites-modal').modal('hide');
                    } 
                    showAlert(response);
                })
                .fail(function() {  
                    showAlert(JSON.parse('{"status":4,"msg":"<?php _e('网络错误 --.','i_theme') ?>"}'));
                }); 
                return false;
            });
        });
        </script>
    </div>
    <?php
}
if(io_get_option('report_button',true))
add_action('wp_footer', 'report_model_body');