<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:01
 * @LastEditors: iowen
 * @LastEditTime: 2024-02-02 00:00:42
 * @FilePath: \onenav\template-blog.php
 * @Description: 
 */
/*
Template Name: 博客页面
*/

get_header();
?>
<div id="content" class="container my-4 my-md-5">
    <div class="slide-blog mb-4">
        <div class="row no-gutters">
        <?php get_template_part( 'templates/slide','blog' ); //文章轮播模块 ?>
        </div>
    </div>
    <main class="content" role="main">
    <div class="content-wrap">
        <div class="content-layout">
            <?php 
            if ($cat = io_get_option('blog_index_cat', '')) {
            ?>
            <div class="text-sm overflow-x-auto white-nowrap blog-tab">
                <a href="<?php the_permalink() ?>" class="btn btn-search py-0 <?php echo(!isset($_GET['cat'])?'current':'') ?> text-gray" data-cat="-1"><?php _e('最新文章','i_theme') ?></a>
            <?php
                $cat = explode(',', $cat);
                foreach ($cat as $value) {
                    echo '<a href="'.get_permalink().'?cat='.$value.'" class="btn btn-search py-0 '.((isset($_GET['cat'])&&$_GET['cat']==$value)?'current':'').' text-gray" data-cat="'.$value.'">'.get_cat_name($value).'</a> ';
                }
            ?>
            </div>
            <?php } ?> 
            <?php get_template_part( 'templates/cat','list' ) ?>
        </div> 
    </div> 
    <?php get_sidebar('blog'); ?>
    </main>
</div>
<?php get_footer(); ?>
