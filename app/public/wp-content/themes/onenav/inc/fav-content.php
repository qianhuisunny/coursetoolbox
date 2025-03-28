<?php
/*!
 * Theme Name:One Nav
 * Theme URI:https://www.iotheme.cn/
 * Author:iowen
 * Author URI:https://www.iowen.cn/
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * 获取不同页面的分类显示数量
 */
function get_card_num(){
    $post_id = isset($_REQUEST['post_id'])?sanitize_key($_REQUEST['post_id']):get_queried_object_id();
    $default = array(
        'favorites'   => 20,
        'apps'        => 16,
        'books'       => 16,
        'category'    => 16,
    );
    $quantity = $default;
    if (is_home() || is_front_page() || ( $post_id==0 && defined( 'DOING_AJAX' ) && DOING_AJAX)) {
        $quantity = io_get_option('card_n', $default);
    }else{ 
        if(get_post_meta( $post_id, '_count_type', true )){
            $quantity = get_post_meta($post_id, 'card_n', true)?:$default;
        }else{
            $quantity = io_get_option('card_n', $default);
        }
    }
    return $quantity;
}
if(!function_exists('fav_con')):
/**
 * 加载单个分类内容
 * 用于 分类object
 * @param object $mid   分类对象
 * @param object $parent_term 父级分类
 * @return null
 */
function fav_con($mid,$parent_term = null) { 
    $taxonomy = $mid->taxonomy;
    $quantity = get_card_num();
    $icon     = get_tag_ico($taxonomy,(array)$mid);
    $tag_id   = (null !== $parent_term) ? ($parent_term->term_id . '-' . $mid->term_id) : $mid->term_id;
    ?>
        <div class="d-flex flex-fill align-items-center mb-4">
            <h4 class="text-gray text-lg m-0">
                <i class="site-tag <?php echo $icon ?> icon-lg mr-1" id="term-<?php echo $tag_id ?>"></i>
                <?php if( null !== $parent_term && io_get_option("tab_p_n",false)&& !wp_is_mobile() ){ 
                    echo $parent_term->name . '<span style="color:#f1404b"> · </span>';
                } 
                echo $mid->name; ?>
            </h4>
            <div class="flex-fill"></div>
            <?php 
            $site_n           = $quantity[get_type_name($taxonomy)];
            $category_count   = $mid->category_count;
            $count            = $site_n;
            if($site_n == 0)  $count = min(get_option('posts_per_page'),$category_count);
            if($site_n >= 0 && $count < $category_count){
                $link = is_mininav() ? esc_url( get_term_link( $mid, $taxonomy ).'?menu-id='.get_post_meta( get_queried_object_id(), 'nav-id', true ).'&mininav-id='.get_queried_object_id() ) : esc_url( get_term_link( $mid, $taxonomy ) );
                echo "<a class='btn-move text-xs' href='$link'>"._iol(io_get_option('term_more_text','more+'))."</a>";
            } 
            ?>
        </div>
        <div class="row io-mx-n2">
        <?php show_card($site_n,$mid->term_id,$taxonomy); ?>
        </div>   
<?php }
endif;  
if(!function_exists('fav_con_a')):
/**
 * 加载单个菜单内容
 * 用于 菜单数组
 * @param array $mid   菜单数组
 * @param array $parent_term 父级菜单
 * @return null
 */
