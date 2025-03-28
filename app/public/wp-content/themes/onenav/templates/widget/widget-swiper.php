<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-01-13 19:56:02
 * @LastEditors: iowen
 * @LastEditTime: 2023-04-04 00:10:02
 * @FilePath: \onenav\templates\widget\widget-swiper.php
 * @Description: big轮播
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if( is_home() || is_front_page() ) {
    $datas = io_get_option('home_widget_swiper',array());
}else{
    $datas = get_post_meta(get_queried_object_id(),'widget_swiper',true);
} 
if(is_array($datas) && count($datas)>0){
?>
<div class="swiper-widgets-card position-relative">
    <div class="swiper swiper-widgets rounded-xl">
        <div class="swiper-wrapper">
            <?php foreach($datas as $data){ ?>
            <div class="swiper-slide media media-21x9">
                <div class="media-content media-title-bg" <?php echo get_lazy_img_bg($data['img']) ?>>
                    <div class="media-title d-flex align-items-center">
                        <div class="flex-fill swiper-widgets-content">
                            <p class="text-sm position-relative pl-3"><?php echo _iol($data['title']) ?></p>
                            <h3 class="d-none d-md-block mt-2"><?php echo _iol($data['info']) ?></h3>
                            <?php if($data['type']!="img" ){ ?>
                            <a href="<?php echo get_term_link((int)$data['cat']) ?>" class="btn btn-detailed px-4 px-lg-5 py-lg-2 mt-2 mt-lg-3"><?php _e('查看详情','i_theme') ?></a>
                            <?php if(!wp_is_mobile()){ ?>
                            <div class="d-none d-md-flex justify-content-end mt-n4 mt-lg-0">
                                <div class="swiper swiper-term-content">
                                    <div class="swiper-wrapper">
                                        <?php echo get_tab_post_html($data,'swiper','swiper-slide mx-1 mx-lg-2') ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php }else{ ?>
                            <a href="<?php echo($data['is_ad']?$data['url']:go_to($data['url'])) ?>" target="_blank" class="btn btn-detailed px-4 px-lg-5 py-lg-2 mt-2 mt-lg-3"><?php _e('查看详情', 'i_theme') ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div> 
            </div> 
            <?php } ?>
        </div> 
    </div>
    <div class="swiper swiper-widgets-thumbs">
        <div class="swiper-wrapper">
            <?php foreach($datas as $data){ ?>
            <div class="swiper-slide tab-card position-relative d-block mx-2">
                <div class="img-post media media-16x9 rounded-xl overflow-hidden" >
                    <div class="media-content img-rounded img-responsive" <?php echo get_lazy_img_bg($data['img']) ?>></div>
                    <div class="caption d-flex align-items-center h-100 position-absolute"><span class="overflowClip_2 text-sm"><?php echo _iol($data['title']) ?></span></div>
                </div> 
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>