<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-10 21:22:37
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-04 01:21:32
 * @FilePath: \onenav\inc\widgets\w.hot.apps.php
 * @Description: 热门app小工具
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

CSF::createWidget( 'io_hot_apps', array(
    'title'       => '热门 App',
    'classname'   => 'io-widget-hot-apps',
    'description' => '按条件显示热门软件资源，可选“浏览数”“下载数”“点赞收藏数”“评论量”',
    'fields'      => array(

        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '名称',
            'default' => '热门软件',
        ),

        array(
            'id'      => 'title_ico',
            'type'    => 'icon',
            'title'   => '图标代码',
            'default' => 'iconfont icon-app',
        ),

        array(
            'id'          => 'new-window',
            'type'        => 'switcher',
            'title'       => '在新窗口打开链接',
            'default'     => true,
        ), 

        array(
            'id'          => 'meta-key',
            'type'        => 'select',
            'title'       => '选择数据条件',
            'options'     => array(
                'views'         => '浏览数',
                '_down_count'   => '下载数',
                'like'      => '点赞收藏',
                'comment'   => '评论量',
            ),
            'default' => 'views',
        ), 

        array(
            'id'          => 'opt-num',
            'type'        => 'number',
            'title'       => '显示数量',
            'unit'        => '条', 
            'default'     => 5,
        ),

        array(
            'id'          => 'opt-day',
            'type'        => 'number',
            'title'       => '时间周期',
            'unit'        => '天',
            'default'     => 120,
            'help'        => '只显示此选项设置时间内发布的内容',
        )
    )
) );
if ( ! function_exists( 'io_hot_apps' ) ) {
    function io_hot_apps( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            $title_ico = !empty($instance['title_ico']) ? '<i class="mr-2 '.$instance['title_ico'].'"></i>' : ''; 
            echo $args['before_title'] . $title_ico. apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        global $post;
        $basis_args = array(
            'post_type'           => 'app', 
            'post_status'         => array( 'publish', 'private' ),//'publish',
            'perm'                => 'readable',
            'ignore_sticky_posts' => 1,              
            'posts_per_page'      => $instance['opt-num'],
            'date_query' => array(
                array(
                    'after' => $instance['opt-day'].' day ago',
                ),
            ),              
        );
        switch ($instance['meta-key']){
            case 'views':  
                $order_args = array(
                    'meta_key'  => 'views',      
                    'orderby'   => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ), 
                );
                break;
            case '_down_count':  
                $order_args = array(
                    'meta_key'  => '_down_count',      
                    'orderby'   => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ), 
                );
                break;
            case 'like':  
                $meta_key =io_get_option('user_center',false)?'_star_count':'_like_count';
                $order_args = array(
                    'meta_key'  => $meta_key,      
                    'orderby'   => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ), 
                );
                break;
            case 'comment': 
                $order_args = array(
                    'orderby' => 'comment_count',
                    'order' => 'dsc',
                );
                break;
            default:  
                $order_args = array(
                    'meta_key'            => $instance['meta-key'],      
                    'orderby'             =>  array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ), 
                );
        }
        $p_args         = array_merge($basis_args,$order_args);
        $myposts        = new WP_Query( $p_args );
        
        $before_div             = '<div class="row row-sm">';
        $after_div              = '</div>';
        $instance['before_div'] = $before_div;
        $instance['after_div']  = $after_div;

        echo'<div class="card-body">';
        if(!$myposts->have_posts()): ?>
            <div class="col-lg-12">
                <div class="nothing mb-4"><?php _e('没有数据！','i_theme') ?></div>
            </div>
        <?php
        elseif ($myposts->have_posts()):
            echo load_widgets_min_app_html($myposts,$instance);
        endif; 
        wp_reset_postdata();  
        echo '</div>';

        echo $args['after_widget'];
    }
}
