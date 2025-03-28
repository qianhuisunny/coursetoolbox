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
// 文章分类SEO设置
if( class_exists( 'CSF' ) ) {
    $prefix = 'category_meta'; 
  
    CSF::createTaxonomyOptions( $prefix, array(
        'taxonomy'  => 'category',
        'data_type' => 'serialize', 
    ) );

  
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => __('文章分类SEO设置（可留空）','io_setting'),
            ),
            array(
                'id'    => 'seo_title',
                'type'  => 'text',
                'title' => __('自定义标题','io_setting'),
            ),
            array(
                'id'    => 'seo_metakey',
                'type'  => 'text',
                'title' => __('设置关键词','io_setting'),
            ),
            array(
                'id'    => 'seo_desc',
                'type'  => 'textarea',
                'title' => __('自定义描述','io_setting'),
            ),

        )
    ));
}

// 文章分类SEO设置
if( class_exists( 'CSF' ) ) {
    $prefix = 'category_op_meta'; 
  
    CSF::createTaxonomyOptions( $prefix, array(
        'taxonomy'  => 'category',
        'data_type' => 'unserialize', 
    ) );

  
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => '自定义选项',
            ),
            array(
                'id'        => 'card_mode',
                'type'      => 'image_select',
                'title'     => __('文章卡片样式','io_setting'),
                'options'   => array(
                    'null'    => get_theme_file_uri('/images/option/op-null.png'),
                    'card'    => get_theme_file_uri('/images/option/op-app-c-card.png'),
                    'default' => get_theme_file_uri('/images/option/op-post-c-def.png'),
                ),
                'default'   => 'null',
            ),
        )
    ));
}

// 文章标签SEO设置
if( class_exists( 'CSF' ) ) {
    $prefix = 'post_tag_meta'; 
  
    CSF::createTaxonomyOptions( $prefix, array(
        'taxonomy'  => 'post_tag',
        'data_type' => 'serialize', 
    ) );

  
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => __('文章标签SEO设置（可留空）','io_setting'),
            ),
            array(
                'id'    => 'seo_title',
                'type'  => 'text',
                'title' => __('自定义标题','io_setting'),
            ),
            array(
                'id'    => 'seo_metakey',
                'type'  => 'text',
                'title' => __('设置关键词','io_setting'),
            ),
            array(
                'id'    => 'seo_desc',
                'type'  => 'textarea',
                'title' => __('自定义描述','io_setting'),
            ),

        )
    ));
}
// 网址分类SEO设置
if( class_exists( 'CSF' ) && IO_PRO ) {
    $prefix = 'favorites_options'; 
  
    CSF::createTaxonomyOptions( $prefix, array(
        'taxonomy'  => 'favorites',
        'data_type' => 'unserialize', 
    ) );

  
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'type'    => 'notice',
                'style'   => 'danger',
                'content' => __('注意，最多2级，且父级不应有内容','io_setting'),
            ),
            array(
                'id'      => '_term_order',
                'type'    => 'number',
                'title'   => __('排序','io_setting'),
                'default' => 0,
                'after'   => '此选项仅对父级分类归档页有效',
            ), 
            array(
                'type'    => 'subheading',
                'content' => __('SEO设置（可留空）','io_setting'),
            ),
            array(
                'id'    => 'seo_title',
                'type'  => 'text',
                'title' => __('自定义标题','io_setting'),
            ),
            array(
                'id'    => 'seo_metakey',
                'type'  => 'text',
                'title' => __('设置关键词','io_setting'),
            ),
            array(
                'id'    => 'seo_desc',
                'type'  => 'textarea',
                'title' => __('自定义描述','io_setting'),
            ),
            array(
                'type'    => 'subheading',
                'content' => '自定义选项',
            ),
            array(
                'id'        => 'card_mode',
                'type'      => 'image_select',
                'title'     => __('网址卡片样式','io_setting'),
                'options'   => array(
                    'null'    => get_theme_file_uri('/images/option/op-null.png'),
                    'max'     => get_theme_file_uri('/images/option/op-site-c-max.png'),
                    'default' => get_theme_file_uri('/images/option/op-site-c-def.png'),
                    'min'     => get_theme_file_uri('/images/option/op-site-c-min.png'),
                ),
                'default'   => 'null',
            ),
            array(
                'id'           => 'columns_type',
                'type'         => 'button_set',
                'title'        => __('网址列数','io_setting'),
                'options'      => array(
                    'global'     => __('全局设置','io_setting'),
                    'custom'      => __('单独设置','io_setting'),
                ),
                'default'      => 'global',
            ),
            array(
                'id'        => 'columns',
                'type'      => 'fieldset',
                'title'     => '列数',
                'subtitle'  => __('网址块列表一行显示的个数','io_setting'),
                'fields'    => array(
                    array(
                        'type'    => 'submessage',
                        'style'   => 'success',
                        'content' => $tip_ico.'注意：有效值范围只有<b>1-10</b>',
                    ),
                    array(
                        'id'      => 'sm',
                        'type'    => 'number',
                        'title'   => '小屏幕(≥576px)',
                        'subtitle'=> '数量',
                        'unit'    => '个',
                        'default' => 2,
                    ),
                    array(
                        'id'      => 'md',
                        'type'    => 'number',
                        'title'   => '中等屏幕(≥768px)',
                        'subtitle'=> '数量',
                        'unit'    => '个',
                        'default' => 2,
                    ),
                    array(
                        'id'      => 'lg',
                        'type'    => 'number',
                        'title'   => '大屏幕(≥992px)',
                        'subtitle'=> '数量',
                        'unit'    => '个',
                        'default' => 3,
                    ),
                    array(
                        'id'      => 'xl',
                        'type'    => 'number',
                        'title'   => '加大屏幕(≥1200px)',
                        'subtitle'=> '数量',
                        'unit'    => '个',
                        'default' => 5,
                    ),
                    array(
                        'id'      => 'xxl',
                        'type'    => 'number',
                        'title'   => '加加大屏幕(≥1400px)',
                        'subtitle'=> '数量',
                        'unit'    => '个',
                        'default' => 6,
                    ),
                ),
                'dependency' => array( 'columns_type', '==', 'custom' )
            ),
        )
    ));
}