function fav_con_a($mid,$parent_term = null) { 
    $taxonomy = $mid['object'];
    $quantity = get_card_num();
    $icon     = get_tag_ico($taxonomy,$mid);
    $tag_id   = (null !== $parent_term) ? ($parent_term['object_id'] . '-' . $mid['object_id']) : $mid['object_id'];
    /**
     * -----------------------------------------------------------------------
     * HOOK : ACTION HOOK
     * io_before_show_category_code
     * 
     * 在内容卡片前挂载其他内容。
     * 也可以在特定内容前挂载其他内容，通过判断$parent_term['object_id']
     * @since  3.0731
     * -----------------------------------------------------------------------
     */
    do_action( 'io_before_show_category_code' ,$mid );
    ?>
        <div class="d-flex flex-fill align-items-center mb-4">
            <h4 class="text-gray text-lg m-0">
                <i class="site-tag <?php echo $icon ?> icon-lg mr-1" id="term-<?php echo $tag_id ?>"></i>
                <?php if( null !== $parent_term && io_get_option("tab_p_n",false)&& !wp_is_mobile() ){ 
                    echo $parent_term['title'] . '<span style="color:#f1404b"> · </span>';
                } 
                echo $mid['title']; ?>
            </h4>
            <div class="flex-fill"></div>
            <?php 
            $site_n           = $quantity[get_type_name($taxonomy)];
            $category_count   = io_get_category_count($mid['object_id'],$taxonomy);//10;//$mid->category_count;
            $count            = $site_n;
            if($site_n == 0)  $count = min(get_option('posts_per_page'),$category_count);
            if($site_n >= 0 && $count < $category_count){
                $link =  is_mininav() ? $mid['url'].'?menu-id='.get_post_meta( get_queried_object_id(), 'nav-id', true ).'&mininav-id='.get_queried_object_id() : $mid['url'];//esc_url( get_term_link( $mid, $taxonomy ) );
                echo "<a class='btn-move text-xs' href='$link'>"._iol(io_get_option('term_more_text','more+'))."</a>";
            } 
            ?>
        </div>
        <div class="row io-mx-n2">
        <?php show_card($site_n,$mid['object_id'],$taxonomy); ?>
        </div>   
<?php } 
endif;   
if(!function_exists('fav_con_tab')):
/**
 * 加载完整菜单tab卡片
 * @param array $category 子菜单
 * @param array $parent_term  父级菜单
 * @param array $is_ajax  是否为ajax加载
 * @return *
 */
