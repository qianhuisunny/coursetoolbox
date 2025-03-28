<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:55:57
 * @LastEditors: iowen
 * @LastEditTime: 2023-03-28 21:59:45
 * @FilePath: \onenav\author.php
 * @Description: 
 */
?>
<?php if ( ! defined( 'ABSPATH' ) ) { exit; }?>
<?php 
if(!io_get_option('user_center',false)){
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
    echo '<meta http-equiv="refresh" content="0;url='.esc_url(home_url()).'">';
    exit;
}
$author = get_user_by('ID', $author);
$user_bg = function_exists('io_get_user_cover')?io_get_user_cover($author->ID ,"full"):get_theme_file_uri('/images/user-default-cover-full.jpg');
?>
<?php get_header(); ?>
    <div class="user-bg d-flex" style="background-image: url(<?php echo $user_bg ?>)">
        <div class="container d-flex align-items-end position-relative mb-4"> 
            <?php echo get_avatar($author->ID,70) ?> 	
            <div class="author-meta-r ml-0 ml-md-3">
                <div class="h2 text-white mb-2"><?php echo $author->display_name; ?>
                    <small class="text-xs"><span class="badge vc-violet2 btn-outline mt-2">
                        <?php echo io_get_user_cap_string($author->ID) ?>
                    </span></small>
                </div>
                <div class="text-white text-sm"><?php echo get_user_desc($author->ID); ?></div>
            </div> 
        </div> 
    </div>
    <div id="content" class="container user-area my-4"> 
            <div class="card">
                <div class="card-body">
                    <div class="text-lg pb-3 border-bottom border-light border-2w mb-3"><?php _e('基本信息','i_theme') ?></div>  
                    <div class="row">
                    <?php echo io_author_con_datas($author->ID) ?>
                    </div>
                </div>
            </div>
	</div> 
<?php get_footer(); ?>