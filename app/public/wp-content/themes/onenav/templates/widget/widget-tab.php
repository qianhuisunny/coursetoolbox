<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-01-13 19:56:02
 * @LastEditors: iowen
 * @LastEditTime: 2023-04-03 23:06:16
 * @FilePath: \onenav\templates\widget\widget-tab.php
 * @Description: tab卡片
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if( is_home() || is_front_page() ) {
    $datas = io_get_option('home_widget_tab',array());
}else{
    $datas = get_post_meta(get_queried_object_id(),'widget_tab',true);
}
?>
<div class="card tab-sites-widget rounded-xl">
    <div class=" tab-sites-body p-2 d-flex">
        <div class="tab-widget-nav">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical"> 
                <?php 
                if(is_array($datas) && $datas){ 
                    for ($i=0; $i < count($datas); $i++) { 
                ?>
                <a class="nav-link tab-widget-link <?php echo( $i==0?'active load':'' ) ?>" id="home_widget_<?php echo($datas[$i]['cat']) ?>-tab" data-action="get_tab_widget_post" data-datas="<?php echo  esc_attr( json_encode(array('data'=>$datas[$i]),JSON_UNESCAPED_UNICODE) )  ?>" data-toggle="pill" href="#home_widget_<?php echo($datas[$i]['cat']) ?>" role="tab" aria-controls="home_widget" aria-selected="<?php echo( $i==0?'true':'false' ) ?>">
                    <i class="<?php echo($datas[$i]['ico']) ?>"></i>
                    <span class="text-xs text-muted mt-1 d-none d-md-block overflowClip_1"><?php echo(_iol($datas[$i]['title'])) ?></span>
                </a> 
                <?php } } ?>
            </div>
        </div>
        <div class="tab-widget-content ml-2 p-2 tab-content" id="v-pills-tabContent">
            <?php 
            if(is_array($datas) && $datas){ 
                for ($i=0; $i < count($datas); $i++) { 
            ?>
            <div class="tab-pane fade <?php echo( $i==0?'show active':'' ) ?>" id="home_widget_<?php echo($datas[$i]['cat']) ?>" role="tabpanel" aria-labelledby="home_widget_<?php echo($datas[$i]['cat']) ?>-tab">
                <div class="widget-item item-<?php echo $datas[$i]['type'] ?>">
                    <?php 
                    if($i==0){
                        echo get_tab_post_html($datas[$i],'tab');
                    } else{
                        echo '<div class="d-flex justify-content-center align-items-center position-absolute w-100 h-100" style="left:0;top:0"><div class="spinner-border m-4" role="status"><span class="sr-only">Loading...</span></div></div>';
                    }
                    ?>
                </div>
            </div>
            <?php } } ?>
        </div>
        <?php if(!wp_is_mobile()){ ?>
        <div class="ml-2 p-2 tab-sidebar d-none d-md-block">
            <?php dynamic_sidebar('sidebar-tab-sites') ?>
        </div>
        <?php } ?>
    </div>
</div>
