<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-07-20 16:23:07
 * @LastEditors: iowen
 * @LastEditTime: 2024-04-11 16:27:51
 * @FilePath: /onenav/inc/theme-settings.php
 * @Description: 
 */

if ( ! defined( 'ABSPATH' )  ) { die; }
if (!is_admin()) return;
$prefix = 'io_get_option';
$rewrite_rules_btn = '<a href="'.add_query_arg(array('page'=>'theme_settings','action'=>'rewrite_rules'),admin_url('admin.php')).'" class="but c-yellow ml-3" style="padding:4px 8px">刷新固定连接</a>';
CSF::createOptions( $prefix, array(
    'framework_title' => 'OneNav <small>V'.wp_get_theme()->get('Version').' <a class="ml-3 text-help" href="https://www.iotheme.cn/one-nav-zhutishouce.html" target="_blank"><i class="fab fa-hire-a-helper"></i> 使用手册</a>'.$rewrite_rules_btn.'</small>',
    'menu_title'      => '主题设置',
    'menu_slug'       => 'theme_settings', 
    'menu_position'   => 58,
    'save_defaults'   => true,
    'ajax_save'       => true,
    'show_bar_menu'   => false,
    'theme'           => 'dark',
    'class'           => 'io-option',
    'show_search'     => true,
    'footer_text'     => esc_html__('运行在', 'io_setting' ).'： WordPress '. get_bloginfo('version') .' / PHP '. PHP_VERSION,
    'footer_credit'   => '感谢您使用 <a href="https://www.iotheme.cn/" target="_blank">一为</a>的WordPress主题',
));

$views_use_ajax = ( defined( 'WP_CACHE' ) && WP_CACHE )?'':'csf-depend-visible csf-depend-on';
$set_search = '<a href="'.esc_url(add_query_arg('page', 'search_settings', admin_url('options-general.php'))).'" class="button button-primary '.(io_get_option('custom_search')?'':'disabled').'">前往配置</a>';
$tip_ico = '<i class="fa fa-fw fa-info-circle"></i> ';

function get_sorter_options($type = 'term'){
    if($type == 'term'){
        return get_all_taxonomy();
    }else if($type == 'top_widget'){
        return  apply_filters('io_home_widget_list_filters',array(
            'carousel-post' => '文章轮播模块',
            'tab'           => 'Tab 内容模块',
            'swiper'        => 'Big 轮播模块',  
        ));
    }
}
$prk_list = array(
    'qq'        => 'QQ',
    'wx'        => '微信',
    'alipay'    => '支付宝',
    'sina'      => '微博',
    'baidu'     => '百度',
    'huawei'    => '华为',
    'google'    => '谷歌',
    'microsoft' => '微软',
    'facebook'  => 'Facebook',
    'twitter'   => 'Twitter',
    'dingtalk'  => '钉钉',
    'github'    => 'GitHub',
    'gitee'     => 'Gitee',
); 
$is_server_load = !empty(IOTOOLS::get_server_load());
$hot_list_order = array(
    'modified'      => '最新修改',
    'date'          => '最新添加',
    'views'         => '查看次数',
    'comment_count' => '评论量',
    '_down_count'   => '下载最多(APP)',
    'random'        => '随机',
);
if(!io_get_option('user_center',false)){
    $hot_list_order['_like_count'] = '点赞(大家喜欢)';
}
//
// 开始使用
//
CSF::createSection( $prefix, array(
    'title'        => __('开始使用','io_setting'),
    'icon'         => 'fa fa-shopping-cart',
    'fields'       => array(
        array(
            'type'    => 'submessage',
            'style'   => 'danger',
            'content' => '<h3 style="color:#ee187e"><i class="fa fa-heart fa-fw"></i> 感谢您使用 ioTheme 一为主题</h3><p style="font-size:13px">'.$tip_ico.'<b>注意 & 必看</b></p>
            <li><b>必须配置</b> 服务器 <b>伪静态规则</b> 和 <b>wp<a href="'.admin_url('options-permalink.php').'">固定链接</a></b> 格式，否则会造成<b>404</b>，<a href="https://www.iotheme.cn/wordpressweijingtaihewordpressgudinglianjieshezhi.html" target="_blank">伪静态设置方法</a></li>
            <li><b>演示数据</b>： <a href="https://www.iotheme.cn/one-nav-yidaohangyanshishujushiyongjiaocheng.html" target="_blank">下载</a> （<b>警告</b>：安装演示数据前必须配置服务器 <b>伪静态规则</b>）</li>
            <li><a href="https://www.iotheme.cn/update-log" target="_blank">查看更新日志</a></li>',
        ),
        array(
            'type'     => 'callback',
            'function' => 'active_html',
        ),
        array(
            'id'      => 'update_theme',
            'type'    => 'switcher',
            'title'   => __('检测主题更新','io_setting'),
            'label'   => __('在线更新为替换更新，如果你修改了主题代码，请关闭（如需修改，可以使用子主题）','io_setting'),
            'default' => true,
            'desc'    => $tip_ico.'请勿修改主题文件夹名称，可能会导致检查不到更新。<br />'.$tip_ico.'基于wp更新通道，请勿关闭wp更新检查。',
        ),
        array(
            'id'      => 'update_beta',
            'type'    => 'switcher',
            'title'   => __('加入Beta版体验','io_setting'),
            'label'   => '可体验最新功能',
            'before'   => '<a href="'.admin_url('update-core.php').'" class="but c-blue" style="margin:0">检查更新</a>',
            'desc'   => '开启后加入Beta版更新通道，Beta版及测试版，可体验最新功能，同时也可能会有各种bug。',
            'class'   => 'compact min',
            'default' => false,
            'dependency' => array( 'update_theme', '==', true )
        ),
        array(
            'type'    => 'content',
            'title'   => '系统环境',
            'content' => io_get_system_info(),
        ),
        array(
            'type'    => 'content',
            'title'   => '推荐环境',
            'content' => '<ul><li><strong>WordPress</strong>：6.1+，推荐使用最新版</li>
            <li><strong>PHP</strong>：PHP7.4及以上</li>
            <li><strong>服务器配置</strong>：1核2G，不推荐虚拟机</li>
            <li><strong>操作系统</strong>：无要求，不推荐使用Windows系统</li>
            <li><strong>环境</strong>：推荐宝塔或者自建</li></ul>',
        ),
    )
));

/**
 * -----------------------------------------------------------------------
 * HOOK : ACTION HOOK
 * io_setting_option_begin_code
 * 
 * 在主题设置菜单前挂载其他内容
 * @since   
 * -----------------------------------------------------------------------
 */
do_action( 'io_setting_option_begin_code' , $prefix,'自定义项','fas fa-dot-circle' ); 

//
// 图标设置
//
CSF::createSection( $prefix, array(
    'title'        => __('图标设置','io_setting'),
    'icon'         => 'fa fa-star',
    'fields'       => array(
    array(
        'id'        => 'logo_normal',
        'type'      => 'upload',
        'title'     => '暗色主题Logo',
        'add_title' => __('上传','io_setting'),
        'after'     => '<p class="cs-text-muted">'.__('建议高80px，长小于360px','io_setting'),
        'default'   => get_theme_file_uri( '/images/logo@2x.png'),
    ),
    array(
        'id'        => 'logo_normal_light',
        'type'      => 'upload',
        'title'     => __('亮色主题Logo','io_setting'),
        'add_title' => __('上传','io_setting'),
        'after'     => '<p class="cs-text-muted">'.__('建议高80px，长小于360px','io_setting'),
        'default'   => get_theme_file_uri('/images/logo_l@2x.png'),
    ),
    array(
        'id'        => 'logo_small',
        'type'      => 'upload',
        'title'     => __('暗色主题方形 Logo','io_setting'),
        'add_title' => __('上传','io_setting'),
        'after'     => '<p class="cs-text-muted">'.__('建议 80x80','io_setting'),
        'default'   => get_theme_file_uri('/images/logo-collapsed@2x.png'),
    ),
    array(
        'id'        => 'logo_small_light',
        'type'      => 'upload',
        'title'     => __('亮色主题方形 Logo','io_setting'),
        'add_title' => __('上传','io_setting'),
        'after'     => '<p class="cs-text-muted">'.__('建议 80x80','io_setting'),
        'default'   => get_theme_file_uri('/images/logo-dark_collapsed@2x.png'),
    ),
    array(
        'id'        => 'favicon',
        'type'      => 'upload',
        'title'     => __('上传 Favicon','io_setting'),
        'add_title' => __('上传','io_setting'),
        'default'   => get_theme_file_uri('/images/favicon.png'),
    ),
    array(
        'id'        => 'apple_icon',
        'type'      => 'upload',
        'title'     => __('上传 apple_icon','io_setting'),
        'add_title' => __('上传','io_setting'),
        'default'   => get_theme_file_uri('/images/app-ico.png'),
    ),
    )
));

//
// 颜色&样式
//
CSF::createSection( $prefix, array(
    'title'        => __('颜色&样式','io_setting'),
    'icon'         => 'fa fa-tachometer',
    'fields'       => array( 
        array(
            'id'      => 'theme_mode',
            'type'    => 'radio',
            'title'   => __('颜色主题','io_setting'),
            'inline'  => true,
            'options' => array(
                'io-black-mode'  => __('暗色','io_setting'),
                'io-white-mode'  => __('黑白','io_setting'),
                'io-grey-mode'   => __('亮灰','io_setting'),
            ),
            'default' => 'io-grey-mode',
            'after'   => __('设置好后需清除浏览器cookie才能生效','io_setting')
        ),
        array(
            'type'    => 'notice',
            'style'   => 'warning',
            'content' => '<li style="font-size:18px;color: red">自动切换模式下【颜色主题】不能选择【暗色】</li>',
            'dependency' => array( 'theme_mode|theme_auto_mode', '==|not-any', 'io-black-mode|manual-theme,null' )
        ),
        array(
            'id'      => 'theme_auto_mode',
            'type'    => "radio",
            'title'   => '主题切换模式',
            'options' => array(
                'null'         => '不切换(关闭切换按钮)',
                'manual-theme' => '手动模式',
                'auto-system'  => '根据系统自动切换',
                'time-auto'    => '自定义时间段',
            ),
            'default' => 'auto-system',
            'after'   => '主题最高<b>优先级</b>来自<b>用户选择</b>，也就是<b>浏览器缓存</b>，只有当用户未手动切换主题时<b>自动切换</b>才有效。<br>'.$tip_ico.'<b>根据系统自动切换</b>支持win10、win11、最新Mac OS和最新的移动端操作系统的<b>夜间模式</b>。',
            'class'   => ''
        ),
		array(
			'id'        => 'time_auto',
			'type'      => 'datetime',
			'title'     => '时间段设置',
			'from_to'   => true,
			'text_from' => '浅色',
			'text_to'   => '深色',
			'settings' => array(
				'noCalendar' => true,
				'enableTime' => false,
				'dateFormat' => 'H',
				'time_24hr'  => true,
				'allowInput' => true,
				'allFormat'  => 'H',
			),
			'default' => array(
				'from'  => "7",
				'to'    => '18'
			),
			'dependency' => array( 'theme_auto_mode', '==', 'time-auto' ),
            'after'   => $tip_ico.'填24小时时间，如浅色：7，深色：18',
			'class'   => 'compact'
		),
        array(
            'id'      => 'back_to_top_ico',
            'type'    => 'icon',
            'title'   => '次级导航返回后按钮',
            'default' => 'iconfont icon-back',
        ),
        array(
            'id'      => 'mobile_header_layout',
            'type'    => "image_select",
            'title'   => '移动端导航布局',
            'options' => array(
                'header-center' => get_theme_file_uri('/images/option/op_header_layout_center.png'),
                'header-left'   => get_theme_file_uri('/images/option/op_header_layout_left.png'),
            ),
            'default' => "header-center",
            'class'   => '',
        ),
        array(
            'id'      => 'h_width',
            'type'    => 'slider',
            'title'   => '自定义首页内容宽度',
            'class'   => '',
            'min'     => 1320,
            'max'     => 2000,
            'step'    => 10,
            'unit'    => 'px',
            'default' => 1900,
            'after'   => '默认 1900px',
        ),
        array(
            'id'      => 'sidebar_width',
            'type'    => 'slider',
            'title'   => '侧边栏菜单宽度',
            'class'   => '',
            'min'     => 120,
            'max'     => 320,
            'step'    => 10,
            'unit'    => 'px',
            'default' => 220,
            'after'   => '默认 220px，调整后可能需要修改 Logo 图片宽度',
        ),
        array(
            'id'      => 'loading_fx',
            'type'    => 'switcher',
            'title'   => __('全屏加载效果','io_setting'),
            'default' => false,
        ),
        array(
            'id'        => 'loading_type',
            'type'      => 'image_select',
            'title'     => __('加载效果','io_setting'),
            'options'   => array(
                'rand'  => get_theme_file_uri('/images/loading/load0.png'),
                '1'     => get_theme_file_uri('/images/loading/load1.png'),
                '2'     => get_theme_file_uri('/images/loading/load2.png'),
                '3'     => get_theme_file_uri('/images/loading/load3.png'),
                '4'     => get_theme_file_uri('/images/loading/load4.png'),
                '5'     => get_theme_file_uri('/images/loading/load5.png'),
                '6'     => get_theme_file_uri('/images/loading/load6.png'),
                '7'     => get_theme_file_uri('/images/loading/load7.png'),
            ),
            'default'   => '1',
            'class'     => '',
            'subtitle'  => __('包括go跳转页,go跳转页不受上面开关影响','io_setting'),
        ),
        array(
            'id'        => 'login_ico',
            'type'      => 'upload',
            'title'     => __('登录页图片','io_setting'),
            'add_title' => __('上传','io_setting'),
            'default'   => get_theme_file_uri('/images/login.jpg'),
        ),
        array(
            'id'        => 'login_color',
            'type'      => 'color_group',
            'title'     => '登录页背景色',
            'class'     => '',
            'options'   => array(
                'color-l'   => '左边',
                'color-r'   => '右边',
            ),
            'default'   => array(
                'color-l'   => '#7d00a0',
                'color-r'   => '#c11b8d',
            ),
        ),
        array(
            'id'      => 'custom_color',
            'type'    => 'switcher',
            'title'   => __('自定义颜色','io_setting'),
            'default' => false,
        ),
        array(
            'id'        => 'bnt_c',
            'type'      => 'color_group',
            'title'     => '按钮颜色',
            'options'   => array(
                'color'   => '默认颜色',
                'color-t' => '默认文字颜色',
                'hover'   => 'hover 颜色',
                'hover-t' => 'hover 文字颜色',
            ),
            'default'   => array(
                'color'   => '#f1404b',
                'color-t' => '#ffffff',
                'hover'   => '#14171B',
                'hover-t' => '#ffffff',
            ),
            'dependency' => array( 'custom_color', '==', true )
        ),
        array(
            'id'      => 'link_c',
            'type'    => 'link_color',
            'title'   => '文章 a 链接颜色',
            'default' => array(
                'color' => '#f1404b',
                'hover' => '#f9275f',
            ),
            'dependency' => array( 'custom_color', '==', true )
        ),
        array(
            'id'      => 'card_a_c',
            'type'    => 'color',
            'title'   => '卡片链接高亮',
            'default' => '#f1404b',
            'dependency' => array( 'custom_color', '==', true )
        ),
        array(
            'id'      => 'piece_c',
            'type'    => 'color',
            'title'   => '高亮色块',
            'default' => '#f1404b',
            'dependency' => array( 'custom_color', '==', true )
        ),
    )
));

//
// 基础设置
//
CSF::createSection( $prefix, array(
    'title'  => __('基础设置','io_setting'),
    'icon'   => 'fa fa-th-large',
    'fields' => array(
        array(
            'id'      => 'nav_login',
            'type'    => 'switcher',
            'title'   => __('顶部登录按钮','io_setting'),
            'default' => true,
        ),
        array(
            'id'      => 'm_language',
            'type'    => 'switcher',
            'title'   => __('多语言','io_setting'),
            'default' => false,
            'desc'    => '设置项启用多语言支持。<br />单语言站请不要开启，如：只有中文、只有英语等。<br /><br />
                        <b>注意</b>：开启后需配合“多语言”插件，插件请自行查找（推荐：Polylang）；开启此选项仅用于主题设置选项填写的内容增加翻译。<br /><br />
                        <b>词条翻译</b> 如选项名称有“多语言”字样，可填多语言内容，格式为：<code>zh<b>=*=</b>服务内容<b>|*|</b>en<b>=*=</b>Service Content</code> <br />如果不需要翻译，直接填内容，如：服务内容'
        ),
        array(
            'id'      => 'lang_list',
            'type'    => 'text',
            'title'   => '语言列表',
            'after'   => '填语言代码 - 最好是2个字母ISO 639-1（例如：en），如有很多，用<code>|</code>分割（例如：en|ja）<br />默认语言不需要填<br /><a href="https://zh.wikipedia.org/wiki/ISO_639-1" target="_blank">各国语言缩写参考</a><br /><br />
                        <span style="color:#f00">警告：设置后需重新保存一次<a href="'.admin_url('options-permalink.php').'">固定链接</a></span>',
            'default' => 'en', 
            'class'   => 'compact min',
            'dependency' => array( 'm_language', '==', 'true' )
        ),
        array(
            'id'      => 'nav_comment',
            'type'    => 'switcher',
            'title'   => __('站点评论','io_setting'),
            'default' => true,
            'label'   => '全局开关',
        ),
        array(
            'id'      => 'no_dead_url',
            'type'    => 'switcher',
            'title'   => __('首页屏蔽“确认失效的链接”','io_setting'),
            'default' => false,
            'class'   => 'new',
        ),
        array(
            'id'      => 'global_remove',
            'type'    => 'radio',
            'title'   => '内容显示权限',
            'options' => array(
                'admin' => '仅查看对应权限内容',
                'user'  => '仅限制未登录用户',
                'point' => '不限制仅提示',
                'close' => '关闭',
            ),
            'default' => 'close',
            'after'   => '<div class="ajax-form"><p>'.$tip_ico.'注意：开启此选项可能会导致文章404，解决方法：</p>
                                <li>手动编辑文章，在“权限&商品”选项卡中选择“所有”</li>
                                <li>点击自动添加，会自动扫描全部文章内容添加对应字段，注意：先备份数据库，有问题请恢复。<a class="ajax-get" href="' . add_query_arg(array('action' => 'io_update_post_purview'), admin_url('admin-ajax.php')) . '">立即添加-></a></li>
                            <div class="ajax-notice"></div>
                            </div>
                            <p><b>选项说明：</b></p><ol><li><b>仅查看对应权限内容</b>：根据权限，彻底阻止访问未授权的内容，访问高权资源会404，相当于网站不存在对应资源。</li>
                            <li><b>不限制仅提示</b>：在首页等列表能看到站点所有资源，进入高权资源会提示引导相关操作，如登录，升级用户组等。</li>
                            <li><b>仅限制未登录用户</b>：未登录用户如[仅查看对应权限内容]选项，登录用户如[不限制仅提示]选项</li>
                            <li><b>关闭</b>：内容权限等级不生效</li></ol>
                            <p>内容权限请到内容编辑页内修改</p>
                            注意：(必看)<br/>1、如果未关闭此功能，管理员可见资源永远只能管理员看到。<br/>2、采集类直接写库的任务需增加字段<code>_user_purview_level</code>，取值<code>all</code>。',
        ),
        array(
            'id'      => 'min_nav',
            'type'    => 'switcher',
            'title'   => __('mini 侧边栏（收缩侧边栏菜单）','io_setting'),
            'label'   => __('开启后，左侧菜单默认收缩，开启前请设置好菜单项图标','io_setting'),
            'default' => false,
        ),
        array(
            'id'        => 'sidebar_layout',
            'type'      => 'radio',
            'title'     => '默认侧边栏布局',
            'inline'    => true,
            'options'   => array(
                'sidebar_left'  => '靠左',
                'sidebar_right' => '靠右',
            ),
            'default'   => "sidebar_right",
            'after'     => '<p style="color:#4abd23"><i class="fa fa-fw fa-info-circle fa-fw"></i> 如需要关掉小工具，可尝试清空对应小工具内容或者到对应页面的选项里关掉小工具。</p>',
        ),
        array(
            'id'      => 'nav_top_mobile',
            'type'    => 'text',
            'title'   => '移动设备顶部菜单名称（多语言）',
            'after'   => __('大屏顶部菜单在移动设备上显示到侧边栏菜单，-->留空则不显示<--','io_setting'),
            'default' => '站点推荐',
            'class'   => '',
        ),
        array(
            'id'      => 'nav_top_mobile_ico',
            'type'    => 'icon',
            'title'   => '移动设备顶部菜单图标',
            'default' => 'iconfont icon-category',
            'class'   => 'compact min',
        ),
        array(
            'id'          => 'cdn_resources',
            'type'        => 'select',
            'title'       => '静态文件使用公共库', 
            'options'     => array(
                'local'         => '本地',
                'jsdelivr'      => 'jsdelivr.net',
                'bytecdntp'     => '字节 cdn.bytedance.com',
                'staticfile'    => 'bootcdn.net',
            ),
            'settings' => array(
                'width'   => '120px',
            ),
            'default'     => 'local'
        ),
        array(
            'id'      => 'jsdelivr-cdn',
            'type'    => 'text',
            'title'   => 'jsdelivr 国内加速地址',
            'after'   => '可用：<code>fastly.jsdelivr.net</code> 、 <code>cdn.iocdn.cc</code><br>'.$tip_ico.'留空则使用<code>cdn.jsdelivr.net</code>',
            'default' => 'cdn.iocdn.cc',
            'class'   => 'compact',
            'dependency' => array( 'cdn_resources', '==', 'jsdelivr' )
        ),
        array(
            'id'      => 'bing_cache',
            'type'    => 'switcher',
            'title'   => __('必应背景图片本地缓存','io_setting'),
            'label'   => __('文明获取，避免每次都访问 bing 服务器','io_setting'),
            'desc'    => __('使用了oss等图床插件的请关闭此功能','io_setting'),
            'default' => false,
        ),
        array(
            'id'        => 'sticky_tag',
            'type'      => 'fieldset',
            'title'     => __('置顶标签','io_setting'),
            'fields'    => array(
                array(
                    'id'    => 'switcher',
                    'type'  => 'switcher',
                    'title' => __('显示','io_setting'),
                ),
                array(
                    'id'    => 'name',
                    'type'  => 'text',
                    'title' => __('显示内容','io_setting'),
                ),
            ),
            'default'        => array(
                'switcher'    => false,
                'name'        => 'T',
            ),
        ),
        array(
            'id'        => 'new_tag',
            'type'      => 'fieldset',
            'title'     => __('NEW 标签','io_setting'),
            'fields'    => array(
                array(
                    'id'    => 'switcher',
                    'type'  => 'switcher',
                    'title' => __('显示','io_setting'),
                ),
                array(
                    'id'    => 'name',
                    'type'  => 'text',
                    'title' => __('显示内容','io_setting'),
                ),
                array(
                    'id'    => 'date',
                    'type'  => 'spinner',
                    'title' => __('时间','io_setting'),
                    'after' => __('几天内的内容标记为新内容','io_setting'),
                    'unit'  => '天',
                    'step'  => 1,
                ),
            ),
            'default'        => array(
                'switcher'    => false,
                'name'        => 'N',
                'date'        => 7,
            ),
        ),
        array(
            'id'      => 'is_nofollow',
            'type'    => 'switcher',
            'title'   => __('网址块添加nofollow','io_setting'),
            'label'   => __('详情页开启则不添加','io_setting'),
            'default' => true,
        ),
        array(
            'id'      => 'new_window',
            'type'    => 'switcher',
            'title'   => __('新标签中打开内链','io_setting'),
            'label'   => __('站点所有内部链接在新标签中打开','io_setting'),
            'default' => true,
        ),
        array(
            'id'      => 'lazyload',
            'type'    => 'switcher',
            'title'   => __('图片懒加载','io_setting'),
            'label'   => __('所有图片懒加载','io_setting'),
            'default' => true,
        ),
        array(
            'id'      => 'show_friendlink',
            'type'    => 'switcher',
            'title'   => __('启用友链','io_setting'),
            'label'   => __('启用自定义文章类型“链接(友情链接)”，启用后需刷新页面','io_setting'),
            'default' => true,
        ),
        array(
            'id'         => 'links',
            'type'       => 'switcher',
            'title'      => __('友情链接','io_setting'),
            'label'      => __('在首页底部添加友情链接','io_setting'),
            'default'    => true,
            'class'      => 'compact min',
            'dependency' => array( 'show_friendlink', '==', true )
        ),
        array(
            'id'          => 'home_links',
            'type'        => 'checkbox',
            'title'       => __('首页显示分类','io_setting'),
            'after'       => __('不选则全部显示。','io_setting'),
            'inline'      => true,
            'class'       => 'compact min',
            'options'     => 'categories',
            'query_args'  => array(
                'taxonomy'  => 'link_category',
            ),
            'dependency'  => array( 'show_friendlink|links', '==|==', 'true|true' )
        ),
        //array(
        //    'id'          => 'links_pages',
        //    'type'        => 'select',
        //    'title'       => __('友情链接归档页','io_setting'),
        //    'after'       => __(' 如果没有，新建页面，选择“友情链接”模板并保存。','io_setting'),
        //    'options'     => 'pages',
        //    'class'       => 'compact min',
        //    'query_args'  => array(
        //        'posts_per_page'  => -1,
        //    ),
        //    'placeholder' => __('选择友情链接归档页面', 'io_setting'),
        //    //'default'     => io_get_template_page_url('template-links.php',true),
        //    'dependency'  => array( 'show_friendlink|links', '==|==', 'true|true' )
        //),
        array(
            'id'      => 'save_image',
            'type'    => 'switcher',
            'title'   => __('本地化外链图片','io_setting'),
            'label'   => __('自动存储外链图片到本地服务器','io_setting'),
            'desc'    => __('<p>只支持经典编辑器</p><strong>注：</strong>使用古腾堡(区块)编辑器的请不要开启，否则无法保存文章','io_setting'),
            'default' => false,
        ),
        array(
            'id'      => 'exclude_image', 
            'type'    => 'textarea',
            'title'   => __('本地化外链图片白名单','io_setting'),
            'after'   => __('一行一个地址，注意不要有空格。<br>不需要包含http(s)://<br>如：iowen.cn','io_setting'),
            'class'   => 'compact',
            'default' => 'alicdn.com',
            'dependency' => array( 'save_image', '==', true )
        ),
    )
));

