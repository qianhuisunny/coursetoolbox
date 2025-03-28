<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-10 21:22:37
 * @LastEditors: iowen
 * @LastEditTime: 2022-07-15 17:35:58
 * @FilePath: \onenav\inc\widgets\w.ranking.php
 * @Description: 热门内容，排行榜
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

CSF::createWidget( 'ranking_post', array(
    'title'       => '★ 热门内容 ★',
    'classname'   => 'io-widget-ranking-list',
    'description' => '注意：根据排行榜数据显示热门内容，需开启[主题设置->统计浏览]里的“按天记录统计数据”功能',
    'fields'      => array(

        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '名称',
            'default' => '热门网址',
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
            'title'      => '文章类型',
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
            'help'        => '匹配同标签或者分类',
        ), 

        array(
            'id'          => 'number',
            'type'        => 'number',
            'title'       => '显示数量',
            'unit'        => '条',
            'default'     => 6,
        ),

        array(
            'id'          => 'range',
            'type'        => 'select',
            'title'       => '范围',
            'options'     => array(
                'today'     => '今天',
                'yesterday' => '昨天',
                'week'      => '本周',
                'last_week' => '上周',
                'month'     => '本月',
                'all'       => '所有',
            ),
            'default'     => 'sites'
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
if ( ! function_exists( 'ranking_post' ) ) {
    function ranking_post( $args, $instance ) {
        echo $args['before_widget'];
        global $post;
        if ( ! empty( $instance['title'] ) ) {
            $title_ico = !empty($instance['title_ico']) ? '<i class="mr-2 '.$instance['title_ico'].'"></i>' : ''; 
            echo '<div class="d-flex sidebar-header">'
            .$args['before_title'] . $title_ico. apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']
            .'</div>';
        }
        echo'<div class="card-body">';
        $taxonomy_tag   = 'sitetag';
        $taxonomy_cat   = 'favorites';
        switch ($instance['type']) {
            case 'post':
                $taxonomy_tag   = 'post_tag';
                $taxonomy_cat   = 'category';
                $before_div     = '<div class="list-grid list-rounded my-n2">';
                $after_div      = '</div>';
                break;
            case 'app':
                $taxonomy_tag   = 'apptag';
                $taxonomy_cat   = 'apps';
                $before_div     = '<div class="row row-sm">';
                $after_div      = '</div>';
                break;
            case 'book':
                $taxonomy_tag   = 'booktag';
                $taxonomy_cat   = 'books';
                $before_div     = '<div class="row row-sm">';
                $after_div      = '</div>';
                break;
            
            case 'sites':
            default:
                $taxonomy_tag   = 'sitetag';
                $taxonomy_cat   = 'favorites';
                $before_div     = '<div class="row row-sm my-n1">';
                $after_div      = '</div>';
                break;
        }
        
        $html       = '';
        $terms      = array(); 
        
        $instance['before_div'] = $before_div;
        $instance['after_div']  = $after_div;

        //匹配同类
        if(is_single() && $instance['similar'] && $post->post_type === $instance['type']){
            $post_num   = $instance['number'];
            $post_id    = $post->ID;
            $exclude    = array($post_id);
            $posttags   = get_the_terms( $post_id, $taxonomy_tag );  
            $taxterms   = get_the_terms( $post_id, $taxonomy_cat );
            
            if($posttags || $taxterms){
                if($posttags) foreach ( $posttags as $tag )  $terms[]= $tag->term_id ;
                if($taxterms) foreach ( $taxterms as $term ) $terms[]= $term->term_id ; 
            }
        }

        $myposts = io_get_post_rankings($instance['range'], $instance['type'], $instance['number'], $terms, true);
        if($myposts && $myposts->have_posts()){
            switch ($instance['type']) {
                case 'post':
                    $html .= load_widgets_min_post_html($myposts,$instance);
                    break;
                case 'app':
                    $html .= load_widgets_min_app_html($myposts,$instance);
                    break;
                case 'book':
                    $html .= load_widgets_min_book_html($myposts,$instance);
                    break;
                case 'sites':
                default:
                    $html .= load_widgets_min_sites_html($myposts,$instance);
                    break;
            }
        }else{
            $html .= '<div class="d-flex h-100 w-100"><div class="empty-list p-4"><i class="iconfont icon-nothing1"></i></div></div>';
        }

        echo $html;
        echo'</div>';
        echo $args['after_widget'];
    }
}

