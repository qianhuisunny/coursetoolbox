<?php 
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-03-01 10:19:00
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-21 18:11:26
 * @FilePath: \onenav\404.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); 
?>
<div class="container">
    <div class="row">
        <div class="col-12 col-lg-12">
            <section class="text-center py-5 my-5">
                <h1 class="h3 d-none">404</h1>
                <img src="<?php echo get_theme_file_uri('/images/404.svg') ?>" style="max-width: 560px;" alt="404"/>
                <p class="text-sm text-muted mt-3"><?php _e('抱歉，没有你要找的内容...','i_theme') ?></p>
                <div style="margin-top: 30px">
                    <a class="btn btn-shadow vc-red btn-lg px-5 rounded-pill text-lg" href="<?php echo esc_url( home_url() ) ?>"><?php _e('返回首页','i_theme') ?></a>
                </div>
            </section>
        </div>
    </div>
</div><!-- main-content end -->
<?php get_footer(); ?>