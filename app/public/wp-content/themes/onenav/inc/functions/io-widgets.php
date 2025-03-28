<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-07-09 14:00:16
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-04 01:16:32
 * @FilePath: \onenav\inc\functions\io-widgets.php
 * @Description: 
 */


/**
 * 侧边栏网址html
 * 
 * @param object $myposts
 * @param array $instance 选项
 * @param int $index 计数
 * @return array|string
 */
function load_widgets_min_sites_html($myposts, $instance, $index=''){
    $newWindow = '';
    if ((isset($instance['window']) && $instance['window']) || 
        (isset($instance['newWindow']) && $instance['newWindow']) || //向下
        (isset($instance['new-window']) && $instance['new-window']) //向下
    ){
        $newWindow = "target='_blank'";
    }
    $temp = isset($instance['before_div']) ? $instance['before_div'] : '';  
    if(!$myposts->have_posts()): 
        $temp .= '<div class="col-lg-12"><div class="nothing mb-4">'.__('没有数据！','i_theme').'</div></div>';
    elseif ($myposts->have_posts()): 
        while ($myposts->have_posts()): $myposts->the_post();  
            $sites_meta     = get_sites_card_meta(); 
            $sites_type     = $sites_meta['sites_type'];
            $link_url       = $sites_meta['link_url'];
            $default_ico    = $sites_meta['default_ico'];
            $ico            = $sites_meta['ico'];
            $url            = get_permalink();
            $nofollow       = '';
            $is_views       = '';
            if(($instance['go'] && $sites_type == "sites" && $link_url != '') || ($sites_type == "sites" && $link_url != '' && !io_get_option('details_page',false))){
                $url        = $instance['nofollow'] ? $link_url : go_to($link_url);
                $nofollow   = $instance['nofollow'] ? '' : nofollow($link_url);
                $is_views   = 'is-views ';
            }
            $temp .= '<div class="url-card col-6 '. before_class(get_the_ID()) .' my-1">';
            $temp .= '<a href="'.$url.'" '.$newWindow.' '.$nofollow.' class="'.$is_views.'card post-min m-0" data-url="'.($link_url ? : get_permalink()).'" data-id="'.get_the_ID().'">';
            $temp .= '<div class="card-body" style="padding:0.3rem 0.5rem;"><div class="url-content d-flex align-items-center"><div class="url-img rounded-circle">';
            if($sites_meta['first_api_ico']){
                $temp .= get_lazy_img( $ico, $sites_meta['title'], 'auto', '', $default_ico, true,'onerror=null;src=ioLetterAvatar(alt,40)');
            }else{
                $temp .= get_lazy_img( $ico, $sites_meta['title'], 'auto', '', $default_ico);
            }
            $temp .= '</div><div class="url-info pl-1 flex-fill"><div class="text-xs overflowClip_1">'.$sites_meta['title'].'</div></div></div></div>';
            $temp .= '</a></div>';
            if(''!==$index)$index++; 
        endwhile; 
    endif;
    $temp .= isset($instance['after_div']) ? $instance['after_div'] : '';  
    if(''===$index){
        return $temp;
    }
    return array(
        'html'  =>$temp,
        'index' =>$index,
    );
}

/**
 * 侧边栏文章html
 * 
 * @param object $myposts
 * @param array $instance 选项
 * @param int $index 计数
 * @return array|string
 */
