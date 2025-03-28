<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-10 21:22:37
 * @LastEditors: iowen
 * @LastEditTime: 2022-07-09 17:04:49
 * @FilePath: \onenav\inc\widgets\w.random.sites.php
 * @Description: 随机网址小工具
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

CSF::createWidget( 'random_sites', array(
    'title'       => '★ 随机内容 ★',
    'classname'   => 'io-widget-random-list',
    'description' => '按条件显示热门网址，可选“浏览数”“点赞收藏数”“评论量”',
    'fields'      => array(

        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '名称',
            'default' => '随机网址',
        ),

        array(
            'id'      => 'title_ico',
            'type'    => 'icon',
            'title'   => '图标代码',
            'default' => 'iconfont icon-chart-pc',
        ),

        array(
            'id'          => 'newWindow',
            'type'        => 'switcher',
            'title'       => '在新窗口打开链接',
            'default'     => true,
        ), 
        array(
            'id'         => 'type',
            'type'       => 'select',
            'title'      => '文章类型 bate',
            'options'    => array(
                'post'  => '文章',
                'sites' => '网址',
                'app'   => 'APP',
                'book'  => '书籍',
            ),
            'default'    => 'sites'
        ),
        array(
            'id'          => 'similar',
            'type'        => 'switcher',
            'title'       => '匹配同类',
            'default'     => true,
            'help'        => '匹配同标签和分类',
        ), 

        array(
            'id'          => 'number',
            'type'        => 'number',
            'title'       => '显示数量',
            'unit'        => '条',
            'default'     => 6,
        ),

        array(
            'id'          => 'show_thumbs',
            'type'        => 'switcher',
            'title'       => '显示缩略图',
            'default'     => true,
            'dependency'  => array( 'type', '==', 'post' )
        ),

        array(
            'id'          => 'go',
            'type'        => 'switcher',
            'title'       => '直达',
            'default'     => false,
            'help'        => '如果主题设置中关闭了“详情页”，则默认直达',
            'dependency'  => array( 'type', '==', 'sites' )
        ),

        array(
            'id'          => 'nofollow',
            'type'        => 'switcher',
            'title'       => '不使用 go 跳转和 nofollow',
            'default'     => false,
            'dependency'  => array( 'go|type', '==|==', 'true|sites' )
        )
    )
) );
if ( ! function_exists( 'random_sites' ) ) {
    function random_sites( $args, $instance ) {
        $show_thumbs = isset($instance['show_thumbs']) ? $instance['show_thumbs'] : true;
        $type        = isset($instance['type']) ? $instance['type'] : 'sites';
        echo $args['before_widget'];
        global $post;
        if ( ! empty( $instance['title'] ) ) {
            $title_ico = !empty($instance['title_ico']) ? '<i class="mr-2 '.$instance['title_ico'].'"></i>' : ''; 
            echo '<div class="d-flex sidebar-header">'
            .$args['before_title'] . $title_ico. apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']
            .'<span class="ml-auto load">
            <a href="javascript:" class="sidebar-rand-post" data-action="load_random_sites" data-id="#'.$args['id'].'" data-post_id="'.(is_single()&&$instance['similar']?$post->ID:'').'" data-post_type="'.(is_single()&&$instance['similar']?$post->post_type:'').'" data-window="'.$instance['newWindow'].'" data-type="'.$type.'" data-show_thumbs="'.$show_thumbs.'" data-go="'.$instance['go'].'" data-nofollow="'.$instance['nofollow'].'" data-number="'.$instance['number'].'" title="'.__('刷新','i_theme').'"><i class="iconfont icon-refresh"></i></a>
            </span></div>';
        }
        echo'<div class="card-body ajax-panel">';
        echo'<div class="my-5"></div>';
        echo'<div class="d-flex justify-content-center align-items-center position-absolute w-100 h-100" style="top:0;left:0"><div class="spinner-border m-4" role="status"><span class="sr-only">Loading...</span></div></div>';
        echo'</div>';
        echo $args['after_widget'];
    }
}
