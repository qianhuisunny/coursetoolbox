<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-03-01 10:19:07
 * @LastEditors: iowen
 * @LastEditTime: 2022-07-05 21:10:32
 * @FilePath: \onenav\templates\card-appcard.php
 * @Description: 
 */
/*!
 * Theme Name:One Nav
 * Theme URI:https://www.iotheme.cn/
 * Author:iowen
 * Author URI:https://www.iowen.cn/
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }  ?>
<?php 
$ico_info = get_post_meta(get_the_ID(), 'app_ico_o', true);
$bg = '';$size='';
if($ico_info && $ico_info['ico_a']){
    $bg ='style="background-image: linear-gradient(130deg, '.$ico_info['ico_color']['color-1'].', '.$ico_info['ico_color']['color-2'].');"';
    $size = 'background-size: '.$ico_info["ico_size"].'%';
}
?>
    <div class="card-app card">
        <div class="card-body align-items-center d-flex flex-fill py-3">
            <div class="media size-70 p-0 app-rounded" <?php echo $bg ?>>
                <?php if(io_get_option('lazyload',false)): ?>
                <a class="media-content" href="<?php the_permalink(); ?>" <?php echo new_window() ?> data-bg="url(<?php echo get_post_meta_img(get_the_ID(), '_app_ico', true) ?>)" style="<?php echo $size ?>"></a>
                <?php else: ?>
                <a class="media-content" href="<?php the_permalink(); ?>" <?php echo new_window() ?>  style="background-image: url(<?php echo get_post_meta_img(get_the_ID(), '_app_ico', true) ?>);<?php echo $size ?>"></a>
                <?php endif ?>
            </div>
            <div class="app-content flex-fill pl-2 pr-1">
                <div class="mb-2"><a href="<?php the_permalink(); ?>" <?php echo new_window() ?> class="text-md overflowClip_1"><?php show_sticky_tag( is_sticky() ) . show_new_tag(get_the_time('Y-m-d H:i:s')) ?><?php the_title(); ?><?php echo  '<span class="text-xs"> - '.get_post_meta(get_the_ID(), 'app_down_list', true)[0]['app_version'].'</span>' ?></a></div>
                <div class="text-muted text-xs overflowClip_1"><?php echo get_post_meta(get_the_ID(), '_app_sescribe', true) ?></div>
                <div class="app-like"> 
                    <div class="d-flex align-items-center" style="white-space: nowrap;">
                        <div class="tga text-xs">
                        <?php 
                        $post_tags = get_the_terms(get_the_ID(),'apptag');
                        if(!$post_tags) $post_tags = get_the_terms(get_the_ID(),'apps');
                        if ($post_tags) {
                            $c = count($post_tags)>4 ? 4 : count($post_tags);
                            for( $i = 0; $i < $c; $i++ ) {
                                echo '<span class="mr-1"><a href="'.get_tag_link($post_tags[$i]->term_id).'" rel="tag">' . $post_tags[$i]->name . '</a></span>';
                            }
                        } else {
                            echo '<span class="mr-1"><a class="no-tag">'.__('没添加标签','i_theme').'</a></span>';
                        }

                        ?>
                        </div>
                        <div class="flex-fill"></div>
                        <div class="text-muted text-xs text-center mr-1"> 
                            <span class="down"><i class="iconfont icon-down"></i> <?php echo get_post_meta(get_the_ID(), '_down_count', true)?:0 ?></span>
                            <?php like_home_button($post->ID,'app') ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-platform"><?php if( $platform=get_post_meta(get_the_ID(), '_app_platform', true) ) foreach($platform as $pl) {  echo '<i class="iconfont '.$pl.' mr-1"></i>'; }?></div>
        </div>
    </div>