function load_widgets_min_post_html($myposts, $instance, $index=''){
    $newWindow      = '';
    $show_thumbs    = isset($instance['show_thumbs']) ? $instance['show_thumbs'] : true;
    $meta_key       = isset($instance['meta-key']) ? $instance['meta-key'] : 'views';

    if ((isset($instance['window']) && $instance['window']) || 
        (isset($instance['newWindow']) && $instance['newWindow']) || //向下
        (isset($instance['new-window']) && $instance['new-window']) //向下
    ){
        $newWindow = "target='_blank'";
    }
    $temp = isset($instance['before_div']) ? $instance['before_div'] : '';   
    if(!$myposts->have_posts()): 
        $temp .= '<div class="col-lg-12"><div class="nothing mb-4">'.__('没有数据！','i_theme').'</div></div>';
    elseif ($myposts->have_posts()): 
        while ($myposts->have_posts()): $myposts->the_post();  
            $post_title = get_the_title();
            switch ( $meta_key ){
                case 'views':
                    $s_data = !function_exists( 'the_views' )?'': the_views( false, '<span class="views"><i class="iconfont icon-chakan"></i> ','</span>' );
                    break;
                case 'like': 
                    $s_data ='<span class="discuss"><i class="iconfont icon-like"></i>'.get_like(get_the_ID(),'post').'</span>';
                    break;
                case 'comment': 
                    $s_data = '<span class="discuss"><i class="iconfont icon-comment"></i>'.get_comments_number().'</span>';
                    break;
                default:  
                    $s_data = '<span class="discuss"><i class="iconfont icon-comment"></i>'.get_comments_number().'</span>';
            }
            $temp .= "<div class='list-item py-2'>";
            if($show_thumbs){
                $temp .= "<div class='media media-3x2 rounded col-4 mr-3'>";
                $thumbnail =  io_theme_get_thumb();
                if(io_get_option('lazyload',false))
                    $temp .= '<a class="media-content" href="'.get_permalink().'" '. $newWindow .' title="'.get_the_title().'" data-src="'.$thumbnail.'"></a>';
                else
                    $temp .= '<a class="media-content" href="'.get_permalink().'" '. $newWindow .' title="'.get_the_title().'" style="background-image: url('.$thumbnail.');"></a>';
                $temp .= "</div>"; 
            }
            $temp .= '
                <div class="list-content py-0">
                    <div class="list-body">
                        <a href="'.get_permalink().'" class="list-title overflowClip_2" '. $newWindow .' rel="bookmark">'.get_the_title().'</a>
                    </div>
                    <div class="list-footer">
                        <div class="d-flex flex-fill text-muted text-xs">
                            <time class="d-inline-block">'.timeago(get_the_time('Y-m-d G:i:s')).'</time>
                            <div class="flex-fill"></div>' 
                            .$s_data.
                        '</div>
                    </div> 
                </div> 
            </div>'; 
            if(''!==$index)$index++; 
        endwhile; 
    endif;
    $temp .= isset($instance['after_div']) ? $instance['after_div'] : '';  
    if(''===$index){
        return $temp;
    }
    return array(
        'html'  =>$temp,
        'index' =>$index,
    );
}

/**
 * 侧边栏 APP html
 * 
 * @param object $myposts
 * @param array $instance 选项
 * @param int $index 计数
 * @return array|string
 */
function load_widgets_min_app_html($myposts, $instance, $index=''){
    $new_window      = false;

    if ((isset($instance['window']) && $instance['window']) || 
        (isset($instance['newWindow']) && $instance['newWindow']) || //向下
        (isset($instance['new-window']) && $instance['new-window']) //向下
    ){
        $new_window = true;
    }
    $temp = isset($instance['before_div']) ? $instance['before_div'] : '';  
    if(!$myposts->have_posts()): 
        $temp .= '<div class="col-lg-12"><div class="nothing mb-4">'.__('没有数据！','i_theme').'</div></div>';
    elseif ($myposts->have_posts()): 
        while ($myposts->have_posts()): $myposts->the_post();  
            $temp .= '<div class="col-12">';
            ob_start();
            include( get_theme_file_path('/templates/card-appmin.php') ); 
            $temp .= ob_get_contents();
            ob_end_clean();
            $temp .= '</div>';
            if(''!==$index)$index++; 
        endwhile; 
    endif;
    $temp .= isset($instance['after_div']) ? $instance['after_div'] : '';  
    if(''===$index){
        return $temp;
    }
    return array(
        'html'  => $temp,
        'index' => $index,
    );
}

/**
 * 侧边栏书籍html
 * 
 * @param object $myposts
 * @param array $instance 选项
 * @param int $index 计数
 * @return array|string
 */
function load_widgets_min_book_html($myposts, $instance, $index=''){
    $new_window      = false;

    if ((isset($instance['window']) && $instance['window']) || 
        (isset($instance['newWindow']) && $instance['newWindow']) || //向下
        (isset($instance['new-window']) && $instance['new-window']) //向下
    ){
        $new_window = true;
    }
    $temp = isset($instance['before_div']) ? $instance['before_div'] : '';  
    if(!$myposts->have_posts()): 
        $temp .= '<div class="col-lg-12"><div class="nothing mb-4">'.__('没有数据！','i_theme').'</div></div>';
    elseif ($myposts->have_posts()): 
        while ($myposts->have_posts()): $myposts->the_post();  
            $temp .= '<div class="col-4">';
            ob_start();
            include( get_theme_file_path('/templates/card-book.php') ); 
            $temp .= ob_get_contents();
            ob_end_clean();
            $temp .= '</div>';
            if(''!==$index)$index++; 
        endwhile; 
    endif;
    $temp .= isset($instance['after_div']) ? $instance['after_div'] : '';  
    if(''===$index){
        return $temp;
    }
    return array(
        'html'  => $temp,
        'index' => $index,
    );
}