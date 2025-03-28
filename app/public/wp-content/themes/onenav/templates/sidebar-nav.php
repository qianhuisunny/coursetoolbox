<?php
/*!
 * Theme Name:One Nav
 * Theme URI:https://www.iotheme.cn/
 * Author:iowen
 * Author URI:https://www.iowen.cn/
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<?php 

$is_min_nav = false;
$categories = io_get_menu_categories($is_min_nav);

$logo_class = '';
$logo_light_class = 'class="d-none"';
if(io_get_option('theme_mode',"io-grey-mode")=="io-grey-mode"){
    $logo_class = 'class="logo-dark d-none"';
    $logo_light_class = 'class="logo-light"';
} 
$is_home = ( is_home() || is_front_page() || is_mininav() );
$smooth = $is_home?'smooth':'';
?>
        <div id="sidebar" class="sticky sidebar-nav fade">
            <div class="modal-dialog h-100  sidebar-nav-inner">
                <div class="sidebar-logo border-bottom border-color">
                    <!-- logo -->
                    <div class="logo overflow-hidden">
                        <?php if ( $is_home ) : ?>
                        <h1 class="text-hide position-absolute"><?php bloginfo('name') ?></h1>
                        <?php endif; ?>
                        <a href="<?php echo esc_url( home_url() ) ?>" class="logo-expanded">
                            <img src="<?php echo io_get_option('logo_normal_light','') ?>" height="40" <?php echo $logo_light_class ?> alt="<?php bloginfo('name') ?>">
                            <img src="<?php echo io_get_option('logo_normal','') ?>" height="40" <?php echo $logo_class ?> alt="<?php bloginfo('name') ?>">
                        </a>
                        <a href="<?php echo esc_url( home_url() ) ?>" class="logo-collapsed">
                            <img src="<?php echo io_get_option('logo_small_light','') ?>" height="40" <?php echo $logo_light_class ?> alt="<?php bloginfo('name') ?>">
                            <img src="<?php echo io_get_option('logo_small','') ?>" height="40" <?php echo $logo_class ?> alt="<?php bloginfo('name') ?>">
                        </a>
                    </div>
                    <!-- logo end -->
                </div>
                <div class="sidebar-menu flex-fill">
                    <div class="sidebar-scroll" >
                        <div class="sidebar-menu-inner">
                            <ul> 
                                <?php if( $is_min_nav ): ?>
                                <li class="sidebar-item">
                                    <?php $callback = isset($_GET['mininav-id'])?esc_url(get_permalink(intval($_GET['mininav-id']))):esc_url(home_url()); ?>
                                    <a href="<?php echo $callback ?>" class=" change-href">
                                        <i class="<?php echo io_get_option('back_to_top_ico','iconfont icon-back') ?> icon-fw icon-lg"></i>
                                        <span><?php _e('返回上级','i_theme') ?></span>
                                    </a>
                                </li>  
                                <?php endif; ?>
                                <?php if( wp_is_mobile() && $nav_name = _iol(io_get_option('nav_top_mobile','') )): ?>
                                <li class="sidebar-item top-menu">
                                    <a href="javascript:;" class="<?php echo $smooth ?>  change-href">
                                        <i class="<?php echo io_get_option('nav_top_mobile_ico','iconfont icon-category') ?> icon-fw icon-lg"></i>
                                        <span><?php echo $nav_name ?></span>
                                    </a>
                                    <i class="iconfont icon-arrow-r-m sidebar-more text-sm"></i>
                                    <ul>
                                        <?php wp_menu('main_menu'); ?>
                                    </ul>
                                </li> 
                                <?php endif;
                                /**
                                 * -----------------------------------------------------------------------
                                 * HOOK : ACTION HOOK
                                 * io_before_show_sidebar_nav
                                 * 
                                 * 在侧边栏导航前挂载其他菜单。
                                 * 注意菜单html结构
                                 * @since  3.xxx
                                 * -----------------------------------------------------------------------
                                 */
                                do_action( 'io_before_show_sidebar_nav' );
                            $base_url = isset($_GET['mininav-id'])?get_permalink(intval($_GET['mininav-id'])):esc_url(home_url()).'/';
                            foreach($categories as $category) {
                                if(get_post_meta( $category['ID'], 'purview', true )<=io_get_user_level()):
                                if($category['menu_item_parent'] == 0){ 
                                    if($category['type'] != 'taxonomy' && empty($category['submenu'])){
                                        //处理不是分类类型的菜单项
                                        $icon = get_tag_ico('',$category,'iconfont icon-category'); 
                                        $url = trim($category['url']);
                                        if( strlen($url)>1 && substr( $url, 0, 1 ) == '#') { ?>
                                            <li class="sidebar-item">
                                                <a href="<?php echo( $is_home?'': $base_url ) ?><?php echo $url ?>" class="<?php echo $smooth ?>">
                                                    <i class="<?php echo $icon ?> icon-fw icon-lg"></i>
                                                    <span><?php echo $category['title']; ?></span>
                                                </a>
                                            </li> 
                                        <?php }elseif( strlen($url)>1 && substr( $url, 0, 4 ) == 'http'){ ?>
                                            <li class="sidebar-item">
                                                <a href="<?php echo $url ?>" target="<?php echo $category['target'] ?>">
                                                    <i class="<?php echo $icon ?> icon-fw icon-lg"></i>
                                                    <span><?php echo $category['title']; ?></span>
                                                </a>
                                            </li> 
                                        <?php
                                        }
                                        continue;
                                    }else{
                                        $icon = get_tag_ico('',$category,'iconfont icon-category');
                                    }
                                    if(empty($category['submenu'])){ // 不含子菜单处理 ?>
                                        <li class="sidebar-item">
                                            <a href="<?php echo( $is_home?'':  $base_url ) ?>#term-<?php echo $category['object_id'];?>" class="<?php echo $smooth ?>">
                                                <i class="<?php echo $icon ?> icon-fw icon-lg"></i>
                                                <span><?php echo $category['title']; ?></span>
                                            </a>
                                        </li> 
                                    <?php }else {  // 含子菜单处理
                                        $open = !io_get_option('min_nav',false)&&get_post_meta( $category['ID'], 'open', true )?' sidebar-show':''; //默认展开
                                    ?>
                                        <li class="sidebar-item<?php echo $open ?>">
                                            <?php $href_change =  ($is_home?'': $base_url ).'#term-' .$category['object_id']; ?>
                                            <a href="<?php echo $href_change ?>" class="<?php echo $smooth ?>" data-change="<?php echo $href_change ?>">
                                                <i class="<?php echo $icon ?> icon-fw icon-lg"></i>
                                                <span><?php echo $category['title']; ?></span>
                                            </a>
                                            <i class="iconfont icon-arrow-r-m sidebar-more text-sm"></i>
                                            <ul <?php echo $open!=''?'style="display:block"':'' ?>>
                                            <?php foreach ($category['submenu'] as $mid) { 
                                                $url = trim($mid['url']); 
                                                if( $mid['type'] != 'taxonomy' && strlen($url)>1 && substr( $url, 0, 4 ) == 'http'){ 
                                                ?>
                                                <li>
                                                    <a href="<?php echo $url ?>" target="<?php echo $mid['target'] ?>">
                                                        <span><?php echo $mid['title']; ?></span>
                                                    </a>
                                                </li> 
                                                <?php }else{ ?> 
                                                <li>
                                                    <a href="<?php echo (  $is_home?'': $base_url  ) ?>#term-<?php echo $category['object_id'] . '-' . $mid['object_id'];?>" class="<?php echo $smooth ?>"><span><?php echo $mid['title']; ?></span></a>
                                                </li>
                                            <?php }} ?>
                                            </ul>
                                        </li>
                                <?php }
                                } 
                                endif;
                            }
                            /**
                             * -----------------------------------------------------------------------
                             * HOOK : ACTION HOOK
                             * io_after_show_sidebar_nav
                             * 
                             * 在侧边栏导航后挂载其他菜单。
                             * 注意菜单html结构
                             * @since  3.xxx
                             * -----------------------------------------------------------------------
                             */
                            do_action( 'io_after_show_sidebar_nav' );
                            ?> 
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="border-top py-2 border-color">
                    <div class="flex-bottom">
                        <ul> 
                            <?php wp_menu('nav_main');?> 
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        