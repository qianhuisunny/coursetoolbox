<?php
/*
Template Name: 投稿模板
*/

if (!io_get_option('is_contribute',true)) {
    set404();
}

add_filter('io_show_sidebar', '__return_true');

get_header(); 

$type            = isset($_GET['type']) ? $_GET['type'] : 'sites';
$contribute_can  = io_get_option('contribute_can','user');
$contribute_type = io_get_option('contribute_type',array('sites'));
?>
    <div id="content" class="container my-4 my-md-5"> 
        <h1 id="comments-list-title" class="comments-title h5 mx-1 my-4">
            <i class="iconfont icon-tishi mr-2"></i><?php _e('投稿须知','i_theme') ?> 
            <?php edit_post_link('<i class="iconfont icon-modify mr-1"></i>'.__('编辑','i_theme'), '<span class="edit-link text-xs text-muted">', '</span>' ); ?>
        </h1> 
        <div class="panel card">
            <div class="card-body">
                <div class="panel-body single mt-2"> 
                    <?php while( have_posts() ): the_post(); ?>
                    <?php the_content(); ?>
                    <?php endwhile; ?> 
                </div>
            </div>
        </div>
        <?php 
        if( 'all' === $contribute_can || ('user' === $contribute_can && is_user_logged_in()) || ('admin' === $contribute_can && current_user_can('manage_options'))){
            if(is_array($contribute_type) && count($contribute_type)>1){
        ?>
            <div class="text-center mb-3">
                <div class="tab-btn-group text-sm">
                    <?php 
                    foreach($contribute_type as $_type){
                        $name = __('新网址','i_theme');
                        switch ($_type){
                            case 'post':
                                $name = __('新文章','i_theme');
                                break;
                            case 'sites':
                                $name = __('新网址','i_theme');
                                break;
                            case 'app':
                                $name = __('新APP','i_theme');
                                break;
                            case 'book':
                                $name = __('新书籍','i_theme');
                                break;
                        }
                        echo '<a href="'.io_get_template_page_url('template-contribute.php').'/?type='.$_type.'" class="tab-btn '.($_type==$type?'active':'').'">'.$name.'</a>';
                    } 
                    ?>
                </div>
            </div>
            <?php 
            }else{
                $type = $contribute_type[0];
            } 
            get_template_part( 'templates/contribute/'.$type ); 
        }else{
        ?>
        <div class="panel panel-tougao card">
            <div class="card-body"> 
                <div class="container my-5 py-md-5 text-center">
                    <img src="<?php echo get_theme_file_uri('/images/no.svg') ?>" width="300"/>
                    <?php if('admin' === $contribute_can && is_user_logged_in() && !current_user_can('manage_options')){ ?>
                    <h3 class="text-sm text-muted mt-3"><i class="iconfont icon-crying mr-2"></i><?php _e('无权操作，请联系管理员！','i_theme') ?></h3>
                    <?php }else{ ?>
                    <h3 class="text-sm text-muted mt-3"><i class="iconfont icon-crying mr-2"></i><?php _e('需要登录才能访问！','i_theme') ?></h3>
                    <a class="btn btn-danger px-5 mt-5" href="<?php echo esc_url(wp_login_url( io_get_current_url() )) ?>"><?php _e('登录','i_theme') ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
<script>
    var tg_data = {
        sites_img_max:"<?php echo io_get_option('sites_tg_opt',64,'img_size') ?>",
        post_img_max:"<?php echo io_get_option('post_tg_opt',1024,'img_size') ?>",
        theme_key:"<?php echo ioThemeKey() ?>",
        local : {
            only_img:"<?php _e('只能上传图片！','i_theme') ?>",
            only_jpg:"<?php _e('图片类型只能是jpeg,jpg,png！','i_theme') ?>",
            timeout:"<?php _e('网络连接错误！','i_theme') ?>",
            select_file:"<?php _e('请选择文件！','i_theme') ?>",
            sites_img_max_msg:"<?php echo sprintf(__('图片大小不能超过 %s kb','i_theme'),io_get_option('sites_tg_opt',64,'img_size')) ?>",
            post_img_max_msg:"<?php echo sprintf(__('图片大小不能超过 %s kb','i_theme'),io_get_option('post_tg_opt',1024,'img_size')) ?>",
            get_failed:"<?php _e('获取失败，请再试试，或者手动填写！','i_theme') ?>",
            get_success:"<?php _e('获取成功，没有的请手动填写！','i_theme') ?>",
            timeout2:"<?php _e('访问超时，请再试试，或者手动填写！','i_theme') ?>",
            code_error:"<?php _e('验证码错误！','i_theme') ?>",
            v_first:"<?php _e('请先验证！！！','i_theme') ?>",
            url_error:"<?php _e('链接格式错误！','i_theme') ?>",
            fill_url:"<?php _e('请先填写网址链接！','i_theme') ?>",
            v_success:"<?php _e('验证成功','i_theme') ?>",
            v_canceled:"<?php _e('您取消了验证！','i_theme') ?>",
            v_text:"<?php _e('验证','i_theme') ?>",
        }
    }
</script>
<?php wp_enqueue_script('new-post') ?>
<?php get_footer(); ?>
