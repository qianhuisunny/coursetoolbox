<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:00
 * @LastEditors: iowen
 * @LastEditTime: 2023-03-11 03:15:53
 * @FilePath: \onenav\single-book.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); 
while( have_posts() ): the_post();
global $book_type;
$book_type = get_post_meta(get_the_ID(), '_book_type', true);
?>
<div id="content" class="container my-4 my-md-5">
    <?php
    echo io_header_fx();
    $is_hide = false;
    $header  = io_book_header($is_hide);
    if ($is_hide){
        iopay_get_auto_ad_html('page', 'mb-3 mt-n3 mt-md-n4');
    }else{
        iopay_get_auto_ad_html('page', 'my-n3 my-md-n4');
        echo $header;
    }
    ?>
    <main class="content" role="main">
        <div class="content-wrap">
            <div class="content-layout">
                <?php  
                if($is_hide){
                    echo $header;
                }else{
                    io_book_content();
                }
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif; 
                ?>
            </div><!-- content-layout end -->
        </div><!-- content-wrap end -->
        <?php get_sidebar('book');  ?>
    </main>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>