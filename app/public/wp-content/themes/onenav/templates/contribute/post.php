<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-07-04 18:39:10
 * @LastEditors: iowen
 * @LastEditTime: 2024-01-28 23:20:15
 * @FilePath: \onenav\templates\contribute\post.php
 * @Description: 
 */

$u_id   = get_current_user_id();
$option = io_get_option('post_tg_opt',array(
    'is_publish'   => false,
    'tag_limit'    => 5,
    'img_size'     => 1024,
    'cat'          => array(),
    'title_limit'  => array(
        'width'  => 5,
        'height' => 30,
    ),
));

if (!$option['is_publish']) {
    $btn_text = __('提交审核','i_theme');
} else {
    $btn_text = __('提交发布','i_theme');
}

if ($option['img_size'] > 0) {
    add_filter('io_tinymce_upload_img', '__return_true');
}


?>
    <form class="post-tg is-post">
        <div class="content-wrap">
            <div class="content-layout">
                <div class="panel card">
                <div class="card-body">
                    <div class="new-posts-title mb-3">
                        <input type="text" class="form-control new-title" name="post_title" placeholder="<?php _e('请输入文章标题', 'i_theme') ?>"></input>
                    </div>
                    <?php
                    $content   = '';
                    $editor_id = 'post_content';
                    $settings  = array(
                        'textarea_rows'  => 20,
                        'editor_height'  => (wp_is_mobile() ? 360 : 460),
                        'media_buttons'  => false,
                        'default_editor' => 'tinymce',
                        'quicktags'      => false,                            
                        'editor_css'     => '<link rel="stylesheet" href="' . get_theme_file_uri('/css/new-posts.css?ver=' . IO_VERSION).'" type="text/css">',
                        'teeny'          => false,
                    );
                    wp_editor($content, $editor_id, $settings);
                    $view_btn = '';
                    $post_uptime = '';
                    ?>
                    <?php echo '<div class="text-sm mt10"><span class="view-btn">' . $view_btn . '</span><span class="modified-time">' . $post_uptime . '</span></div>'; ?>
                </div>
                </div>
            </div>
        </div>
        <div class="sidebar show-sidebar">
            <div class="card rounded-lg mb-3 relative">
                <div class="card-header widget-header">
                    <h3 class="text-md mb-0"><i class="iconfont icon-publish mr-2"></i><?php _e('文章选项','i_theme') ?></h3>
                </div>
                <div class="card-body">
                    <p class="text-muted text-sm mb-2"><?php _e('分类','i_theme') ?></p>
                    <div class="form-select">
                            <?php
                            
                            $cat_args = array(
                                'show_option_all'     => __('选择分类','i_theme'),
                                'hide_empty'          => 0,
                                'id'                  => 'post_cat', 
                                'name'                => 'category',
                                'class'               => 'form-control',
                                'show_count'          => 1,
                                'hierarchical'        => 1,
                            );
                            if(isset($option['cat'])){
                                $cat_args['include'] = $option['cat'];
                            }
                            wp_dropdown_categories($cat_args);
                            ?>
                    </div>
                    <p class="text-muted text-sm mb-2 mt-3"><?php _e('标签','i_theme') ?></p>
                    <textarea class="form-control" rows="3" name="tags" placeholder="<?php _e('输入标签','i_theme') ?>" tabindex="6"></textarea>
                    <p class="text-muted text-xs mt-2"><i class="iconfont icon-tishi"></i><?php _e('填写标签，每个标签用逗号隔开','i_theme') ?></p>
                </div>
            </div>
            <?php if (!$u_id) { ?>
            <div class="card rounded-lg mb-3 relative">
                <div class="card-header widget-header">
                    <h3 class="text-md mb-0"><i class="iconfont icon-user-circle mr-2"></i><?php _e('用户信息','i_theme') ?></h3>
                </div>
                <div class="card-body">
                    <p class="text-muted text-sm mb-2"><?php _e('昵称','i_theme') ?></p>
                    <div class="mb20">
                        <input class="form-control" name="user_name" placeholder="<?php _e('请输入昵称','i_theme') ?>">
                    </div>
                    <p class="text-muted text-sm mb-2 mt-3"><?php _e('联系方式','i_theme') ?></p>
                    <input class="form-control" name="contact_details" placeholder="<?php _e('输入联系方式','i_theme') ?>">
                </div>
            </div>
            <?php } ?>
            <input type="hidden" name="action" value="io_posts_submit"></input>
			<?php wp_nonce_field('posts_submit'); ?>
            <div class="card rounded-lg relative">
                <div class="card-body">
                    <?php echo get_captcha_input_html('io_posts_submit') ?>
                    <button type="submit" id="submit" name="submit" class="btn btn-danger btn-block is-post"><i class="iconfont icon-upload-wd mr-2"></i><?php echo $btn_text ?></button>
                </div>
            </div>
        </div>
    </form>
