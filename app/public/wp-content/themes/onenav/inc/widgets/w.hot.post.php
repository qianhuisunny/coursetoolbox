<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-10 21:22:37
 * @LastEditors: iowen
 * @LastEditTime: 2022-07-15 17:35:48
 * @FilePath: \onenav\inc\widgets\w.hot.post.php
 * @Description: 热门文章小工具
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

CSF::createWidget( 'hot_post_img', array(
    'title'       => '热门文章',
    'classname'   => 'io-widget-post-list',
    'description' => '按条件显示热门文章，可选“浏览数”“点赞收藏数”“评论量”',
    'fields'      => array(

        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '名称',
            'default' => '热门文章',
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
            'id'          => 'meta-key',
            'type'        => 'select',
            'title'       => '选择数据条件',
            'options'     => array(
                'views'     => '浏览数',
                'like'      => '点赞收藏',
                'comment'   => '评论量',
            ),
            'default' => 'views',
        ), 

        array(
            'id'          => 'number',
            'type'        => 'number',
            'title'       => '显示数量',
            'unit'        => '条',
            'default'     => 5,
        ),

        array(
            'id'          => 'days',
            'type'        => 'number',
            'title'       => '时间周期',
            'unit'        => '天',
            'default'     => 120,
            'help'        => '只显示此选项设置时间内发布的内容',
        ),

        array(
            'id'          => 'show_thumbs',
            'type'        => 'switcher',
            'title'       => '显示缩略图',
            'default'     => true,
        ),
    )
) );
if ( ! function_exists( 'hot_post_img' ) ) {
    function hot_post_img( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            $title_ico = !empty($instance['title_ico']) ? '<i class="mr-2 '.$instance['title_ico'].'"></i>' : ''; 
            echo $args['before_title'] . $title_ico. apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        global $post;
        $basis_args =  array(
            'post_type'             => array( 'post' ),
            'posts_per_page'        => $instance['number'],
            'ignore_sticky_posts'   => true,
            'date_query'            => array(
                array(
                    'after' => $instance['days'].' day ago',
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
                    'orderby' => 'comment_count',
                    'order' => 'dsc',
                );
        }
        $p_args         = array_merge($basis_args,$order_args);
        $myposts        = new WP_Query( $p_args );
        
        $before_div             = '<div class="list-grid list-rounded my-n2">';
        $after_div              = '</div>';
        $instance['before_div'] = $before_div;
        $instance['after_div']  = $after_div;
        
        echo'<div class="card-body"><div class="list-grid list-rounded my-n2">';
        if(!$myposts->have_posts()){ ?>
            <div class="col-lg-12">
                <div class="nothing mb-4"><?php _e('没有数据！','i_theme') ?></div>
            </div>
        <?php
        }elseif ($myposts->have_posts()){
            echo load_widgets_min_post_html($myposts,$instance);
        }
        wp_reset_postdata();  
        echo '</div></div>';

        echo $args['after_widget'];
    }
}