//
// 首页设置
//
CSF::createSection( $prefix, array(
    'id'    => 'home_setting',
    'title' => __('首页设置','io_setting'),
    'icon'  => 'fas fa-laptop-house',
));


//
// 首页设置-首页常规
//
CSF::createSection( $prefix, array(
    'parent'      => 'home_setting',
    'title'       => __('首页常规','io_setting'),
    'icon'        => 'fas fa-igloo', 
    'fields'       => array(  
        array(
            'id'      => 'po_prompt',
            'type'    => 'radio',
            'title'   => __('网址块弹窗提示(悬停、hover)','io_setting'),
            'desc'    => __('网址块默认的弹窗提示内容','io_setting'),
            'default' => 'url',
            'inline'  => true,
            'options' => array(
                'null'      => __('无','io_setting'),
                'url'       => __('链接','io_setting'),
                'summary'   => __('简介','io_setting'),
                'qr'        => __('二维码','io_setting'),
            ),
            'after'   => __('如果网址添加了自定义二维码，此设置无效','io_setting'),
        ),
        array(
            'id'        => 'card_n',
            'type'      => 'fieldset',
            'title'     => __('在首页分类下显示的内容数量','io_setting'),
            'fields'    => array(
                array(
                    'id'    => 'favorites',
                    'type'  => 'spinner',
                    'title' => __('网址数量','io_setting'),
                    'step'  => 1,
                ),
                array(
                    'id'    => 'apps',
                    'type'  => 'spinner',
                    'title' => __('App 数量','io_setting'),
                    'step'  => 1,
                    'class' => 'compact min',
                ),
                array(
                    'id'    => 'books',
                    'type'  => 'spinner',
                    'title' => __('书籍数量','io_setting'),
                    'step'  => 1,
                    'class' => 'compact min',
                ),
                array(
                    'id'    => 'category',
                    'type'  => 'spinner',
                    'title' => __('文章数量','io_setting'),
                    'step'  => 1,
                    'class' => 'compact min',
                ),
            ),
            'default'        => array(
                'favorites'   => 20,
                'apps'        => 16,
                'books'       => 16,
                'category'    => 16,
            ),
            'after'      => $tip_ico.'填写需要显示的数量，如果分类包含内容大于显示数量，则显示“更多”按钮。<br>-1 为显示分类下所有网址<br>&nbsp;0 为根据<a href="'.home_url().'/wp-admin/options-reading.php">系统设置数量显示</a>',
        ),
        array(
            'id'         => 'term_more_text',
            'type'       => 'text',
            'title'      => '更多按钮文案（多语言）',
            'default'    => 'more+', 
        ),
        array(
            'id'      => 'show_sticky',
            'type'    => 'switcher',
            'title'   => __('置顶内容前置','io_setting'),
            'label'   => __('首页置顶的内容显示在前面','io_setting'),
            'default' => false,
        ),
        array(
            'id'      => 'category_sticky',
            'type'    => 'switcher',
            'title'   => __('分类&归档页置顶内容前置','io_setting'),
            'label'   => __('注意：会导致第一页内容超过设置的显示数量','io_setting'),
            'default' => false, 
            'class'   => 'compact min',
            'dependency' => array( 'show_sticky', '==', true )
        ),
        array(
            'id'        => 'home_sort',
            'type'      => 'fieldset',
            'title'     => __('首页分类排序','io_setting'),
            'fields'    => array(
                array(
                    'id'    => 'favorites',
                    'type'  => 'radio',
                    'title' => __('网址排序','io_setting'),
                    'inline'     => true,
                    'options'    => apply_filters('io_set_home_sort_favorites_filters', array(
                        '_sites_order'  => '自定义排序字段',
                        'ID'            => 'ID',
                        'modified'      => '修改日期',
                        'date'          => '创建日期',
                        'views'         => '查看次数',
                        'comment_count' => '评论量',
                    )),
                ),
                array(
                    'id'    => 'apps',
                    'type'  => 'radio',
                    'title' => __('APP 排序','io_setting'),
                    'inline'     => true,
                    'options'    => apply_filters('io_set_home_sort_apps_filters', array(
                        'ID'            => 'ID',
                        'modified'      => '修改日期',
                        'date'          => '创建日期',
                        'views'         => '查看次数',
                        '_down_count'   => '下载次数',
                        'comment_count' => '评论量',
                    )),
                    'class' => 'compact min',
                ),
                array(
                    'id'    => 'books',
                    'type'  => 'radio',
                    'title' => __('书籍排序','io_setting'),
                    'inline'     => true,
                    'options'    => apply_filters('io_set_home_sort_books_filters', array(
                        'ID'            => 'ID',
                        'modified'      => '修改日期',
                        'date'          => '创建日期',
                        'views'         => '查看次数',
                        'comment_count' => '评论量',
                    )),
                    'class' => 'compact min',
                ),
                array(
                    'id'    => 'category',
                    'type'  => 'radio',
                    'title' => __('文章排序','io_setting'),
                    'inline'     => true,
                    'options'    => apply_filters('io_set_home_sort_category_filters', array(
                        'ID'            => 'ID',
                        'modified'      => '修改日期',
                        'date'          => '创建日期',
                        'views'         => '查看次数',
                        'comment_count' => '评论量',
                    )),
                    'class' => 'compact min',
                ),
            ),
            'default'        => array(
                'favorites'   => '_sites_order',
                'apps'        => 'modified',
                'books'       => 'modified',
                'category'    => 'date',
            ),
            'after'   => '<p style="color: red">'.__('启用“查看次数”“下载次数”等排序方法请开启相关统计，如果对象没有相关数据，则不会显示。','io_setting').'</p>',
        ),
        array(
            'id'      => 'same_ico',
            'type'    => 'switcher',
            'title'   => __('统一图标','io_setting'),
            'label'   => __('首页侧边栏和内容标题统一图标','io_setting'),
            'default' => false,
            'class'   => '',
        ),
        array(
            'id'      => 'tab_type',
            'type'    => 'switcher',
            'title'   => __('tab(选项卡)模式','io_setting'),
            'label'   => __('首页使用标签模式展示2级收藏网址','io_setting'),
            'default' => true,
        ),
        array(
            'id'      => 'tab_ajax',
            'type'    => 'switcher',
            'title'   => __('tab模式 ajax 加载','io_setting'),
            'label'   => __('降低首次载入时间，但切换tab时有一定延时','io_setting'),
            'default' => true,
            'class'   => 'compact min',
            'dependency' => array( 'tab_type', '==', true )
        ),
        array(
            'id'      => 'tab_p_n',
            'type'    => 'switcher',
            'title'   => __('父级名称','io_setting'),
            'label'   => __('网址块分类名前面显示父级分类名称','io_setting'),
            'default' => false,
        ),
    )
));

//
// 首页设置-首页头部
//
CSF::createSection( $prefix, array(
    'parent'      => 'home_setting',
    'title'       => __('首页头部','io_setting'),
    'icon'        => 'fas fa-bread-slice', 
    'fields'       => array(  
        array(
            'id'           => 'home_widget',
            'type'         => 'sorter',
            'title'        => '头部内容',
            'subtitle'     => '模块启用和排序',
            'default'      => array(
                'enabled'    => array(),
                'disabled'   => get_sorter_options('top_widget'),
            ),
            'before'      => $tip_ico.'警告：启用模块后下方对应模块的内容请认真填写，选项不能留空，否则网站将爆炸。',
            'options_id'  => 'top_widget',
            'is_enabled'  => false,
            'refresh'     => true,
            'class'       => '',
        ),
        array(
            'id'        => 'home_widget_tab',
            'type'      => 'group',
            'title'     => '[Tab 内容模块] 内容设置',
            'before'    => $tip_ico.'警告：添加内容后请认真填写选项，选项不能留空，否则网站将爆炸。',
            'fields'    => array(
                array(
                    'id'    => 'title',
                    'type'  => 'text',
                    'title' => '名称（多语言）',
                ),
                array(
                    'id'         => 'type',
                    'type'       => 'button_set',
                    'title'      => '类型',
                    'options'    => array(
                        'favorites' => '网址',
                        'apps'      => 'App',
                        'books'     => '书籍',
                        'category'  => '文章',
                    ),
                    'class'      => 'home-widget-type compact min',
                    'default'    => 'favorites',
                ),
                array(
                    'id'          => 'cat',
                    'type'        => 'select',
                    'title'       => '选择分类',
                    'placeholder' => '选择一个类别',
                    'chosen'      => true,
                    'ajax'        => true,
                    'options'     => 'categories',
                    'query_args'  => array(
                        'taxonomy'  => 'favorites',
                    ),
                    'before'      => $tip_ico.'选择类型后输入<b>分类名称</b>关键字搜索分类',
                    'settings'    => array(
                        'min_length' => 2,
                        'width'      => '50%'
                    ),
                    'class'       => 'home-widget-cat compact min',
                ),
                array(
                    'id'        => 'order',
                    'type'      => 'radio',
                    'title'     => '排序',
                    'inline'    => true,
                    'options'   => array(
                        'ID'            => 'ID',
                        'modified'      => '修改日期',
                        'date'          => '创建日期',
                        'views'         => '查看次数',
                        'rand'          => '随机',
                    ),
                    'default'   => 'modified',
                    'class'     => 'compact min',
                ),
                array(
                    'id'      => 'num',
                    'type'    => 'spinner',
                    'title'   => '显示数量',
                    'step'    => 1,
                    'default' => 24,
                    'class'   => 'compact min',
                ),
                array(
                    'id'          => 'go',
                    'type'        => 'switcher',
                    'title'       => '直达',
                    'label'       => '直达目标网站',
                    'class'       => 'compact min',
                    'dependency'  => array( 'type', '==', 'favorites' ),
                ),
                array(
                    'id'      => 'ico',
                    'type'    => 'icon',
                    'title'   => '图标',
                    'default' => 'io io-bianqian',
                    'class'   => 'compact min',
                ),
            ),
            'button_title' => '添加内容'
        ),
        array(
            'id'        => 'home_widget_swiper',
            'type'      => 'group',
            'title'     => '[Big 轮播模块] 内容设置',
            'before'    => $tip_ico.'警告：添加内容后请认真填写选项，选项不能留空，否则网站将爆炸。',
            'fields'    => array(
                array(
                    'id'    => 'title',
                    'type'  => 'text',
                    'title' => '名称（多语言）',
                ),
                array(
                    'id'    => 'info',
                    'type'  => 'text',
                    'title' => '简介（多语言）',
                    'class' => 'compact min',
                ),
                array(
                    'id'        => 'img',
                    'type'      => 'upload',
                    'title'     => __('图片','io_setting'),
                    'after'     => $tip_ico.'图片尺寸推荐 21:9',
                    'class'     => 'compact min',
                ),
                array(
                    'id'         => 'type',
                    'type'       => 'button_set',
                    'title'      => '类型',
                    'options'    => array(
                        'favorites' => '网址',
                        'apps'      => 'App',
                        'books'     => '书籍',
                        'category'  => '文章',
                        'img'       => '图片链接',
                    ),
                    'class'      => 'home-widget-type compact min',
                    'default'    => 'favorites',
                ),
                array(
                    'id'          => 'cat',
                    'type'        => 'select',
                    'title'       => '选择系列',
                    'placeholder' => '选择一个系列',
                    'chosen'      => true,
                    'ajax'        => true,
                    'options'     => 'categories',
                    'query_args'  => array(
                        'taxonomy'  => 'favorites',
                    ),
                    'before'      => $tip_ico.'选择类型后输入<b>系列名称</b>关键字搜索系列',
                    'settings'    => array(
                        'min_length' => 2,
                        'width'      => '50%'
                    ),
                    'class'       => 'home-widget-cat compact min',
                    'dependency'  => array( 'type', '!=', 'img' ),
                ),
                array(
                    'id'        => 'order',
                    'type'      => 'radio',
                    'title'     => '排序',
                    'inline'    => true,
                    'options'   => array(
                        'ID'            => 'ID',
                        'modified'      => '修改日期',
                        'date'          => '创建日期',
                        'views'         => '查看次数',
                    ),
                    'default'   => 'modified',
                    'class'     => 'compact min',
                    'dependency'  => array( 'type', '!=', 'img' ),
                ),
                array(
                    'id'      => 'num',
                    'type'    => 'spinner',
                    'title'   => '显示数量',
                    'step'    => 1,
                    'default' => 10,
                    'class'   => 'compact min',
                    'dependency'  => array( 'type', '!=', 'img' ),
                ),
                array(
                    'id'          => 'go',
                    'type'        => 'switcher',
                    'title'       => '直达',
                    'label'       => '直达目标网站',
                    'class'       => 'compact min',
                    'dependency'  => array( 'type', '==', 'favorites' ),
                ),
                array(
                    'id'    => 'url',
                    'type'  => 'text',
                    'title' => 'Url',
                    'class'       => 'compact min',
                    'dependency'  => array( 'type', '==', 'img' ),
                ),
                array(
                    'id'          => 'is_ad',
                    'type'        => 'switcher',
                    'title'       => '是广告',
                    'label'       => '注意：广告将直达目标URL,不会添加跳转和nofollow',
                    'class'       => 'compact min',
                    'dependency'  => array( 'type', '==', 'img' ),
                )
            ),
            'button_title' => '添加内容'
        ),
        array(
            'id'         => 'article_n',
            'type'       => 'spinner',
            'title'      => '[文章轮播模块] 内容设置',
            'subtitle'   => __('幻灯片数量','io_setting'),
            'max'        => 10,
            'min'        => 1,
            'step'       => 1,
            'default'    => 5,
            'after'      => '显示置顶的文章，请把需要显示的文章置顶。<br>'.$tip_ico.'可在<b>[轮播&广告]</b>中添加广告卡片。', 
        ),
        array(
            'id'          => 'two_article',
            'type'        => 'text',
            'title'       => ' ',
            'subtitle'    => __('两篇文章','io_setting'),
            'after'       => __('自定义文章模块中间的两篇文章，留空则随机展示。<br>填写两个文章id，用英语逗号分开，如：11,100','io_setting'),
            'class'       => 'compact', 
        ),
        array(
            'id'          => 'article_not_in',
            'type'        => 'text',
            'title'       => ' ',
            'subtitle'    => __('资讯列表排除分类','io_setting'),
            'after'       => __('填写分类id，用英语逗号分开，如：11,100<br>文章分类id列表：','io_setting').get_cats_id(),
            'class'       => 'compact', 
        ),   
    )
));
//
// 首页设置-首页内容
//
CSF::createSection( $prefix, array(
    'parent'      => 'home_setting',
    'title'       => __('首页内容','io_setting'),
    'icon'        => 'fa fa-home', 
    'fields'   => array(  
        array(
            'id'      => 'show_bulletin',
            'type'    => 'switcher',
            'title'   => __('启用公告','io_setting'),
            'label'   => __('启用自定义文章类型“公告”，启用后刷新页面','io_setting'),
            'default' => true,
        ),
        array(
            'id'         => 'bulletin',
            'type'       => 'switcher',
            'title'      => __('显示公告','io_setting'),
            'label'      => __('在首页顶部显示公告','io_setting'),
            'default'    => true,
            'class'      => 'compact',
            'dependency' => array( 'show_bulletin', '==', true )
        ),
        array(
            'id'         => 'bulletin_n',
            'type'       => 'spinner',
            'title'      => __('公告数量','io_setting'),
            'after'      => __('需要显示的公告篇数','io_setting'),
            'max'        => 10,
            'min'        => 1,
            'step'       => 1,
            'default'    => 2,
            'class'      => 'compact',
            'dependency' => array( 'bulletin|show_bulletin', '==|==', 'true|true' )
        ),
        //array(
        //    'id'             => 'all_bull',
        //    'type'           => 'select',
        //    'title'          => __('公告归档页','io_setting'),
        //    'after'           => __(' 如果没有，新建页面，选择“所有公告”模板并保存。','io_setting'),
        //    'options'        => 'pages',
        //    'query_args'     => array(
        //        'posts_per_page'  => -1,
        //    ),
        //    'class'          => 'compact',
        //    'placeholder'    => __('选择公告归档页面', 'io_setting'),
        //    'dependency'     => array( 'bulletin|show_bulletin', '==|==', 'true|true' )
        //),
        array(
            'id'        => 'bull_img',
            'type'      => 'upload',
            'title'     => '公告页头部图片',
            'class'     => 'compact',
            'default'   => '//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/banner/banner015.jpg',
        ),
        array(
            'id'         => 'customize_card',
            'type'       => 'switcher',
            'title'      => __('自定义网址（我的导航）','io_setting'),
            'label'      => __('显示游客自定义网址模块，允许游客自己添加网址和记录最近点击，数据保存于游客电脑。','io_setting'),
            'default'    => false,
        ),
        array(
            'id'         => 'customize_d_n',
            'type'       => 'text',
            'title'      => __('预设网址（每日推荐）','io_setting'),
            'class'      => 'compact min',
            'after'      => __('自定义网址模块添加预设网址，显示位置：<br>1、首页“我的导航”模块预设网址<br>2、“mini 书签页”快速导航列表<br><br>例：1,22,33,44 用英语逗号分开（填文章ID）','io_setting'), 
        ),
        array(
            'id'         => 'customize_show',
            'type'       => 'switcher',
            'title'      => __('始终显示[预设网址（每日推荐）]','io_setting'),
            'label'      => __('开启用户中心后仍然显示预设网址','io_setting'), 
            'default'    => true,
            'class'      => 'compact min',
            'dependency' => array( 'customize_card', '==', true )
        ),
        array(
            'id'         => 'customize_count',
            'type'       => 'spinner',
            'title'      => __('最多分类','io_setting'),
            'after'      => __('最多显示多少用户自定义网址分类，0 为全部显示','io_setting'), 
            'step'       => 1,
            'default'    => 8,
            'class'      => 'compact min',
            'dependency' => array( 'customize_card', '==', true )
        ),
        array(
            'id'         => 'customize_n',
            'type'       => 'spinner',
            'title'      => __('最近点击','io_setting'),
            'after'      => __('最近点击网址记录的最大数量','io_setting'),
            'max'        => 50,
            'min'        => 1,
            'step'       => 1,
            'default'    => 10,
            'class'      => 'compact min',
            'dependency' => array( 'customize_card', '==', true )
        ),
        array(
            'id'         => 'hot_card',
            'type'       => 'switcher',
            'title'      => __('首页热门网址','io_setting'),
            'label'      => __('首页显示热门网址模块，需开启访问统计，并产生了访问和点赞数据','io_setting'),
            'default'    => false,
        ),
        array(
            'id'        => 'home_hot_list',
            'type'      => 'group',
            'title'     => '热门内容',
            'fields'    => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '名称（多语言）',
                    'placeholder' => '热门网址'
                ),
                array(
                    'id'       => 'type',
                    'type'     => 'button_set',
                    'title'    => '类型',
                    'options'  => array(
                        'sites'    => '网址',
                        'app'      => 'App',
                        'book'     => '书籍',
                        'post'     => '文章',
                    ),
                    'class'    => 'compact min',
                    'default'  => 'sites',
                ),
                array(
                    'id'        => 'order',
                    'type'      => 'radio',
                    'title'     => '规则',
                    'inline'    => true,
                    'options'   => $hot_list_order,
                    'default'   => 'date',
                    'class'     => 'compact min',
                ),
                array(
                    'id'      => 'num',
                    'type'    => 'spinner',
                    'title'   => '显示数量',
                    'step'    => 1,
                    'default' => 10,
                    'class'   => 'compact min',
                ),
                array(
                    'id'         => 'mini',
                    'type'       => 'switcher',
                    'title'      => 'mini网址块',
                    'class'      => 'compact min',
                    'default'    => false,
                    'dependency'  => array( 'type', '==', 'sites' ),
                ),
                array(
                    'type'    => 'submessage',
                    'style'   => 'danger',
                    'content' => $tip_ico.'警告：<b>下载最多</b>只能用于 <b>App</b> ！！！ ☚ ☚ ☚ ☚',
                    'dependency'  => array( 'order|type', '==|!=', '_down_count|apps' ),
                ),
            ),
            'button_title' => '添加项目',
            'class'        => 'compact',
            'dependency'   => array( 'hot_card', '==', true ),
            'default'      => array(
                array(
                    'title'  => '热门网址',
                    'type'   => 'sites',
                    'order'  => 'views',
                    'num'    => 10,
                    'mini'   => false,
                ),
                array(
                    'title'  => '最新网址',
                    'type'   => 'sites',
                    'order'  => 'date',
                    'num'    => 10,
                    'mini'   => false,
                ),
            ),
            'after'        => '热门网址,大家喜欢,最新网址,热门 App,最爱 App,最新 App,下载最多 App,热门书籍,最爱书籍,最新书籍'
        ),
    )
));