function fav_con_tab($category,$parent_term,$is_ajax = false) { 
    global $is_sidebar;
    $_link    = '';  
    $quantity = get_card_num();
    $icon     = get_tag_ico($parent_term['object'],$parent_term); 
    
    /**
     * -----------------------------------------------------------------------
     * HOOK : ACTION HOOK
     * io_before_show_category_code
     * 
     * 在内容卡片前挂载其他内容。
     * 也可以在特定内容前挂载其他内容，通过判断$parent_term['object_id']
     * @since  3.0731
     * -----------------------------------------------------------------------
     */
    do_action( 'io_before_show_category_code' ,$parent_term );
    ?>
        <?php if(io_get_option("tab_p_n",false) ){ ?>
        <h4 class="text-gray text-lg">
            <i class="site-tag <?php echo $icon ?> icon-lg mr-1" id="term-<?php echo $parent_term['object_id'] ?>"></i>
            <?php echo $parent_term['title']; ?>
        </h4>
        <?php 
        }else{
            echo '<div class="parent-category" id="term-'.$parent_term['object_id'].'"></div>';
        } 
        ?>
        <!-- tab模式菜单 -->
        <div class="d-flex flex-fill flex-tab align-items-center">
            <div class='slider_menu mini_tab ajax-list-home' sliderTab="sliderTab" data-id="<?php echo  $parent_term['object_id'] ?>">
                <ul class="nav nav-pills tab-auto-scrollbar menu overflow-x-auto" role="tablist"> 
                <?php 
                $i_menu = 0; 
                foreach($category as $mid) { 
                    if($mid['type'] != 'taxonomy' ){
                        $url = trim($mid['url']);
                        if( strlen($url)>1 ) {
                            if(substr( $url, 0, 1 ) == '#' || substr( $url, 0, 4 ) == 'http' )
                                continue;
                        }
                    }
                    $site_n           = $quantity[get_type_name($mid['object'])];
                    $category_count   = io_get_category_count($mid['object_id'],$mid['object']);
                    $count            = $site_n;
                    if($site_n == 0)  $count = min(get_option('posts_per_page'),$category_count);
                    $link = '';
                    if($site_n >= 0 && $count < $category_count){
                        $link = is_mininav() ? $mid['url'].'?menu-id='.get_post_meta( get_queried_object_id(), 'nav-id', true ).'&mininav-id='.get_queried_object_id() : $mid['url'];
                    }
                    if($i_menu == 0) $_link = $link;
                    $load = '';
                    if(!$is_ajax || ($is_ajax&&$i_menu == 0))$load =' load';
                    //echo '<li class="pagenumber swiper-slide nav-item"><a id="term-'. $mid['object_id'] .'" class="nav-link '. ($i_menu==0?'active':'') .'" data-post_id="'.get_queried_object_id().'" data-action="load_home_tab" data-taxonomy="'. $taxonomy .'" data-id="'. $mid['object_id'] .'" >'. $mid['title'] .'</a></li>';
                    echo '<li class="pagenumber nav-item" data-sidebar="'.($is_sidebar?1:0).'" data-post_id="'.get_queried_object_id().'" data-action="load_home_tab" data-taxonomy="'. $mid['object'] .'" data-id="'. $mid['object_id'] .'">
                    <a id="term-' . $parent_term['object_id'] . '-' . $mid['object_id'] .'" class="nav-link tab-noajax '. ($i_menu==0?('active'.$load):$load) .'" data-toggle="pill" href="#tab-' . $parent_term['object_id'] . '-' . $mid['object_id'].'" data-link="'.$link.'">'. $mid['title'].'</a>
                    </li>';
                    $i_menu++; 
                } ?>
                </ul>
            </div>
            <div class="flex-fill"></div>
            <?php  
            //显示更多按钮，通过js切换href
            if($_link != ''){
                echo "<a class='btn-move tab-move text-xs ml-2' href='$_link'>"._iol(io_get_option('term_more_text','more+'))."</a>";
            } else {
                echo "<a class='btn-move tab-move text-xs ml-2' href='#' style='display:none'>"._iol(io_get_option('term_more_text','more+'))."</a>";
            }
            ?>
        </div>
        <!-- tab模式菜单 end -->
        <div class="tab-content mt-4">
            <?php  
            $i_menu_box = 0; 
            foreach($category as $mid) { 
                if($mid['type'] != 'taxonomy' ){
                    $url = trim($mid['url']);
                    if( strlen($url)>1 ) {
                        if(substr( $url, 0, 1 ) == '#' || substr( $url, 0, 4 ) == 'http' )
                            continue;
                    }
                }
                $site_n = $quantity[get_type_name($mid['object'])];
            ?>
            <div id="tab-<?php echo $parent_term['object_id'] . '-' . $mid['object_id']; ?>" class="tab-pane  <?php echo $i_menu_box==0?'active':'' ?>">  
                <div class="row io-mx-n2 mt-4 ajax-list-body position-relative">
                <?php 
                if(!$is_ajax){
                    show_card($site_n, $mid['object_id'], $mid['object']);
                }else{
                    if($i_menu_box == 0){
                        show_card($site_n, $mid['object_id'], $mid['object']);
                    }else{
                        echo '<div class="col-lg-12 customize_nothing"><div class="nothing mb-4"><i class="iconfont icon-loading icon-spin mr-2"></i>'. __('加载中...', 'i_theme' ).'</div></div>';
                    }
                }
                ?>
                </div>
            </div>
            <?php 
                $i_menu_box++;
            } 
            ?>
        </div> 
<?php } 
endif;  

if(!function_exists('show_card')):
/**
 * 显示分类内容
 * @param  string $site_n 需显示的数量
 * @param  int $cat_id 分类id
 * @param  string $taxonomy 分类名
 * @param  string $ajax  
 */
