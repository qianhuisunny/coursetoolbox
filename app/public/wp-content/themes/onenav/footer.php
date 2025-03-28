<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:55:57
 * @LastEditors: iowen
 * @LastEditTime: 2023-04-20 23:14:47
 * @FilePath: \onenav\footer.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }?>
<?php
if($bm_route = get_query_var('bookmark_id')){
    get_template_part( 'templates/bookmark/bm.footer' ); 
    exit;
}
show_ad('ad_footer_top',false);
/**
 * -----------------------------------------------------------------------
 * HOOK : ACTION HOOK
 * io_before_footer
 * 
 * 在<footer>前挂载其他菜单。
 * @since  3.xxx
 * -----------------------------------------------------------------------
 */
do_action( 'io_before_footer' );

$socials = io_get_option('footer_social','');
$footer_class = array(
    'footer'    => 'p-4 footer-type-def',
    'content'   => '',
    'body'      => ''
);
if(io_get_option('footer_layout','def')=="big"){
    $footer_class = array(
        'footer'    => 'container container-fluid customize-width pt-4 pb-3 footer-type-big',
        'content'   => 'card rounded-xl m-0',
        'body'      => 'card-body text-muted text-center text-md-left'
    );
}
?> 
<div class="main-footer footer-stick <?php echo $footer_class['footer'] ?>">

    <div class="footer-inner <?php echo $footer_class['content'] ?>">
        <div class="footer-text <?php echo $footer_class['body'] ?>">
            <?php if(io_get_option('footer_layout','def')=="big"): ?>
            <div class="row my-4">
                <?php if(!wp_is_mobile() || (wp_is_mobile() && io_get_option('footer_t1',true))): ?>
                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <a class="footer-logo" href="<?php echo esc_url( home_url() ) ?>" title="<?php bloginfo('name') ?>">
                        <img src="<?php echo io_get_option('logo_normal_light','') ?>" class="logo-light mb-3" alt="<?php bloginfo('name') ?>" height="40">
                        <img src="<?php echo io_get_option('logo_normal','') ?>" class="logo-dark d-none mb-3" alt="<?php bloginfo('name') ?>" height="40">
                    </a>
                    <div class="text-sm"><?php echo io_get_option('footer_t1_code','') ?></div>
                </div>
                <?php endif; ?>
                <div class="col-12 col-md-5 mb-4 mb-md-0"> 
                    <?php if(io_get_option('footer_t2_code','')) echo'<p class="footer-links text-sm mb-3">'.io_get_option('footer_t2_code','').'</p>' ?>
                    <?php if(io_get_option('footer_t2_nav','')): ?>
                    <div class="footer-nav-links d-flex justify-content-center justify-content-md-start text-sm mb-3 ">
                    <?php wp_nav_menu( array('menu' => io_get_option('footer_t2_nav',''),'container' => false, 'items_wrap' => '%3$s')) ?>
                    </div>
                    <?php endif; ?>
                    <div class="footer-social">
                        <?php 
                        if(is_array($socials) && count($socials)>0){
                            foreach($socials as $social){
                                if ($social['loc']!="tools") {
                                    if ($social['type']=='img') {
                                        ?><a class="rounded-circle bg-light qr-img" href="javascript:;" data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='<?php echo $social['url'] ?>' height='100' width='100'>">
                                    <i class="<?php echo $social['ico'] ?>"></i>
                                </a><?php
                                    } else {
                                        $url = $social['url'];
                                        if(preg_match('|wpa.qq.com(.*)uin=([0-9]+)\&|',$url,$matches)){
                                            $url = IOTOOLS::qq_url($matches[2]);
                                        }
                                        ?><a class="rounded-circle bg-light" href="<?php echo $url ?>" target="_blank"  data-toggle="tooltip" data-placement="top" title="<?php echo $social['name'] ?>" rel="external noopener nofollow">
                                    <i class="<?php echo $social['ico'] ?>"></i>
                                </a><?php
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php if(!wp_is_mobile() || (wp_is_mobile() && io_get_option('footer_t3',false))): ?>
                <div class="col-12 col-md-3 text-md-right mb-4 mb-md-0">
                <?php
                $f_imgs = io_get_option('footer_t3_img','');
                if (is_array($f_imgs) && count($f_imgs)>0) {
                    foreach ($f_imgs as $f_img) {
                ?>
                    <div class="footer-mini-img" data-toggle="tooltip" title="<?php echo $f_img['text'] ?>">
                        <p class="bg-light rounded-lg p-1">
                            <img class=" " src="<?php echo $f_img['image'] ?>" alt="<?php  echo $f_img['text'] . get_bloginfo('name') ?>">
                        </p>
                        <span class="text-muted text-ss mt-2"><?php echo $f_img['text'] ?></span>
                    </div>
                <?php
                    }
                }
                ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="footer-copyright text-xs">
            <?php io_copyright() ?>
            </div>
        </div>
    </div>
</div>
</div><!-- main-content end -->

<footer>
    <div id="footer-tools" class="d-flex flex-column">
        <a href="javascript:" id="go-to-up" class="btn rounded-circle go-up m-1" rel="go-top">
            <i class="iconfont icon-to-up"></i>
        </a>
        <?php  
        if(is_array($socials) && count($socials)>0){
            $index = 0;
            foreach($socials as $social){
                if ($social['loc']!="footer") {
                    if ($social['type']=='img') {
                        ?><a class="btn rounded-circle custom-tool<?php echo $index ?> m-1 qr-img" href="javascript:;" data-toggle="tooltip" data-html="true" data-placement="left" title="<img src='<?php echo $social['url'] ?>' height='100' width='100'>">
                    <i class="<?php echo $social['ico'] ?>"></i>
                </a><?php
                    } else {
                        $url = $social['url'];
                        if(preg_match('|wpa.qq.com(.*)uin=([0-9]+)\&|',$url,$matches)){
                            $url = IOTOOLS::qq_url($matches[2]);
                        }
                        ?><a class="btn rounded-circle custom-tool<?php echo $index ?> m-1" href="<?php echo $url ?>" target="_blank"  data-toggle="tooltip" data-placement="left" title="<?php echo $social['name'] ?>" rel="external noopener nofollow">
                    <i class="<?php echo $social['ico'] ?>"></i>
                </a><?php
                    }
                }
                $index++;
            }
        }
        ?>
        <?php if( io_get_option('search_position',array('home')) && in_array("tool",io_get_option('search_position',array('home'))) ){ ?>
        <a href="javascript:" data-toggle="modal" data-target="#search-modal" class="btn rounded-circle m-1" rel="search">
            <i class="iconfont icon-search"></i>
        </a>
        <?php } ?>
        <?php if(io_get_option('weather',false) && io_get_option('weather_location','footer')=='footer'){ ?>
        <!-- 天气  -->
        <div class="btn-weather rounded-circle weather m-1">
            <?php io_get_weather_widget() ?>
        </div>
        <!-- 天气 end -->
        <?php } ?>
        <?php 
        if(io_get_option('user_center',false) && io_get_option('mini_panel',true)){
            if(is_user_logged_in()){  
                global $current_user; 
                $bm_id = base64_io_encode(sprintf("%08d", $current_user->ID));
        ?>
        <a href="<?php echo esc_url(home_url()).'/bookmark/' . $bm_id ?>" class="btn rounded-circle m-1 bookmark-user" data-bm_id="<?php echo $bm_id ?>" data-toggle="tooltip" data-placement="left" title="<?php _e('我的书签','i_theme') ?>" target="_blank">
            <i class="iconfont icon-tags"></i>
        </a>
        <?php } ?>
        <?php  ?>
        <a href="<?php echo esc_url(home_url()).'/bookmark/' ?>" class="btn rounded-circle m-1 bookmark-home" data-toggle="tooltip" data-placement="left" title="<?php _e('mini 书签','i_theme') ?>">
            <i class="iconfont icon-minipanel"></i>
        </a>
        <?php } ?>
        <?php if (io_get_option('theme_auto_mode', 'manual-theme')!='null'){?>
        <a href="javascript:" id="switch-mode" class="btn rounded-circle switch-dark-mode m-1" data-toggle="tooltip" data-placement="left" title="<?php _e('夜间模式','i_theme') ?>">
            <i class="mode-ico iconfont icon-light"></i>
        </a>
        <?php } ?>
    </div>
</footer>
<?php 
/**
 * -----------------------------------------------------------------------
 * HOOK : ACTION HOOK
 * io_after_footer
 * 
 * 在</footer>后挂载其他内容。
 * @since  3.xxx
 * -----------------------------------------------------------------------
 */
do_action( 'io_after_footer' );
?>
<?php if(io_get_option('search_position',array('home')) && ( in_array("top",io_get_option('search_position',array('home'))) || in_array("tool",io_get_option('search_position',array('home'))) ) ){ ?>  
<div class="modal fade search-modal" id="search-modal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">  
            <div class="modal-body">
                <?php get_template_part( 'templates/search/modal' ); ?>  
                <div class="px-1 mb-3"><i class="text-xl iconfont icon-hot mr-1" style="color:#f1404b;"></i><span class="h6"><?php _e('热门推荐：','i_theme') ?> </span></div>
                <div class="mb-3">
                    <?php wp_menu("search_menu") ?>
                </div>
            </div>  
            <div style="position: absolute;bottom: -40px;width: 100%;text-align: center;"><a href="javascript:" data-dismiss="modal"><i class="iconfont icon-close-circle icon-2x" style="color: #fff;"></i></a></div>
        </div>
    </div>  
</div>
<?php } ?>
<?php wp_footer(); ?> 
<!-- 自定义代码 -->
<?php echo io_get_option('code_2_footer','');?>
<!-- end 自定义代码 -->
</body>
</html>