//
// 搜索设置
//
CSF::createSection( $prefix, array(
    'title'       => __('搜索设置','io_setting'),
    'icon'        => 'fas fa-search', 
    'fields'   => array(   
        array(
            'id'      => 'search_position',
            'type'    => 'checkbox',
            'title'   => __('搜索位置','io_setting'),
            'inline'  => true,
            'options' => array(
                'home'      => __('默认位置','io_setting'),
                'top'       => __('头部','io_setting'),
                'tool'      => __('页脚小工具','io_setting'),
            ), 
            'default' => array('home'),
            'after'   => __('默认位置在首页内容前面和分类内容前面显示搜索框','io_setting'),
        ),
        array(
            'id'         => 'baidu_hot_words',
            'type'       => 'radio',
            'title'      => __('搜索词补全','io_setting'),
            'default'    => 'baidu',
            'inline'     => true,
            'options'    => array(
                'null'    => '无',
                'baidu'   => '百度',
                'google'  => 'Google',
            ),
            'after'      => '选择搜索框词补全源，选无则不补全。',
        ),
        array(
            'id'        => 'search_skin',
            'type'      => 'fieldset',
            'title'     => __('首页顶部搜索设置','io_setting'),
            'fields'    => array(
                array(
                    'id'      => 'search_big',
                    'type'    => "image_select",
                    'title'   => '搜索布局样式',
                    'options' => array(
                        'def'     => get_theme_file_uri('/images/option/op_search_layout_def.png'),
                        '1'    => get_theme_file_uri('/images/option/op_search_layout_big.png'),
                    ),
                    'default' => 'def',
                ),
                array(
                    'id'         => 'search_station',
                    'type'       => 'switcher',
                    'title'      => __('前置站内搜索','io_setting'),
                    'label'      => __('开头显示站内搜索，关闭将不显示搜索推荐','io_setting'),
                    'default'    => true,
                    'class'      => 'compact min',
                    'dependency' => array( 'search_big', '==', true )
                ),
                array(
                    'id'         => 'big_title',
                    'type'       => 'text',
                    'title'      => '大字标题（多语言）',
                    'after'      => $tip_ico.__('留空不显示，支持 html','io_setting'), 
                    'class'      => 'compact min',
                    'dependency' => array( 'search_big', '==', true )
                ),
                array(
                    'id'         => 'changed_bg',
                    'type'       => 'switcher',
                    'title'      => __('暗色主题压暗背景','io_setting'),
                    'label'      => __('切换到暗色主题时自动压暗背景','io_setting'),
                    'default'    => true,
                    'class'      => 'compact min',
                    'dependency' => array( 'search_big', '==', true )
                ),
                array(
                    'id'      => 'big_skin',
                    'type'    => 'radio',
                    'title'   => __('背景模式','io_setting'),
                    'default' => 'css-color',
                    'inline'  => true,
                    'options' => array(
                        'no-bg'         => __('无背景','io_setting'),
                        'css-color'     => __('颜色','io_setting'),
                        'css-img'       => __('自定义图片','io_setting'),
                        'css-bing'      => __('bing 每日图片','io_setting'),
                        'canvas-fx'     => __('canvas 特效','io_setting'),
                    ),
                    'class'      => 'compact min',
                    'dependency' => array( 'search_big', '==', true )
                ),
                array(
                    'id'        => 'search_color',
                    'type'      => 'color_group',
                    'title'     => '背景颜色',
                    'options'   => array(
                        'color-1' => 'Color 1',
                        'color-2' => 'Color 2',
                        'color-3' => 'Color 3',
                    ),
                    'default'   => array(
                        'color-1' => '#ff3a2b',
                        'color-2' => '#ed17de',
                        'color-3' => '#f4275e',
                    ),
                    'class'      => 'compact min',
                    'dependency' => array( 'search_big|big_skin', '==|==', 'true|css-color' )
                ),
                array(
                    'id'        => 'search_img',
                    'type'      => 'upload',
                    'title'     => '背景图片',
                    'add_title' => __('上传','io_setting'),
                    'class'      => 'compact min',
                    'dependency' => array( 'search_big|big_skin', '==|==', 'true|css-img' )
                ),
                array(
                    'id'      => 'canvas_id',
                    'type'    => 'radio',
                    'title'   => __('canvas 样式','io_setting'),
                    'default' => '0',
                    'inline'  => true,
                    'options' => get_all_fx_bg(),
                    'class'      => 'compact min',
                    'dependency' => array( 'search_big|big_skin', '==|==', 'true|canvas-fx' )
                ),
                array(
                    'id'         => 'custom_canvas',
                    'type'       => 'text',
                    'title'      => __('canvas地址','io_setting'),
                    'after'      => __('留空会爆炸，既然选择了，请不要留空！！！<br>示例：//owen0o0.github.io/ioStaticResources/canvas/01.html<br>注意：可能会有跨域问题，解决方法百度。','io_setting'), 
                    'default'    => '//owen0o0.github.io/ioStaticResources/canvas/01.html',
                    'attributes' => array(
                        'style'    => 'width: 100%'
                    ),
                    'class'      => 'compact min',
                    'dependency' => array( 'search_big|canvas_id|big_skin', '==|==|==', 'true|custom|canvas-fx' )
                ),
                array(
                    'id'         => 'bg_gradual',
                    'type'       => 'switcher',
                    'title'      => __('背景渐变','io_setting'),
                    'default'    => false,
                    'class'      => 'compact min',
                    'dependency' => array( 'search_big|big_skin', '==|!=', 'true|no-bg' )
                ),
                array(
                    'id'         => 'post_top',
                    'type'       => 'switcher',
                    'title'      => __('文章轮播上移','io_setting'),
                    'default'    => false,
                    'class'      => 'compact min',
                    'dependency' => array( 'search_big', '==', true )
                ),
            ),
            'dependency' => array( 'search_position', 'any', 'home' )
        ),
        array(
            'id'      => 'custom_search',
            'type'    => 'switcher',
            'title'   => __('自定义搜索列表','io_setting'),
            'label'   => __('启用后 先保存设置 再 刷新页面 最后点 前往配置','io_setting'),
            'desc'    => $set_search,
            'default' => false,
        ), 
        array(
            'id'           => 'search_page_post',
            'type'         => 'sorter',
            'title'        => '搜索页选项卡',
            'default'      => array(
                'enabled'  => array(
                    'sites' => '网站',
                    'post'  => '文章',
                    'app'   => '软件',
                    'book'  => '书籍',
                ),
                'disabled'   => array(),
            ),
            'before'      => __('拖动排序，不需要的拖到“禁用”。注：启用必须保留一个','io_setting'),
        ),
    )
));
//
// 统计浏览
//
CSF::createSection( $prefix, array(
    'title'    => __('统计浏览','io_setting'),
    'icon'     => 'fa fa-eye',
    'fields'   => array(  
        array(
            'id'      => 'post_views',
            'type'    => 'switcher',
            'title'   => __('访问统计','io_setting'),
            'label'   => __('启用前先禁用WP-PostViews插件，因为功能重叠','io_setting'),
            'default' => true,
        ),
        array(
            'type'    => 'notice',
            'style'   => 'danger',
            'content' => '注意：关闭“访问统计”后，以下功能会受影响！',
            'dependency' => array( 'post_views', '==', false )
        ),
        array(
            'id'      => 'views_n',
            'type'    => 'text',
            'title'   => __('访问基数','io_setting'),
            'after'   => __('随机访问基数，取值范围：(0~10)*访问基数<br>设置大于0的整数启用，会导致访问统计虚假，酌情开启，关闭请填0','io_setting'),
            'default' => 0,
            'class'      => 'compact min',
            'dependency' => array( 'post_views', '==', true )
        ),
        array(
            'id'      => 'views_r',
            'type'    => 'text',
            'title'   => __('访问随机计数','io_setting'),
            'after'   => __('访问一次随机增加访问次数，比如访问一次，增加5次<br>取值范围：(1~10)*访问随机数<br>设置大于0的数字启用，可以是小数，如：0.5，但小于0.5会导致取0值<br>会导致访问统计虚假，酌情开启，关闭请填0','io_setting'),
            'default' => 0,
            'class'      => 'compact min',
            'dependency' => array( 'post_views', '==', true )
        ),
        array(
            'id'      => 'like_n',
            'type'    => 'text',
            'title'   => __('点赞基数','io_setting'),
            'after'   => __('随机点赞基数，取值范围：(0~10)*点赞基数<br>设置大于0的整数启用，会导致点赞统计虚假，酌情开启，关闭请填0','io_setting'),
            'default' => 0,
            'dependency' => array( 'user_center', '==', false,'all' )
        ),
        array(
            'id'      => 'leader_board',
            'type'    => 'switcher',
            'title'   => __('按天记录统计数据','io_setting'),
            'default' => true,
        ),
        array(
            'id'      => 'details_chart',
            'type'    => 'switcher',
            'title'   => __('网址详情页显示统计图表','io_setting'),
            'class'   => 'compact min',
            'default' => true,
            'dependency' => array( 'leader_board', '==', true )
        ),
        array(
            'id'         => 'how_long',
            'type'       => 'spinner',
            'title'      => __('统计数据保留天数','io_setting'),
            'after'      => __('最少30天','io_setting'),
            'unit'       => '天',
            'step'       => 1,
            'default'    => 30,
            'class'      => 'compact min',
            'dependency' => array( 'leader_board', '==', true )
        ),
        array(
            'id'        => 'views_options',
            'type'      => 'fieldset',
            'title'     => __('浏览计数设置','io_setting'),
            'fields'    => array(
                array(
                    'id'          => 'count',
                    'type'        => 'select',
                    'title'       => __( '计数来源', 'io_setting' ),
                    'options'     => array(
                        '0'  => __( '所有人', 'io_setting' ),
                        '1'  => __( '只有访客', 'io_setting' ),
                        '2'  => __( '只有注册用户', 'io_setting' ),
                    ),
                ),
                array(
                    'id'      => 'exclude_bots',
                    'type'    => 'switcher',
                    'title'   => __('排除机器人(爬虫等)','io_setting'),
                ),
                array(
                    'id'          => 'template',
                    'type'        => 'select',
                    'title'       => __( '显示模板', 'io_setting' ),
                    'options'     => array(
                        '0'  => __( '正常显示计数', 'io_setting' ),
                        '1'  => __( '以千单位显示', 'io_setting' ),
                    ),
                ),
                array(
                    'id'      => 'use_ajax',
                    'type'    => 'switcher',
                    'title'   => __('使用Ajax更新浏览次数','io_setting'),
                    'class'   => $views_use_ajax,
                    'label'      => '如果启用了静态缓存，将使用AJAX更新浏览计数，且“随机计数”失效。',
                ),
            ),
            'default'        => array(
                'count'         => '0',
                'exclude_bots'  => true,
                'template'      => '0',
                'use_ajax'      => true,
            ),
            'dependency' => array( 'post_views', '==', true )
        ),
    )
));
//
// 内容设置
//
CSF::createSection( $prefix, array(
    'id'    => 'srticle_settings',
    'title' => __('内容设置','io_setting'),
    'icon'  => 'fa fa-file-text',
));
//
// 内容设置-文章博客
//
CSF::createSection( $prefix, array(
    'parent'   => 'srticle_settings',
    'title'    => __('文章博客','io_setting'),
    'icon'     => 'fas fa-newspaper',
    'fields'   => array(  
        array(
            'id'        => 'post_card_mode',
            'type'      => 'image_select',
            'title'     => __('首页文章卡片样式','io_setting'),
            'options'   => array(
                'card'    => get_theme_file_uri('/images/option/op-app-c-card.png'),
                'default' => get_theme_file_uri('/images/option/op-post-c-def.png'),
            ),
            'after'     => $tip_ico.'分类设置中的样式优先级最高，如发现此设置无效，请检查分类设置。',
            'default'   => 'default',
        ),
        array(
            'id'          => 'blog_index_cat',
            'type'        => 'text',
            'title'       => '博客页分类筛选',
            'after'       => '填写分类id，用英语逗号分开，如：11,100<br><br>文章分类id列表：'.get_cats_id(),
            'class'       => '',
        ), 
        array(
            'id'        => 'post_copyright_multi',
            'type'      => 'group',
            'title'     => '版权提示内容',
            'fields'    => array(
                array(
                    'id'    => 'language',
                    'type'  => 'text',
                    'title' => '语言缩写',
                    'after' => '如：zh  en ，<a href="https://zh.wikipedia.org/wiki/ISO_639-1" target="_blank">各国语言缩写参考</a>'
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'textarea',
                    'title'      => '内容',
                    'desc'       => '支持HTML代码，请注意代码规范及标签闭合',
                    'attributes' => array(
                        'rows' => 2,
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact min',
                ),
            ),
            'before'       => '需在基础设置开启多语言(默认语言放第一个)',
            'button_title' => '添加语言',
            'accordion_title_prefix' => '语言：',
            'default'   => array(
                array(
                    'language' => 'zh',
                    'content'  => '文章版权归作者所有，未经允许请勿转载。',
                ),
                array(
                    'language' => 'en',
                    'content'  => 'The copyright of the article belongs to the author, please do not reprint without permission.',
                ),
            ),
        ),
        array(
            'id'      => 'post_related',
            'type'    => 'switcher',
            'title'   => __('相关文章','io_setting'),
            'default' => true,
        ),
    )
));
//
// 内容设置-网址设置
//
CSF::createSection( $prefix, array(
    'parent'   => 'srticle_settings',
    'title'    => __('网址设置','io_setting'),
    'icon'     => 'fa fa-sitemap',
    'fields'   => array(  
        array(
            'id'        => 'site_card_mode',
            'type'      => 'image_select',
            'title'     => __('首页网址卡片样式','io_setting'),
            'options'   => array(
                'max'     => get_theme_file_uri('/images/option/op-site-c-max.png'),
                'default' => get_theme_file_uri('/images/option/op-site-c-def.png'),
                'min'     => get_theme_file_uri('/images/option/op-site-c-min.png'),
            ),
            'default'   => 'default',
            'after'   => '选择首页网址块显示风格：大、中、小<br>'.$tip_ico.'分类设置中的样式优先级最高，如发现此设置无效，请检查分类设置。',
        ),
        array(
            'id'      => 'two_columns',
            'type'    => 'switcher',
            'title'   => __('小屏显示两列','io_setting'),
            'label'   => __('手机等小屏幕显示两列。不支持[大]号卡片样式','io_setting'),
            'default' => false,
            'dependency' => array( 'site_card_mode', '!=', 'max' )
        ),
        array(
            'id'        => 'site_archive_n',
            'type'      => 'number',
            'title'     => __('网址分类页显示数量','io_setting'),
            'default'   => 30,
            'after'     => '填写需要显示的数量。填写 0 为根据<a href="'.home_url().'/wp-admin/options-reading.php">系统设置数量显示</a>',
        ),
        array(
            'id'      => 'sites_archive_order',
            'type'    => 'switcher',
            'title'   => __('网址分类页"失效链接"排最后','io_setting'),
            'class'   => 'compact min new',
            'default' => true,
        ),
        array(
            'id'      => 'sites_sortable',
            'type'    => 'switcher',
            'title'   => __('网址拖拽排序','io_setting'),
            'label'   => __('在后台网址列表使用拖拽排序,请同时选择“首页网址分类排序”为“自定义排序字段”','io_setting'),
            'desc'    => __('如果想继续使用老版的排序字段，请关闭此功能','io_setting'),
            'class'   => '',
            'default' => true,
        ),
        array(
            'id'         => 'no_ico',
            'type'       => 'switcher',
            'title'      => __('无图标模式','io_setting'),
            'default'    => false,
        ),
        array(
            'id'         => 'is_letter_ico',
            'type'       => 'switcher',
            'title'      => __('首字图标','io_setting'),
            'label'      => '未手动上传图标的网址使用首字图标',
            'default'    => false,
            'dependency' => array( 'no_ico', '==', false )
        ),
        array(
            'id'         => 'first_api_ico',
            'type'       => 'switcher',
            'title'      => __('优先 api 图标','io_setting'),
            'label'      => '如果 api 图标获取失败，则使用首字图标',
            'default'    => false,
            'class'      => 'compact',
            'dependency' => array( 'no_ico|is_letter_ico', '==|==', 'false|true' )
        ),
        array(
            'id'      => 'letter_ico_s',
            'type'    => 'slider',
            'title'   => '首字图标饱和度',
            'min'     => 0,
            'max'     => 100,
            'step'    => 1,
            'unit'    => '%',
            'default' => 40,
            'class'   => 'compact',
            'dependency' => array( 'no_ico|is_letter_ico|first_api_ico', '==|==|==', 'false|true|false' )
        ),
        array(
            'id'      => 'letter_ico_b',
            'type'    => 'slider',
            'title'   => '首字图标亮度',
            'min'     => 0,
            'max'     => 100,
            'step'    => 1,
            'unit'    => '%',
            'default' => 90,
            'class'   => 'compact',
            'dependency' => array( 'no_ico|is_letter_ico|first_api_ico', '==|==|==', 'false|true|false' )
        ),
        array(
            'id'         => 'report_button',
            'type'       => 'switcher',
            'title'      => '举报反馈按钮',
            'label'      => '在详情页显示举报反馈按钮',
            'class'      => '',
            'default'    => true,
        ),
        array(
            'id'         => 'server_link_check',
            'type'       => 'switcher',
            'title'      => '自动检查网址状态',
            'label'      => '服务器定时检查网址状态（1小时一次），会占用部分服务器资源，',
            'default'    => false,
        ),
        //array(  选项无效？？？？？？？？？
        //    'id'        => 'check_rate',
        //    'type'      => 'radio',
        //    'title'     => '自动检查频率',
        //    'inline'    => true,
        //    'options'   => array(
        //        '10min'         => '10分钟一次',
        //        'hourly'        => '一小时一次',
        //        'twicedaily'    => '每天两次',
        //        'daily'         => '每天一次',
        //    ),
        //    'default'   => "hourly",
        //    'class'     => 'compact',
        //    'dependency' => array( 'server_link_check', '==', true )
        //),
        array(
            'id'      => 'link_check_options',
            'type'    => 'fieldset',
            'title'   => __('自动检查设置','io_setting'),
            'fields'  => array(
                array(
                    'id'      => 'check_threshold',
                    'type'    => 'number',
                    'title'   => '检查频率',
                    'unit'    => '小时',
                    'after'    => '设置单个链接每隔多久检查一次，新增链接将立即检测。',
                ),
                array(
                    'id'      => 'timeout',
                    'type'    => 'number',
                    'title'   => '超时',
                    'unit'    => '秒',
                    'after'   => '若链接在检测时超时多久被视为失效。',
                    'class'   => 'compact min',
                ),
                array(
                    'id'      => 'max_execution_time',
                    'type'    => 'number',
                    'title'   => '最大执行时间',
                    'unit'    => '秒',
                    'after'   => '设置后台每次检查最多可以运行时长。',
                    'class'   => 'compact min',
                ),
                array(
                    'id'      => 'server_load_limit',
                    'type'    => 'number',
                    'title'   => '服务器负载限制',
                    'after'   => $is_server_load ? '当前负载：<span id="io_current_load" data-url="'.esc_url(admin_url('admin-ajax.php', 'relative')).'">0.00</span><br><br>
                                    当平均服务器负载超过此值时，链接检查将会停止。此栏填0以关闭负载限制。<br>'.$tip_ico.'如果不懂意思，请保持默认，默认值：4 。' :
                                    '当前服务器不支持负载限制设置！（一般只支持 Linux 系统）',
                    'class'   => ($is_server_load?'':'disabled').' compact min',
                ),
                array(
                    'id'      => 'exclusion_list',
                    'type'    => 'textarea',
                    'title'   => __('排除列表','io_setting'),  
                    'before'  => '不检测含有以下关键字的链接（每个关键字用<b>英语逗号</b>分隔，不能有空格）',
                    'class'   => 'compact min',
                ),
            ),
            'default'    => array(
                'check_threshold'       => 72,
                'timeout'               => 30,
                'max_execution_time'    => 420,
                'server_load_limit'     => 4,
                'exclusion_list'        => 'baidu.com,google.com,iowen.cn,iotheme.cn',
            ),
            'class'   => 'compact',
            'dependency' => array( 'server_link_check', '==', true )
        ),
        array(
            'id'      => 'details_page',
            'type'    => 'switcher',
            'title'   => __('详情页','io_setting'),
            'subtitle'=> __('启用网址详情页','io_setting'),
            'label'   => __('关闭状态为网址块直接跳转到目标网址。','io_setting'),
            'desc'    => __('<strong>“公众号”</strong>和<strong>“下载资源”</strong>默认开启详情页，不受此选项限制。','io_setting'),
            'default' => false,
        ),
        array(
            'id'      => 'url_rank',
            'type'    => 'switcher',
            'title'   => __('网址权重','io_setting'),
            'label'   => __('详情页显示网址权重','io_setting'),
            'default' => true, 
            'class'   => 'compact min',
            'dependency' => array( 'details_page', '==', true )
        ),
        array(
            'id'      => 'sites_preview',
            'type'    => 'switcher',
            'title'   => __('网址预览','io_setting'),
            'label'   => __('显示目标网址预览，如api服务失效，请关闭。','io_setting'),
            'default' => false,
            'class'   => 'compact min',
            'dependency' => array( 'details_page', '==', true )
        ),
        array(
            'id'      => 'togo',
            'type'    => 'switcher',
            'title'   => __('【网址块】直达按钮','io_setting'),
            'label'   => __('【网址块】显示直达按钮','io_setting'),
            'default' => true,
            'class'   => 'compact min',
            'dependency' => array( 'details_page', '==', true )
        ),
        array(
            'id'        => 'url_reverse',
            'type'      => 'switcher',
            'title'     => __('【网址块】“直达”和“详情页” url 颠倒','io_setting'),
            'label'     => __('没事不要开启','io_setting'),
            'class'     => 'compact min',
            'dependency'  => array( 'details_page', '==', true ),
        ),
        array(
            'id'          => 'sites_default_content',
            'type'        => 'switcher',
            'title'       => __('网址详情页“数据评估”开关','io_setting'),
            'label'       => __('内容可在主题文件夹里的 inc\functions\io-single-site.php get_data_evaluation()方法里修改，或者在子主题中重写此方法。','io_setting'),
            'class'        => 'compact min',
            'dependency'  => array( 'details_page', '==', true ),
        ),
        array(
            'id'      => 'mobile_view_btn',
            'type'    => 'switcher',
            'title'   => __('手机查看按钮','io_setting'),
            'default' => true,
            'class'   => 'compact min',
            'dependency' => array( 'details_page', '==', true )
        ),
        array(
            'id'        => 'sites_columns',
            'type'      => 'tabbed',
            'title'     => '网址列数',
            'subtitle'  => __('网址块列表一行显示的个数','io_setting'),
            'tabs'      => array(
                array(
                    'title'     => '小屏幕（≥576px）',
                    'icon'      => 'fas fa-mobile-alt',
                    'fields'    => array(
                        array(
                            'id'      => 'sm',
                            'type'    => 'number',
                            'title'   => '数量',
                            'unit'    => '个',
                            'default' => 2,
                        ),
                        array(
                            'type'    => 'submessage',
                            'style'   => 'success',
                            'content' => $tip_ico.'注意：有效值范围只有<b>1-10</b>',
                        ),
                    )
                ),
                array(
                    'title'     => '中等屏幕（≥768px）',
                    'icon'      => 'fas fa-mobile-alt',
                    'fields'    => array(
                        array(
                            'id'      => 'md',
                            'type'    => 'number',
                            'title'   => '数量',
                            'unit'    => '个',
                            'default' => 2,
                        ),
                        array(
                            'type'    => 'submessage',
                            'style'   => 'success',
                            'content' => $tip_ico.'注意：有效值范围只有<b>1-10</b>',
                        ),
                    )
                ),
                array(
                    'title'     => '大屏幕（≥992px）',
                    'icon'      => 'fas fa-tv',
                    'fields'    => array(
                        array(
                            'id'      => 'lg',
                            'type'    => 'number',
                            'title'   => '数量',
                            'unit'    => '个',
                            'default' => 3,
                        ),
                        array(
                            'type'    => 'submessage',
                            'style'   => 'success',
                            'content' => $tip_ico.'注意：有效值范围只有<b>1-10</b>',
                        ),
                    )
                ),
                array(
                    'title'     => '加大屏幕（≥1200px）',
                    'icon'      => 'fas fa-tv',
                    'fields'    => array(
                        array(
                            'id'      => 'xl',
                            'type'    => 'number',
                            'title'   => '数量',
                            'unit'    => '个',
                            'default' => 5,
                        ),
                        array(
                            'type'    => 'submessage',
                            'style'   => 'success',
                            'content' => $tip_ico.'注意：有效值范围只有<b>1-10</b>',
                        ),
                    )
                ),
                array(
                    'title'     => '加加大屏幕（≥1400px）',
                    'icon'      => 'fas fa-tv',
                    'fields'    => array(
                        array(
                            'id'      => 'xxl',
                            'type'    => 'number',
                            'title'   => '数量',
                            'unit'    => '个',
                            'default' => 6,
                        ),
                        array(
                            'type'    => 'submessage',
                            'style'   => 'success',
                            'content' => $tip_ico.'注意：有效值范围只有<b>1-10</b>',
                        ),
                    )
                ),
            ),
            'after'     => $tip_ico.'注意：如果内容没有根据此设置变化，请检查对应分类的设置。',
        ),
        array(
            'id'      => 'sites_related',
            'type'    => 'switcher',
            'title'   => __('相关网址','io_setting'),
            'default' => true,
        ),
    )
));
//
// 内容设置-app设置
//
CSF::createSection( $prefix, array(
    'parent'   => 'srticle_settings',
    'title'    => __('app设置','io_setting'),
    'icon'     => 'fa fa-shopping-bag',
    'fields'   => array( 
        array(
            'id'        => 'app_card_mode',
            'type'      => 'image_select',
            'title'     => __('首页 app 卡片样式','io_setting'),
            'options'   => array(
                'card'    => get_theme_file_uri('/images/option/op-app-c-card.png'),
                'default' => get_theme_file_uri('/images/option/op-app-c-def.png'),
            ),
            'default'   => 'default',
            'after'   => '选择首页app块显示风格<br>'.$tip_ico.'分类设置中的样式优先级最高，如发现此设置无效，请检查分类设置。',
        ), 
        array(
            'id'        => 'app_archive_n',
            'type'      => 'number',
            'title'     => __('App 分类页显示数量','io_setting'),
            'default'   => 30,
            'after'     => '填写需要显示的数量。<br>填写 0 为根据<a href="'.home_url().'/wp-admin/options-reading.php">系统设置数量显示</a>',
        ),
        array(
            'id'          => 'is_app_down_nogo',
            'type'        => 'switcher',
            'title'       => __('下载地址禁止GO跳转','io_setting'),
            'label'       => '依赖 “seo设置”->“Go 跳转” 中的 “内链跳转(go跳转)”',
            'desc'        => $tip_ico.'可以通过go跳转白名单解决单个控制',
            'class'       => '',
            'dependency'  => array( 'is_go', '==', true, 'all', 'visible' ),
        ),
        //array(
        //    'id'        => 'default_app_screen',
        //    'type'      => 'upload',
        //    'title'     => __('app 默认截图','io_setting'),
        //    'add_title' => __('添加','io_setting'),
        //    'after'     => __('app截图为空时显示这项设置的内容','io_setting'),
        //    'default'   => get_theme_file_uri('/screenshot.jpg'),
        //),
        array(
            'id'      => 'app_related',
            'type'    => 'switcher',
            'title'   => __('相关app','io_setting'),
            'default' => true,
            'class'   => '',
        ),
    )
));
//
// 内容设置-书籍设置
//
CSF::createSection( $prefix, array(
    'parent'   => 'srticle_settings',
    'title'    => __('书籍设置','io_setting'),
    'icon'     => 'fa fa-book',
    'fields'   => array(  
        array(
            'id'        => 'book_archive_n',
            'type'      => 'number',
            'title'     => __('书籍分类页显示数量','io_setting'),
            'default'   => 20,
            'after'     => '填写需要显示的数量。<br>填写 0 为根据<a href="'.home_url().'/wp-admin/options-reading.php">系统设置数量显示</a>',
        ),
        array(
            'id'            => 'books_metadata',
            'type'          => 'group',
            'title'         => '书籍&影视元数据默认值',
            'fields'        => array(
                array(
                    'id'    => 'term',
                    'type'  => 'text',
                    'title' => '项目(控制在5个字内)',
                ),
                array(
                    'id'          => 'detail',
                    'type'        => 'text',
                    'title'       => '内容',
                    'placeholder' => __('如留空，请删除此项','io_setting'),
                ),
            ),
            'default' => array(
                array(
                    'term'    => '作者',
                ),
                array(
                    'term'    => '出版社',
                ),
                array(
                    'term'    => '发行日期',
                    'detail'  => date('Y-m',current_time( 'timestamp' )),
                ),
            ),
        ),
        array(
            'id'      => 'book_related',
            'type'    => 'switcher',
            'title'   => __('相关book','io_setting'),
            'default' => true,
            'class'   => '',
        ),
    )
));

//
// SEO设置
//
CSF::createSection( $prefix, array(
    'id'    => 'seo_settings',
    'title' => __('SEO设置','io_setting'),
    'icon'  => 'fa fa-paw',
));
//
// SEO-基础设置
//
CSF::createSection( $prefix, array(
    'parent'   => 'seo_settings',
    'title'       => __('基础设置','io_setting'),
    'icon'        => 'fab fa-slack',
    'description' => __('主题seo获取规则：<br>标题：页面、文章的标题<br>关键词：默认获取文章的标签，如果没有，则为标题加网址名称<br>描述：默认获取文章简介','io_setting'),
    'fields'      => array(
        array(
            'id'     => 'seo_home_keywords',
            'type'   => 'text',
            'title'  => __('首页关键词（多语言）','io_setting'),
            'after'  => __('其他页面如果获取不到关键词，默认调取此设置','io_setting'),
        ),
        array(
            'id'     => 'seo_home_desc',
            'type'   => 'textarea',
            'title'  => __('首页描述（多语言）','io_setting'),
            'after'  => __('其他页面如果获取不到描述，默认调取此设置','io_setting'),
        ),              
        array(
            'id'      => 'seo_linker',
            'type'    => 'text',
            'title'   => __('连接符','io_setting'),
            'after'   =>  __('一般用“-”“|”，如果需要左右留空，请自己左右留空格。','io_setting'),
            'default' => ' | ',
        ),
        array(
            'id'          => 'og_switcher',
            'type'        => 'switcher',
            'title'       => __('OG标签','io_setting'),
            'label'       => __('在头部显示OG标签','io_setting'),
            'default'     => true,
        ),
        array(
            'id'        => 'og_img',
            'type'      => 'upload',
            'title'     => __('og 标签默认图片','io_setting'),
            'add_title' => __('上传','io_setting'),
            'after'     => __('QQ、微信分享时显示的缩略图<br>主题会默认获取文章、网址等内容的图片，但是如果内容没有图片，则获取此设置','io_setting'),
            'default'   => get_theme_file_uri('/screenshot.jpg'),
            'class'     => 'compact',
            'dependency' => array( 'og_switcher', '==', 'true'),
        ),
        array(
            'id'        => 'tag_c',
            'type'      => 'fieldset',
            'title'     => __('关键词链接','io_setting'),
            'subtitle'  => '自动为文章中的关键词添加链接',
            'fields'    => array(
                array(
                    'id'        => 'switcher',
                    'type'      => 'switcher',
                    'title'     => __('开启','io_setting'),
                    'default'   => true,
                ),
                array(
                    'id'        => 'tags',
                    'type'      => 'group',
                    'title'     => '自定义关键词',
                    'fields'    => array(
                        array(
                            'id'        => 'tag',
                            'type'      => 'text',
                            'title'     => '关键词',
                        ),
                        array(
                            'id'        => 'url',
                            'type'      => 'text',
                            'title'     => '链接地址'
                        ),
                        array(
                            'id'        => 'describe',
                            'type'      => 'text',
                            'title'     => '描述',
                            'after'     => '为链接title属性',
                        ),
                    ),
                    'dependency' => array( 'switcher', '==', 'true', '', 'visible' ),
                ),
                array(
                    'id'         => 'auto',
                    'type'       => 'switcher',
                    'title'      => __('自动关键词','io_setting'),
                    'label'      => '自动将文章的标签当作关键词',
                    'default'    => true,
                    'dependency' => array( 'switcher', '==', 'true', '', 'visible' ),
                ),
                array(
                    'id'         => 'chain_n',
                    'title'      => __('链接数量','io_setting'),
                    'default'    => '1',
                    'type'       => 'number',
                    'desc'       => __('一篇文章中同一个标签最多自动链接几次，建议不大于2','io_setting'),
                    'dependency' => array( 'switcher', '==', 'true', '', 'visible' ),
                ),
            )
        ),   
    )
));
//
// SEO-GO 跳转
//
CSF::createSection( $prefix, array(
    'parent'   => 'seo_settings',
    'title'       => __('GO 跳转','io_setting'),
    'icon'        => 'fas fa-user-secret',
    'fields'      => array(
		array(
			'type'	=> 'subheading',
			'content' => __('内链跳转，效果：http://您的域名/go/?url=外链','io_setting'),
		),  
        array(
            'id'      => 'is_go',
            'type'    => 'switcher',
            'title'   => __('内链跳转(go跳转)','io_setting'),
            'label'   => __('站点所有外链跳转，效果：http://您的域名/go/?url=外链','io_setting'),
            'default' => false,
        ),
        array(
            'id'      => 'is_must_on_site',
            'type'    => 'switcher',
            'title'   => '必须从站内点击才跳转',
            'label'   => '通过 referer 判断域名，如果不是在站内点击则跳转到网站首页。',
            'desc'    => $tip_ico.'如果开启选项导致所有链接都跳转到首页，请关闭！<br>一些 <b>WP插件</b> 或者 <b>浏览器插件</b> 可能会关闭 referer ，比如<b>采集插件</b>。',
            'default' => true,
            'class'   => 'compact',
            'dependency' => array( 'is_go', '==', 'true'),
        ),
        array(
            'id'        => 'ref_id',
            'type'      => 'group',
            'title'     => '自定义来源id',
            'before'    => '在收藏网址的URL后面添加参数，如：https://www.iotheme.cn?key1=value1',
            'fields'    => array(
                array(
                    'id'    => 'key',
                    'type'  => 'text',
                    'title' => '键名(key)',
                ),
                array(
                    'id'    => 'value',
                    'type'  => 'text',
                    'title' => '值(value)',
                ),
            ),
            'default'   => array(
                array(
                    'key'   => 'ref',
                    'value' => parse_url(home_url())['host'], 
                ),
            ),
            'class'   => 'compact',
            'dependency' => array( 'is_go', '==', 'true'),
        ),
        array(
            'id'        => 'go_tip',
            'type'      => 'fieldset',
            'title'     => __('跳转提示','io_setting'),
            'fields'    => array(
                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => __('启用','io_setting'), 
                    'label' => __('提示用户即将离开本站，注意账号和财产安全。','io_setting'),
                    'subtitle' => '颜色效果中的“全屏加载效果”选项无效',
                ),
                array(
                    'id'    => 'time',
                    'type'  => 'spinner',
                    'title' => __('等待跳转','io_setting'),
                    'after' => '等待多少秒自动跳转<br>注意：填0为手动点击按钮跳转',
                    'unit'  => '秒',
                    'step'  => 1,
                ),
            ),
            'default'        => array( 
                'switch'  => true,
                'time'    => 0, 
            ),
            'class'      => 'compact',
            'dependency' => array( 'is_go', '==', 'true'),
        ),
        array(
            'id'      => 'exclude_links', 
            'type'    => 'textarea',
            'title'   => __('go跳转白名单','io_setting'),
            'subtitle'=> __('go跳转和正文nofollow白名单','io_setting'),
            'after'   => __('一行一个地址，注意不要有空格。<br>需要包含http(s)://<br>iowen.cn和www.iowen.cn为不同的网址<br>此设置同时用于 nofollow 的排除。','io_setting'),
            'attributes' => array(
                'rows' => 4,
            ),
        ),
    )
));

