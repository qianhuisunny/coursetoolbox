<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-04-10 16:08:29
 * @LastEditors: iowen
 * @LastEditTime: 2023-03-30 01:17:07
 * @FilePath: \onenav\templates\bookmark\bm.footer.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }?>
<?php global $bookmark_id; ?>
    <?php show_ad('ad_footer_top',false) ?> 
    <footer class="main-footer footer-type-1 position-relative text-xs">
        <div id="footer-tools" class="d-flex flex-column">
            <a href="javascript:" id="go-to-up" class="btn rounded-circle go-up m-1" rel="go-top">
                <i class="iconfont icon-to-up"></i>
            </a>
            <?php 
            if(is_user_logged_in() && $bookmark_id=='default'){  
                global $current_user;
                $bm_id = base64_io_encode(sprintf("%08d", $current_user->ID));
            ?>
            <a href="<?php echo esc_url(home_url().'/bookmark/'. $bm_id) ?>" class="btn rounded-circle m-1 bookmark-user" data-bm_id="<?php echo $bm_id ?>" data-toggle="tooltip" data-placement="left" title="<?php _e('我的书签','i_theme') ?>" target="_blank">
                <i class="iconfont icon-tags"></i>
            </a>
            <?php } ?>
            <a href="<?php echo esc_url(home_url()) ?>" class="btn rounded-circle m-1" data-toggle="tooltip" data-placement="left" title="<?php _e('首页','i_theme') ?>" target="_blank">
                <i class="iconfont icon-home"></i>
            </a>
        </div>
        <div class="footer-inner text-center text-light py-3">
            <div class="footer-text">
                <?php io_copyright() ?>
            </div>
        </div>
    </footer> 
<?php wp_footer(); ?> 
<!-- 自定义代码 -->
<?php echo io_get_option('code_2_footer','');?>
<!-- end 自定义代码 -->
</body>
</html>