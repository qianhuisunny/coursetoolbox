<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-01-20 22:13:24
 * @LastEditors: iowen
 * @LastEditTime: 2022-07-09 16:58:27
 * @FilePath: \onenav\inc\widgets\w.about.website.php
 * @Description: 关于作者
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

CSF::createWidget( 'about_website', array(
    'title'       => '关于本站',
    'classname'   => 'io-widget-about-website',
    'description' => '本站信息、微信、微博、QQ等',
    'fields'      => array(
        array(
            'id'        => 'about_img',
            'type'      => 'upload',
            'title'     => '网站图标',
            'add_title' => '上传',
            'default'   => get_theme_file_uri('/images/avatar.png'),
        ),
        array(
            'id'        => 'about_back',
            'type'      => 'upload',
            'title'     => '网站图标',
            'add_title' => '上传',
            'default'   => '//cdn.iocdn.cc/gh/owen0o0/ioStaticResources@master/banner/wHoOcfQGhqvlUkd.jpg',
        ),
        array(
            'id'      => 'show_social_icon',
            'type'    => 'switcher',
            'title'   => '显示社交图标',
            'default' => true
        ),
        array(
            'id'        => 'social',
            'type'      => 'group',
            'title'     => '社交信息',
            'fields'    => array(
                array(
                    'id'    => 'name',
                    'type'  => 'text',
                    'title' => '名称',
                ),
                array(
                    'id'        => 'ico',
                    'type'      => 'icon',
                    'title'     => '图标', 
                    'default'   => 'iconfont icon-related',
                ),
                array(
                    'id'        => 'type',
                    'type'      => 'button_set',
                    'title'     => '类型',
                    'options'   => array(
                        'url'       => 'URL连接',
                        'img'       => '图片弹窗（如微信二维码）',
                    ),
                    'default'   => 'url',
                ),
                array(
                    'id'    => 'url',
                    'type'  => 'text',
                    'title' => '地址：',
                    'after' => '<p class="cs-text-muted">【图片弹窗】请填图片地址<br><i class="fa fa-fw fa-info-circle fa-fw"></i> 如果要填QQ，请转换为URL地址，格式为：<br><code>http://wpa.qq.com/msgrd?V=3&uin=xxxxxxxx&Site=QQ&Menu=yes</code><br>将xxxxxx改为您自己的QQ号</p>',
                ),
            ), 
            'default'   => array(
                array(
                    'name'  => '微信',
                    'ico'   => 'iconfont icon-wechat',
                    'type'  => 'img',
                    'url'   => get_theme_file_uri('/images/wechat_qrcode.png'),
                ),
                array(
                    'name'  => 'QQ',
                    'ico'   => 'iconfont icon-qq',
                    'type'  => 'url',
                    'url'   => 'http://wpa.qq.com/msgrd?V=3&uin=xxxxxxxx&Site=QQ&Menu=yes',
                ),
                array(
                    'name'  => '微博',
                    'ico'   => 'iconfont icon-weibo',
                    'type'  => 'url',
                    'url'   => 'https://www.iotheme.cn',
                ),
                array(
                    'name'  => 'GitHub',
                    'ico'   => 'iconfont icon-github',
                    'type'  => 'url',
                    'url'   => 'https://www.iotheme.cn',
                )
            ),
            'max'       => 5,
            'dependency'=> array( 'show_social_icon', '==', true )
        )
    )
) );
if ( ! function_exists( 'about_website' ) ) {
    function about_website( $args, $instance ) {
        echo $args['before_widget'];
        ?>
        <div class="widget-author-cover">
            <div class="media media-2x1">
                <div class="media-content" style="background-image: url(<?php echo $instance['about_back'] ?>);"></div>
            </div>
            <div class="widget-author-avatar"> 
                <div class="flex-avatar"> 
                    <img src="<?php echo $instance['about_img']; ?>" height="90" width="90"> 
                </div>
            </div>
        </div>
        <div class="widget-author-meta text-center p-4">
            <div class="h6 mb-3"><?php echo get_bloginfo('name') ?><small class="d-block mt-2"><?php echo get_bloginfo('description') ?></small> </div>
            <?php if($instance['show_social_icon']) { ?> 
            <div class="row no-gutters text-center my-3">
                <?php 
                if(is_array($instance['social']) && count($instance['social'])>0){
                    foreach($instance['social'] as $social){
                        if($social['type']=='img'){
                        ?>
                        <div class="col">
                            <span data-toggle="tooltip" data-placement="top" data-html="true" title="<img src='<?php echo $social['url'] ?>' height='100' width='100'>"><i class="<?php echo $social['ico'] ?> icon-lg"></i></span>
                        </div>
                        <?php
                        }else{
                            $url = $social['url'];
                            if(preg_match('|wpa.qq.com(.*)uin=([0-9]+)\&|',$url,$matches)){
                                $url = IOTOOLS::qq_url($matches[2]);
                            }
                        ?> 
                        <div class="col">
                            <a href="<?php echo $url ?>" target="_blank"  data-toggle="tooltip" data-placement="top" title="<?php echo $social['name'] ?>" rel="external nofollow"><i class="<?php echo $social['ico'] ?> icon-lg"></i></a>
                        </div>
                        <?php
                        }
                    }
                }
                ?>
            </div>
            <?php } ?>
            <div class="desc text-xs mb-3 overflowClip_2"></div>
            <div class="row no-gutters text-center">
                <div class="col">
                    <span class="font-theme font-weight-bold text-md"><?php echo wp_count_posts('sites')->publish ?></span><small class="d-block text-xs text-muted"><?php _e('收录网站','i_theme') ?></small>
                </div>
                <div class="col">
                    <span class="font-theme font-weight-bold text-md"><?php echo wp_count_posts('app')->publish ?></span><small class="d-block text-xs text-muted"><?php _e('收录 App','i_theme') ?></small>
                </div>
                <div class="col">
                    <span class="font-theme font-weight-bold text-md"><?php $count_posts = wp_count_posts(); echo $published_posts = $count_posts->publish;?></span><small class="d-block text-xs text-muted"><?php _e('文章','i_theme') ?></small>
                </div>
                <div class="col">
                    <span class="font-theme font-weight-bold text-md"><?php author_posts_views(); ?></span><small class="d-block text-xs text-muted"><?php _e('访客','i_theme') ?></small>
                </div>
            </div>
        </div>
        <?php
        echo $args['after_widget'];
    }
}