//
// SEO-链接规则
//
CSF::createSection( $prefix, array(
    'parent'   => 'seo_settings',
    'title'       => __('链接规则','io_setting'),
    'icon'        => 'fas fa-link',
    'fields'      => array(
        array(
            'type'    => 'submessage',
            'style'   => 'info',
            'content' => '<p style="font-size:18px"><i class="fa fa-fw fa-info-circle fa-fw"></i> 本页内容修改后必须重新保存一次固定链接，且所有选项不能为<b>空</b>。前往<a href="'.admin_url('/options-permalink.php').'">wp设置</a>保存</p>',
        ),
        array(
            'id'         => 'rewrites_types',
            'type'       => 'button_set',
            'title'      => __('网址&软件&书籍固定链接模式','io_setting'),
            'subtitle'   => '<span style="color:#f00">'.__('设置后需重新保存一次固定链接','io_setting').'</span>',
            'desc'       => '<span style="color:#000"><i class="fa fa-fw fa-info-circle fa-fw"></i> “网址”“app”“书籍”的<a href="'.admin_url('options-permalink.php').'">固定链接</a>模式<br>
                            默认文章的固定链接设置请前往<a href="'.admin_url('/options-permalink.php').'">wp设置</a>中设置，推荐 <code>/%post_id%.html</code></span>',
            'options'    =>  array(
                'post_id'  => '/%post_id%/',
                'postname' => '/%postname%/',
            ),
            'default'    => 'post_id'
        ),
        array(
            'id'         => 'rewrites_end',
            'type'       => 'switcher',
            'title'      => __('html 结尾','io_setting'),
            'subtitle'   => '<span style="color:#f00">'.__('设置后需重新保存一次固定链接','io_setting').'</span>',
            'label'      => __('如：http://w.w.w/123.html','io_setting'),
            'default'    => true,
        ),
        array(
            'id'         => 'new_page_type',
            'type'       => 'switcher',
            'title'      => '新分页格式【（看清描述）】',
            'subtitle'   => '<span style="color:#f00">'.__('设置后需重新保存一次固定链接','io_setting').'</span>',
            'label'      => '看清描述',
            'desc'       => '<span style="color:#000">文章：<code>http://w.w.w/123.html/2</code>  改为 <code>http://w.w.w/123-2.html</code> (需设置链接以 .html 结尾)<br>
                            分类：<code>http://w.w.w/tag/123.html/page/2</code>  改为 <code>http://w.w.w/tag/123-2.html</code> (需开启下方 [分类&标签固定链接模式] 的选项)</span><br>
                            <p style="color:#f00;margin-top:10px"><i class="fa fa-fw fa-info-circle fa-fw"></i> 注意：此选项可能会和其他插件不兼容而造成404，出现问题请关闭</p>' ,
            'default'    => false,
            'class'      => '',
        ),
        array(
            'id'        => 'rewrites_category_types',
            'type'      => 'fieldset',
            'title'     => '分类&标签固定链接模式',
            'subtitle'  => '<span style="color:#f00">'.__('设置后需重新保存一次固定链接','io_setting').'</span>',
            'after'     => '<span style="color:#f00"><i class="fa fa-fw fa-info-circle fa-fw"></i> 注意：此选项可能会和其他插件不兼容而造成404，出现问题请关闭</span>',
            'fields'    => array(
                array(
                    'type'    => 'submessage',
                    'style'   => 'success',
                    'content' => '<i class="fa fa-fw fa-info-circle fa-fw"></i> <b>警告：</b>[优化设置]->[优化加速]中“<b>去除分类标志</b>”选项失效。',
                    'dependency' => array( 'types|rewrites', 'any|!=', 'cat|default'), 
                ),
                array(
                    'id'         => 'rewrites',
                    'type'       => 'button_set',
                    'title'      => __('模式','io_setting'),
                    'options'    =>  array(
                        'default'   => '默认规则',
                        'term_id'   => 'id.html',
                        'term_name' => 'name.html',
                    ),
                ),
                array(
                    'id'      => 'types',
                    'type'    => 'checkbox',
                    'title'   => __('启用类型','io_setting'),
                    'inline'  => true,
                    'options' => array(
                        'tag'   => __('标签','io_setting'),
                        'cat'   => __('分类','io_setting'),
                    ),
                    'class' => 'compact min',
                    'after'   => __('默认位置在首页内容前面和分类内容前面显示搜索框','io_setting'),
                    'dependency' => array( 'rewrites', '!=', 'default'),
                ),
                array(
                    'type'    => 'submessage',
                    'style'   => 'danger',
                    'content' => '<i class="fa fa-fw fa-info-circle fa-fw"></i> <b>警告：</b>必须选一项。',
                    'dependency' => array( 'types|rewrites', '==|!=', '|default'),
                    'class'      =>'compact'
                ),
            ),
            'default' => array(
                'rewrites' => 'default',
                'types'    => array('tag'),
            ),
        ),
        array(
            'id'        => 'sites_rewrite',
            'type'      => 'fieldset',
            'title'     => '网址文章固定链接前缀',
            'subtitle'   => '<span style="color:#f00">'.__('设置后需重新保存一次固定链接','io_setting').'</span>',
            'fields'    => array(
                array(
                    'id'    => 'post',
                    'type'  => 'text',
                    'title' => '网址',
                ),
                array(
                    'id'    => 'taxonomy',
                    'type'  => 'text',
                    'title' => '网址分类',
                    'class' => 'compact min',
                ),
                array(
                    'id'    => 'tag',
                    'type'  => 'text',
                    'title' => '网址标签',
                    'class' => 'compact min',
                ),
            ),
            'default'        => array(
                'post'        => 'sites',
                'taxonomy'    => 'favorites',
                'tag'         => 'sitetag',
            ),
            'after'     => '<i class="fa fa-fw fa-info-circle fa-fw"></i> '.__('设置后需重新保存一次<a href="'.admin_url('options-permalink.php').'">固定链接</a>，且所有选项不能为空','io_setting'),
        ),
        array(
            'id'        => 'app_rewrite',
            'type'      => 'fieldset',
            'title'     => 'app文章固定链接前缀',
            'subtitle'   => '<span style="color:#f00">'.__('设置后需重新保存一次固定链接','io_setting').'</span>',
            'fields'    => array(
                array(
                    'id'    => 'post',
                    'type'  => 'text',
                    'title' => 'app',
                ),
                array(
                    'id'    => 'taxonomy',
                    'type'  => 'text',
                    'title' => 'app分类',
                    'class' => 'compact min',
                ),
                array(
                    'id'    => 'tag',
                    'type'  => 'text',
                    'title' => 'app标签',
                    'class' => 'compact min',
                ),
            ),
            'default'        => array(
                'post'        => 'app',
                'taxonomy'    => 'apps',
                'tag'         => 'apptag',
            ),
            'after'     => '<i class="fa fa-fw fa-info-circle fa-fw"></i> '.__('设置后需重新保存一次<a href="'.admin_url('options-permalink.php').'">固定链接</a>，且所有选项不能为空','io_setting'),
        ),
        array(
            'id'        => 'book_rewrite',
            'type'      => 'fieldset',
            'title'     => '书籍文章固定链接前缀',
            'subtitle'   => '<span style="color:#f00">'.__('设置后需重新保存一次固定链接','io_setting').'</span>',
            'fields'    => array(
                array(
                    'id'    => 'post',
                    'type'  => 'text',
                    'title' => '书籍',
                ),
                array(
                    'id'    => 'taxonomy',
                    'type'  => 'text',
                    'title' => '书籍分类',
                    'class' => 'compact min',
                ),
                array(
                    'id'    => 'tag',
                    'type'  => 'text',
                    'title' => '书籍标签',
                    'class' => 'compact min',
                ),
                array(
                    'id'    => 'series',
                    'type'  => 'text',
                    'title' => '书籍系列',
                    'default' => 'series',
                    'class' => 'compact min',
                ),
            ),
            'default'        => array(
                'post'        => 'book',
                'taxonomy'    => 'books',
                'tag'         => 'booktag',
                'series'      => 'series',
            ),
            'after'     => '<i class="fa fa-fw fa-info-circle fa-fw"></i> '.__('设置后需重新保存一次<a href="'.admin_url('options-permalink.php').'">固定链接</a>，且所有选项不能为空','io_setting'),
        ),
    )
));

