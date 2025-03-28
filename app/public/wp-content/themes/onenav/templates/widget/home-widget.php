<?php 
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-02-05 16:03:25
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-08 17:01:18
 * @FilePath: \onenav\templates\widget\home-widget.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$style = '';
$search_position = io_get_option('search_position', array('home'));
if( $search_position && io_get_option('search_skin','1','search_big')=='1' && 
    io_get_option('search_skin','no-bg','big_skin')!='no-bg' && 
    io_get_option('search_skin',false,'post_top') && 
    in_array("home",$search_position) ){
    if (is_home() || is_front_page() || (is_page() && get_post_meta(get_queried_object_id(),'search_box',true))) {
        $style = 'style="margin-top:-5.525rem!important"';
    }
}
$widgets = '';
if( is_home() || is_front_page() ) {
    $widgets = io_get_option('home_widget',array(),'enabled');
}elseif(is_page()){
    if( $_widget = get_post_meta(get_queried_object_id(),'widget',true) ){
        $widgets = isset($_widget['enabled'])?$_widget['enabled']:'';
    }
}
if( !empty($widgets) || io_get_option('is_show_hot',true) && 
    (((is_home() || is_front_page())) && !empty(io_get_option('hot_new',array())) || (is_page() && get_post_meta(get_queried_object_id(),'hot_new',true)))){
?>
<div class="home-widget-group" <?php echo $style ?>>
<?php
if (!empty($widgets)) {
    foreach ($widgets as $key => $value) {
        get_template_part('templates/widget/widget', $key);
    }
}
?>
</div>
<?php } ?>