function show_card($site_n, $cat_id, $taxonomy, $ajax=''){ 
    if ( !in_array( $taxonomy, get_menu_category_list() ) ){
        echo "<div class='card py-3 px-4'><p style='color:#f00'><i class='iconfont icon-crying mr-3'></i>该菜单内容不是分类，请到菜单删除并重新添加正确的内容。</p></div>";
        return;
    }
    $_order = io_get_option('home_sort','',get_type_name($taxonomy));
    $args = get_term_order_args($_order);
    $args2 = array(
        'post_type'      => to_post_type($taxonomy),
        //'ignore_sticky_posts' => 1,              
        'posts_per_page' => $site_n,
        'post_status'    => array('publish', 'private'),//'publish',
        'perm'           => 'readable',
        'tax_query'      => array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'id',
                'terms'    => $cat_id,
            )
        ),
    );
    if (io_get_option('no_dead_url', false)) {
        $args2['meta_query'] = array(
            array(
                'key'     => '_affirm_dead_url',
                'compare' => 'NOT EXISTS'
            )
        );
    }
    $cache_key = 'io_home_posts_'.$cat_id.'_'.$taxonomy.'_'. $site_n.'_'.$ajax;//.':'.wp_cache_get_last_changed('home-card');
    $myposts = wp_cache_get( $cache_key ,'home-card');
    if ( false === $myposts || 'random' === $_order) {
        $myposts = new WP_Query( array_merge($args,$args2) );
        if( io_get_option('show_sticky',false))
            $myposts= sticky_posts_to_top($myposts,to_post_type($taxonomy),$taxonomy,$cat_id);
        //show_post($myposts,$taxonomy,$ajax);
        if( $_order == '_down_count' || $_order == 'views' )
            wp_cache_set( $cache_key, $myposts ,'home-card', 1 * HOUR_IN_SECONDS); 
        else
            wp_cache_set( $cache_key, $myposts ,'home-card', 24 * HOUR_IN_SECONDS); 
        wp_reset_postdata();
    }
    show_post($myposts,$taxonomy,$ajax,$cat_id);

    /**
     * -----------------------------------------------------------------------
     * HOOK : ACTION HOOK
     * io_show_card_after_code
     * 
     * 在分类卡片输出完成后挂载其他内容
     * @since  3.1418
     * -----------------------------------------------------------------------
     */
    do_action( 'io_show_card_after_code' ,$taxonomy,$cat_id,$ajax ); 
    
}
endif;
if(!function_exists('show_post')){
/**
 * 输出内容卡片
 * @param WP_Query $myposts 
 * @param string $taxonomy 分类名
 * @param string $ajax  
 * @param string $cat_id 分类id
 */
function show_post($myposts,$taxonomy,$ajax,$cat_id=0){
    global $post, $is_sidebar;
    if(!$myposts->have_posts()): ?>
        <div class="col-lg-12">
            <div class="nothing mb-4"><?php _e('没有内容','i_theme') ?></div>
        </div>
    <?php
    elseif ($myposts->have_posts()): while ($myposts->have_posts()): $myposts->the_post(); 
    
    if($taxonomy == "favorites"||$taxonomy == "sitetag"){
        if($card_mode = get_term_meta( $cat_id, 'card_mode', true )){
            switch($card_mode){
                case 'null':
                    goto S_SETTING;
                    break;
                case 'max':
                    goto S_MAX;
                    break;
                case 'min':
                    goto S_MIM;
                    break;
                default:
                    goto S_DEF;
            }
        }
        S_SETTING: 
        if(io_get_option('site_card_mode','max') == 'max'){ 
            S_MAX:
            echo '<div class="url-card io-px-2 '.get_columns('sites',$cat_id,false,$is_sidebar,'max').' '. before_class($post->ID).' '.$ajax.'">';
            include( get_theme_file_path('/templates/card-sitemax.php') );
            echo '</div>';
        }elseif(io_get_option('site_card_mode','max') == 'min'){ 
            S_MIM:
            echo '<div class="url-card io-px-2 '.get_columns('sites',$cat_id,false,$is_sidebar).' '. before_class($post->ID).' '.$ajax.'">';
            include( get_theme_file_path('/templates/card-sitemini.php') );
            echo '</div>';
        }else{ 
            S_DEF:
            echo '<div class="url-card io-px-2 '.(io_get_option('two_columns',false)?"col-6 ":"").get_columns('sites',$cat_id,false,$is_sidebar).' '. before_class($post->ID).' '.$ajax.'">';
            include( get_theme_file_path('/templates/card-site.php') );
            echo '</div>';
        }
        
    } elseif($taxonomy == "apps"||$taxonomy == "apptag") {
        if($card_mode = get_term_meta( $cat_id, 'card_mode', true )){
            switch($card_mode){
                case 'null':
                    goto A_SETTING;
                    break;
                case 'card':
                    goto A_CARD;
                    break;
                default:
                    goto A_DEF;
            }
        }
        A_SETTING: 
        if(io_get_option('app_card_mode','card') == 'card'){
            A_CARD:
            echo'<div class="io-px-2 col-12 col-md-6 col-lg-4 col-xxl-5a '.$ajax.'">';
            include( get_theme_file_path('/templates/card-appcard.php') ); 
            echo'</div>';
        }else{
            A_DEF:
            echo'<div class="io-px-2 col-4 col-md-3 col-lg-2 col-xl-8a col-xxl-10a pb-1 '.$ajax.'">';
            include( get_theme_file_path('/templates/card-app.php') ); 
            echo'</div>';
        }
    } elseif($taxonomy == "books"||$taxonomy == "series"||$taxonomy == "booktag") { 
            echo'<div class="io-px-2 col-6 col-sm-4 col-md-3 col-lg-2 col-xxl-8a '.$ajax.'">';
            include( get_theme_file_path('/templates/card-book.php') ); 
            echo'</div>'; 
    } elseif($taxonomy == "category"||$taxonomy == "post_tag") {
        if($card_mode = get_term_meta( $cat_id, 'card_mode', true )){
            switch($card_mode){
                case 'null':
                    goto P_SETTING;
                    break;
                case 'card':
                    goto P_CARD;
                    break;
                default:
                    goto P_DEF;
            }
        }
        P_SETTING:
        if(io_get_option('post_card_mode','card')=="card"){
            P_CARD:
            echo '<div class="io-px-2 col-12 col-sm-6 col-lg-4 col-xxl-3 '.$ajax.'">';
            get_template_part( 'templates/card','postmin' );
            echo '</div>';
        }elseif(io_get_option('post_card_mode','card')=="default"){
            P_DEF:
            echo '<div class="io-px-2 col-6 col-md-4 col-xl-3 col-xxl-6a py-2 py-md-3 '.$ajax.'">';
            get_template_part( 'templates/card','post' );
            echo '</div>';
        } 
    }

    endwhile; 
    endif;
}
}
 
if(!function_exists('to_post_type')):
function to_post_type($taxonomy){
    if( $taxonomy=="favorites"||$taxonomy=="sitetag" )
        return 'sites';
    if( $taxonomy=="apps"||$taxonomy=="apptag" )
        return 'app';
    if( $taxonomy=="books"||$taxonomy=="booktag" ||$taxonomy=="series")
        return 'book';
    if( $taxonomy=="category"||$taxonomy=="post_tag")
        return 'post';
}
endif;  
if(!function_exists('to_post_tag')):
function to_post_tag($post){
    if( $post=="sites" )
        return 'sitetag';
    if( $post=="app" )
        return 'apptag';
    if( $post=="book" )
        return 'booktag';
    return 'post_tag';
}
endif;  
if(!function_exists('get_type_name')):
function get_type_name($taxonomy){
    if( $taxonomy=="favorites"||$taxonomy=="sitetag" )
        return 'favorites';
    if( $taxonomy=="apps"||$taxonomy=="apptag" )
        return 'apps';
    if( $taxonomy=="books"||$taxonomy=="booktag" ||$taxonomy=="series" )
        return 'books';
    if( $taxonomy=="category"||$taxonomy=="post_tag" )
        return 'category';
}
endif;  