//
// SEO-SiteMAP&推送
//
CSF::createSection( $prefix, array(
    'parent'   => 'seo_settings',
    'title'       => __('SiteMAP&推送','io_setting'),
    'icon'        => 'fas fa-sitemap',
    'fields'      => array(
        array(
            'id'      => 'site_map',
            'title'   => __('SiteMAP','io_setting'),
            'type'    => 'switcher',
            'label'   => __('启用主题 sitemap，生成 sitemap.xml 文件','io_setting'),
            'desc'    => __('不适应于多站点模式，请改用其他插件。','io_setting'),
            'default' => false,
        ),
        array(
            'id'        => 'site_options',
            'type'      => 'fieldset',
            'title'     => __('SiteMAP选项','io_setting'),
            'fields'    => array(
                array(
                    'type'    => 'content',
                    'content' => '<span>自动生成xml文件，遵循Sitemap协议，用于指引搜索引擎快速、全面的抓取或更新网站上内容及处理错误信息。兼容百度、google、360等主流搜索引擎。</span><span style="display:block; margin-top: 10px;">注意：参数需要保存后才生效，请设置完参数保存后再点击&quot;生成sitemap&quot;按钮。</span>',
                ),
                array(
                    'id'      => 'baidu-post-types',
                    'type'    => 'checkbox',
                    'title'   => __('生成链接文章类型','io_setting'),
                    'options' => 'post_types',
                    'inline'  => true,
                    'after'      => '例：如果仅希望生成文章的sitemap，则只勾选文章即可。'
                ),
                array(
                    'id'      => 'baidu-taxonomies',
                    'type'    => 'checkbox',
                    'title'   => __( '生成链接分类', 'io_setting' ),
                    'options' => 'setting_get_taxes',
                    'inline'  => true,
                ),
                array(
                    'id'      => 'baidu-num',
                    'type'    => 'text',
                    'title'   => __('生成链接数量','io_setting'),
                    'after'   => '链接数越大所占用的资源也越大，根据自己的服务器配置情况设置数量。最新发布的文章首先排在最前。 <br />-1 表示所有。如果文章太多生成失败，请使用第三方插件。<br />此数量仅指post type的数量总和，不包括分类，勾选的分类是全部生成链接。',
                ),
                array(
                    'id'      => 'baidu-auto-update',
                    'type'    => 'switcher',
                    'title'   => __('自动更新','io_setting'),
                    'label'   => '勾选则发布新文章或者删除文章时自动更新sitemap。',
                ),
                array(
                    'type'     => 'callback',
                    'function' => 'io_site_map_but',
                ),
            ),
            'default' => array(
                'sitemap-file'        => 'sitemap', 
                'baidu-post-types'    => array( 'post', 'page' ),
                'baidu-taxonomies'    => array( 'category' ),
                'baidu-num'            => '500',
                'baidu-auto-update'    => true,
            ),
            'class'      => 'compact min',
            'dependency' => array( 'site_map', '==', true )
        ),
        array(
            'id'        => 'baidu_submit',
            'type'      => 'fieldset',
            'title'     => __('百度主动推送','io_setting'),
            'fields'    => array(
                array(
                    'id'    => 'switcher',
                    'type'  => 'switcher',
                    'title' => __('开启','io_setting'),
                ),    
                array(
                    'id'       => 'token_p',
                    'type'     => 'text',
                    'title'    => __('推送token值','io_setting'),
                    'after'    => __('输入百度主动推送token值','io_setting'),
                    'class'    => 'compact min',
                    'dependency'   => array( 'switcher', '==', 'true' )
                ), 
            ),
            'default'        => array(
                'switcher'    => false,
            ),
        ),
        array(
            'id'        => 'baidu_xzh',
            'type'      => 'fieldset',
            'title'     => __('百度熊掌号推送','io_setting'),
            'fields'    => array(
                array(
                    'id'    => 'switcher',
                    'type'  => 'switcher',
                    'title' => __('开启','io_setting'),
                ),
                array(
                    'id'       => 'xzh_id',
                    'title'    => __('熊掌号 appid','io_setting'),
                    'type'     => 'text',
                    'class'    => 'compact min',
                    'dependency'   => array( 'switcher', '==', 'true' )
                ),
                array(
                    'id'       => 'xzh_token',
                    'title'    => __('熊掌号 token','io_setting'),
                    'type'     => 'text',
                    'class'    => 'compact min',
                    'dependency'   => array( 'switcher', '==', 'true' )
                ),
            ),
            'default'        => array(
                'switcher'    => false,
            ),
        ),
    )
));
//
// 其他功能
//
CSF::createSection( $prefix, array(
    'id'    => 'other',
    'title' => __('其他功能','io_setting'),
    'icon'  => 'fa fa-flask',
));
//
// 其他功能 - 其他杂项
//
CSF::createSection( $prefix, array(
    'parent'      => 'other',
    'title'  => __('其他杂项','io_setting'),
    'icon'   => 'fa fa-info-circle',
    'fields' => array(
        array(
            'id'      => 'weather',
            'type'    => 'switcher',
            'title'   => __('天气','io_setting'),
            'label'   => __('显示天气小工具','io_setting'),
            'default' => false,
        ),
        array(
            'id'      => 'weather_location',
            'type'    => 'radio',
            'title'   => __('天气位置','io_setting'),
            'default' => 'footer',
            'inline'  => true,
            'options' => array(
                'header'  => __('头部', 'io_setting'),
                'footer'  => __('右下小工具', 'io_setting'),
            ),
            'class'      => 'compact',
            'dependency' => array( 'weather', '==', true )
        ),
        array(
            'id'      => 'hitokoto',
            'type'    => 'switcher',
            'title'   => __('一言', 'io_setting'),
            'label'   => __('右上角显示一言', 'io_setting'), 
            'default' => false,
        ),

        array(
            'id'         => 'hitokoto_code',
            'type'       => 'textarea',
            'title'      => '一言自定义代码', 
            'default'    => '<script src="//v1.hitokoto.cn/?encode=js&select=%23hitokoto" defer></script>'.PHP_EOL.'<span id="hitokoto"></span>',
            'sanitize'   => false,
            'attributes' => array(
                'rows' => 3,
            ),
            'class'      => 'compact',
            'after'      => '<i class="fa fa-fw fa-info-circle fa-fw"></i> 自己搭建：<a href="https://www.iowen.cn/hitokoto-api-single-page/" target="_blank">教程--></a>',
            'dependency' => array( 'hitokoto', '==', true )
        ),
        
        array(
            'id'         => 'is_iconfont',
            'type'       => 'button_set',
            'title'      => __('字体图标源', 'io_setting'),
            'label'      => __('fa 和阿里图标二选一，为轻量化资源，不能共用。', 'io_setting'),
            'desc'       => $tip_ico.'使用方法：<a href="https://www.iotheme.cn/onenavzhuticaidantubiaoshezhi.html" target="_blank">教程--></a>',
            'options'    => array(
                ''  => 'fa图标',
                '1' => '阿里图标',
            ),
            'default'    => '1',
        ),
        array(
            'type'       => 'notice',
            'style'      => 'success',
            'content'    => __('fa图标库使用CDN，cdn地址修改请在 inc\theme-start.php 文件里修改。默认 CDN 由 staticfile.org 提供', 'io_setting'),
            'dependency' => array( 'is_iconfont', '==', '' )
        ),
        array(
            'id'         => 'iconfont_url',  
            'type'       => 'textarea',
            'title'      => __('阿里图标库地址', 'io_setting'),
            'after'       => '<h4>输入阿里图标库在线链接，可多个，一行一个地址，注意不要有空格。</h4>图标库地址：<a href="https://www.iconfont.cn/" target="_blank">--></a><br>教程地址：<a href="https://www.iotheme.cn/one-nav-yidaohangzhutishiyongaliyuntubiaodefangfa.html" target="_blank">--></a>
            <br><p><i class="fa fa-fw fa-info-circle fa-fw"></i> 阿里图标库项目的 FontClass/Symbol前缀 必须为 “<b>io-</b>”，Font Family 必须为 “<b>io</b>”，具体看上面的教程。</p>注意：项目之间的<b>图标名称</b>不能相同，<b>彩色</b>图标不支持变色。',
            'class'      => 'compact min',
            'attributes' => array(
                'rows' => 4,
            ),
            'default'    => '//at.alicdn.com/t/font_1620678_18rbnd2homc.css',
            'dependency' => array( 'is_iconfont', '==', '1' )
        ),
        array(
            'id'        => 'ip_location',
            'type'      => 'fieldset',
            'title'     => __('IP归属地','io_setting'),
            'fields'    => array(
                array(
                    'id'      => 'level',
                    'type'    => "select",
                    'title'   => 'IP归属地显示格式',
                    'options' => array(
                        '1' => '仅国家',
                        '2' => '仅省',
                        '3' => '仅市',
                        '4' => '国家+省',
                        '5' => '省+市',
                        '6' => '详细',
                    ),
                    'default' => '2',
                ),
                array(
                    'id'      => 'v4_type',
                    'type'    => 'radio',
                    'title'   => 'IPv4归属地数据库版本',
                    'inline'  => true,
                    'options' => array(
                        'qqwry'     => 'QQwry',
                        'ip2region' => 'Ip2Region',
                    ), 
                    'class'   => 'compact min',
                    'default' => 'qqwry',
                ),
                array(
                    'id'      => 'comment',
                    'type'    => 'switcher',
                    'title'   => '评论显示用户归属地',
                    'default' => false,
                    'class'   => 'compact min',
                ),
                array(
                    'type'    => 'submessage',
                    'style'   => 'info',
                    'content' => ip_db_manage(),
                ),
            ),
        ),
        array(
            'id'      => 'ico-source',
            'type'    => 'fieldset',
            'title'   => __('图标源设置','io_setting'),
            'subtitle'   => __('自建图标源api源码地址：','io_setting').'<a href="https://api.iowen.cn/favicon" target="_blank">--></a>',
            'fields'  => array(
                array(
                    'id'      => 'url_format',
                    'type'    => 'switcher',
                    'title'   => __('不包含 http(s)://','io_setting'),
                    'subtitle'    => __('根据图标源 api 要求设置，如果api要求不能包含协议名称，请开启此选项','io_setting'),
                ),
                array(
                    'id'      => 'ico_url',
                    'type'    => 'text',
                    'title'   => __('图标源','io_setting'),
                    'subtitle'    => __('api 地址','io_setting'),
                ),
                array(
                    'id'      => 'ico_png',
                    'type'    => 'text',
                    'title'   => __('图标源api后缀','io_setting'),
                    'subtitle'=> __('如：.png ,请根据api格式要求设置，如不需要请留空','io_setting'),
                )
            ),
            'default'    => array(
                'url_format' => true,
                'ico_url'    => 'https://api.iowen.cn/favicon/',
                'ico_png'    => '.png',
            )
        ),
        array(
            'id'          => 'qr_api',
            'type'        => 'button_set',
            'title'       => '二维码api源',
            'options'     => array(
                'local'  => '本地',
                'other'  => '第三方',
            ),
            'default'     => 'local',
        ),
        array(
            'id'         => 'qr_url',
            'type'       => 'text',
            'title'      => __('二维码api','io_setting'),
            'subtitle'   => __('可用二维码api源地址：','io_setting').'<a href="https://www.iowen.cn/latest-qr-code-api-service-https-available/" target="_blank">--></a>',
            'default'    => '//api.qrserver.com/v1/create-qr-code/?size=$sizex$size&margin=10&data=$url',
            'after'      => '参数：<br>$size 大小 <br>$url  地址 <br>如：s=$size<span style="color: #ff0000;">x</span>$size 、 size=$size 、 width=$size&height=$size<br><br>默认内容：//api.qrserver.com/v1/create-qr-code/?size=$sizex$size&margin=10&data=$url',
            'class'      => 'compact min',
            'dependency' => array( 'qr_api', '==', 'other' )
        ),
        array(
            'id'         => 'random_head_img',
            'type'       => 'textarea',
            'title'      => __('博客随机头部图片','io_setting'),
            'subtitle'   => __('缩略图、文章页随机图片','io_setting'),
            'after'      => __('一行一个图片地址，注意不要有空格<br>','io_setting'),
            'attributes' => array(
                'rows' => 10,
            ),
            'default'    => '//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/screenshots/1.jpg'.PHP_EOL.'//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/screenshots/2.jpg'.PHP_EOL.'//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/screenshots/3.jpg'.PHP_EOL.'//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/screenshots/4.jpg'.PHP_EOL.'//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/screenshots/5.jpg'.PHP_EOL.'//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/screenshots/6.jpg'.PHP_EOL.'//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/screenshots/7.jpg'.PHP_EOL.'//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/screenshots/8.jpg'.PHP_EOL.'//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/screenshots/9.jpg'.PHP_EOL.'//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/screenshots/0.jpg',
        ),
    )
));

//
// 其他功能 - 邮箱发信
//
CSF::createSection( $prefix, array(
    'parent'      => 'other',
    'title'       => '邮箱发信',
    'icon'        => 'fa fa-envelope',
    'description' => '',
    'fields'      => array(
        array(
            'type'    => 'submessage',
            'style'   => 'danger',
            'content' => '<h4>邮件发信服务设置</h4><p>如果你不需要评论邮件通知等功能，可不设置。<br/>国内一般使用 SMTP 服务</p>
            <p><i class="fa fa-fw fa-info-circle fa-fw"></i> 注：如果要关闭或者使用<b>第三方插件</b>
            请选择“关闭”，不能和其他SMTP插件一起开启！同时请注意开启服务器对应的端口！</p>
            <a href="' . io_get_admin_csf_url('用户安全/用户注册') . '">登录/注册功能设置</a>',
        ),
        array(
            'id'      => 'i_default_mailer',
            'type'    => 'radio',
            'title'   => 'SMTP服务',
            'default' => 'php',
            'inline'  => true,
            'options' => array(
                'php'   => '关闭',
                'smtp'  => 'SMTP'
            ),
            'after'    => __('使用 “SMTP” 或 “关闭”用第三方插件 作为默认邮件发送方式','io_setting'),
        ),
        array(
            'id'         => 'i_smtp_host',
            'type'       => 'text',
            'title'      => __('SMTP 主机','io_setting'),
            'after'      => __('您的 SMTP 服务主机','io_setting'),
            'class'      => 'compact',
            'dependency' => array( 'i_default_mailer', '==', 'smtp' )
        ), 
        array(
            'id'         => 'i_smtp_port',
            'type'       => 'text',
            'title'      => __('SMTP 端口','io_setting'),
            'after'      => __('您的 SMTP 服务端口','io_setting'),
            'default'    => 465,
            'class'      => 'compact',
            'dependency' => array( 'i_default_mailer', '==', 'smtp' )
        ), 
        array(
            'id'       => 'i_smtp_secure',
            'type'     => 'radio',
            'title'    => __('SMTP 安全','io_setting'),
            'after'    => __('您的 SMTP 服务器安全协议','io_setting'),
            'default'  => 'ssl',
            'inline'   => true,
            'options'  => array(
                'auto'   => 'Auto',
                'ssl'    => 'SSL',
                'tls'    => 'TLS',
                'none'   => 'None'
            ),
            'class'      => 'compact',
            'dependency' => array( 'i_default_mailer', '==', 'smtp' )
        ), 
        array(
            'id'      => 'i_smtp_username',
            'type'    => 'text',
            'title'   => __('SMTP 用户名','io_setting'),
            'after'   => __('您的 SMTP 用户名','io_setting'),
            'class'   => 'compact',
            'dependency'   => array( 'i_default_mailer', '==', 'smtp' )
        ),  
        array(
            'id'      => 'i_smtp_password',
            'type'    => 'text',
            'title'   => __('SMTP 密码','io_setting'),
            'after'   => __('您的 SMTP 密码','io_setting'),
            'class'   => 'compact',
            'dependency'   => array( 'i_default_mailer', '==', 'smtp' )
        ),  
        array(
            'id'      => 'i_smtp_name',
            'type'    => 'text',
            'title'   => __('你的姓名','io_setting'),
            'default' => get_bloginfo('name'),
            'after'   => __('你发送的邮件中显示的名称','io_setting'),
            'class'   => 'compact',
            'dependency'   => array( 'i_default_mailer', '==', 'smtp' ), 
        ), 
        array(
            'type'    => 'submessage',
            'style'   => 'warning',
            'content' => '<h4>邮件发送测试</h4>
            <p>输入接收邮件的邮箱号码，发送测试邮件</p>
            <ajaxform class="ajax-form" ajax-url="' . admin_url('admin-ajax.php') . '">
            <p><input type="text" class="not-change" ajax-name="email"></p>
            <div class="ajax-notice"></div>
            <p><a href="javascript:;" class="button button-primary ajax-submit"><i class="fa fa-paper-plane-o"></i> 发送测试邮件</a></p>
            <input type="hidden" ajax-name="action" value="test_mail">
            </ajaxform>',
        ),
    ),
));
//
// 其他功能 - 短信接口
//
CSF::createSection( $prefix, array(
    'parent'      => 'other',
    'title'       => '短信接口',
    'icon'        => 'fa fa-comments',
    'description' => '',
    'fields'      => array(
        array(
            'type'    => 'submessage',
            'style'   => 'danger',
            'content' => '<h4>如需使用手机账户等相关功能，请在下方设置短信接口</h4>
            <li>阿里云和腾讯云需要网站备案！其它接口无需备案</li>
            <li>短信能正常发送后，请记得开启手机绑定、手机号登录、手机验证等功能</li>
            <li><a href="' . io_get_admin_csf_url('用户安全/用户注册') . '">登录/注册功能设置</a></li>',
        ),
        array(
            'id'      => 'sms_sdk',
            'type'    => "select",
            'title'   => '设置短信接口',
            'options' => array(
                'ali'     => __('阿里云短信', 'io_setting'),
                'tencent' => __('腾讯云短信', 'io_setting'),
                'smsbao'  => __('短信宝', 'io_setting'),
            ),
            'default' => 'null',
        ),
        array(
            'id'         => 'sms_ali_option',
            'type'       => 'accordion',
            'title'      => '阿里云',
            'accordions' => array(
                array(
                    'title'  => '阿里云短信配置',
                    'fields' => array(
                        array(
                            'id'      => 'keyid',
                            'type'    => 'text',
                            'title'   => 'AccessKey Id',
                            'default' => '',
                        ),
                        array(
                            'id'      => 'keysecret',
                            'type'    => 'text',
                            'title'   => 'AccessKey Secret',
                            'class'   => 'compact min',
                            'default' => '',
                        ),
                        array(
                            'id'      => 'sign_name',
                            'type'    => 'text',
                            'title'   => '签名',
                            'class'   => 'compact min',
                            'desc'    => '已审核的的短信签名，示例：一为主题',
                            'default' => '',
                        ),
                        array(
                            'id'      => 'template_code',
                            'type'    => 'text',
                            'title'   => '模板CODE',
                            'class'   => 'compact min',
                            'desc'    => '已审核的的短信模板代码，示例：SMS_154950000<br>
                            模板内容示例：<code>您的验证码为：${code}，......</code> 必须要有 <code style="color: #ee0f00;padding:0px 3px">${code}</code> 变量。<br>
                            <a target="_blank" href="https://www.aliyun.com/product/sms?source=5176.11533457&userCode=d7pz9hw8">申请地址</a>',
                            'default' => '',
                        ),
                    ),
                ),
            ),
        ),
        array(
            'id'         => 'sms_tencent_option',
            'type'       => 'accordion',
            'title'      => '腾讯云',
            'accordions' => array(
                array(
                    'title'  => '腾讯云短信配置',
                    'fields' => array(
                        array(
                            'id'      => 'app_id',
                            'type'    => 'text',
                            'title'   => 'SDK AppID',
                        ),
                        array(
                            'id'      => 'app_key',
                            'type'    => 'text',
                            'title'   => 'App Key',
                            'class'   => 'compact min',
                            'desc'    => '腾讯云短信应用的SDK AppID和AppKey',
                        ),
                        /* SDK3.0需要的参数
                        array(
                            'id'    => 'secret_id',
                            'type'  => 'text',
                            'title' => 'Access Id',
                            'class' => 'compact min',
                        ),
                        array(
                            'id'    => 'secret_key',
                            'type'  => 'text',
                            'title' => 'Access Key',
                            'class' => 'compact min',
                        ),
                        */
                        array(
                            'id'      => 'sign_name',
                            'type'    => 'text',
                            'title'   => '签名',
                            'class'   => 'compact min',
                            'desc'    => '已审核的的短信签名，示例：一为主题',
                        ),
                        array(
                            'id'      => 'template_id',
                            'type'    => 'text',
                            'title'   => '模板ID',
                            'class'   => 'compact min',
                            'desc'    => '已审核的的短信模板ID，示例：1660000<br>
                            模板内容示例：<code>您的验证码为{1}，{2}分钟内有效，......</code> 必须要有 <code style="color: #ee0f00;padding:0px 3px">{1}</code> 和 <code style="color: #ee0f00;padding:0px 3px">{2}</code> 变量。<br>
                            <a target="_blank" href="https://cloud.tencent.com/act/cps/redirect?redirect=10068&cps_key=bda57913e36ec90681a3b90619e44708">申请地址</a>',
                        ),
                    ),
                ),
            ),
        ),
        array(
            'id'         => 'sms_smsbao_option',
            'type'       => 'accordion',
            'title'      => '短信宝',
            'accordions' => array(
                array(
                    'title'  => '短信宝配置',
                    'fields' => array(
                        array(
                            'id'      => 'userame',
                            'type'    => 'text',
                            'title'   => '账号用户名',
                        ),
                        array(
                            'id'      => 'password',
                            'type'    => 'text',
                            'title'   => '账号密码',
                            'class'   => 'compact min',
                        ),
                        array(
                            'id'      => 'api_key',
                            'type'    => 'text',
                            'title'   => 'ApiKey',
                            'class'   => 'compact min',
                            'desc'    => '短信宝ApiKey（可选）<br/>ApiKey是短信宝新推出的接口方式，更高效安全，ApiKey和密码二选一即可',
                        ),
                        array(
                            'id'      => 'template',
                            'type'    => 'text',
                            'class'   => 'compact min',
                            'title'   => '模板内容',
                            'desc'    => '模板内容，必须要有<code style="color: #ee0f00;padding:0px 3px">{code}</code>变量。<br>
                            模板内容示例：<code>【一为主题】您的验证码为{code}，{time}分钟内有效。</code><br>
                            <a target="_blank" href="http://www.smsbao.com/reg?r=12245">短信宝官网</a>',
                        ),
                    ),
                ),
            ),
        ),
        array(
            'type'    => 'submessage',
            'style'   => 'warning',
            'content' => '<h4>短信发送测试：</h4>
            <p>输入接收短信的手机号码，在此发送验证码为8888的测试短信</p>
            <ajaxform class="ajax-form" ajax-url="' . admin_url('admin-ajax.php') . '">
            <p><input type="text" class="not-change" ajax-name="phone_number"></p>
            <div class="ajax-notice"></div>
            <p><a href="javascript:;" class="button button-primary ajax-submit"><i class="fa fa-paper-plane-o"></i> 发送测试短信</a></p>
            <input type="hidden" ajax-name="action" value="test_send_sms">
            </ajaxform>',
        ),
    ),
));
//
// 轮播广告
//
CSF::createSection( $prefix, array(
    'id'    => 'add-ad',
    'title' => __('轮播&广告','io_setting'),
    'icon'  => 'fa fa-google',
));
//
// 轮播广告-弹窗轮播
//
CSF::createSection( $prefix, array(
    'parent'      => 'add-ad',
    'title'       => __('弹窗轮播','io_setting'),
    'icon'        => 'fas fa-solar-panel',
    'fields'      => array(
        array(
            'id'    => 'enable_popup',
            'type'  => 'switcher',
            'title' => __('启用弹窗','io_setting'),
            'class'     => '',
        ),
        array(
            'id'        => 'popup_set',
            'type'      => 'fieldset',
            'title'     => __('全局弹窗','io_setting'),
            'fields'    => array(
                array(
                    'id'    => 'delay',
                    'type'  => 'spinner',
                    'title' => __('延时','io_setting'),
                    'after' => __('延时多少秒后显示弹窗','io_setting'),
                    'unit'  => '秒',
                    'step'  => 1,
                ),
                array(
                    'id'    => 'only_home',
                    'type'  => 'switcher',
                    'title' => __('仅首页显示','io_setting'),
                ),
                array(
                    'id'    => 'show',
                    'type'  => 'switcher',
                    'title' => __('显示一次','io_setting'),
                    'label' => __('同一个游客id一天只显示一次','io_setting'),
                ),
                array(
                    'id'    => 'logged_show',
                    'type'  => 'switcher',
                    'title' => __('登录用户只显示一次','io_setting'),
                    'label' => __('同一个用户登录有效期只显示一次','io_setting'),
                ),
                array(
                    'id'    => 'update_date',
                    'type'  => 'date',
                    'title' => __('公告日期','io_setting'),
                    'settings' => array(
                        'dateFormat'      => 'yy-mm-dd',
                        'changeMonth'     => true,
                        'changeYear'      => true, 
                        'showButtonPanel' => true,
                    ),
                    'after'      => __('用于登录用户判断是否有更新（不会显示在弹窗里）','io_setting'),
                    'class'      => 'compact min',
                    'dependency' => array( 'logged_show', '==', 'true' ),
                ),
                array(
                    'id'         => 'title',
                    'type'       => 'text',
                    'title'      => __('标题','io_setting'), 
                    'subtitle'   => __('留空不显示','io_setting'),
                ),
                array(
                    'id'          => 'content',
                    'type'        => 'wp_editor',
                    'title'       => __('弹窗内容','io_setting'),
                    'height'      => '100px',
                    'sanitize'    => false,
                    'after'       => '如果a标签想关闭弹窗，请添加class:  popup-close',
                ),
                array(
                    'id'      => 'width',
                    'type'    => 'slider',
                    'title'   => '宽度',
                    'class'   => '',
                    'min'     => 340,
                    'max'     => 1024,
                    'step'    => 10,
                    'unit'    => 'px',
                ),
                array(
                    'id'    => 'valid',
                    'type'  => 'switcher',
                    'title' => __('有效期','io_setting'),
                    'label' => __('设置弹窗有效期','io_setting'),
                ),
                array(
                    'id'    => 'begin_time',
                    'type'  => 'date',
                    'title' => __('开始时间','io_setting'),
                    'settings' => array(
                        'dateFormat'      => 'yy-mm-dd',
                        'changeMonth'     => true,
                        'changeYear'      => true, 
                        'showButtonPanel' => true,
                    ),
                    'dependency' => array( 'valid', '==', 'true' ),
                ), 
                array(
                    'id'    => 'end_time',
                    'type'  => 'date',
                    'title' => __('结束时间','io_setting'),
                    'settings' => array(
                        'dateFormat'      => 'yy-mm-dd',
                        'changeMonth'     => true,
                        'changeYear'      => true, 
                        'showButtonPanel' => true,
                    ),
                    'dependency' => array( 'valid', '==', 'true' ),
                ), 
            ),
            'default'        => array(
                'delay'         => 0,
                'show'          => true,
                'update_date'   => date('Y-m-d',current_time( 'timestamp' )),
                'begin_time'    => date('Y-m-d',current_time( 'timestamp' )),
                'end_time'      => date("Y-m-d", strtotime("+10 day",current_time( 'timestamp' ))),
                'width'         => 560,
            ),
            'dependency'  => array( 'enable_popup', '==', 'true', '', 'visible' ),
        ),
        array(
            'id'        => 'carousel_img',
            'type'      => 'repeater',
            'title'     => '首页&博客轮播模块',
            'fields'    => array(
                array(
                    'id'        => 'title',
                    'type'      => 'text',
                    'title'     => '标题',
                ),
                array(
                    'id'      => 'img',
                    'type'    => 'upload',
                    'title'   => __('图片','io_setting'),
                    'library' => 'image',
                    'after'   => $tip_ico.'图片尺寸为 21:9',
                ),
                array(
                    'id'        => 'url',
                    'type'      => 'text',
                    'title'     => '目标URL',
                ),
                array(
                    'id'      => 'is_ad',
                    'type'    => 'switcher',
                    'title'   => '是广告',
                    'label'   => __('注意：广告将直达目标URL,不会添加跳转和nofollow','io_setting'),
                ),
            )
        ),
        //TODO 待加轮播
    )
));
//
// 轮播广告-首页广告
//
CSF::createSection( $prefix, array(
    'parent'      => 'add-ad',
    'title'       => __('首页广告','io_setting'),
    'icon'        => 'fa fa-google',
    'fields'      => array(
        //首页顶部广告位
        array(
            'id'        => 'ad_home_top',
            'type'      => 'fieldset',
            'title'     => '首页顶部广告位',
            'subtitle'  => '注意：需关掉‘big搜索’才能显示“首页顶部广告位”内容',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options'    =>  array(
                        '1'  => 'All',
                        '2'  => '仅移动端',
                        '3'  => '仅PC端',
                    ),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'      => 'tow',
                    'type'    => 'switcher',
                    'title'   => __('第二广告位','io_setting'),
                    'label'   => __('大屏并排显示2个广告位，小屏幕自动隐藏','io_setting'),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'code_editor',
                    'title'      => __('广告位内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content2',
                    'type'       => 'code_editor',
                    'title'      => __('第二广告位内容','io_setting'),
                    'subtitle'   => __('第二个广告位的内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch|tow', '==|==', 'true|true' )
                ),
            ),
            'sanitize'   => false,
            'default'        => array(
                'switch'     => false,
                'loc'        => '1',
                'tow'        => false,
                'content'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
                'content2'   => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            ),
        ),
        //首页网址块上方广告位
        array(
            'id'        => 'ad_home_card_top',
            'type'      => 'fieldset',
            'title'     => '首页网址块上方广告位',
            'subtitle'  => '网址块上方广告位',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options'    =>  array(
                        '1'  => 'All',
                        '2'  => '仅移动端',
                        '3'  => '仅PC端',
                    ),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'      => 'tow',
                    'type'    => 'switcher',
                    'title'   => __('第二广告位','io_setting'),
                    'label'   => __('大屏并排显示2个广告位，小屏幕自动隐藏','io_setting'),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'code_editor',
                    'title'      => __('广告位内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content2',
                    'type'       => 'code_editor',
                    'title'      => __('第二广告位内容','io_setting'),
                    'subtitle'   => __('第二个广告位的内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch|tow', '==|==', 'true|true' )
                ),
            ),
            'sanitize'   => false,
            'default'        => array(
                'switch'     => false,
                'loc'        => '1',
                'tow'        => false,
                'content'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
                'content2'   => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            ),
        ),
        //友链上方广告位
        array(
            'id'        => 'ad_home_link_top',
            'type'      => 'fieldset',
            'title'     => '友链上方广告位',
            'subtitle'  => '首页底部友链上方广告位',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options'    =>  array(
                        '1'  => 'All',
                        '2'  => '仅移动端',
                        '3'  => '仅PC端',
                    ),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'      => 'tow',
                    'type'    => 'switcher',
                    'title'   => __('第二广告位','io_setting'),
                    'label'   => __('大屏并排显示2个广告位，小屏幕自动隐藏','io_setting'),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'code_editor',
                    'title'      => __('广告位内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content2',
                    'type'       => 'code_editor',
                    'title'      => __('第二广告位内容','io_setting'),
                    'subtitle'   => __('第二个广告位的内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch|tow', '==|==', 'true|true' )
                ),
            ),
            'sanitize'   => false,
            'default'        => array(
                'switch'     => false,
                'loc'        => '1',
                'tow'        => false,
                'content'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
                'content2'   => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            ),
        ),
        //footer 广告位
        array(
            'id'        => 'ad_footer_top',
            'type'      => 'fieldset',
            'title'     => 'footer 广告位',
            'subtitle'  => '全站 footer 位广告',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options'    =>  array(
                        '1'  => 'All',
                        '2'  => '仅移动端',
                        '3'  => '仅PC端',
                    ),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'code_editor',
                    'title'      => __('广告位内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
            ),
            'sanitize'   => false,
            'default'        => array(
                'switch'     => false,
                'loc'        => '1',
                'content'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            ),
        ),
    )
));
//
// 轮播广告-文章广告
//
CSF::createSection( $prefix, array(
    'parent'      => 'add-ad',
    'title'       => __('文章广告','io_setting'),
    'icon'        => 'fa fa-google',
    'fields'      => array(
        //网址详情页右边广告位
        array(
            'id'        => 'ad_site_right',
            'type'      => 'fieldset',
            'title'     => '网址详情页右边广告位',
            'subtitle'  => '注意：如果[网站详情页侧边栏]设置了内容，则此项无效。',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options'    =>  array(
                        '1'  => 'All',
                        '2'  => '仅移动端',
                        '3'  => '仅PC端',
                    ),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'code_editor',
                    'title'      => __('广告位内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
            ),
            'sanitize'   => false,
            'default'        => array(
                'switch'     => false,
                'loc'        => '1',
                'content'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            ),
        ), 
        //网址、app正文上方广告位
        array(
            'id'        => 'ad_app_content_top',
            'type'      => 'fieldset',
            'title'     => '网址、app、书籍正文上方广告位',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options'    =>  array(
                        '1'  => 'All',
                        '2'  => '仅移动端',
                        '3'  => '仅PC端',
                    ),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'code_editor',
                    'title'      => __('广告位内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
            ),
            'sanitize'   => false,
            'default'        => array(
                'switch'     => false,
                'loc'        => '1',
                'content'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            ),
        ), 
        //正文标题下广告位
        array(
            'id'        => 'ad_post_title_bottom',
            'type'      => 'fieldset',
            'title'     => '正文标题下广告位',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options'    =>  array(
                        '1'  => 'All',
                        '2'  => '仅移动端',
                        '3'  => '仅PC端',
                    ),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'code_editor',
                    'title'      => __('广告位内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
            ),
            'sanitize'   => false,
            'default'        => array(
                'switch'     => false,
                'loc'        => '1',
                'content'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            ),
        ), 
        //正文底部广告位
        array(
            'id'        => 'ad_post_content_bottom',
            'type'      => 'fieldset',
            'title'     => '正文底部广告位',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options'    =>  array(
                        '1'  => 'All',
                        '2'  => '仅移动端',
                        '3'  => '仅PC端',
                    ),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'code_editor',
                    'title'      => __('广告位内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
            ),
            'sanitize'   => false,
            'default'        => array(
                'switch'     => false,
                'loc'        => '1',
                'content'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            ),
        ), 

        array(
            'id'         => 'ad_po',
            'type'       => 'code_editor',
            'title'      => __('文章内广告短代码','io_setting'),
            'default'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            'settings'   => array(
                'theme'  => 'dracula',
                'mode'   => 'javascript',
            ),
            'sanitize'   => false,
            'subtitle'   => __('在文章中添加短代码 [ad] 即可调用','io_setting'),
        ), 
        //评论上方广告位
        array(
            'id'        => 'ad_comments_top',
            'type'      => 'fieldset',
            'title'     => '评论上方广告位',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options'    =>  array(
                        '1'  => 'All',
                        '2'  => '仅移动端',
                        '3'  => '仅PC端',
                    ),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'code_editor',
                    'title'      => __('广告位内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
            ),
            'sanitize'   => false,
            'default'        => array(
                'switch'     => false,
                'loc'        => '1',
                'content'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            ),
        ), 
        //下载弹窗广告位
        array(
            'id'        => 'ad_res_down_popup',
            'type'      => 'fieldset',
            'title'     => '下载弹窗广告位',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options'    =>  array(
                        '1'  => 'All',
                        '2'  => '仅移动端',
                        '3'  => '仅PC端',
                    ),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'code_editor',
                    'title'      => __('广告位内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
            ),
            'sanitize'   => false,
            'default'        => array(
                'switch'     => false,
                'loc'        => '1',
                'content'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            ),
        ), 
    )
));

