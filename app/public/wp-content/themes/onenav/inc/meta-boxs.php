<?php
/*!
 * Theme Name:One Nav
 * Theme URI:https://www.iotheme.cn/
 * Author:iowen
 * Author URI:https://www.iowen.cn/
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if(!is_admin()) return;

$tip_ico = '<i class="fa fa-fw fa-info-circle"></i> ';

$post_meta_base_list = apply_filters('io_post_meta_base_filters', array('post','page','sites','app','book'));

//SEO meta
if( class_exists( 'CSF' ) ){
    $post_options = 'post-seo_post_meta';
    CSF::createMetabox($post_options, array(
        'title'     => 'SEO',
        'post_type' => $post_meta_base_list,
        'data_type' => 'unserialize',
        'theme'     => 'light',
        'context'   => 'side',
        'priority'  => 'default',
    ));
    $fields = apply_filters('io_post_seo_meta_filters',
        array(
            array(
                'id'    => '_seo_title',
                'type'  => 'text',
                'title' => __('自定义标题','io_setting'),
                'desc' => 'Title 一般建议15到30个字符',
                'after' => __('留空则获取文章标题','io_setting'),
                'attributes' => array(
                    'style'    => 'width: 100%'
                ),
            ),
            array(
                'id'    => '_seo_metakey',
                'type'  => 'text',
                'title' => __('自定义关键词','io_setting'),
                'desc' => 'Keywords 每个关键词用英语逗号隔开',
                'after' => __('留空则获取文章标签','io_setting'),
                'attributes' => array(
                    'style'    => 'width: 100%'
                ),
            ),
            array(
                'id'    => '_seo_desc',
                'type'  => 'textarea',
                'title' => __('自定义描述','io_setting'),
                'desc' => 'Description 一般建议50到150个字符',
                'after' => __('留空则获取文章简介或摘要','io_setting'),
                'attributes' => array(
                    'style'    => 'width: 100%'
                ),
            ),
        )
    );
    CSF::createSection( $post_options, array( 'fields' => $fields ));
}

//文章参数
if( class_exists( 'CSF' ) ) {
    $page_options = 'page-parameter_post_meta';
    CSF::createMetabox($page_options, array(
        'title'     => '文章参数',
        'post_type' => $post_meta_base_list,
        'context'   => 'side',
        'data_type' => 'unserialize',
    ));
    $fields =  apply_filters('io_post_parameter_meta_filters',
        array(
            array(
                'id'    => 'views',
                'type'  => 'text',
                'title' => __('浏览量','io_setting'), 
                'class' => 'io-horizontal',
                'default' => '0',
            ),
            array(
                'id'    => '_like_count',
                'type'  => 'text',
                'title' => __('点赞量','io_setting'), 
                'class' => 'io-horizontal',
                'default' => '0',
            ),
            array(
                'id'    => '_down_count',
                'type'  => 'text',
                'title' => __('下载量','io_setting'), 
                'class' => 'io-horizontal',
                'default' => '0',
            ),
            array(
                'type'    => 'submessage',
                'style'   => 'normal',
                'content' => '<i class="fa fa-fw fa-info-circle fa-fw"></i>此文章类型不一定包含以上所有数据',
            ),
        )
    );
    CSF::createSection( $page_options, array( 'fields' => $fields ));
}

//页面扩展
if( class_exists( 'CSF' ) && IO_PRO ) {
    $page_options = 'page-option_post_meta';
    CSF::createMetabox($page_options, array(
        'title'     => '页面扩展',
        'post_type' => array('post','page','sites','app','book','bulletin'),
        'context'   => 'side',
        'data_type' => 'unserialize',
        'priority'  => 'high',
    ));
    $fields = apply_filters('io_page_extend_option_meta_filters',
        array(
            array(
                'id'      => 'sidebar_layout',
                'type'    => 'radio',
                'title'   => '侧边栏布局',
                'options' => array(
                    'default'       => '跟随主题设置',
                    'sidebar_no'    => '无侧边栏',
                    'sidebar_left'  => '侧边栏靠左',
                    'sidebar_right' => '侧边栏靠右',
                ),
                'default' => 'default',
            )
        )
    );
    CSF::createSection( $page_options, array( 'fields' => $fields ));
}

if (class_exists('CSF') && IO_PRO) {
    $post_options = 'post_post_meta';
    CSF::createMetabox($post_options, array(
        'title'     => __('查看权限','io_setting'),
        'post_type' => 'post',
        'data_type' => 'unserialize',
        'theme'     => 'light',
        'priority'  => 'high',
        'nav'       => 'inline',
    ));
    CSF::createSection( $post_options, array( 'fields' => get_user_purview_filters() ));
}

$sortable = '';
if(io_get_option('sites_sortable',false)){
    $sortable = 'disabled';
}
// 网站
if( class_exists( 'CSF' ) && IO_PRO ) {
    $site_options = 'sites_post_meta';
    CSF::createMetabox($site_options, array(
        'title'     => __('站点信息','io_setting'),
        'post_type' => 'sites',
        'data_type' => 'unserialize',
        'theme'     => 'light',
        'priority'  => 'high',
        'nav'       => 'inline',
    ));
    $fields = apply_filters('io_sites_post_meta_filters',
        array(
            array(
                'id'           => '_sites_type',
                'type'         => 'button_set',
                'title'        => __('类型','io_setting'),
                'options'      => array(
                    'sites'  => __('网站','io_setting'),
                    'wechat' => __('公众号/小程序','io_setting'),
                    'down'   => __('下载资源','io_setting'),
                ),
                'default'      => 'sites',
            ),
            array(
                'type'		 => 'submessage',
                'style'		=> 'danger',
                'content'	  => __('下载资源已不再支持，请使用“APP/资源”添加内容，已经存在的内容不受影响。','io_setting'), 
                'dependency' => array( '_sites_type', '==', 'down' ), 
            ),
            array(
                'id'      => '_goto',
                'type'    => 'switcher',
                'title'   => __('直接跳转','io_setting'),
                'label'   => '不添加 go 跳转和 nofollow',
                'default' => false,
                'dependency' => array( '_sites_type', '==', 'sites' ),
            ),
            array(
                'id'      => '_wechat_id',
                'type'    => 'text',
                'title'   => __('微信号','io_setting'),
                'dependency' => array( '_sites_type', '==', 'wechat' ),
            ),
            array(
                'id'      => '_is_min_app',
                'type'    => 'switcher',
                'title'   => __('小程序','io_setting'),
                'default' => false,
                'dependency' => array( '_sites_type', '==', 'wechat' ),
            ),
            array(
                'id'      => '_sites_link',
                'type'    => 'text',
                'class'   => 'sites_link',
                'title'   => __('链接','io_setting'),
                'desc'    => __('需要带上http://或者https://','io_setting'),
                'dependency' => array( '_sites_type', '!=', 'down' ),
            ),
            array(
                'id'      => '_spare_sites_link',
                'type'    => 'group',
                'title'   => __('备用链接地址（其他站点）','io_setting'),
                'subtitle'=> __('如果有多个链接地址，请在这里添加。','io_setting'),
                'fields'  => array(
                    array(
                        'id'    => 'spare_name',
                        'type'  => 'text',
                        'title' => __('站点名称','io_setting'),
                    ),
                    array(
                        'id'    => 'spare_url',
                        'type'  => 'text',
                        'title' => __('站点链接','io_setting'),
                        'desc'  => __('需要带上http://或者https://','io_setting'),
                    ),
                    array(
                        'id'    => 'spare_note',
                        'type'  => 'text',
                        'title' => __('备注','io_setting'),
                    ),
                ),
                'dependency' => array( '_sites_type', '==', 'sites' ),
            ),
            array(
                'id'      => '_sites_sescribe',
                'type'    => 'textarea',
                'title'   => __('一句话描述（简介）','io_setting'),
                'after'   => __('推荐不要超过80个字符，详细介绍加正文。','io_setting'),
                'class'   => 'sites_sescribe auto-height',
                'attributes' => array(
                    'rows'   => "2",
                ),
            ),
            array(
                'id'      => '_sites_language',
                'type'    => 'text',
                'title'   => __('站点语言','io_setting'),
                'after'   => __('站点支持的语言，多个用英语逗号分隔，请使用缩写，如：zh,en ，','io_setting').'<a href="https://zh.wikipedia.org/wiki/ISO_639-1" target="_blank">各国语言缩写参考</a>',
                'dependency' => array( '_sites_type', '==', 'sites' ),
            ),
            array(
                'id'      => '_sites_country',
                'type'    => 'text',
                'class'   => 'sites_country',
                'title'   => __('站点所在国家或地区','io_setting'),
                'dependency' => array( '_sites_type', '==', 'sites' ),
            ),
            array(
                'id'      => '_sites_order',
                'type'    => 'text',
                'title'   => __('排序','io_setting'),
                'desc'    => $sortable==''?__('网址排序数值越大越靠前','io_setting'):'您已经启用拖动排序，请前往列表拖动内容排序',
                'default' => '0',
                'class'   => $sortable,
            ),
            array(
                'id'      => '_thumbnail',
                'type'    => 'upload',
                'title'   => __('LOGO，标志','io_setting'),
                'library' => 'image',
                'class'   => 'sites-ico',
                'before'  => '① <b>获取图标：</b>可以自动下载目标图标到本地。 <br>② <b>生成图标：</b>可生成名字首字图标。（“data:image”图片信息，不会有预览。）<br><span class="sites-ico-msg" style="display:none;color:#dc1e1e;"></span>',
                'desc'    => __('留空则使用api自动获取图标','io_setting'),
            ),
            array(
                'id'      => '_sites_preview',
                'type'    => 'upload',
                'title'   => __('网站预览截图','io_setting'),
                'before'  => '优先级高于主题设置中的<a href="'.admin_url('admin.php?page=theme_settings#tab=%e5%86%85%e5%ae%b9%e8%ae%be%e7%bd%ae/%e7%bd%91%e5%9d%80%e8%ae%be%e7%bd%ae').'">详情页-预览api</a>',
                'dependency' => array( '_sites_type', '==', 'sites' ),
            ),
            array(
                'id'      => '_wechat_qr',
                'type'    => 'upload',
                'title'   => __('公众号二维码','io_setting'),
                'dependency' => array( '_sites_type', '!=', 'down' ),
            ),
            array(
                'id'      => '_down_version',
                'type'    => 'text',
                'title'   => __('资源版本','io_setting'),
                'dependency' => array( '_sites_type', '==', 'down' ),
            ),
            array(
                'id'      => '_down_size',
                'type'    => 'text',
                'title'   => __('资源大小','io_setting'),
                'after'   => __('填写单位：KB,MB,GB,TB' ,'io_setting'),
                'dependency' => array( '_sites_type', '==', 'down' ),
            ),
            array(
                'id'     => '_down_url_list',
                'type'   => 'group',
                'title'  => __('下载地址列表','io_setting'),
                'before' => __('添加下载地址，提取码等信息','io_setting'),
                'fields' => array(
                    array(
                        'id'    => 'down_btn_name',
                        'type'  => 'text',
                        'title' => __('按钮名称','io_setting'),
                        'default' => __('百度网盘','io_setting'),
                    ),
                    array(
                        'id'    => 'down_btn_url',
                        'type'  => 'text',
                        'title' => __('下载地址','io_setting'),
                    ),
                    array(
                        'id'    => 'down_btn_tqm',
                        'type'  => 'text',
                        'title' => __('提取码','io_setting'),
                    ),
                    array(
                        'id'    => 'down_btn_info',
                        'type'  => 'text',
                        'title' => __('描述','io_setting'),
                    ),
                ), 
                'dependency' => array( '_sites_type', '==', 'down' ),
            ),
            array(
                'id'      => '_dec_password',
                'type'    => 'text',
                'title'   => __('解压密码','io_setting'),
                'dependency' => array( '_sites_type', '==', 'down' ),
            ),
            array(
                'id'      => '_app_platform',
                'type'    => 'checkbox',
                'title'   => __('支持平台','io_setting'),
                'inline'  => true,
                'options' => array(
                    'icon-microsoft'        => 'PC',
                    'icon-mac'              => 'MAC OS',
                    'icon-linux'            => 'linux',
                    'icon-android'          => __('安卓','io_setting'),
                    'icon-app-store-fill'   => 'ios',
                ),
                'dependency' => array( '_sites_type', '==', 'down' ),
            ),
            array(
                'id'      => '_down_preview',
                'type'    => 'text',
                'title'   => __('演示地址','io_setting'),
                'dependency' => array( '_sites_type', '==', 'down' ),
            ),
            array(
                'id'      => '_down_formal',
                'type'    => 'text',
                'title'   => __('官方地址','io_setting'),
                'dependency' => array( '_sites_type', '==', 'down' ),
            ),
            array(
                'id'     => '_screenshot',
                'type'   => 'repeater',
                'title'  => '截图',
                'fields' => array(
                    array(
                        'id'      => 'img',
                        'type'    => 'upload',
                        'preview' => true,
                    ),
                ),
                'button_title' => '添加截图',
                'dependency' => array( '_sites_type', '==', 'down' ),
            ),
        )
    );
    CSF::createSection( $site_options, array( 
        'title'  => '基础信息',
        'icon'   => 'fas fa-dice-d6',
        'fields' => $fields )
    );
    CSF::createSection( $site_options, array( 
        'title'  => '权限&商品',
        'icon'   => 'fa fa-shopping-cart',
        'fields' => get_user_purview_filters() )
    );
}

// app
if( class_exists( 'CSF' ) && IO_PRO ) {
    $app_options = 'app_post_meta';
    CSF::createMetabox($app_options, array(
        'title'     => __('APP 信息','io_setting'),
        'post_type' => 'app',
        'data_type' => 'unserialize',
        'theme'     => 'light',
        'priority'  => 'high', 
        'nav'       => 'inline',
    ));
    
    $fields_basal = apply_filters('io_app_post_basal_meta_filters',
        array(
            array(
                'id'           => '_app_type',
                'type'         => 'button_set',
                'title'        => __('类型','io_setting'),
                'options'      => array(
                    'app'    => __('软件','io_setting'),
                    'down'   => __('资源','io_setting'),
                ),
                'default'      => 'app',
            ),
            array(
                'type'    => 'content',
                'content' => __('排序：根据文章修改时间排序','io_setting'),//文章标题和seo标题为：app名称+app版本+更新日期+简介+APP状态<br>
            ),
            array(
                'id'      => '_app_ico',
                'type'    => 'upload',
                'title'   => __('图标 *','io_setting'),
                'subtitle'=> __('推荐256x256 必填','io_setting'),
                'library' => 'image',
                'class'   => 'cust_app_ico',
                'desc'    => __('添加图标地址，调用自定义图标','io_setting'),
            ),
            array(
                'id'     => 'app_ico_o',
                'type'   => 'fieldset',
                'title'  => __('图标选项','io_setting'),
                'fields' => array(
                    array(
                        'type'    => 'content',
                        'content' => __('预览','io_setting'),
                        'dependency' => array( 'ico_a', '==', true )
                    ),
                    array(
                        'id'    => 'ico_a',
                        'type'  => 'switcher',
                        'title' => __('透明','io_setting'),
                        'label' => __('图片是否透明？','io_setting'),
                    ),
                    array(
                        'id'        => 'ico_color',
                        'type'      => 'color_group',
                        'title'     => __('背景颜色','io_setting'),
                        'options'   => array(
                            'color-1' => __('颜色 1','io_setting'),
                            'color-2' => __('颜色 2','io_setting'),
                        ),
                        'default'   => array(
                            'color-1' => '#f9f9f9',
                            'color-2' => '#e8e8e8',
                        ),
                        'dependency' => array( 'ico_a', '==', true )
                    ),
                    array(
                        'id'      => 'ico_size',
                        'type'    => 'slider',
                        'title'   => __('缩放','io_setting'),
                        'min'     => 20,
                        'max'     => 100,
                        'step'    => 1,
                        'unit'    => '%',
                        'default' => 70,
                        'dependency' => array( 'ico_a', '==', true )
                    ),
                ),
            ),
            array(
                'id'      => '_app_name',
                'type'    => 'text',
                'title'   => __('名称','io_setting'),
                'after'   => 'SEO title 取值为: 此项名称+app版本+是否有广告+APP状态+更新日期<br>留空则取文章名称。',
            ),
            array(
                'id'      => '_app_platform',
                'type'    => 'checkbox',
                'title'   => __('支持平台','io_setting'),
                'inline'  => true,
                'options' => array(
                    'icon-microsoft'        => 'PC',
                    'icon-mac'              => 'MAC OS',
                    'icon-linux'            => 'linux',
                    'icon-android'          => __('安卓','io_setting'),
                    'icon-app-store-fill'   => 'ios',
                ),
            ),
            array(
                'id'      => '_down_formal',
                'type'    => 'text',
                'title'   => __('官方地址','io_setting'),
                'attributes' => array(
                    'style'    => 'width: 100%'
                ),
            ),
            array(
                'id'     => '_screenshot',
                'type'   => 'repeater',
                'title'  => '截图',
                'fields' => array(
                    array(
                        'id'      => 'img',
                        'type'    => 'upload',
                        'preview' => true,
                    ),
                ),
                'button_title' => '添加截图',
            ),
            array(
                'id'      => '_app_sescribe',
                'type'    => 'text',
                'title'   => __('简介','io_setting'),
                'after'   => '<br>'.__('推荐不要超过150个字符，详细介绍加正文。','io_setting'),
                'attributes' => array(
                    'style'    => 'width: 100%'
                ),
            ), 
        )
    ); 
    CSF::createSection( $app_options, array( 
        'title'  => '基础信息',
        'icon'   => 'fas fa-dice-d6',
        'fields' => $fields_basal )
    );
    $fields_ver = apply_filters('io_app_post_ver_meta_filters',
        array(
            array(
                'content' => '<h4>填写资源下载地址和版本控制</h4>如果需要开启付费下载，请到【权限&商品】选项卡开启“付费”-“附件下载”',
                'style'   => 'info',
                'type'    => 'submessage',
            ), 
            array(
                'id'     => 'app_down_list',
                'type'   => 'group', 
                'before' => __('APP 版本信息（添加版本，可构建历史版本）', 'io_setting'),
                'fields' => array(
                    array(
                        'id'    => 'app_version',
                        'type'  => 'text',
                        'title' => __('版本号','io_setting'),
                        'placeholder'=>__('添加版本号','io_setting'),
                    ),
                    array(
                        'id'      => 'index',
                        'type'    => 'spinner',
                        'title'   => '商品 ID',
                        'min'     => 1,
                        'max'     => 1000,
                        'step'    => 1,
                        'after'   => 'ID 不能小于1，且必须唯一，也不要随意修改，因为购买凭证和此ID关联。',
                        'class'   => 'compact min',
                        'dependency' => array('price_type', '==', 'multi', 'all'),
                    ),
                    array(
                        'id'    => 'app_date',
                        'type'  => 'date',
                        'title' => __('更新日期','io_setting'),
                        'settings' => array(
                            'dateFormat'      => 'yy-m-d',
                            'changeMonth'     => true,
                            'changeYear'      => true, 
                            'showButtonPanel' => true,
                        ),
                        'class'      => 'compact min',
                        'default' => date('Y-m-d',current_time( 'timestamp' )),
                    ),
                    array(
                        'id'     => 'app_size',
                        'type'   => 'text',
                        'title'  => __('APP 大小', 'io_setting'),
                        'after'  => __('填写单位：KB,MB,GB,TB' ,'io_setting'),
                        'class'  => 'compact min',
                    ),
                    array(
                        'id'         => 'pay_price',
                        'type'       => 'number',
                        'title'      => '销售价格',
                        'class'      => 'compact min',
                        'default'    => 0,
                        'dependency' => array( 'price_type', '==', 'multi', 'all' ), 
                    ),
                    array(
                        'id'         => 'price',
                        'type'       => 'number',
                        'title'      => '原价',
                        'class'      => 'compact min',
                        'dependency' => array( 'price_type', '==', 'multi', 'all' ), 
                    ),
                    array(
                        'id'     => 'down_url',
                        'type'   => 'group',
                        'before' => __('下载地址信息','io_setting'),
                        'fields' => array(
                            array(
                                'id'    => 'down_btn_name',
                                'type'  => 'text',
                                'title' => __('按钮名称','io_setting'),
                            ),
                            array(
                                'id'    => 'down_btn_url',
                                'type'  => 'text',
                                'title' => __('下载地址','io_setting'),
                                'class' => 'compact min',
                            ),
                            array(
                                'id'    => 'down_btn_tqm',
                                'type'  => 'text',
                                'title' => __('提取码','io_setting'),
                                'class' => 'compact min',
                            ),
                            array(
                                'id'    => 'down_btn_info',
                                'type'  => 'text',
                                'title' => __('描述','io_setting'),
                                'class' => 'compact min',
                            ),
                        ), 
                    ),
                    array(
                        'id'      => 'app_status',
                        'type'    => 'radio',
                        'title'   => __('APP状态','io_setting'),
                        'inline'  => true,
                        'options' => array(
                            'official'  => __('官方版','io_setting'),
                            'cracked'   => __('开心版','io_setting'),
                            'other'     => __('自定义','io_setting'),
                        ),
                        'default' => 'official',
                    ),
                    array(
                        'id'      => 'status_custom',
                        'type'    => 'text',
                        'title'   => __('自定义状态','io_setting'),
                        'class'   => 'compact min',
                        'desc'    => '留空则不显示',
                        'dependency' => array( 'app_status', '==', 'other' )
                    ),
                    array(
                        'id'    => 'app_ad',
                        'type'  => 'switcher',
                        'title' => __('是否有广告','io_setting'),
                    ),
                    array(
                        'id'      => 'app_language',
                        'type'    => 'text',
                        'title'   => __('支持语言','io_setting'),
                        'default' => __('中文','io_setting'),
                    ),
                    array(
                        'id'            => 'version_describe',
                        'type'          => 'wp_editor',
                        'title'         => __('版本描述','io_setting'), 
                        'tinymce'       => true,
                        'quicktags'     => true,
                        'media_buttons' => false,
                        'height'        => '100px',
                    ),
                ),
                'button_title' => '添加版本信息和下载地址',
                'default' => array(
                    array( 
                        'app_version' => '最新版',
                        'index'       => 1,
                        'app_date'    => date('Y-m-d',current_time( 'timestamp' )),
                        'down_url'    => array(
                            array(
                                'down_btn_name' => __('百度网盘','io_setting'),
                            )
                        ), 
                        'app_status'   => 'official',
                        'app_language' => __('中文','io_setting')
                    ),
                )
            ),
        )
    );
    CSF::createSection( $app_options, array( 
        'title'  => '下载地址',
        'icon'   => 'fab fa-vine',
        'fields' => $fields_ver )
    );
    CSF::createSection( $app_options, array( 
        'title'  => '权限&商品',
        'icon'   => 'fa fa-shopping-cart',
        'fields' => get_user_purview_filters() )
    );
}

// 书籍
if( class_exists( 'CSF' ) && IO_PRO ) {
    $book_options = 'book_post_meta';
    CSF::createMetabox($book_options, array(
        'title'     => __('书籍信息','io_setting'),
        'post_type' => 'book',
        'data_type' => 'unserialize',
        'theme'     => 'light',
        'priority'  => 'high',
        'nav'       => 'inline',
    ));
    $fields = apply_filters('io_book_post_meta_filters',
        array(
            array(
                'id'           => '_book_type',
                'type'         => 'button_set',
                'title'        => __('类型','io_setting'),
                'options'      => array(
                    'books'      => __('图书','io_setting'),
                    'periodical' => __('期刊','io_setting'),
                    'movie'      => __('电影','io_setting'),
                    'tv'         => __('电视剧','io_setting'),
                    'video'      => __('小视频','io_setting'),
                ),
                'default'      => 'books',
            ),
            array(
                'id'      => '_thumbnail',
                'type'    => 'upload',
                'title'   => __('封面','io_setting'),
                'library' => 'image',
            ),
            array(
                'id'      => '_summary',
                'type'    => 'text',
                'title'   => __('一句话描述（简介）','io_setting'),
                'after'   => '<br>'.__('推荐不要超过150个字符，详细介绍加正文。','io_setting'),
                'attributes' => array(
                    'style'    => 'width: 100%'
                ),
            ),
            array(
                'id'      => '_journal',
                'type'    => 'radio',
                'title'   => __('期刊类型','io_setting'),
                'default' => '3',
                'inline'  => true,
                'options' => array(
                    '12'       => __('周刊','io_setting'),
                    '9'        => __('旬刊','io_setting'),
                    '6'        => __('半月刊','io_setting'),
                    '3'        => __('月刊','io_setting'),
                    '2'        => __('双月刊','io_setting'),
                    '1'        => __('季刊','io_setting'),
                ),
                'dependency' => array( '_book_type', '==', 'periodical' ),
            ),
            array(
                'id'     => '_books_data',
                'type'   => 'group',
                'title'  => __('元数据','io_setting'),
                'fields' => array(
                    array(
                        'id'    => 'term',
                        'type'  => 'text',
                        'title' => __('项目(控制在5个字内)','io_setting'),
                    ),
                    array(
                        'id'    => 'detail',
                        'type'  => 'text',
                        'title' => __('内容','io_setting'),
                        'placeholder' => __('如留空，请删除项','io_setting'),
                    ),
                ), 
                'default' => io_get_option('books_metadata',array()),
            ),
            array(
                'id'     => '_buy_list',
                'type'   => 'group',
                'title'  => __('获取列表','io_setting'),
                'fields' => array(
                    array(
                        'id'      => 'term',
                        'type'    => 'text',
                        'title'   => __('按钮名称','io_setting'),
                        'default' => __('当当网','io_setting'),
                    ),
                    array(
                        'id'    => 'url',
                        'type'  => 'text',
                        'title' => __('URL地址','io_setting'),
                    ),
                    array(
                        'id'    => 'price',
                        'type'  => 'text',
                        'title' => __('价格(可忽略)','io_setting'),
                    ),
                ), 
            ),
            array(
                'id'     => '_down_list',
                'type'   => 'group',
                'title'  => __('下载地址列表','io_setting'),
                'before' => __('添加下载地址，提取码等信息','io_setting'),
                'fields' => array(
                    array(
                        'id'    => 'name',
                        'type'  => 'text',
                        'title' => __('按钮名称','io_setting'),
                        'default' => __('百度网盘','io_setting'),
                    ),
                    array(
                        'id'    => 'url',
                        'type'  => 'text',
                        'title' => __('下载地址','io_setting'),
                    ),
                    array(
                        'id'    => 'tqm',
                        'type'  => 'text',
                        'title' => __('提取码','io_setting'),
                    ),
                    array(
                        'id'    => 'info',
                        'type'  => 'text',
                        'title' => __('描述','io_setting'),
                        'placeholder' => __('格式、大小等','io_setting'),
                    ),
                ), 
            ),
        )
    );
    CSF::createSection( $book_options, array( 
        'title'  => '基础信息',
        'icon'   => 'fas fa-dice-d6',
        'fields' => $fields )
    );
    CSF::createSection( $book_options, array( 
        'title'  => '权限&商品',
        'icon'   => 'fa fa-shopping-cart',
        'fields' => get_user_purview_filters() )
    );
}

// 公告
if( class_exists( 'CSF' ) && IO_PRO ) {
    $site_options = 'bulletin_post_meta';
    CSF::createMetabox($site_options, array(
        'title'     => __('公告设置','io_setting'),
        'post_type' => 'bulletin',
        'data_type' => 'unserialize',
        'theme'     => 'light',
        'priority'  => 'high',
    ));
    $fields = apply_filters('io_bulletin_post_meta_filters',
        array(
            array(
                'id'      => '_goto',
                'type'    => 'text',
                'title'   => __('直达地址','io_setting'),
                'after'   => '<br>'.__('添加直达地址，如：https://www.baidu.com','io_setting'),
                'attributes' => array(
                    'style'    => 'width: 100%'
                ),
            ),
            array(
                'id'    => '_is_go',
                'type'  => 'switcher', 
                'title'   => __('GO 跳转','io_setting'),
                'text_on' => '启用',
                'text_off'=> '禁用',
                'default' => false,
                'dependency' => array( '_goto', '!=', '' )
            ),
            array(
                'id'    => '_nofollow',
                'type'  => 'switcher', 
                'title'   => __('nofollow','io_setting'),
                'text_on' => '启用',
                'text_off'=> '禁用',
                'default' => false,
                'dependency' => array( '_goto', '!=', '' )
            ),
        )
    );
    CSF::createSection( $site_options, array( 'fields' => $fields ));
}




// Metabox 选项框架
if( class_exists( 'CSF' ) && IO_PRO ) {
    $prefix = 'links_post_options';
    CSF::createMetabox( $prefix, array(
        'title'           => '友情链接选项',
        'post_type'       => 'page',
        'page_templates'  => 'template-links.php',
        'context'         => 'side',
        'data_type'       => 'unserialize'
    ) );
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'id'      => '_disable_links_content',
                'type'    => 'switcher', 
                'title'   => '默认内容',
                'text_on' => '启用',
                'text_off'=> '禁用',
                'default' => true,
            ),
            array(
                'id'      => '_links_form',
                'type'    => 'switcher', 
                'title'   => '投稿表单',
                'text_on' => '启用',
                'text_off'=> '禁用',
                'default' => true,
            ),
        )
    ));
}

if( class_exists( 'CSF' ) && IO_PRO ) {
    $prefix = 'mininav_post_options';
    CSF::createMetabox( $prefix, array(
        'title'           => '次级导航选项',
        'post_type'       => 'page',
        'page_templates'  => 'template-mininav.php',
        'priority'        => 'high',
        'data_type'       => 'unserialize'
    ) );
    $fields = apply_filters('io_template_mininav_meta_filters',
        array(
            array(
                'id'          => 'nav-id',
                'type'        => 'select',
                'title'       => '请选择菜单',
                'placeholder' => '选择菜单',
                'options'     => 'menus'
            ),
            array(
                'id'           => '_count_type',
                'type'         => 'button_set',
                'title'        => __('显示数量','io_setting'),
                'options'      => array(
                    '0' => __('继承首页设置','io_setting'),
                    '1' => __('自定义','io_setting'),
                ),
                'default'      => '0',
            ),
            array(
                'id'        => 'card_n',
                'type'      => 'fieldset',
                'title'     => __('内容数量配置','io_setting'),
                'fields'    => array(
                    array(
                        'id'    => 'favorites',
                        'type'  => 'spinner',
                        'title' => __('网址数量','io_setting'),
                        'step'       => 1,
                    ),
                    array(
                        'id'    => 'apps',
                        'type'  => 'spinner',
                        'title' => __('App 数量','io_setting'),
                        'step'       => 1,
                    ),
                    array(
                        'id'    => 'books',
                        'type'  => 'spinner',
                        'title' => __('书籍数量','io_setting'),
                        'step'       => 1,
                    ),
                    array(
                        'id'    => 'category',
                        'type'  => 'spinner',
                        'title' => __('文章数量','io_setting'),
                        'step'       => 1,
                    ),
                ),
                'default'        => array(
                    'favorites'   => 20,
                    'apps'        => 16,
                    'books'       => 16,
                    'category'    => 16,
                ),
                'after'      => '填写需要显示的数量。<br>-1 为显示分类下所有网址<br>&nbsp;0 为根据<a href="'.home_url().'/wp-admin/options-reading.php">系统设置数量显示</a>',
                'class'      => 'compact',
                'dependency' => array( '_count_type', '==', '1' )
            ),
            array(
                'id'        => 'search_box',
                'type'      => 'switcher', 
                'title'     => '顶部搜索框',
                'after'     => '依赖于首页设置',
                'text_on'   => '启用',
                'text_off'  => '禁用',
                'default'   => true,
            ),
            /**
             * 次级导航自定义搜索
             */
            array(
                'id'        => '_search_id',
                'type'      => 'select',
                'title'     => '自定义搜索列表ID',
                'options'   => get_search_min_list(),
                'after'     => '<i class="fa fa-fw fa-info-circle fa-fw"></i> '.'需先在主题设置中开启“<a href="'.admin_url('admin.php?page=theme_settings#tab=%e6%90%9c%e7%b4%a2%e8%ae%be%e7%bd%ae').'">自定义搜索列表</a>”，然后在搜索列表设置中设置“<a href="'.admin_url('options-general.php?page=search_settings#tab=%e6%ac%a1%e7%ba%a7%e5%af%bc%e8%88%aa%e8%87%aa%e5%ae%9a%e4%b9%89%e6%90%9c%e7%b4%a2').'">次级导航自定义搜索</a>”',
                'dependency' => array( 'search_box', '==', true )
            ),
            
            array(
                'id'           => 'widget',
                'type'         => 'sorter',
                'title'        => '头部内容',
                'subtitle'     => '模块启用和排序',
                'default'      => array(
                    'enabled'    => array(),
                    'disabled'   => get_sorter_options('top_widget'),
                ),
                'options_id'  => 'top_widget',
                'is_enabled'  => false,
                'refresh'     => true,
                'after'       => $tip_ico.'<b>文章轮播模块</b>使用首页配置',
            ),
            array(
                'id'        => 'widget_tab',
                'type'      => 'group',
                'title'     => '[Tab 内容模块] 内容设置',
                'fields'    => array(
                    array(
                        'id'    => 'title',
                        'type'  => 'text',
                        'title' => '名称',
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
                        'before'      => $tip_ico.'选择类型后输入关键字搜索分类',
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
                'id'        => 'widget_swiper',
                'type'      => 'group',
                'title'     => '[Big 轮播模块] 内容设置',
                'fields'    => array(
                    array(
                        'id'    => 'title',
                        'type'  => 'text',
                        'title' => '名称',
                    ),
                    array(
                        'id'    => 'info',
                        'type'  => 'text',
                        'title' => '简介',
                        'class' => 'compact min',
                    ),
                    array(
                        'id'        => 'img',
                        'type'      => 'upload',
                        'title'     => __('图片','io_setting'),
                        'after'     => $tip_ico.'图片尺寸为 21:9',
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
                            'random'        => '随机',
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
                'id'        => 'hot_box',
                'type'      => 'switcher', 
                'title'     => '热门内容',
                'after'     => '依赖于首页设置',
                'text_on'   => '启用',
                'text_off'  => '禁用',
                'default'   => true,
            ),
            array(
                'id'        => 'hot_new',
                'type'      => 'group',
                'title'     => '新闻热搜',
                'fields'    => get_hot_list_option([], true),
                'max'     => 6,
            ),
        )
    );
    CSF::createSection( $prefix, array( 'fields' => $fields ));
}
if( class_exists( 'CSF' ) && IO_PRO ) {
    $prefix = 'ranking_post_options';
    CSF::createMetabox( $prefix, array(
        'title'           => '排行榜选项',
        'post_type'       => 'page',
        'page_templates'  => 'template-rankings.php',
        'data_type'       => 'unserialize'
    ) );
    $fields = apply_filters('io_template_rankings_meta_filters',
        array(
            array(
                'id'          => '_show-count',
                'type'        => 'spinner',
                'title'       => '数量',
                'after'       => __('列表显示数量','io_setting'),
                'step'        => 1,
                'default'     => 10,
            ),
            array(
                'id'           => '_show-list',
                'type'         => 'sorter',
                'title'        => '显示和排序',
                'default'      => array(
                    'enabled'    => array(
                        'sites'  => '网址排行榜',
                        'post'   => '文章排行榜',
                    ),
                    'disabled'   => array(
                        'book' => '书籍排行榜',
                        'app'  => '软件排行榜',
                    ),
                ),
            ),
            array(
                'id'          => '_url_go',
                'type'        => 'switcher',
                'title'       => '直达目标网址',
                'label'       => '依赖于主题设置，如果主题设置中关闭了详情页，则此设置无效。',
            ),
        )
    );
    CSF::createSection( $prefix, array( 'fields' => $fields ));
}
