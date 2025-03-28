<?php
/*!
 * Theme Name:One Nav
 * Theme URI:https://www.iotheme.cn/
 * Author:iowen
 * Author URI:https://www.iowen.cn/
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
// 最新文章 ------------------------------------------------------
class new_cat extends WP_Widget {
    public function __construct() {
        $widget_ops = array(
            'classname' => 'new_cat',
            'description' => '显示全部分类或某个分类的最新文章',
            'customize_selective_refresh' => true,
        );
        parent::__construct('new_cat', '最新文章', $widget_ops);
    }

    public function io_defaults() {
        return array(
            'show_thumbs'   => 1,
        );
    }

    function widget( $args, $instance ) {
        extract( $args );
        $defaults = $this -> io_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
        $hideTitle = !empty($instance['hideTitle']) ? true : false;
        $newWindow = !empty($instance['newWindow']) ? true : false;
        $title_ico = !empty($instance['title_ico']) ? '<i class="mr-2 '.$instance['title_ico'].'"></i>' : '';
        echo $before_widget;
        if ($newWindow) $newWindow = "target='_blank'";
            
        if ( ! empty( $title ) )
            echo $before_title . $title_ico . $title . $after_title; 
    ?> 
    <div class="card-body"> 
        <div class="list-grid list-rounded my-n2">
            <?php 
                global $post;
                if ( is_single() ) {
                $q =  new WP_Query(array(
                    'ignore_sticky_posts' => 1,
                    'showposts' => $instance['numposts'],
                    'post__not_in' => array($post->ID),
                    'category__and' => $instance['cat'],
                ));
                } else {
                $q =  new WP_Query(array(
                    'ignore_sticky_posts' => 1,
                    'showposts' => $instance['numposts'],
                    'category__and' => $instance['cat'],
                ));
            } ?>
            <?php while ($q->have_posts()) : $q->the_post(); ?>
            <div class="list-item py-2">
                <?php if($instance['show_thumbs']) { ?>
                <div class="media media-3x2 rounded col-4 mr-3">
                    <?php if(io_get_option('lazyload',false)): ?>
                    <a class="media-content" href="<?php the_permalink(); ?>" <?php echo $newWindow ?> title="<?php the_title(); ?>" data-src="<?php echo  io_theme_get_thumb() ?>"></a>
                    <?php else: ?>
                    <a class="media-content" href="<?php the_permalink(); ?>" <?php echo $newWindow ?> title="<?php the_title(); ?>" style="background-image: url(<?php echo  io_theme_get_thumb() ?>);"></a>
                    <?php endif ?>
                </div>
                <?php } ?>
                <div class="list-content py-0">
                    <div class="list-body">
                        <a href="<?php the_permalink(); ?>" class="list-title overflowClip_2" <?php echo $newWindow ?> rel="bookmark"><?php the_title(); ?></a>
                    </div>
                    <div class="list-footer">
                        <div class="d-flex flex-fill text-muted text-xs">
                            <time class="d-inline-block"><?php echo timeago(get_the_time('Y-m-d G:i:s')); ?></time>
                            <div class="flex-fill"></div>
                            <?php if( function_exists( 'the_views' ) ) { the_views( true, '<span class="views"><i class="iconfont icon-chakan"></i> ','</span>' ); } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>

    <?php
    echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance = array();
        $instance['show_thumbs'] = $new_instance['show_thumbs']?1:0;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['title_ico'] = strip_tags($new_instance['title_ico']);
        $instance['hideTitle'] = isset($new_instance['hideTitle']);
        $instance['newWindow'] = isset($new_instance['newWindow']);
        $instance['numposts'] = $new_instance['numposts'];
        $instance['cat'] = $new_instance['cat'];
        return $instance;
    }

    function form( $instance ) {
        $defaults = $this -> io_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults );
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '最新文章',
            'title_ico' => 'iconfont icon-category',
            'numposts' => 5,
            'cat' => 0));
            ?> 

            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('title_ico'); ?>">图标代码：</label>
                <input class="widefat" id="<?php echo $this->get_field_id('title_ico'); ?>" name="<?php echo $this->get_field_name('title_ico'); ?>" type="text" value="<?php echo $instance['title_ico']; ?>" />
            </p> 
            <p>
                <input type="checkbox" id="<?php echo $this->get_field_id('newWindow'); ?>" class="checkbox" name="<?php echo $this->get_field_name('newWindow'); ?>" <?php checked(isset($instance['newWindow']) ? $instance['newWindow'] : 0); ?> />
                <label for="<?php echo $this->get_field_id('newWindow'); ?>">在新窗口打开链接</label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'numposts' ); ?>">显示文章数：</label>
                <input class="number-text" id="<?php echo $this->get_field_id( 'numposts' ); ?>" name="<?php echo $this->get_field_name( 'numposts' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['numposts']; ?>" size="3" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('cat'); ?>">'选择分类：
                <?php wp_dropdown_categories(array('name' => $this->get_field_name('cat'), 'show_option_all' => '全部分类', 'hide_empty'=>0, 'hierarchical'=>1, 'selected'=>$instance['cat'])); ?></label>
            </p>
            <p>
                <input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_thumbs') ); ?>" <?php checked( (bool) $instance["show_thumbs"], true ); ?>>
                <label for="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>">显示缩略图</label>
            </p>
    <?php }
}

add_action( 'widgets_init', 'new_cat_init' );
function new_cat_init() {
    register_widget( 'new_cat' );
}

// 最新公告 ------------------------------------------------------
class new_bulletin extends WP_Widget {
    public function __construct() {
        $widget_ops = array(
            'classname' => 'new_bulletin',
            'description' => '显示所有公告',
            'customize_selective_refresh' => true,
        );
        parent::__construct('new_bulletin', '最新公告', $widget_ops);
    }

    public function io_defaults() {
        return array(
            'newWindow'   => 1,
        );
    }

    function widget( $args, $instance ) {
        extract( $args );
        $defaults = $this -> io_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
        $hideTitle = !empty($instance['hideTitle']) ? true : false;
        $newWindow = !empty($instance['newWindow']) ? true : false;
        $title_ico = !empty($instance['title_ico']) ? '<i class="mr-2 '.$instance['title_ico'].'"></i>' : '';
        echo $before_widget;
        if ($newWindow) $newWindow = "target='_blank'";
            
        if ( ! empty( $title ) )
            echo $before_title . $title_ico . $title . $after_title; 
    ?> 
    <div class="card-body"> 
        <div class="list-grid list-bulletin my-n2">
            <?php 
                $q =  new WP_Query(array(
                    'post_type' => 'bulletin', 
                    'posts_per_page' => $instance['numposts'],
                    'ignore_sticky_posts' => 1,
                ));
            ?>
            <?php while ($q->have_posts()) : $q->the_post(); ?>
            <div class="list-item py-2">
                    <i class="iconfont icon-point"></i>
                <div class="list-content py-0">
                    <div class="list-body">
                        <a href="<?php the_permalink(); ?>" class="list-title overflowClip_2" <?php echo $newWindow ?> rel="bulletin<?php echo ((get_post_meta(get_the_ID(),'_goto',true)&&get_post_meta(get_the_ID(),'_nofollow',true))?' external nofollow':'') ?>"><?php the_title(); ?></a>
                    </div>
                    <div class="list-footer">
                        <div class="d-flex flex-fill text-muted text-xs">
                            <time class="d-inline-block"><?php echo timeago(get_the_time('Y-m-d G:i:s')); ?></time>
                            <div class="flex-fill"></div>
                            <?php if( function_exists( 'the_views' ) ) { the_views( true, '<span class="views"><i class="iconfont icon-chakan"></i> ','</span>' ); } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
        <div class="mt-4"><a href="<?php echo io_get_template_page_url('template-bulletin.php') ?>" target="_blank" class="btn btn-outline-danger btn-block"><?php _e('更多','i_theme') ?></a></div>
    </div>

    <?php
    echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['title_ico'] = strip_tags($new_instance['title_ico']);
        $instance['hideTitle'] = isset($new_instance['hideTitle']);
        $instance['newWindow'] = isset($new_instance['newWindow']);
        $instance['numposts'] = $new_instance['numposts'];
        return $instance;
    }

    function form( $instance ) {
        $defaults = $this -> io_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults );
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '站点公告',
            'title_ico' => 'iconfont icon-bulletin',
            'numposts' => 5));
            ?> 

            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('title_ico'); ?>">图标代码：</label>
                <input class="widefat" id="<?php echo $this->get_field_id('title_ico'); ?>" name="<?php echo $this->get_field_name('title_ico'); ?>" type="text" value="<?php echo $instance['title_ico']; ?>" />
            </p> 
            <p>
                <input type="checkbox" id="<?php echo $this->get_field_id('newWindow'); ?>" class="checkbox" name="<?php echo $this->get_field_name('newWindow'); ?>" <?php checked(isset($instance['newWindow']) ? $instance['newWindow'] : 0); ?> />
                <label for="<?php echo $this->get_field_id('newWindow'); ?>">在新窗口打开链接</label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'numposts' ); ?>">显示文章数：</label>
                <input class="number-text" id="<?php echo $this->get_field_id( 'numposts' ); ?>" name="<?php echo $this->get_field_name( 'numposts' ); ?>" type="number" step="1" min="1" value="<?php echo $instance['numposts']; ?>" size="3" />
            </p>
    <?php }
}

add_action( 'widgets_init', 'new_bulletin_init' );
function new_bulletin_init() {
    register_widget( 'new_bulletin' );
}

// 热门标签 ------------------------------------------------------
class cx_tag_cloud extends WP_Widget {
    public function __construct() {
        $widget_ops = array(
            'classname' => 'cx_tag_cloud',
            'description' => '包含所有标签类型',
            'customize_selective_refresh' => true,
        );
        parent::__construct('cx_tag_cloud','热门标签', $widget_ops);
    }

    public function io_defaults() {
        return array(
            'title_ico' => 'iconfont icon-tags'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $defaults = $this -> io_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = apply_filters( 'widget_title', $instance['title'] );
        $title_ico = !empty($instance['title_ico']) ? '<i class="mr-2 '.$instance['title_ico'].'"></i>' : '';
        echo $before_widget;
        if ( ! empty( $title ) )
        echo $before_title . $title_ico . $title . $after_title;
        $number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 20;
    ?> 
        
        <div class="card-body">
        <div class="tags text-justify">
            <?php 
            $tax = array('post_tag','apptag','sitetag','booktag');
            foreach (get_terms( array('taxonomy' => $tax, 'number' => $number, 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => false) ) as $tag){
                $tag_link = get_term_link($tag->term_id);
                ?> 
                <a href="<?php echo $tag_link ?>" title="<?php echo $tag->name ?>" class="tag-<?php echo $tag->slug ?> color-<?php echo mt_rand(0, 8) ?>">
                <?php echo $tag->name ?><span>(<?php echo $tag->count ?>)</span></a>
            <?php } ?> 
        </div>
        </div>

    <?php
    echo $after_widget;
    }
    function update( $new_instance, $old_instance ) {
        if (!isset($new_instance['submit'])) {
            return false;
        }
        $instance = $old_instance;
        $instance = array();
        $instance['title_ico'] = strip_tags($new_instance['title_ico']);
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['number'] = strip_tags($new_instance['number']);
        return $instance;
    }
    function form($instance) {
        $defaults = $this -> io_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults );
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = '热门标签';
        }
        global $wpdb;
        $instance = wp_parse_args((array) $instance, array('number' => '20'));
        $number = strip_tags($instance['number']);
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('title_ico'); ?>">图标代码：</label>
        <input class="widefat" id="<?php echo $this->get_field_id('title_ico'); ?>" name="<?php echo $this->get_field_name('title_ico'); ?>" type="text" value="<?php echo $instance['title_ico']; ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'number' ); ?>">显示数量：</label>
        <input class="number-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
    </p>

    <input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
function cx_tag_cloud_init() {
    register_widget( 'cx_tag_cloud' );
}
add_action( 'widgets_init', 'cx_tag_cloud_init' );
 
// 相关文章 ------------------------------------------------------
class related_post extends WP_Widget {
    public function __construct() {
        $widget_ops = array(
            'classname' => 'related_post',
            'description' => '显示相关文章',
            'customize_selective_refresh' => true,
        );
        parent::__construct('related_post', '相关文章', $widget_ops);
    }

    public function io_defaults() {
        return array(
            'show_thumbs'   => 1,
        );
    }

    function widget($args, $instance) {
        extract($args);
        $defaults = $this -> io_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = apply_filters( 'widget_title', $instance['title'] );
        $newWindow = !empty($instance['newWindow']) ? true : false;
        $title_ico = !empty($instance['title_ico']) ? '<i class="mr-2 '.$instance['title_ico'].'"></i>' : '';
        if ($newWindow) $newWindow = "target='_blank'";
        echo $before_widget;
        if ( ! empty( $title ) )
        echo $before_title . $title_ico . $title . $after_title;
        $number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 5;
    ?>


    
    <div class="card-body"> 
        <div class="list-grid list-rounded my-n2">
            <?php
                $post_num = $number;
                global $post;
                $tmp_post = $post;
                $tags = ''; $i = 0;
                if ( get_the_tags( $post->ID ) ) {
                foreach ( get_the_tags( $post->ID ) as $tag ) $tags .= $tag->slug . ',';
                $tags = strtr(rtrim($tags, ','), ' ', '-');
                $myposts = get_posts('numberposts='.$post_num.'&tag='.$tags.'&exclude='.$post->ID);
                foreach($myposts as $post) {
                setup_postdata($post);
            ?>  
                <div class="list-item py-2">
                    <?php if($instance['show_thumbs']) { ?>
                    <div class="media media-3x2 rounded col-4 mr-3">
                        <?php if(io_get_option('lazyload',false)): ?>
                        <a class="media-content" href="<?php the_permalink(); ?>" <?php echo $newWindow ?> title="<?php the_title(); ?>" data-src="<?php echo  io_theme_get_thumb() ?>"></a>
                        <?php else: ?>
                        <a class="media-content" href="<?php the_permalink(); ?>" <?php echo $newWindow ?> title="<?php the_title(); ?>" style="background-image: url(<?php echo  io_theme_get_thumb() ?>);"></a>
                        <?php endif ?>
                    </div>
                    <?php } ?>
                    <div class="list-content py-0">
                        <div class="list-body">
                            <a href="<?php the_permalink(); ?>" class="list-title overflowClip_2" <?php echo $newWindow ?> rel="bookmark"><?php the_title(); ?></a>
                        </div>
                        <div class="list-footer">
                            <div class="d-flex flex-fill text-muted text-xs">
                                <time class="d-inline-block"><?php echo timeago(get_the_time('Y-m-d G:i:s')); ?></time>
                                <div class="flex-fill"></div>
                                <span class="discuss"><?php comments_number( '<i class="iconfont icon-comment"></i> 0', '<i class="iconfont icon-comment"></i> 1', '<i class="iconfont icon-comment"></i> %' ); ?></span>
                            </div>
                        </div>
                    </div>
                </div> 
            <?php
                $i += 1;
                }
                }
                if ( $i < $post_num ) {
                $post = $tmp_post; setup_postdata($post);
                $cats = ''; $post_num -= $i;
                foreach ( get_the_category( $post->ID ) as $cat ) $cats .= $cat->cat_ID . ',';
                $cats = strtr(rtrim($cats, ','), ' ', '-');
                $myposts = get_posts(array(
                    'numberposts'=>$post_num,
                    'category'=>$cats,
                    'exclude'=>$post->ID
                ));
                foreach($myposts as $post) {
                setup_postdata($post);
            ?>
                <div class="list-item py-2">
                    <?php if($instance['show_thumbs']) { ?>
                    <div class="media media-3x2 rounded col-4 mr-3">
                        <?php if(io_get_option('lazyload',false)): ?>
                        <a class="media-content" href="<?php the_permalink(); ?>" <?php echo $newWindow ?> title="<?php the_title(); ?>" data-src="<?php echo  io_theme_get_thumb() ?>"></a>
                        <?php else: ?>
                        <a class="media-content" href="<?php the_permalink(); ?>" <?php echo $newWindow ?> title="<?php the_title(); ?>" style="background-image: url(<?php echo  io_theme_get_thumb() ?>);"></a>
                        <?php endif ?>
                    </div>
                    <?php } ?>
                    <div class="list-content py-0">
                        <div class="list-body">
                            <a href="<?php the_permalink(); ?>" class="list-title overflowClip_2" <?php echo $newWindow ?> rel="bookmark"><?php the_title(); ?></a>
                        </div>
                        <div class="list-footer">
                            <div class="d-flex flex-fill text-muted text-xs">
                                <time class="d-inline-block"><?php echo timeago(get_the_time('Y-m-d G:i:s')); ?></time>
                                <div class="flex-fill"></div>
                                <span class="discuss"><?php comments_number( '<i class="iconfont icon-comment"></i> 0', '<i class="iconfont icon-comment"></i> 1', '<i class="iconfont icon-comment"></i> %' ); ?></span>
                            </div>
                        </div>
                    </div>
                </div> 
            <?php
            }
            }
            $post = $tmp_post; setup_postdata($post);
            ?>
        </div>
    </div>

    <?php
    echo $after_widget;
    }
    function update( $new_instance, $old_instance ) {
        if (!isset($new_instance['submit'])) {
            return false;
        }
            $instance = $old_instance;
            $instance = array();
            $instance['title_ico'] = strip_tags($new_instance['title_ico']);
            $instance['newWindow'] = isset($new_instance['newWindow']);
            $instance['show_thumbs'] = $new_instance['show_thumbs']?1:0;
            $instance['title'] = strip_tags( $new_instance['title'] );
            $instance['number'] = strip_tags($new_instance['number']);
            return $instance;
        }
    function form($instance) {
        $defaults = $this -> io_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults );
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = '相关文章';
        }
        global $wpdb;
        $instance = wp_parse_args((array) $instance, array('number' => '5'));
        $instance = wp_parse_args((array) $instance, array('title_ico' => 'iconfont icon-related'));
        $number = strip_tags($instance['number']);
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('title_ico'); ?>">图标代码：</label>
        <input class="widefat" id="<?php echo $this->get_field_id('title_ico'); ?>" name="<?php echo $this->get_field_name('title_ico'); ?>" type="text" value="<?php echo $instance['title_ico']; ?>" />
    </p>
    <p>
        <input type="checkbox" id="<?php echo $this->get_field_id('newWindow'); ?>" class="checkbox" name="<?php echo $this->get_field_name('newWindow'); ?>" <?php checked(isset($instance['newWindow']) ? $instance['newWindow'] : 0); ?> />
        <label for="<?php echo $this->get_field_id('newWindow'); ?>">在新窗口打开链接</label>
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'number' ); ?>">显示文章数：</label>
        <input class="number-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
    </p>
    <p>
        <input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_thumbs') ); ?>" <?php checked( (bool) $instance["show_thumbs"], true ); ?>>
        <label for="<?php echo esc_attr( $this->get_field_id('show_thumbs') ); ?>">显示缩略图</label>
    </p>
    <input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}

function related_post_init() {
    register_widget( 'related_post' );
}
add_action( 'widgets_init', 'related_post_init' );
 
// 广告位 ------------------------------------------------------
class advert extends WP_Widget {
    public function __construct() {
        $widget_ops = array(
            'classname' => 'advert',
            'description' => '用于侧边添加广告代码',
            'customize_selective_refresh' => true,
        );
        parent::__construct('advert', '广告位', $widget_ops);
    }

    public function io_defaults() {
        return array(
            'text' => ''
        );
    }
    function widget($args, $instance) {
        extract($args);
        $title = apply_filters( 'widget_title', $instance['title'] );
        echo $before_widget;
        if ( ! empty( $title ) )
        echo $before_title . $title . $after_title;

        $text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
    ?>

    <?php if ( ! wp_is_mobile() ) { ?>
    <div id="advert_widget">
        <?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?>
    </div>
    <?php } ?>

    <?php
    echo $after_widget;
    }
    function update( $new_instance, $old_instance ) {
        if (!isset($new_instance['submit'])) {
            return false;
        }
            $instance = $old_instance;
            $instance = array();
            $instance['title'] = strip_tags( $new_instance['title'] );
            if ( current_user_can('unfiltered_html') )
                $instance['text'] =  $new_instance['text'];
            else
                $instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
            $instance['filter'] = ! empty( $new_instance['filter'] );
            return $instance;
        }
    function form($instance) {
        $defaults = $this -> io_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults );
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = '广告位';
        }
        $text = esc_textarea($instance['text']);
        global $wpdb;
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <p><label for="<?php echo $this->get_field_id( 'text' ); ?>">内容：</label>
        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea></p>
        <p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>">自动分段</label></p>
        <input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
    <?php }
}
function advert_init() {
    register_widget( 'advert' );
}
add_action( 'widgets_init', 'advert_init' );

// 关于作者 ------------------------------------------------------
class about_author extends WP_Widget {
    public function __construct() {
        $widget_ops = array(
            'classname' => 'about_author',
            'description' => '只显示在正文和作者页面',
            'customize_selective_refresh' => true,
        );
        parent::__construct('about_author', '关于作者', $widget_ops);
    }

    function widget($args, $instance) {
        extract($args);
        if ( is_author() || is_single() ){ 
            echo $before_widget;
            if ( ! empty( $title ) )
            echo $before_title . $title . $after_title;
         }
    ?>

    <?php if ( is_author() || is_single() ) { ?>
    <?php
        global $wpdb;
        $author_id = get_the_author_meta( 'ID' );
        $comment_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments  WHERE comment_approved='1' AND user_id = '$author_id' AND comment_type not in ('trackback','pingback')" );
        $author_url = get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) );
    ?>

    <div class="widget-author-cover">
        <div class="media media-2x1">
            <?php if(io_get_option('lazyload',false)): ?>
            <div class="media-content" data-src="<?php echo $instance['author_back']; ?>"></div>
            <?php else: ?>
            <div class="media-content" style="background-image: url(<?php echo $instance['author_back']; ?>);"></div>
            <?php endif ?>
        </div>
        <div class="widget-author-avatar">  
            <div class="flex-avatar"> 
            <?php echo get_avatar( get_the_author_meta('user_email'), '80' ); ?>     
              </div>     
          </div>
      </div>
    <div class="widget-author-meta text-center p-4">
          <div class="h6 mb-3"><?php the_author(); ?><small class="d-block">
            <span class="badge  vc-violet2 btn-outline mt-2">
                <?php 
                $user_id=$author_id;//get_post($id)->post_author;
                echo io_get_user_cap_string($user_id);
                ?>
            </span></small>
        </div>
          <div class="desc text-xs mb-3 overflowClip_2"></div>
        <div class="row no-gutters text-center">
              <a href="<?php echo $author_url ?>" target="_blank" class="col">
                <span class="font-theme font-weight-bold text-md"><?php the_author_posts(); ?></span><small class="d-block text-xs text-muted"><?php _e('文章','i_theme') ?></small>
              </a>
              <a href="<?php echo $author_url ?>" target="_blank" class="col">
                <span class="font-theme font-weight-bold text-md"><?php echo $comment_count;?></span><small class="d-block text-xs text-muted"><?php _e('评论','i_theme') ?></small>
              </a>
              <a href="<?php echo $author_url ?>" target="_blank" class="col">
                <span class="font-theme font-weight-bold text-md"><?php author_posts_views(get_the_author_meta('ID'));?></span><small class="d-block text-xs text-muted"><?php _e('浏览','i_theme') ?></small>
              </a>
              <a href="<?php echo $author_url ?>" target="_blank" class="col">
                <span class="font-theme font-weight-bold text-md"><?php author_posts_likes(get_the_author_meta('ID'));?></span><small class="d-block text-xs text-muted"><?php _e('获赞','i_theme') ?></small>
              </a>
        </div>
    </div>

    <?php } ?>

    <?php
        if ( is_author() || is_single() ){ 
            echo $after_widget;
         }
    }
    function update( $new_instance, $old_instance ) {
        if (!isset($new_instance['submit'])) {
            return false;
        }
            $instance = $old_instance;
            $instance = array(); 
            $instance['author_back'] = $new_instance['author_back'];
            // $instance['author_url'] = $new_instance['author_url'];
            return $instance;
        }
    function form($instance) { 
        global $wpdb;
        $instance = wp_parse_args((array) $instance, array('author_back' => '//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/banner/wHoOcfQGhqvlUkd.jpg'));
        $author_back = $instance['author_back'];
    ?> 
    <p>
        <label for="<?php echo $this->get_field_id('author_back'); ?>">背景图片：</label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'author_back' ); ?>" name="<?php echo $this->get_field_name( 'author_back' ); ?>" type="text" value="<?php echo $author_back; ?>" />
    </p>
    <input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
function about_author_init() {
    register_widget( 'about_author' );
}
add_action( 'widgets_init', 'about_author_init' );
 