//
// 轮播广告-GO 跳转广告
//
CSF::createSection( $prefix, array(
    'parent'      => 'add-ad',
    'title'       => __('GO 跳转广告','io_setting'),
    'icon'        => 'fa fa-google',
    'fields'      => array(
        //下载弹窗广告位
        array(
            'id'        => 'ad_go_page_content',
            'type'      => 'fieldset',
            'title'     => 'GO 跳转页中间广告',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options'    =>  array(
                        '1'  => 'All',
                        '2'  => '仅移动端',
                        '3'  => '仅PC端',
                    ),
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'content',
                    'type'       => 'code_editor',
                    'title'      => __('广告位内容','io_setting'),
                    'settings'   => array(
                        'theme'  => 'dracula',
                        'mode'   => 'javascript',
                    ),
                    'sanitize'   => false,
                    'class'      => 'compact',
                    'dependency' => array( 'switch', '==', true )
                ),
            ),
            'sanitize'   => false,
            'default'        => array(
                'switch'     => false,
                'loc'        => '1',
                'content'    => '<a href="https://www.iowen.cn/wordpress-version-webstack/" target="_blank"><img src="' . get_theme_file_uri('/images/ad.jpg').'" alt="广告也精彩" /></a>',
            ),
        ), 
    )
));

//
// 用户&安全
//
CSF::createSection( $prefix, array(
    'id'    => 'user_security',
    'title' => __('用户&安全','io_setting'),
    'icon'  => 'fa fa-street-view',
));
//
// 用户&安全-用户注册
//
CSF::createSection( $prefix, array(
    'parent'      => 'user_security',
    'title'       => '用户注册',
    'icon'        => 'fa fa-user-plus',
    'fields'      => array(
        array(
            'type' => 'submessage',
            'style' => 'danger',
            'content' => '<p style="margin:22px 0">'.$tip_ico.'您已关掉用户中心，<b>“用户&安全”</b> 选项卡内设置基本都无效。</p>',
            'dependency' => array( 'user_center', '==', 'false'), 
        ),
        array(
            'id'      => 'user_center',
            'type'    => 'switcher',
            'title'   => __('启用用户中心','io_setting'),
            'label'   => __('同时启用个性化登录页','io_setting'),
            'desc'    => '<p style="color: #e31313;"><i class="fa fa-fw fa-info-circle fa-fw"></i> 启用和禁用<b>[用户中心]</b>后必须<b>重新保存<a href="'.admin_url('options-permalink.php').'">固定链接</a></b></p>'
                    .(get_option('users_can_register')?'':'您的站点已禁止注册，请前往开启“<a href="'.admin_url('options-general.php').'">任何人都可以注册</a>”'),
            'default' => false,
        ),
        array(
            'id'      => 'modify_default_style',
            'type'    => 'switcher',
            'title'   => __('美化默认登录页','io_setting'),
            'default' => true,
            'class'   => 'compact min',
            'dependency' => array( 'user_center', '==', 'false'), 
        ),
        array(
            'id'      => 'mini_panel',
            'type'    => 'switcher',
            'title'   => '右下角 mini 书签按钮','io_setting',
            'default' => true,
            'class'   => 'compact min',
            'dependency' => array( 'user_center', '==', 'true'), 
        ),
        array(
            'content' => '<h4>本页某些设置依赖一下功能：</h4><ol>
            <li><a href="' . io_get_admin_csf_url('用户安全/社交登录') . '">社交登录</a></li>
            <li><a href="' . io_get_admin_csf_url('其他功能/邮箱发信') . '">邮件发信设置</a></li>
            <li><a href="' . io_get_admin_csf_url('其他功能/短信接口') . '">短信接口设置</a></li>
            </ol><p>'.$tip_ico.'开启前请检查相关设置是否配置正确。</p>',
            'style' => 'info',
            'type' => 'submessage',
        ),
        array(
            'type' => 'submessage',
            'style' => 'danger',
            'content' => '<p style="text-align: center">'.$tip_ico.'【注册验证项】至少选一项</p>',
            'dependency' => array( 'reg_verification|reg_type', '==|==', 'true|'), 
        ),
        array(
            'id'      => 'reg_verification',
            'type'    => 'switcher',
            'title'   => __('注册时验证','io_setting'),
            'label'   => __('发送验证码，请先在“其他功能”中配置好发信服务。','io_setting'),
            'default' => false
        ),
        array(
            'id'      => 'reg_type',
            'type'    => 'checkbox',
            'title'   => __('注册验证项','io_setting'),
            'inline'  => true,
            'options' => array(
                'email' => __('邮箱','io_setting'),
                'phone' => __('手机','io_setting'),
            ), 
            'class'   => 'compact min',
            'default' => array('email'),
            'after'   => $tip_ico.'如果都勾选，注册时可任选一项验证。【至少选一项】',
            'dependency' => array( 'reg_verification', '==', 'true', '', 'visible' ), 
        ),
        array(
            'type' => 'submessage',
            'style' => 'info',
            'content' => '<h4>社交登录后是否提示绑定邮箱/手机。</h4><ol><li>不绑定：就是不绑定。</li><li>提醒绑定：提示绑定，并跳转到绑定页，可跳过。</li><li>强制绑定：用户第一次使用社交登录后并未完成注册，需添加邮箱/手机、密码等操作后才能真正完成注册，同时也可以绑定现有账号（比如用户以前用邮箱注册了账号，就可以通过登录以前的账号自动关联社交账户）。</li></ol>
            <p>'.$tip_ico.'如果选择“强制绑定”，用户没有完成绑定前不会插入用户表，同时实现绑定已有账号。</p>
            <p>'.$tip_ico.'此功能需<a href="' . io_get_admin_csf_url('其他功能/邮箱发信') . '">邮件发信设置</a>和<a href="' . io_get_admin_csf_url('其他功能/短信接口') . '">短信接口设置</a>，请提前配置好相关设置。</p>',
        ),
        array(
            'type' => 'submessage',
            'style' => 'danger',
            'content' => '<p style="text-align: center">'.$tip_ico.'【绑定项】至少选一项</p>',
            'dependency' => array( 'bind_email|bind_type', 'any|==', 'bind,must|'), 
        ),
        array(
            'id'        => 'bind_email',
            'type'      => 'button_set',
            'title'     => '绑定设置',
            'options'   => array(
                'null'  => __('不绑定','io_setting'),
                'bind'  => __('提醒绑定','io_setting'),
                'must'  => __('强制绑定','io_setting'),
            ),
            'default'   => 'null',
        ),
        array(
            'id'      => 'bind_type',
            'type'    => 'checkbox',
            'title'   => __('绑定项','io_setting'),
            'inline'  => true,
            'options' => array(
                'email' => __('邮箱','io_setting'),
                'phone' => __('手机','io_setting'),
            ), 
            'class'   => 'compact min',
            'default' => array('email'),
            'after'   => $tip_ico.'如果都勾选，则必须都绑定。【至少选一项】',
            'dependency' => array( 'bind_email', 'any', 'bind,must', '', 'visible' ), 
        ),
        array(
            'id'      => 'remind_bind',
            'type'    => 'switcher',
            'title'   => __('提醒绑定','io_setting'),
            'label'   => __('未绑定的用户，每次登录都提醒绑定','io_setting'),
            'default' => false,
            'class'   => 'compact min',
            'dependency' => array( 'bind_email', 'any', 'bind,must', '', 'visible' ), 
        ),
        array(
            'id'      => 'remind_only',
            'type'    => 'switcher',
            'title'   => __('绑定提醒1次','io_setting'),
            'label'   => __('一天只提醒一次（同一个会话周期）','io_setting'),
            'default' => true,
            'class'   => 'compact min',
            'dependency' => array( 'remind_bind|bind_email', '==|any', 'true|bind,must', '', 'visible' ), 
        ),
        array(
            'type'    => 'switcher',
            'id'      => 'bind_phone',
            'title'   => '手机绑定',
            'label'   => '用户中心显示绑定、修改手机号功能',
            'default' => false,
        ),
        array(
            'id'      => 'user_login_phone',
            'type'    => 'switcher',
            'title'   => '手机号登录',
            'label'   => '允许使用手机号作为用户名登录',
            'default' => false,
        ),
        array(
            'id'       => 'lost_verify_type',
            'type'     => "checkbox",
            'title'    => '找回密码',
            'subtitle' => '找回密码验证方式',
            'inline'  => true,
            'options'  => array(
                'email'       => '邮箱',
                'phone'       => '手机',
            ),
            'default'  => "email",
            'after'    => $tip_ico.'如果都勾选，找回密码时可任选一项验证。【至少选一项】',
        ),
        array(
            'type' => 'submessage',
            'style' => 'danger',
            'content' => '<p style="text-align: center">'.$tip_ico.'【找回密码】至少选一项</p>',
            'dependency' => array( 'lost_verify_type', '==', ''), 
        ),
        array(
            'id'        => 'user_agreement',
            'type'      => 'fieldset',
            'title'     => '用户协议',
            'fields'    => array(
                array(
                    'id'      => 'switch',
                    'type'    => 'switcher',
                    'title'   => __('开关','io_setting'),
                ),
                array(
                    'id'      => 'pact_page',
                    'type'    => 'select',
                    'title'   => '用户协议页面', 
                    'options'    => 'pages',
                    'query_args' => array(
                        'posts_per_page' => -1,
                    ),
                    'desc'       => '新建页面，写入用户协议，然后选择该页面',
                    'class'      => 'compact min',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'         => 'privacy_page',
                    'type'       => 'select',
                    'title'      => '隐私协议页面',
                    'options'    => 'pages',
                    'query_args' => array(
                        'posts_per_page' => -1,
                    ),
                    'desc'       => '新建页面，写入隐私协议，然后选择该页面',
                    'class'      => 'compact min',
                    'dependency' => array( 'switch', '==', true )
                ),
                array(
                    'id'      => 'default',
                    'type'    => 'switcher',
                    'title'   => '默认勾选',
                    'dependency' => array( 'switch', '==', true )
                ),
            ),
            'default' => array(
                'switch'       => false,
                'pact_page'    => '',
                'privacy_page' => '',
                'default'      => false,
            ),
        ), 
        array(
            'id'        => 'user_nickname_stint',
            'type'      => 'textarea',
            'title'     => __('用户昵称限制', 'io_setting'),
            'subtitle'  => __('禁止的昵称关键词', 'io_setting'),
            'desc'      => __('前台注册或修改昵称时，不能使用包含这些关键字的昵称(请用逗号或换行分割)', 'io_setting'),
            'default'   => "赌博,博彩,彩票,性爱,色情,做爱,爱爱,淫秽,傻b,妈的,妈b,admin,test",
            'sanitize'  => false,
        ),
        array(
            'id'        => 'nickname_exists',
            'type'      => 'switcher',
            'title'     => '昵称唯一',
            'label'     => '禁止昵称重复，不允许修改为已存在的昵称',
            'default'   => true,
        ), 
    )
));