// 网址标签SEO设置
if( class_exists( 'CSF' ) ) {
    $prefix = 'sitetag_meta'; 
  
    CSF::createTaxonomyOptions( $prefix, array(
        'taxonomy'  => 'sitetag',
        'data_type' => 'serialize', 
    ) );

  
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => __('SEO设置（可留空）','io_setting'),
            ),
            array(
                'id'    => 'seo_title',
                'type'  => 'text',
                'title' => __('自定义标题','io_setting'),
            ),
            array(
                'id'    => 'seo_metakey',
                'type'  => 'text',
                'title' => __('设置关键词','io_setting'),
            ),
            array(
                'id'    => 'seo_desc',
                'type'  => 'textarea',
                'title' => __('自定义描述','io_setting'),
            ),

        )
    ));
}
// app分类SEO设置
if( class_exists( 'CSF' ) && IO_PRO ) {
    $prefix = 'apps_meta'; 
  
    CSF::createTaxonomyOptions( $prefix, array(
        'taxonomy'  => 'apps',
        'data_type' => 'unserialize', 
    ) );

  
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'type'    => 'notice',
                'style'   => 'danger',
                'content' => __('注意，最多2级，且父级不应有内容','io_setting'),
            ),
            //array(
            //    'id'      => '_term_order',
            //    'type'    => 'number',
            //    'title'   => __('排序','io_setting'),
            //    'default' => 0,
            //    'after'   => '此选项将被弃用，请到主题设置-->基础设置排序',
            //), 
            array(
                'type'    => 'subheading',
                'content' => __('SEO设置（可留空）','io_setting'),
            ),
            array(
                'id'    => 'seo_title',
                'type'  => 'text',
                'title' => __('自定义标题','io_setting'),
            ),
            array(
                'id'    => 'seo_metakey',
                'type'  => 'text',
                'title' => __('设置关键词','io_setting'),
            ),
            array(
                'id'    => 'seo_desc',
                'type'  => 'textarea',
                'title' => __('自定义描述','io_setting'),
            ),
            array(
                'type'    => 'subheading',
                'content' => '自定义选项',
            ),
            array(
                'id'        => 'card_mode',
                'type'      => 'image_select',
                'title'     => __('App 卡片样式','io_setting'),
                'options'   => array(
                    'null'    => get_theme_file_uri('/images/option/op-null.png'),
                    'card'    => get_theme_file_uri('/images/option/op-app-c-card.png'),
                    'default' => get_theme_file_uri('/images/option/op-app-c-def.png'),
                ),
                'default'   => 'null',
            ),
        )
    ));
}

