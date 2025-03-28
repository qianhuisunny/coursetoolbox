<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:02
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-04 01:25:41
 * @FilePath: \onenav\templates\card-sitemini.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }  ?>

<?php $sites_meta=get_sites_card_meta(); 
$is_goto   = $sites_meta["sites_type"] == "sites" && get_post_meta($post->ID, "_goto", true) ? true : false;
$max_url   = 'href="'.$sites_meta["url"].'" '                                              .$sites_meta["blank"]. ' ' .($is_goto ? "" : nofollow($sites_meta["link_url"], io_get_option("details_page",false)));
$goto_url  = 'href="'.($is_goto?$sites_meta['link_url']:go_to($sites_meta['link_url'])). '" target="_blank" '         .($is_goto ? "" : nofollow($sites_meta["link_url"]))                               .' title="'. __("直达","i_theme") .'"';
$max_views =$sites_meta['is_views'];
$goto_views='is-views';
if( $sites_meta['link_url']!="" && !get_post_meta($post->ID, '_goto', true) && io_get_option("details_page",false) && io_get_option("url_reverse",false) ){
    $goto_url  = 'href="'.$sites_meta["url"].'" '                                              .$sites_meta["blank"]. ' ' .($is_goto ? "" : nofollow($sites_meta["link_url"], io_get_option("details_page",false))).' title="'. __("详情","i_theme") .'"';
    $max_url   = 'href="'.($is_goto?$sites_meta['link_url']:go_to($sites_meta['link_url'])). '" target="_blank" '         .($is_goto ? "" : nofollow($sites_meta["link_url"]));
    $max_views ='is-views';
    $goto_views='';
}
$no_ico = io_get_option('no_ico',false)?'no_ico':'';
?>
        <div class="url-body mini <?php echo $no_ico ?>">   
            <a <?php echo $max_url ?> data-id="<?php echo $post->ID ?>" data-url="<?php echo rtrim($sites_meta['link_url'],"/") ?>" class="card <?php echo $max_views ?> mb-3 site-<?php echo $post->ID ?>" <?php echo $sites_meta['tooltip'] . ' ' . $sites_meta['is_html'] ?> title="<?php echo $sites_meta['tip_title'] ?>">
                <div class="card-body">
                <div class="url-content d-flex align-items-center">
                    <?php if('' === $no_ico) : ?>
                    <div class="url-img rounded-circle mr-2 d-flex align-items-center justify-content-center">
                        <?php
                        if($sites_meta['first_api_ico']){
                            echo get_lazy_img( $sites_meta['ico'], $sites_meta['title'], 'auto', '', $sites_meta['default_ico'], true,'onerror=null;src=ioLetterAvatar(alt,25)');
                        }else{
                            echo get_lazy_img( $sites_meta['ico'], $sites_meta['title'], 'auto', '', $sites_meta['default_ico']);
                        }
                        ?> 
                    </div>
                    <?php endif; ?>
                    <div class="url-info flex-fill" style="padding-top: 2px">
                        <div class="text-sm overflowClip_1">
                        <?php show_sticky_tag( is_sticky() ) . show_new_tag(get_the_time('Y-m-d H:i:s')) ?><strong><?php echo $sites_meta['title'] ?></strong>
                        </div>
                    </div>
                </div>
                </div>
            </a> 
            <?php if( $sites_meta['link_url']!="" && io_get_option("togo",false) && io_get_option("details_page",false) ) { ?>
            <a <?php echo $goto_url ?> class="togo text-center text-muted <?php echo $goto_views ?>" data-id="<?php echo $post->ID ?>" data-toggle="tooltip" data-placement="right"><i class="iconfont icon-goto"></i></a>
            <?php } ?>
        </div>