//
// 用户&安全-社交登录
//
CSF::createSection( $prefix, array(
    'parent' => 'user_security',
    'title'  => __('社交登录','io_setting'),
    'icon'   => 'fa fa-share-alt-square',
    'fields' => array(
        array(
            'type'    => 'submessage',
            'style'   => 'danger',
            'content' => '<div style="text-align:center"><b><i class="fa fa-fw fa-ban fa-fw"></i> “微信公众号”和“微信开放平台”请二选一即可，不互通。</b></div>',
        ),
        array(
            'id'         => 'open_login_url',  
            'type'       => 'text',
            'title'      => __('登录后返回地址', 'io_setting'),
            'desc'       => '登录后返回的地址，一般是首页或者个人中心页',
            'default'    => esc_url(home_url()),
        ),
        array(
            'id'         => 'open_qq',
            'type'       => 'switcher',
            'title'      => __('qq登录','io_setting'),
        ),
        array(
            'id'        => 'open_qq_key',
            'type'      => 'fieldset',
            'title'     => __('参数设置','io_setting'),
            'fields'    => array(
                array(
                    'content' => '<h4><b>回调地址：</b>' . esc_url(get_template_directory_uri().'/inc/auth/qq-callback.php') . '</h4>QQ登录申请地址：<a target="_blank" href="https://connect.qq.com/">https://connect.qq.com</a>',
                    'style' => 'info',
                    'type' => 'submessage',
                ),
                array(
                    'id'    => 'appid',
                    'type'  => 'text', 
                    'title' => 'APPID',
                ),    
                array(
                    'id'       => 'appkey',
                    'type'     => 'text',
                    'title'    => 'APPKEY',
                    'class' => 'compact min',
                ), 
            ),
            'class'      => 'compact',
            'dependency'   => array( 'open_qq', '==', 'true' ),
        ),
        array(
            'id'         => 'open_weibo',
            'type'       => 'switcher',
            'title'      => __('微博登录','io_setting'),
        ),
        array(
            'id'        => 'open_weibo_key',
            'type'      => 'fieldset',
            'title'     => __('参数设置','io_setting'),
            'fields'    => array(
                array(
                    'content' => '<h4><b>回调地址：</b>' . esc_url(get_template_directory_uri().'/inc/auth/sina-callback.php') . '</h4>微博登录申请地址：<a target="_blank" href="https://open.weibo.com/authentication/">https://open.weibo.com/authentication</a>',
                    'style' => 'info',
                    'type' => 'submessage',
                ),
                array(
                    'id'    => 'appid',
                    'type'  => 'text', 
                    'title' => 'APPID',
                ),    
                array(
                    'id'       => 'appkey',
                    'type'     => 'text',
                    'title'    => 'APPSECRET',
                    'class' => 'compact min',
                ), 
            ),
            'class'      => 'compact',
            'dependency'   => array( 'open_weibo', '==', 'true' ),
        ),
        array(
            'id'        => 'open_weixin_gzh',
            'type'      => 'switcher',
            'title'     => __('微信登录(公众号模式)', 'io_setting'), 
        ),
        array(
            'title'     => '微信公众号登录配置',
            'id'        => 'open_weixin_gzh_key',
            'type'      => 'fieldset',
            'class'     => 'compact',
            'fields'    => array(
                array(
                    'type'      => 'submessage',
                    'style'     => 'info',
                    'content'   => '<h4><b>服务器接口URL：</b></h4>
                    <li>公众号：' . esc_url(get_template_directory_uri().'/inc/auth/gzh-callback.php') . '</li>
                    <li>订阅号：' . esc_url(get_template_directory_uri().'/inc/auth/dyh-callback.php') . '</li><br/>
                    <h4><b>JS接口安全域名：</b>' . preg_replace('/^(?:https?:\/\/)?([^\/]+).*$/im', '$1', home_url()) . '</h4>
                    申请地址：<a target="_blank" href="https://mp.weixin.qq.com/">https://mp.weixin.qq.com/</a>
                    <br />教程：<a target="_blank" href="https://www.iotheme.cn/yiweizhutidisanfangdenglu-wangzhanjieruweixingongzhonghaodenglutuwenjiaocheng.html">查看设置教程</a>
                    <br /><i class="fa fa-fw fa-info-circle"></i> 微信登录请二选一开启',
                ),
                array(
                    'id'       => 'type',
                    'type'     => 'radio',
                    'title'    => '类型',
                    'after'    => '【公众号】为 300元/年 的<b>服务号</b>，其他的都选【订阅号】<br>注意：<b>订阅号</b>即使交了300认证通过，一样只能选【订阅号】',
                    'inline'   => true,
                    'options'  => array(
                        'gzh'   => '公众号',
                        'dyh'   => '订阅号',
                    ),
                    'default'  => 'gzh',
                ), 
                array(
                    'id'    => 'appid',
                    'type'  => 'text',
                    'title' => '公众号AppID',
                    'class' => 'compact min',
                ),
                array(
                    'id'    => 'appkey',
                    'type'  => 'text',
                    'title' => '公众号AppSecret',
                    'class' => 'compact min',
                ),
                array(
                    'id'    => 'token',
                    'type'  => 'text',
                    'title' => '接口验证token',
                    'class' => 'compact min',
                    'desc'  => '此处token用于在微信平台校验服务器URL时使用，自行填写，和微信平台一致即可。 <a target="_blank" href="https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Access_Overview.html">查看说明</a>',
                ),   
                array(
                    'id'        => 'qr_code',
                    'type'      => 'upload',
                    'title'     => '公众号二维码',
                    'class'     => 'compact min',
                    'dependency' => array( 'type', '==', 'dyh' )
                ), 
                array(
                    'id'         => 'subscribe_msg',
                    'type'       => 'textarea',
                    'title'      => '新关注消息',
                    'desc'       => '用户首次扫码关注后自动回复的消息',
                    'class'      => 'compact min',
                    'default'    => '感谢您的关注' .PHP_EOL. home_url(),
                    'attributes' => array(
                        'rows' => 2
                    ),
                    'sanitize'   => false,
                    'dependency' => array( 'type', '==', 'gzh' )
                ),
                array(
                    'id'         => 'scan_msg',
                    'type'       => 'textarea',
                    'title'      => '扫码登录消息',
                    'desc'       => '已经关注的用户扫码登录时候自动回复的消息',
                    'class'      => 'compact min',
                    'default'    => '登录成功' .PHP_EOL. home_url(),
                    'attributes' => array(
                        'rows' => 2
                    ),
                    'sanitize'   => false,
                    'dependency' => array( 'type', '==', 'gzh' )
                ),
                array(
                    'id'         => 'auto_reply',
                    'type'       => 'accordion',
                    'accordions' => array(
                        array(
                            'title'  => '公众号自动回复配置',
                            'fields' => array(
                                array(
                                    'id'           => 'text',
                                    'type'         => 'group',
                                    'title'        => '文本消息自动回复',
                                    'sanitize'     => false,
                                    'button_title' => '添加',
                                    'before'       => '关键字精准回复内容，关键字不要设置“登录”、“登陆”、“绑定”。',
                                    'fields'       => array(
                                        array(
                                            'id'      => 'in',
                                            'type'    => 'text',
                                            'title'   => '关键词',
                                            'default' => '',
                                        ),
                                        array(
                                            'id'      => 'mode', 
                                            'type'    => "radio",
                                            'title'   => '匹配方式',
                                            'class'   => 'compact min',
                                            'help'    => "包含：收到的消息中含有设置的关键词，等于：收到的消息与设置的关键词完全相同",
                                            'options' => array(
                                                'include' => '包含关键词',
                                                'same'    => '等于关键词',
                                            ),
                                            'inline'  => true,
                                            'default' => 'include',
                                        ),
                                        array(
                                            'id'         => 'out',
                                            'type'       => 'textarea',
                                            'title'      => '回复内容',
                                            'attributes' => array(
                                                'rows' => 1,
                                            ),
                                            'sanitize'   => false,
                                            'class'      => 'compact min',
                                        ),
                                    ),
                                ),
                                array(
                                    'id'         => 'image',
                                    'type'       => 'textarea',
                                    'title'      => '图片消息自动回复',
                                    'attributes' => array(
                                        'rows' => 1,
                                    ),
                                    'sanitize'   => false,
                                    'class'      => 'compact min',
                                ),
                                array(
                                    'id'         => 'voice',
                                    'type'       => 'textarea',
                                    'title'      => '语音消息自动回复',
                                    'attributes' => array(
                                        'rows' => 1,
                                    ),
                                    'sanitize'   => false,
                                    'class'      => 'compact min',
                                ),
                                array(
                                    'id'         => 'default',
                                    'type'       => 'textarea',
                                    'title'      => '其他消息自动回复',
                                    'attributes' => array(
                                        'rows' => 1,
                                    ),
                                    'sanitize'   => false,
                                    'class'      => 'compact min',
                                ),
                            ),
                        ),
                        array(
                            'title'  => '公众号自定义菜单配置',
                            'fields' => array(
                                array(
                                    'type'    => 'content',
                                    'content' => '<div class="options-notice"><div class="explain">
                                    <h4>微信登录功能正常后，请在此设置微信自定义菜单</h4>
                                    <li>在下方粘贴公众号自定义菜单的json配置代码后提交即可</li>
                                    <li>设置成功后会有几分钟的延迟才能生效，请耐心等待</li>
                                    <li>设置菜单选项请移步<a target="_blank" href="https://wei.jiept.com">wei.jiept.com</a>生成json文件</li>
                                    <ajaxform class="ajax-form" ajax-url="' . admin_url("admin-ajax.php") . '">
                                        <p><textarea class="not-change" ajax-name="json" row="4" placeholder="请粘贴菜单的json配置代码" style="width:100%;height:168px">'.str_replace('\"','"',base64_decode(get_option( 'io_gzh_menu_json', '' ))).'</textarea>
                                        注意：微信官方限制，未认证的订阅号由于权限不足没法通过此处设置菜单。</p>
                                        <div class="ajax-notice"></div>
                                        <p><a href="javascript:;" class="button button-primary ajax-submit"><i class="fa fa-paper-plane-o"></i> 设置自定义菜单</a></p>
                                        <input type="hidden" ajax-name="action" value="set_weixin_gzh_menu">
                                    </ajaxform>
                                </div></div>',
                                )
                            ),
                        ),
                    ),
                ),
            ),
            'dependency' => array('open_weixin_gzh', '==', 'true'),
        ), 
        array(
            'id'         => 'open_wechat',
            'type'       => 'switcher',
            'title'      => __('微信登录(开放平台模式)','io_setting'),
        ),
        array(
            'id'        => 'open_wechat_key',
            'type'      => 'fieldset',
            'title'     => __('微信登录参数设置','io_setting'),
            'fields'    => array(
                array(
                    'content' => '<h4><b>开放平台回调地址：</b>' . parse_url(home_url())['host'] . '</h4>
                    微信登录申请地址：<a target="_blank" href="https://open.weixin.qq.com/">https://open.weixin.qq.com</a>
                    <br /><i class="fa fa-fw fa-info-circle fa-fw"></i> 微信登录请三选一开启',
                    'style' => 'info',
                    'type' => 'submessage',
                ),
                array(
                    'id'    => 'appid',
                    'type'  => 'text', 
                    'title' => 'APPID',
                ),    
                array(
                    'id'       => 'appkey',
                    'type'     => 'text',
                    'title'    => 'APPSECRET',
                    'class'    => 'compact min',
                ), 
            ),
            'class'      => 'compact',
            'dependency'   => array( 'open_wechat', '==', 'true' ),
        ),
        array(
            'id'    => 'open_baidu',
            'type'  => 'switcher',
            'title' => __('百度登录', 'io_setting'),
        ),
        array(
            'id'         => 'open_baidu_key',
            'type'       => 'fieldset',
            'title'      => '百度参数配置',
            'fields'     => array(
                array(
                    'content' => '<h4><b>回调地址：</b>' . esc_url(get_template_directory_uri().'/inc/auth/baidu-callback.php') . '</h4>百度登录申请地址：<a target="_blank" href="http://developer.baidu.com/">http://developer.baidu.com</a>',
                    'style'   => 'info',
                    'type'    => 'submessage',
                ),
                array(
                    'id'    => 'appid',
                    'type'  => 'text',
                    'title' => 'API Key',
                ),
                array(
                    'id'    => 'appkey',
                    'type'  => 'text',
                    'title' => 'Secret Key',
                    'class' => 'compact min',
                ),
            ),
            'class'      => 'compact',
            'dependency' => array('open_baidu', '==', 'true'),
        ),
        array(
            'id'    => 'open_gitee',
            'type'  => 'switcher',
            'title' => __('码云(gitee)登录', 'io_setting'),
        ),
        array(
            'id'         => 'open_gitee_key',
            'type'       => 'fieldset',
            'title'      => '码云(gitee)参数配置',
            'fields'     => array(
                array(
                    'type'    => 'submessage',
                    'style'   => 'info',
                    'content' => '<h4><b>回调地址：</b>' . esc_url(get_template_directory_uri().'/inc/auth/gitee-callback.php') . '</h4>
                    码云(gitee)登录申请地址：<a target="_blank" href="https://gitee.com/oauth/applications/">https://gitee.com/oauth/applications</a>',
                    ),
                array(
                    'id'    => 'appid',
                    'type'  => 'text',
                    'title' => 'AppID',
                ),
                array(
                    'id'    => 'appkey',
                    'type'  => 'text',
                    'title' => 'AppKey',
                    'class' => 'compact min',
                ),
            ),
            'class'      => 'compact',
            'dependency' => array('open_gitee', '==', 'true'),
        ),
        array(
            'id'    => 'open_github',
            'type'  => 'switcher',
            'title' => __('GitHub登录', 'io_setting'),
        ),
        array(
            'id'         => 'open_github_key',
            'type'       => 'fieldset',
            'title'      => 'GitHub参数配置',
            'fields'     => array(
                array(
                    'content' => '<h4><b>回调地址：</b>' . esc_url(get_template_directory_uri().'/inc/auth/github-callback.php') . '</h4>
                    GitHub登录申请地址：<a target="_blank" href="https://github.com/settings/developers">https://github.com/settings/developers</a>',
                    'style'   => 'info',
                    'type'    => 'submessage',
                ),
                array(
                    'id'    => 'appid',
                    'type'  => 'text',
                    'title' => 'AppID',
                ),
                array(
                    'id'    => 'appkey',
                    'type'  => 'text',
                    'title' => 'AppKey',
                    'class' => 'compact min',
                ),
            ),
            'class'      => 'compact',
            'dependency' => array('open_github', '==', 'true'),
        ),
        array(
            'id'         => 'open_prk',
            'type'       => 'switcher',
            'title'      => '聚合登录',
        ),
        array(
            'id'        => 'open_prk_key',
            'type'      => 'fieldset',
            'title'     => __('聚合登录参数设置','io_setting'),
            'fields'    => array(
                array(
                    'content'   => '<h4>免一切授权验证等步骤，只需要在聚合登录注册就可以使用qq、微信、微博等登录方式。</h4>
                    <b>接口申请：</b><a target="_blank" href="https://iologin.cc/">查找</a>',
                    'style'     => 'info',
                    'type'      => 'submessage',
                ),
                array(
                    'id'    => 'apiurl',
                    'type'  => 'text', 
                    'title' => 'API地址',
                ), 
                array(
                    'id'    => 'appid',
                    'type'  => 'text', 
                    'title' => 'APPID',
                    'class' => 'compact min',
                ),    
                array(
                    'id'    => 'appkey',
                    'type'  => 'text',
                    'title' => 'APPKEY',
                    'class' => 'compact min',
                ), 
            ),
            'default'  => array(
                'apiurl'    => 'https://iologin.cc/',
            ),
            'class'      => 'compact',
            'dependency'   => array( 'open_prk', '==', 'true' ),
        ),
        array(
            'id'         => 'open_prk_list',
            'type'       => 'checkbox', 
            'title'      => '聚合登录启用项',
            'options'    => $prk_list,
            'inline'     => true,
            'class'      => 'compact',
            'dependency' => array( 'open_prk', '==', 'true' ),
        ),
    )
));

//
// 用户&安全-安全相关
//
CSF::createSection( $prefix, array(
    'parent'      => 'user_security',
    'title'       => '安全相关',
    'icon'        => 'fa fa-shield',
    'fields'      => array(
        array(
            'id'        => 'io_administrator',
            'type'      => 'fieldset',
            'title'     => '禁止冒充管理员留言',
            'fields'    => array(
                array(
                    'id'    => 'admin_name',
                    'type'  => 'text',
                    'title' => __('管理员名称','io_setting'),
                ),
                array(
                    'id'    => 'admin_email',
                    'type'  => 'text',
                    'title' => __('管理员邮箱','io_setting'),
                    'class' => 'compact min',
                ),
            ),
            'default'  => array(
                'admin_email'    => get_option( 'admin_email' ),
            ),
        ),
        array(
            'id'        => 'io_comment_set',
            'type'      => 'fieldset',
            'title'     => '评论过滤',
            'fields'    => array(
                array(
                    'id'    => 'no_url',
                    'type'  => 'switcher',
                    'title' => __('评论禁止链接','io_setting'),
                ),
                array(
                    'id'    => 'no_chinese',
                    'type'  => 'switcher',
                    'title' => __('评论必须包含汉字','io_setting'),
                    'class' => 'compact min',
                ),
            ),
            'default'  => array(
                'no_url'        => true,
                'no_chinese'    => false,
            ),
        ),
        array(
            'id'      => 'bookmark_share',
            'type'    => 'switcher',
            'title'   => __('禁用“用户个人书签页”分享','io_setting'),
            'label'   => __('全局开关，避免非法地址影响域名安全','io_setting'),
            'default' => true,
        ),
        array(
            'id'       => 'captcha_type',
            'type'     => "select",
            'title'    => '人机验证类型',
            'options'  => array(
                'null'     => '关闭',
                'image'    => '图片验证码',
                'slider'   => '滑块验证码',
                'tcaptcha' => '腾讯图形验证码',
                'geetest'  => '极验行为验4.0',
                'vaptcha'  => 'VAPTCHA',
            ),
            'default'  => 'null',
            'after'    => $tip_ico.'注意：切换后请刷新静态缓存、cdn缓存等各种缓存',
        ),
        array(
            'id'        => 'tcaptcha_option',
            'type'      => 'fieldset',
            'title'     => '腾讯图形验证码',
            'class'     => 'compact',
            'fields'    => array(   
                array(
                    'style'   => 'info',
                    'type'    => 'submessage',
                    'content' => '<span style="color:#f00">开启后，请认真填写，填错会造成无法登录后台</span><br/>
                    申请地址：<a target="_blank" href="https://console.cloud.tencent.com/captcha/graphical">腾讯图形验证码【腾讯防水墙】</a>',
                ),
                array(
                    'id'    => 'appid',
                    'type'  => 'text',
                    'title' => '验证码CaptchaAppId',
                ),
                array(
                    'id'    => 'secret_key',
                    'type'  => 'text',
                    'title' => '验证码AppSecretKey',
                    'after' => __('请填写完整，包括后面的**','io_setting'),
                    'class' => 'compact min',
                ),
                //https://console.cloud.tencent.com/cam/capi
                array(
                    'id'    => 'api_secret_id',
                    'type'  => 'text',
                    'title' => 'API密钥SecretId',
                    'after' => $tip_ico.'注意：以前的【腾讯防水墙】版本请留空',
                ),
                array(
                    'id'    => 'api_secret_key',
                    'type'  => 'text',
                    'title' => 'API密钥SecretKey',
                    'class' => 'compact min',
                    'after' => $tip_ico.'注意：以前的【腾讯防水墙】版本请留空',
                ),

                array(
                    'type'    => 'submessage',
                    'style'   => 'danger',
                    'content' => '如果开启人机验证后进不了后台，请将主题文件‘functions.php’里“LOGIN_007”的 true 改为 false 。',
                ),
            ),
            'dependency'=> array( 'captcha_type', '==', 'tcaptcha' ),
        ), 
        array(
            'id'         => 'geetest_option',
            'type'       => 'fieldset',
            'title'      => '极验行为参数 ',
            'class'      => 'compact',
            'fields'     => array(
                array(
                    'style'   => 'info',
                    'type'    => 'submessage',
                    'content' => '<span style="color:#f00">开启后，请认真填写，填错会造成无法登录后台</span><br/>
                    申请地址：<a target="_blank" href="https://www.geetest.com">极验行为验官网</a>',
                ),
                array(
                    'id'    => 'id',
                    'type'  => 'text',
                    'title' => '验证 Id',
                ),
                array(
                    'id'    => 'key',
                    'type'  => 'text',
                    'title' => '验证 Key',
                    'class' => 'compact min',
                ),
                array(
                    'style'   => 'danger',
                    'type'    => 'submessage',
                    'content' => '如果开启人机验证后进不了后台，请将主题文件‘functions.php’里“LOGIN_007”的 true 改为 false 。',
                ),
            ),
            'dependency' => array('captcha_type', '==', 'geetest'),
        ),
        array(
            'id'         => 'vaptcha_option',
            'type'       => 'fieldset',
            'title'      => 'VAPTCHA参数',
            'class'      => 'compact',
            'fields'     => array(
                array(
                    'style'   => 'info',
                    'type'    => 'submessage',
                    'content' => '<span style="color:#f00">开启后，请认真填写，填错会造成无法登录后台</span><br/>
                    申请地址：<a target="_blank" href="https://www.vaptcha.com/">VAPTCHA验证</a>',
                ),
                array(
                    'id'    => 'id',
                    'type'  => 'text',
                    'title' => 'VID',
                ),
                array(
                    'id'    => 'key',
                    'type'  => 'text',
                    'title' => 'KEY',
                    'class' => 'compact min',
                ),
                array(
                    'style'   => 'danger',
                    'type'    => 'submessage',
                    'content' => '如果开启人机验证后进不了后台，请将主题文件‘functions.php’里“LOGIN_007”的 true 改为 false 。',
                ),
            ),
            'dependency' => array('captcha_type', '==', 'vaptcha'),
        ),
		array(
				'id'		 => 'login_limit',
				'type'       => 'spinner',
				'title'      => '登录失败尝试限制',
				'subtitle'	 => '尝试次数',
				'after'      => '默认5次，表示失败5次后要过10分钟才能再次尝试登录。<br>'.$tip_ico.'0为不限制次数',
				'unit'       => '次',
				'default'    => 5,
				'class'      => '',
		),
		array(
				'id'		 => 'login_limit_time',
				'type'       => 'spinner',
				'title'      => '登录失败重试频率',
				'after'      => '默认10分钟，表示失败5次后要过10分钟才能再次尝试登录。',
				'unit'       => '分钟',
				'default'    => 10,
				'class'      => 'compact min',
		),
    )
));

//
// 用户&安全-用户投稿
//
CSF::createSection( $prefix, array(
    'parent'      => 'user_security',
    'title'       => '用户投稿',
    'icon'        => 'fas fa-edit',
    'fields'      => array(
        array(
            'id'      => 'is_contribute',
            'type'    => 'switcher',
            'title'   => '前端投稿',
            'default' => true,
            'label'   => '此功能启用后，下面选项才有意义。',
        ),
        //array(
        //    'id'             => 'contribute_pages',
        //    'type'           => 'select',
        //    'title'          => '投稿页面',
        //    'after'          => __(' 如果没有，新建页面，选择“投稿模板”模板并保存。','io_setting'),
        //    'options'        => 'pages',
        //    'query_args'     => array(
        //        'posts_per_page'  => -1,
        //    ),
        //    'placeholder'    => __('选择投稿页面', 'io_setting'), 
        //),
        array(
            'id'           => 'contribute_can',
            'type'         => 'button_set',
            'title'        => '可投稿用户组',
            'options'      => array(
                'all'   => __('所有人','io_setting'),
                'user'  => __('登录用户','io_setting'),
                'admin' => __('仅管理员','io_setting'),
            ),
            'default'   => 'user',
        ),
        array(
            'id'          => 'contribute_time',
            'type'        => 'spinner',
            'title'       => '投稿间隔时间',
            'min'         => 0,
            'unit'        => '秒',
            'default'     => 30,
            'after'       => '填 0 不限制。'
        ),
        array(
            'id'      => 'tag_temp',
            'type'    => 'switcher',
            'title'   => '标签插入正文',
            'default' => true,
            'label'   => '将标签插入正文，避免直接在站点生成标签地址（因为 wp 的标签没有审核状态）。审核发布时需手动将正文中的“标签”剪切到标签选项。',
            'after'   => '<br><br>如果对应文章类型开启了【直接发布】，则此项无效。'
        ),
        array( #TODO: 待完善
            'id'           => 'contribute_type',
            'type'         => 'checkbox',
            'title'        => '可投稿类型',
            'inline'       => true,
            'options'      => array(
                'post'   => __('文章','io_setting'),
                'sites'  => __('网址','io_setting'),
                //'app'    => __('APP','io_setting'),
                //'book'   => __('书籍','io_setting'),
            ),
            'default'   => 'sites',
        ),
        array(
            'id'        => 'sites_tg_opt',
            'type'      => 'fieldset',
            'title'     => '网址投稿选项',
            'fields'    => array(
                array(
                    'id'      => 'is_publish',
                    'type'    => 'switcher',
                    'title'   => __('投稿直接发布', 'io_setting'),
                    'label'   => __('投稿的“网址”不需要审核直接发布', 'io_setting'),
                ),
                array(
                    'id'          => 'auto_category',
                    'type'        => 'select',
                    'title'       => __('免审核投稿分类', 'io_setting'),
                    'after'       => __('不审核直接发布到指定分类，如果设置此项，前台投稿页的分类选择将失效。', 'io_setting'),
                    'placeholder' => __('选择分类','io_setting'),
                    'options'     => 'categories',
                    'class'       => 'compact',
                    'query_args'  => array( 
                        'taxonomy'    => array('favorites'),
                    ),
                    'dependency'  => array( 'is_publish', '==', true )
                ),
                array(
                    'id'          => 'cat',
                    'type'        => 'select',
                    'title'       => '网址可投稿分类',
                    'chosen'      => true,
                    'multiple'    => true,
                    'placeholder' => '选择分类',
                    'options'     => 'categories',
                    'query_args'  => array(
                        'taxonomy'  => 'favorites',
                    ), 
                    'after'       => __('不选则为所有分类', 'io_setting'),
                ),
                array(
                    'id'          => 'tag_limit',
                    'type'        => 'spinner',
                    'title'       => '标签数量限制',
                    'min'         => 0,
                    'unit'        => '个',
                    'after'       => '填 0 不限制。',
                    'default'     => 5
                ),
                array(
                    'id'         => 'img_size',
                    'type'       => 'spinner',
                    'title'      => __('网址投稿图标大小', 'io_setting'),
                    'max'        => 128,
                    'min'        => 16,
                    'step'       => 2,
                    'unit'       => 'kb',
                    'after'      => '默认 64kb<br>'.$tip_ico.'填 0 关闭图标上传。',
                    'default'    => 64,
                ),
            ),
            'default'   => array( 
                'is_publish'        => false,
                'tag_limit'         => 5,
                'img_size'          => 64,
            ),
            'dependency'  => array( 'contribute_type', 'any', 'sites' )
        ),
        array(
            'id'        => 'post_tg_opt',
            'type'      => 'fieldset',
            'title'     => '文章投稿选项',
            'fields'    => array(
                array(
                    'id'      => 'is_publish',
                    'type'    => 'switcher',
                    'title'   => __('投稿直接发布', 'io_setting'),
                    'label'   => __('投稿的“文章”不需要审核直接发布', 'io_setting'),
                ),
                array(
                    'id'          => 'auto_category',
                    'type'        => 'select',
                    'title'       => __('免审核投稿分类', 'io_setting'),
                    'after'       => __('不审核直接发布到指定分类，如果设置此项，前台投稿页的分类选择将失效。', 'io_setting'),
                    'placeholder' => __('选择分类','io_setting'),
                    'options'     => 'categories',
                    'class'       => 'compact',
                    'dependency'  => array( 'is_publish', '==', true )
                ),
                array(
                    'id'            => 'title_limit',
                    'type'          => 'dimensions',
                    'title'         => '标题字数限制',
                    'width_icon'    =>'最少',
                    'height_icon'   =>'最多',
                    'units'         => array( '字' ),
                ),
                array(
                    'id'          => 'tag_limit',
                    'type'        => 'spinner',
                    'title'       => '标签数量限制',
                    'min'         => 0,
                    'unit'        => '个',
                    'after'       => '填 0 不限制。'
                ),
                array(
                    'id'          => 'cat',
                    'type'        => 'select',
                    'title'       => '文章可投稿分类',
                    'chosen'      => true,
                    'multiple'    => true,
                    'placeholder' => '选择分类',
                    'options'     => 'categories',
                    'after'       => __('不选则为所有分类', 'io_setting'),
                ),
                array(
                    'id'      => 'img_size',
                    'type'    => 'spinner',
                    'title'   => '前端图像上传限制',
                    'default' => 1,
                    'desc'    => '前端允许上传的最大图像大小',
                    'after'   => $tip_ico.'填 0 关闭图标上传。',
                    'max'     => 5120,
                    'min'     => 0,
                    'unit'    => 'kb',
                ),
            ),
            'default'   => array( 
                'is_publish'   => false,
                'tag_limit'    => 5,
                'img_size'     => 1024,
                'title_limit'  => array(
                    'width'  => 5,
                    'height' => 30,
                ),
            ),
            'dependency'  => array( 'contribute_type', 'any', 'post' )
        ),
        array(
            'type'        => 'content',
            'content'     => 'APP 待完善',
            'dependency'  => array( 'contribute_type', 'any', 'app' )
        ),
        array(
            'type'        => 'content',
            'content'     => '书籍待完善',
            'dependency'  => array( 'contribute_type', 'any', 'book' )
        ),
    )
));