// app标签SEO设置
if( class_exists( 'CSF' ) ) {
    $prefix = 'apptag_meta'; 
  
    CSF::createTaxonomyOptions( $prefix, array(
        'taxonomy'  => 'apptag',
        'data_type' => 'serialize', 
    ) );

  
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => __('SEO设置（可留空）','io_setting'),
            ),
            array(
                'id'    => 'seo_title',
                'type'  => 'text',
                'title' => __('自定义标题','io_setting'),
            ),
            array(
                'id'    => 'seo_metakey',
                'type'  => 'text',
                'title' => __('设置关键词','io_setting'),
            ),
            array(
                'id'    => 'seo_desc',
                'type'  => 'textarea',
                'title' => __('自定义描述','io_setting'),
            ),

        )
    ));
}

// book分类SEO设置
if( class_exists( 'CSF' ) ) {
    $prefix = 'books_meta'; 
  
    CSF::createTaxonomyOptions( $prefix, array(
        'taxonomy'  => 'books',
        'data_type' => 'unserialize', 
    ) );

  
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'type'    => 'notice',
                'style'   => 'danger',
                'content' => __('注意，最多2级，且父级不应有内容','io_setting'),
            ),
            //array(
            //    'id'      => '_term_order',
            //    'type'    => 'number',
            //    'title'   => __('排序','io_setting'),
            //    'default' => 0,
            //    'after'   => '此选项将被弃用，请到主题设置-->基础设置排序',
            //), 
            array(
                'type'    => 'subheading',
                'content' => __('SEO设置（可留空）','io_setting'),
            ),
            array(
                'id'    => 'seo_title',
                'type'  => 'text',
                'title' => __('自定义标题','io_setting'),
            ),
            array(
                'id'    => 'seo_metakey',
                'type'  => 'text',
                'title' => __('设置关键词','io_setting'),
            ),
            array(
                'id'    => 'seo_desc',
                'type'  => 'textarea',
                'title' => __('自定义描述','io_setting'),
            ),

        )
    ));
}

// book标签SEO设置
if( class_exists( 'CSF' ) ) {
    $prefix = 'booktag_meta'; 
  
    CSF::createTaxonomyOptions( $prefix, array(
        'taxonomy'  => 'booktag',
        'data_type' => 'serialize', 
    ) );

  
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => __('SEO设置（可留空）','io_setting'),
            ),
            array(
                'id'    => 'seo_title',
                'type'  => 'text',
                'title' => __('自定义标题','io_setting'),
            ),
            array(
                'id'    => 'seo_metakey',
                'type'  => 'text',
                'title' => __('设置关键词','io_setting'),
            ),
            array(
                'id'    => 'seo_desc',
                'type'  => 'textarea',
                'title' => __('自定义描述','io_setting'),
            ),

        )
    ));
}

// book系列SEO设置
if( class_exists( 'CSF' ) && IO_PRO ) {
    $prefix = 'series_meta'; 
  
    CSF::createTaxonomyOptions( $prefix, array(
        'taxonomy'  => 'series',
        'data_type' => 'serialize', 
    ) );

  
    CSF::createSection( $prefix, array(
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => __('SEO设置（可留空）','io_setting'),
            ),
            array(
                'id'    => 'seo_title',
                'type'  => 'text',
                'title' => __('自定义标题','io_setting'),
            ),
            array(
                'id'    => 'seo_metakey',
                'type'  => 'text',
                'title' => __('设置关键词','io_setting'),
            ),
            array(
                'id'    => 'seo_desc',
                'type'  => 'textarea',
                'title' => __('自定义描述','io_setting'),
            ),

        )
    ));
}