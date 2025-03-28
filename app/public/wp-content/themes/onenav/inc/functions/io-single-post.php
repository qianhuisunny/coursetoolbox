<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2023-03-23 22:31:10
 * @LastEditors: iowen
 * @LastEditTime: 2023-04-10 23:24:58
 * @FilePath: \onenav\inc\functions\io-single-post.php
 * @Description: 
 */

/**
 * post 头部
 * @return string
 */
function io_single_post_header(&$is_hide){
    global $post, $is_hide;

    $level_d = get_user_level_directions_html('post');
    if($level_d){
        $is_hide = true;
        return $level_d;
    }
    $is_hide = false;
    return '';
}

/**
 * post 正文
 * @return void
 */
function io_single_post_content(){
    global $post;
    $post_id    = $post->ID; 
    ?>
    <div class="panel card">
        <div class="card-body">
        <?php io_single_post_content_header() ?>
        <?php show_ad('ad_post_title_bottom', false, '<div class="post-apd">' , '</div>');  ?>
        <div class="panel-body single mt-2"> 
            <?php 
            do_action('io_single_before', 'post');
            the_content();
            thePostPage();
            do_action('io_single_after', 'post');
            ?>
        </div>
        <?php io_single_post_content_footer($post) ?> 
        <?php show_ad('ad_post_content_bottom', false, '<div class="post-apd">' , '</div>'); ?>
        </div>
    </div>
    <?php
    io_single_post_next_html();
}

function io_single_post_content_header(){
    global $post;
    $post_id   = $post->ID;
    $user_id   = get_the_author_meta('ID');
    $time_html = io_get_post_time();
    $view      = function_exists('the_views') ? the_views(false, '<span class="views mr-3"><i class="iconfont icon-chakan"></i> ', '</span>') : '';
    
    $category  = get_the_category();
    if(!empty($category) && $category[0]){
        $category = '<span class="mr-3 d-none d-sm-block"><a href="'.get_category_link($category[0]->term_id ).'"><i class="iconfont icon-classification"></i> '.$category[0]->cat_name.'</a></span>';
    }else{
        $category = '';
    }

    $html = '<div class="panel-header mb-4">';
    $html .= '<h1 class="h3 mb-3">'. get_the_title() .'</h1>';
    $html .= '<div class="d-flex flex-fill text-muted text-sm pb-4 border-bottom border-t">';
    $html .= $category;
    $html .= '<span class="mr-3"><i class="iconfont icon-time"></i>'.$time_html.'</span>';
    $html .= '<span class="mr-3"><a href="'.get_author_posts_url( $user_id ) .'" title="'. get_the_author_meta('nickname').'"><i class="iconfont icon-user-circle"></i> '.get_the_author_meta('nickname') .'</a></span>';
    $html .= io_get_post_edit_link( $post_id ,null,'<span class="edit-link text-sm text-muted">', '</span>');
    $html .= '<div class="flex-fill"></div>';
    $html .= $view;
    $html .= '<span class="mr-3"><a class="smooth-n" href="#comments"> <i class="iconfont icon-comment"></i> ' . $post->comment_count . '</a></span>';
    $html .= like_button($post_id, 'post', false);
    $html .= '</div>';
    $html .= '</div>';
    echo $html;
}

function io_single_post_content_footer($post){
    $tags = io_get_cat_tags_btn($post->ID, array('category', 'post_tag'),'# ');
    do_action('io_article_content_after', $post);

    if ($tags) {
        echo '<div class="post-tags my-3"><i class="iconfont icon-tags mr-2"></i>' . $tags . '</div>';
    }

    $copy = io_get_option('post_copyright_multi');
    if ($copy) {
        echo '<div class="text-xs text-muted"><div><span>©</span> '.__('版权声明','i_theme').'</div><div class="posts-copyright">' . $copy . '</div></div>';
    }
}

function io_single_post_next_html(){
    ?>
    <div class="near-navigation rounded mt-4 py-2">
        <?php
        $prev_post = get_previous_post(true);//与当前文章同分类的上一篇文章
        $next_post = get_next_post(true);//与当前文章同分类的下一篇文章
        ?>
        <?php if (!empty( $prev_post )) { ?>
        <div class="nav previous border-right border-color">
            <a class="near-permalink" href="<?php echo get_permalink( $prev_post->ID ); ?>">
            <span><?php _e('上一篇','i_theme') ?></span>
            <h4 class="near-title"><?php echo $prev_post->post_title; ?></h4>
            </a>
        </div>
        <?php } else { ?>
        <div class="nav none border-right border-color">
            <span><?php _e('上一篇','i_theme') ?></span>
            <h4 class="near-title"><?php _e('没有更多了...','i_theme') ?></h4>
        </div>
        <?php } ?>
        <?php if (!empty( $next_post )) { ?>
        <div class="nav next border-left border-color">
            <a class="near-permalink" href="<?php echo get_permalink( $next_post->ID ); ?>">
            <span><?php _e('下一篇','i_theme') ?></span>
            <h4 class="near-title"><?php echo $next_post->post_title; ?></h4>
        </a>
        </div>
        <?php } else { ?>
        <div class="nav none border-left border-color" style="text-align: right;">
            <span><?php _e('下一篇','i_theme') ?></span>
            <h4 class="near-title"><?php _e('没有更多了...','i_theme') ?></h4>    
        </div>
        <?php } ?>
    </div>
    <?php
}