//
// 商城设置
//
CSF::createSection($prefix, array(
    'id'    => 'io_pay',
    'title' => '商城设置',
    'icon'  => 'fa fa-cart-plus',
));

//
// 页脚设置
//
CSF::createSection( $prefix, array(
    'title'    => __('页脚设置','io_setting'),
    'icon'     => 'fa fa-caret-square-o-down',
    'fields'   => array( 
        array(
            'id'          => 'footer_copyright',
            'type'        => 'wp_editor',
            'title'       => __('自定义页脚版权','io_setting'),
            'height'      => '100px',
            'sanitize'    => false,
        ),
        array(
            'id'     => 'icp',
            'type'   => 'text',
            'title'  => ' ', 
            'subtitle' => '备案号',
            'after'   => '<i class="fa fa-fw fa-info-circle fa-fw"></i> 此选项“自定义页脚版权”非空则禁用',
            'class'    => 'compact',
            'dependency'  => array( 'footer_copyright', '==', '', '', 'visible' ),
        ),
        array(
            'id'     => 'police_icp',
            'type'   => 'text',
            'title'  => ' ', 
            'subtitle' => '公安备案号',
            'after'   => '<i class="fa fa-fw fa-info-circle fa-fw"></i> 此选项“自定义页脚版权”非空则禁用',
            'dependency'  => array( 'footer_copyright', '==', '', '', 'visible' ),
            'class'    => 'compact',
        ),

		array(
			'id'	  	=> 'io_aff',
			'type'		=> 'switcher',
			'title'   	=> __('显示一为推广按钮','io_setting'),
			'label'		=> __('添加推广链接获取佣金','io_setting'),
            'after'   	=> '<br><p>显示： 由 OneNav 强力驱动</p>',
			'default'	=> true,
		),
        array(
            'id'     	=> 'io_id',
            'type'   	=> 'text',
            'title'  	=> '一为推广ID', 
            'after'   	=> 'iotheme.cn上的用户ID，6位数纯数字ID。<a href="https://www.iotheme.cn/user" target="_blank">获取ID</a>',
            'dependency'  => array( 'io_aff', '==', true),
            'class'    	=> 'compact',
        ),

        array(
            'id'            => 'footer_statistics',
            'type'          => 'wp_editor',
            'title'         => __('统计代码','io_setting'),
            'tinymce'       => false,
            'quicktags'     => true,
            'media_buttons' => false,
            'height'        => '100px',
            'sanitize'      => false,
            'after'         => '<i class="fa fa-fw fa-info-circle fa-fw"></i> 注意：显示在页脚的统计代码，如需要添加到 &lt;/head&gt; 前，请到“添加代码”中添加。',
        ),

        array(
            'id'      => 'footer_layout',
            'type'    => "image_select",
            'title'   => '页脚布局',
            'options' => array(
                'def'   => get_theme_file_uri('/images/option/op_footer_layout_def.png'),
                'big'   => get_theme_file_uri('/images/option/op_footer_layout_big.png'),
            ),
            'default' => "def",
            'class'   => '',
        ),
        
        array(
            'id'      => 'footer_t1',
            'type'    => 'switcher',
            'title'   => '板块一',
            'help'    => '如果不勾选则仅仅在电脑端显示此板块',
            'default' => true,
            'label'   => '移动端显示',
            'class'   => '',
            'dependency'  => array( 'footer_layout', '==', 'big'),
        ),

        array(
            'id'         => 'footer_t1_code',
            'type'       => 'textarea',
            'title'      => ' ',
            'subtitle'   => '更多内容',
            'class'      => 'compact',
            'default'    => 'OneNav 一为导航主题，集网址、资源、资讯于一体的 WordPress 导航主题，简约优雅的设计风格，全面的前端用户功能，简单的模块化配置，欢迎您的体验',
            'sanitize'   => false,
            'attributes' => array(
                'rows' => 3,
            ),
            'dependency'  => array( 'footer_layout', '==', 'big'),
        ),

        array(
            'id'         => 'footer_t2_code',
            'type'       => 'textarea',
            'title'      => '板块二',
            'subtitle'   => '建议为友情链接，或者站内链接',
            'default'    => '<a href="https://www.iotheme.cn">友链申请</a>'.PHP_EOL.'<a href="https://www.iotheme.cn">免责声明</a>'.PHP_EOL.'<a href="https://www.iotheme.cn">广告合作</a>'.PHP_EOL.'<a href="https://www.iotheme.cn">关于我们</a>',
            'sanitize'   => false,
            'attributes' => array(
                'rows' => 4,
            ),
            'class'   => '',
            'dependency'  => array( 'footer_layout', '==', 'big'),
        ),
        array(
            'id'          => 'footer_t2_nav',
            'type'        => 'select',
            'title'       => ' ',
            'subtitle'    => '请选择菜单',
            'placeholder' => '选择菜单',
            'after'       => '<i class="fa fa-fw fa-info-circle fa-fw"></i> 推荐4个一级菜单加各3个子菜单',
            'options'     => 'menus',
            'class'       => 'compact',
            'dependency'  => array( 'footer_layout', '==', 'big' ),
        ),
        array(
            'id'        => 'footer_social',
            'type'      => 'group',
            'title'     => ' ', 
            'subtitle'  => '社交信息',
            'class'     => 'compact',
            'before'    => '<i class="fa fa-fw fa-info-circle fa-fw"></i> 页脚悬浮小工具也在此处设置',
            'fields'    => array(
                array(
                    'id'    => 'name',
                    'type'  => 'text',
                    'title' => '名称',
                ),
                array(
                    'id'      => 'loc',
                    'type'    => 'button_set',
                    'title'   => __('显示位置','io_setting'), 
                    'options' =>  array(
                        'all'       => 'All',
                        'footer'    => '仅 footer',
                        'tools'     => '仅悬浮小工具',
                    ),
                    'class'   => 'compact min',
                    'default' => 'footer',
                ),
                array(
                    'id'        => 'ico',
                    'type'      => 'icon',
                    'title'     => '图标', 
                    'default'   => 'iconfont icon-related',
                    'class'     => 'compact min'
                ),
                array(
                    'id'        => 'type',
                    'type'      => 'button_set',
                    'title'     => __('类型','io_setting'),
                    'options'   => array(
                        'url'       => 'URL连接',
                        'img'       => '图片弹窗（如微信二维码）',
                    ),
                    'default'   => 'url',
                    'class'      => 'compact min'
                ),
                array(
                    'id'    => 'url',
                    'type'  => 'text',
                    'title' => '地址：',
                    'after' => '<p class="cs-text-muted">【图片弹窗】请填图片地址<br><i class="fa fa-fw fa-info-circle fa-fw"></i> 如果要填QQ，请转换为URL地址，格式为：<br><code>http://wpa.qq.com/msgrd?V=3&uin=xxxxxxxx&Site=QQ&Menu=yes</code><br>将xxxxxx改为您自己的QQ号</p>',
                    'class'      => 'compact min'
                ),
            ), 
            'default'   => array(
                array(
                    'name'  => '微信',
                    'loc'   => 'all',
                    'ico'   => 'iconfont icon-wechat',
                    'type'  => 'img',
                    'url'   => get_theme_file_uri('/images/wechat_qrcode.png'),
                ),
                array(
                    'name'  => 'QQ',
                    'loc'   => 'all',
                    'ico'   => 'iconfont icon-qq',
                    'type'  => 'url',
                    'url'   => 'http://wpa.qq.com/msgrd?V=3&uin=xxxxxxxx&Site=QQ&Menu=yes',
                ),
                array(
                    'name'  => '微博',
                    'loc'   => 'footer',
                    'ico'   => 'iconfont icon-weibo',
                    'type'  => 'url',
                    'url'   => 'https://www.iotheme.cn',
                ),
                array(
                    'name'  => 'GitHub',
                    'loc'   => 'footer',
                    'ico'   => 'iconfont icon-github',
                    'type'  => 'url',
                    'url'   => 'https://www.iotheme.cn',
                ),
                array(
                    'name'  => 'Email',
                    'loc'   => 'footer',
                    'ico'   => 'iconfont icon-email',
                    'type'  => 'url',
                    'url'   => 'mailto:1234567788@QQ.COM',
                )
            ), 
        ),

        array(
            'id'      => 'footer_t3',
            'type'    => 'switcher',
            'title'   => '板块三', 
            'label'   => __('移动端显示'),
            'class'   => '',
            'dependency'  => array( 'footer_layout', '==', 'big' ),
        ),

        array(
            'id'           => 'footer_t3_img',
            'type'         => 'group',
            'max'          => 4,
            'button_title' => '添加图片',
            'class'        => 'compact',
            'title'        => ' ',
            'subtitle'     => '页脚图片',
            'placeholder'  => '显示在板块3的图片内容',
            'fields'       => array(
                array(
                    'id'    => 'text',
                    'type'  => 'text',
                    'title' => '显示文字',
                ),
                array(
                    'id'      => 'image',
                    'type'    => 'upload',
                    'title'   => '显示图片',
                    'library' => 'image', 
                    'class'   => 'compact min'
                ),
            ),
            'default'      => array(
                array(
                    'image' => get_theme_file_uri('/images/qr.png'),
                    'text'  => '扫码加QQ群',
                ),
                array(
                    'image' => get_theme_file_uri('/images/qr.png'),
                    'text'  => '扫码加微信',
                ),
            ),
            'dependency'  => array( 'footer_layout', '==', 'big'),
        ),

        array(
            'id'          => 'down_statement',
            'type'        => 'wp_editor',
            'title'       => __('下载页版权声明','io_setting'),
            'default'     => __('本站大部分下载资源收集于网络，只做学习和交流使用，版权归原作者所有。若您需要使用非免费的软件或服务，请购买正版授权并合法使用。本站发布的内容若侵犯到您的权益，请联系站长删除，我们将及时处理。','io_setting'),
            'height'      => '100px',
            'sanitize'    => false,
        ),
    )
));

//
// 添加代码
//
CSF::createSection( $prefix, array(
    'title'       => '添加代码',
    'icon'        => 'fa fa-code',
    'fields'      => array(
        array(
            'id'       => 'custom_css',
            'type'     => 'code_editor',
            'title'    => '自定义样式css代码',
            'subtitle' => '显示在网站头部 &lt;head&gt;',
            'after'    => '<p class="cs-text-muted">'.__('自定义 CSS,自定义美化...<br>如：','io_setting').'body .test{color:#ff0000;}<br><span style="color:#f00">'.__('注意：','io_setting').'</span>'.__('不要填写','io_setting').'<strong>&lt;style&gt; &lt;/style&gt;</strong></p>',
            'settings' => array(
                'tabSize' => 2,
                'theme'   => 'mbo',
                'mode'    => 'css',
            ),
            'sanitize' => false,
        ),
        array(
            'id'       => 'code_2_header',
            'type'     => 'code_editor',
            'title'    => '顶部(header)自定义 js 代码',
            'subtitle' => '显示在网站 &lt;/head&gt; 前',
            'after'    => '<p class="cs-text-muted">'.__('出现在网站顶部 &lt;/head&gt; 前。','io_setting').'<br><span style="color:#f00">'.__('注意：','io_setting').'</span>'.__('必须填写','io_setting').'<strong>&lt;script&gt; &lt;/script&gt;</strong></p>',
            'settings' => array(
                'theme'  => 'dracula',
                'mode'   => 'javascript',
            ),
            'sanitize' => false,
            'class'   => '',
        ),
        array(
            'id'       => 'code_2_footer',
            'type'     => 'code_editor',
            'title'    => '底部(footer)自定义 js 代码',
            'subtitle' => '显示在网站底部',
            'after'    => '<p class="cs-text-muted">'.__('出现在网站底部 body 前。','io_setting').'<br><span style="color:#f00">'.__('注意：','io_setting').'</span>'.__('必须填写','io_setting').'<strong>&lt;script&gt; &lt;/script&gt;</strong></p>',
            'settings' => array(
                'theme'  => 'dracula',
                'mode'   => 'javascript',
            ),
            'sanitize' => false,
        ),
    )
));
//
// 优化设置
//
CSF::createSection( $prefix, array(
    'id'    => 'optimize',
    'title' => __('优化设置','io_setting'),
    'icon'  => 'fa fa-rocket',
));
  
//
// 优化设置-禁用功能
//
CSF::createSection( $prefix, array(
    'parent'      => 'optimize',
    'title'       => __('禁用功能','io_setting'),
    'icon'        => 'fa fa-wordpress',
    'fields'      => array( 
        array(
            'type'    => 'submessage',
            'style'   => 'danger',
            'content' => '<p style="font-size:18px">'.$tip_ico.'注意</p>
            <p>如果不了解下面选项的作用，请保持原样！</p>',
        ),
        array(
            'id'      => 'disable_auto_update',
            'type'    => 'switcher',
            'title'   => __('屏蔽自动更新','io_setting'),
            'label'   => __('关闭自动更新功能，通过手动更新。','io_setting'),
            'text_on' => '已屏蔽',
            'text_off'=> '未屏蔽',
            'text_width' => 80,
            'default' => false,
        ),
        array(
            'id'      => 'disable_rest_api',
            'type'    => 'switcher',
            'title'   => __('禁用REST API','io_setting'),
            'label'   => __('禁用REST API、移除wp-json链接（默认关闭，如果你的网站没有做小程序或是APP，建议禁用REST API）','io_setting'),
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => false
        ),
        
        array(
            'id'      => 'disable_revision',
            'type'    => 'switcher',
            'title'   => __('禁用文章修订功能','io_setting'),
            'label'   => __('禁用文章修订功能，精简 Posts 表数据。(如果古滕堡报错，请关闭该选项)','io_setting'),
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => false
        ),
 
        
        array(
            'id'      => 'disable_texturize',
            'type'    => 'switcher',
            'title'   => __('禁用字符转码','io_setting'),
            'label'   => __('禁用字符换成格式化的 HTML 实体功能。','io_setting'),
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => true
        ),

        array(
            'id'      => 'disable_feed',
            'type'    => 'switcher',
            'title'   => __('禁用站点Feed','io_setting'),
            'label'   => __('禁用站点Feed，防止文章快速被采集。','io_setting'),
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => true
        ),

        array(
            'id'      => 'disable_trackbacks',
            'type'    => 'switcher',
            'title'   => __('禁用Trackbacks','io_setting'),
            'label'   => __('Trackbacks协议被滥用，会给博客产生大量垃圾留言，建议彻底关闭Trackbacks。','io_setting'),
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => true
        ),

        array(
            'id'      => 'disable_gutenberg',
            'type'    => 'switcher',
            'title'   => __('禁用古腾堡编辑器','io_setting'),
            'label'   => __('禁用Gutenberg编辑器，换回经典编辑器。','io_setting'),
            'desc'    => $tip_ico.'注意：古腾堡如果出现json错误，可以重新保存一下<a href="'.admin_url('options-permalink.php').'">固定链接</a>试试。',
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => true
        ),

        array(
            'id'      => 'disable_xml_rpc',
            'type'    => 'switcher',
            'title'   => __('禁用XML-RPC','io_setting'),
            'label'   => __('关闭XML-RPC功能，只在后台发布文章。(如果古滕堡报错，请关闭该选项)','io_setting'),
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => false, 
        ),

        array(
            'id'      => 'disable_privacy',
            'type'    => 'switcher',
            'title'   => __('禁用后台隐私（GDPR）','io_setting'),
            'label'   => __('移除为欧洲通用数据保护条例而生成的隐私页面，如果只是在国内运营博客，可以移除后台隐私相关的页面。','io_setting'),
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => true
        ),
        array(
            'id'      => 'emoji_switcher',
            'type'    => 'switcher',
            'title'   => __('禁用Emoji代码','io_setting'),
            'label'   => __('WordPress 为了兼容在一些比较老旧的浏览器能够显示 Emoji 表情图标，而准备的功能。屏蔽Emoji图片转换功能，直接使用Emoji。','io_setting'),
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => true
        ),
        array(
            'id'      => 'disable_autoembed',
            'type'    => 'switcher',
            'title'   => __('禁用Auto Embeds','io_setting'),
            'label'   => __('禁用 Auto Embeds 功能，加快页面解析速度。 Auto Embeds 支持的网站大部分都是国外的网站，建议禁用。','io_setting'),
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => true
        ),
        array(
            'id'      => 'disable_post_embed',
            'type'    => 'switcher',
            'title'   => __('禁用文章Embed','io_setting'),
            'label'   => __('禁用可嵌入其他 WordPress 文章的Embed功能','io_setting'),
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => false
        ),
        array(
            'id'      => 'remove_dns_prefetch',
            'type'    => 'switcher',
            'title'   => __('禁用s.w.org','io_setting'),
            'label'   => __('移除 WordPress 头部加载 DNS 预获取（s.w.org 国内根本无法访问）','io_setting'),
            'text_on' => '已禁用',
            'text_off'=> '未禁用',
            'text_width' => 80,
            'default' => false
        ),
    )
));

//
// 优化设置-优化加速
//
CSF::createSection( $prefix, array(
    'parent'      => 'optimize',
    'title'       => __('优化加速','io_setting'),
    'icon'        => 'fa fa-envira',
    'fields'      => array(
        array(
            'type'    => 'submessage',
            'style'   => 'danger',
            'content' => '<p style="font-size:18px">'.$tip_ico.'注意</p>
            <p>如果不了解下面选项的作用，请保持原样！</p>',
        ),
        array(
            'id'      => 'remove_head_links',
            'type'    => 'switcher',
            'title'   => __('移除头部代码','io_setting'),
            'label'   => __('WordPress会在页面的头部输出了一些link和meta标签代码，这些代码没什么作用，并且存在安全隐患，建议移除WordPress页面头部中无关紧要的代码。','io_setting'),
            'default' => true
        ),

        array(
            'id'      => 'remove_admin_bar',
            'type'    => 'switcher',
            'title'   => __('移除admin bar','io_setting'),
            'label'   => __('WordPress用户登录的情况下会出现Admin Bar，此选项可以帮助你全局移除工具栏，所有人包括管理员都看不到。','io_setting'),
            'default' => true
        ),
        array(
            'id'      => 'ioc_category',
            'type'    => 'switcher',
            'title'   => __('去除分类标志','io_setting'),
            'label'   => __('去除链接中的分类category标志，有利于SEO优化，每次开启或关闭此功能，都需要重新保存一下<a href="'.admin_url('options-permalink.php').'">固定链接</a>！','io_setting'),
            'default' => true
        ),
        array(
            'id'      => 'gravatar_cdn',
            'type'    => 'radio',
            'title'   => 'Gravatar头像加速',
            'inline'  => true,
            'options' => array(
                'gravatar'    => 'Gravatar 官方服务',
                'cravatar'    => 'Cravatar 国内镜像',
                'iocdn'       => '一为云 加速服务（cdn.iocdn.cc）',
            ),
            'default' => 'iocdn',
            'after'   => '自定义修改：inc/wp-optimization.php',
        ),
        array(
            'id'      => 'remove_help_tabs',
            'type'    => 'switcher',
            'title'   => __('移除帮助按钮','io_setting'),
            'label'   => __('移除后台界面右上角的帮助','io_setting'),
            'default' => false
        ),
        array(
            'id'      => 'remove_screen_options',
            'type'    => 'switcher',
            'title'   => __('移除选项按钮','io_setting'),
            'label'   => __('移除后台界面右上角的选项','io_setting'),
            'default' => false
        ),
        array(
            'id'      => 'no_admin',
            'type'    => 'switcher',
            'title'   => __('禁用 admin','io_setting'),
            'label'   => __('禁止使用 admin 用户名尝试登录 WordPress。','io_setting'),
            'default' => false
        ),
        array(
            'id'      => 'compress_html',
            'type'    => 'switcher',
            'title'   => __('压缩 html 源码','io_setting'),
            'label'   => __('压缩网站源码，提高加载速度。（如果启用发现网站布局错误，请禁用。）','io_setting'),
            'default' => false
        ),
    )
));
 
//
// 今日热点
//
CSF::createSection( $prefix, array(
    'title'        => __('今日热榜','io_setting'),
    'icon'         => 'fab fa-hotjar added',
    'fields'       => array(
        array(
            'type' => 'submessage',
            'style' => 'info',
            'content' => '<h4><b>此选项卡内容留空不影响主题使用，如需要以下服务必须填。</b></h4>1、热搜榜、新闻源等卡片数据获取<br />
            <br />教程：<a href="https://www.iotheme.cn/io-api-user-manual.html"  target="_blank">api 使用手册</a>
            <br><i class="fa fa-fw fa-info-circle fa-fw"></i> 一为热榜 api 服务集成，此服务不影响主题的任何功能
            <br><i class="fa fa-fw fa-info-circle fa-fw"></i> 注意：JSON和RSS为免费服务',
        ),
        array(
            'id'      => 'iowen_key',
            'type'    => 'text',
            'title'   => __('一为热榜 API 激活码','io_setting'),
            'after'   => '一为热榜 API 为订阅服务，购买主题免费赠送一年，请先使用订单激活码<a href="//www.iotheme.cn/user?try=reg" target="_blank" title="注册域名">注册域名</a>。 如果没有购买或者过期，请访问<a href="//www.iotheme.cn/store/iowenapi.html" target="_blank" title="购买服务">iTheme</a>购买。',
        ),
        array(
            'id'      => 'is_show_hot',
            'type'    => 'switcher',
            'title'   => __('使用热榜', 'io_setting'),
            'default' => true,
            'label'   => '注意：热榜总开关，关闭后，站内不显示任何热板内容。',
        ),
        array(
            'type'     => 'callback',
            'class'    => 'csf-field-submessage',
            'function' => 'weixin_data_html',
            'dependency' => array( 'is_show_hot', '==', true , '', 'visible')
        ),
        array(
            'id'        => 'hot_new',
            'type'      => 'group',
            'title'     => '首页新闻热搜',
            'fields'    => get_hot_list_option(array(), true),
            'button_title' => '添加热榜',
            'max'     => 6,
            'dependency' => array( 'is_show_hot', '==', true )
        ),
        array(
            'id'         => 'hot_iframe',
            'type'       => 'switcher',
            'title'      => __('热点 iframe 加载总开关','io_setting'),
            'label'      => __('如果开启了此选项链接还是在新窗口打开，说明对方不支持 iframe 嵌套','io_setting'),
            'default'    => false,
            'dependency' => array( 'is_show_hot', '==', true )
        ),
        array(
            'id'        => 'hot_home_list',
            'type'      => 'group',
            'title'     => '今日热榜页列表',
            'fields'    => get_hot_list_option(),
            'before'  => '<h4>今日热榜页显示的新闻源</h4>热榜地址：<a href="'.esc_url(home_url().'/hotnews').'"  target="_blank">'.esc_url(home_url().'/hotnews').'</a><br>最多添加30个<br><i class="fa fa-fw fa-info-circle fa-fw"></i> ID必填，ID列表：<a target="_blank" href="https://www.ionews.top/list.html">查看</a>',
            'default' => all_topnew_list(),
            'accordion_title_number' => true,
            'max'     => 30,
            'dependency' => array( 'is_show_hot', '==', true )
        ),
    )
));

/**
 * -----------------------------------------------------------------------
 * HOOK : ACTION HOOK
 * io_setting_option_after_code
 * 
 * 在主题设置菜单后挂载其他内容
 * @since   
 * -----------------------------------------------------------------------
 */
do_action( 'io_setting_option_after_code' , $prefix,'自定义项','fas fa-dot-circle' ); 

//
// 备份
//
CSF::createSection( $prefix, array(
    'title'       => __('备份设置','io_setting'),
    'icon'        => 'fa fa-undo',
    'fields'      => array( 

        array(
            'type'     => 'callback',
            'class'  => 'csf-field-submessage',
            'function' => 'io_backup',
        ),
        // 备份
        array(
            'type' => 'backup',
        ),
    )
));

