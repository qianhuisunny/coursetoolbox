<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-06-03 08:56:01
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-04 01:52:51
 * @FilePath: \onenav\templates\bookmark\bm.search.php
 * @Description: 
 */
?>
<header class="navbar navbar-dark justify-content-between">
    <div class="weather">
        <div id="he-plugin-simple" style="display: contents;"></div>
        <script>WIDGET = {CONFIG: {"modules": "20","background": 5,"tmpColor": "888","tmpSize": 14,"cityColor": "888","citySize": 14,"aqiSize": 14,"weatherIconSize": 24,"alertIconSize": 18,"padding": "10px 10px 10px 10px","shadow": "1","language": "auto","borderRadius": 5,"fixed": "false","vertical": "middle","horizontal": "left","key": "a922adf8928b4ac1ae7a31ae7375e191"}}</script>
        <script src="//widget.heweather.net/simple/static/js/he-simple-common.js?v=1.1"></script>
    </div> 
        <ul class="nav navbar-menu"> 
                        <?php if( io_get_option('nav_login',false) ){ 
                            global $user_ID; 
                            if(!$user_ID) {?>
                            <li class="nav-login ml-3">
                                <a href="<?php echo esc_url(wp_login_url( io_get_current_url() )) ?>" title="<?php _e('登录', 'i_theme' ); ?>"><i class="iconfont icon-user icon-2x"></i></a>
                            </li>
                        <?php }else{
                                get_template_part( 'templates/widget/header', 'user' );
                            }
                        } ?>
                        <li class="nav-item ml-3">
                            <a href="javascript:" id="seting-btn"><i class="iconfont icon-seting icon-2x"></i></a>
                        </li>
                    </ul>
</header>