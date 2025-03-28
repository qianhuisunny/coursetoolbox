<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:02
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-04 01:36:13
 * @FilePath: \onenav\templates\slide-blog.php
 * @Description: 文章轮播模块
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } 

$swiper_data = array();

$set_count   = io_get_option('article_n',5);
$img_ad      = io_get_option('carousel_img',array());

if($img_ad){
    foreach($img_ad as $ad){
        if($set_count>0){
            $swiper_data[] = $ad;
            $set_count--;
        }
    }
}
if($set_count>0){
    $query_post = array(
        'post_type' => 'post',
        'posts_per_page' => $set_count,
        'post__in'       => get_option('sticky_posts'),
        'ignore_sticky_posts' => 1,
    );
    $the_query = new WP_Query($query_post);
    if(!$the_query->have_posts()){
        wp_reset_postdata(); 
        $query_post = array(
            'post_type' => 'post',
            'posts_per_page' => $set_count,
            'ignore_sticky_posts' => 1,
        );
        $the_query = new WP_Query($query_post);
    }
    while($the_query->have_posts()):$the_query->the_post(); 
        $data = array();
        $data['url'] =  get_permalink();
        $data['img'] =  io_theme_get_thumb();
        $data['is_ad'] =  false;
        $data['title'] =  get_the_title();
        $swiper_data[] = $data;
    endwhile; 
    wp_reset_postdata(); 
} 
$swiper_css  = 'col-12 col-md-7 col-lg-8 col-xl-6';
$article_css = 'col-12 col-xl-3 d-none d-xl-block pl-0 pl-md-2';
if( is_blog() ){
    $swiper_css  = 'col-12 col-lg-8';
    $article_css = 'col-12 col-lg-4 d-none d-lg-block pl-0 pl-md-2';
}
?>
<div class="<?php echo $swiper_css ?>"> 
    <?php echo io_get_swiper( $swiper_data ) ?>
</div>
<div class="<?php echo $article_css ?>">
    <div class="h-100 d-flex flex-column justify-content-between">
    <?php
    if(io_get_option('two_article','')){
        $args = array(
            'post__in' => explode(',', io_get_option('two_article','')),
            'orderby'        => 'post__in', 
            'posts_per_page' => -1
        );
    }else{
        $args = array( 
            'numberposts' => 2, 
            'post__not_in' => get_option( 'sticky_posts' ),
            'orderby' => 'rand', 
            'post_status' => 'publish' 
        );
    }
    $rand_posts = get_posts( $args );
    foreach( $rand_posts as $post ) : ?>  
        <div class="media media-21x9 rounded-xl">
            <?php if(io_get_option('lazyload',false)): ?>
            <a class="media-content media-title-bg" href="<?php the_permalink(); ?>" <?php echo new_window() ?> data-src="<?php echo  io_theme_get_thumb() ?>">
            <?php else: ?>
            <a class="media-content media-title-bg" href="<?php the_permalink(); ?>" <?php echo new_window() ?>  style="background-image: url(<?php echo  io_theme_get_thumb() ?>);">
            <?php endif ?>
                <span class="media-title d-none d-md-block overflowClip_1"><?php the_title(); ?></span>
            </a>                                                       
        </div> 
    <?php endforeach; wp_reset_postdata(); ?>                       
    </div>
</div>