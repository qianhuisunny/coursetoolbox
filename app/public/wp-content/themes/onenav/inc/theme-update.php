<?php
/*
 * @Theme Name:One Nav
 * @Theme URI:https://www.iotheme.cn/
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2021-04-15 04:33:41
 * @LastEditors: iowen
 * @LastEditTime: 2024-03-23 10:55:38
 * @FilePath: /onenav/inc/theme-update.php
 * @Description: 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function updateDB(){
    $rewrite = false;
    if(is_admin()){
        $version = get_option( 'onenav_version',false );
        if(!$version){
            $version = IO_VERSION;
            update_option( 'onenav_version', $version );
        }
        if ( version_compare( $version, '3.0330', '<' ) && version_compare( $version, '2.0407', '>' ) ) {
            update_option( 'onenav_version', '3.0330' );
            global $wpdb;
            $list = $wpdb->get_results("SELECT * FROM $wpdb->users");
            if($list) {
                foreach($list as $value){
                    if(substr($value->user_login , 0 , 2)=="io"){
                        //update_user_meta($value->ID, 'name_change', 1);
                        if($value->qq_id && !get_user_meta($value->ID,'qq_openid')){
                            update_user_meta($value->ID, 'qq_avatar', get_user_meta($value->ID,'avatar',true));
                            update_user_meta($value->ID, 'qq_name', $value->display_name);
                            update_user_meta($value->ID, 'qq_openid', $value->qq_id);
                            update_user_meta($value->ID, 'avatar_type', 'qq');
                        }
                        if($value->wechat_id && !get_user_meta($value->ID,'wechat_openid')){
                            update_user_meta($value->ID, 'wechat_avatar', get_user_meta($value->ID,'avatar',true));
                            update_user_meta($value->ID, 'wechat_name', $value->display_name);
                            update_user_meta($value->ID, 'wechat_openid', $value->wechat_id);
                            update_user_meta($value->ID, 'avatar_type', 'wechat');
                        }
                        if($value->sina_id && !get_user_meta($value->ID,'sina_openid')){
                            update_user_meta($value->ID, 'sina_avatar', get_user_meta($value->ID,'avatar',true));
                            update_user_meta($value->ID, 'sina_name', $value->display_name);
                            update_user_meta($value->ID, 'sina_openid', $value->sina_id);
                            update_user_meta($value->ID, 'avatar_type', 'sina');
                        }
                    }
                }
            }
            $wpdb->query("ALTER TABLE `$wpdb->iocustomurl` CHANGE `url` `url` TEXT DEFAULT NULL");
            $wpdb->query("ALTER TABLE `$wpdb->iocustomurl` CHANGE `url_name` `url_name` TEXT DEFAULT NULL");
            $wpdb->query("ALTER TABLE `$wpdb->iocustomurl` CHANGE `url_ico` `url_ico` TEXT DEFAULT NULL");
            $wpdb->query("ALTER TABLE `$wpdb->iocustomterm` CHANGE `name` `name` TEXT DEFAULT NULL");
            $wpdb->query("ALTER TABLE `$wpdb->iocustomterm` CHANGE `ico` `ico` TEXT DEFAULT NULL");

            if(!column_in_db_table($wpdb->iocustomurl,'post_id')){
                $wpdb->query("ALTER TABLE $wpdb->iocustomurl ADD post_id bigint(20)");
            }
            if(!column_in_db_table($wpdb->iocustomurl,'summary')){
                $wpdb->query("ALTER TABLE $wpdb->iocustomurl ADD summary varchar(255) DEFAULT NULL");
            }
            $rewrite = true;
        }
        if ( version_compare( $version, '3.0731', '<' ) && version_compare( $version, '3.0330', '>=' ) ) {
            update_option( 'onenav_version', '3.0731' );
            global $wpdb;
            $wpdb->query("ALTER TABLE $wpdb->iocustomterm ADD INDEX `user_id` (`user_id`);");
            $wpdb->query("ALTER TABLE $wpdb->iocustomurl ADD INDEX `user_id` (`user_id`);");
            $wpdb->query("ALTER TABLE $wpdb->iocustomurl ADD INDEX `term_id` (`term_id`);");
            $rewrite = true;
        }
        if ( version_compare( $version, '3.0901', '<' ) && version_compare( $version, '3.0731', '>=' ) ) {
            update_option( 'onenav_version', '3.0901' );
            global $wpdb;
            $wpdb->query("ALTER TABLE `$wpdb->iomessages` CHANGE `msg_read` `msg_read` TEXT DEFAULT NULL");
            if(!column_in_db_table($wpdb->iomessages,'meta')){
                $wpdb->query("ALTER TABLE $wpdb->iomessages ADD `meta` text DEFAULT NULL");
            }
            $rewrite = true;
        }
        if ( version_compare( $version, '3.1421', '<' ) ) {
            update_option( 'onenav_version', '3.1421' );
            global $wpdb ,$iodb;
            //$iodb = new IODB();
            $list = $wpdb->get_results("SELECT * FROM `$wpdb->postmeta` WHERE (`meta_key` IN ('_app_screenshot','_sites_screenshot') AND `meta_value` != '')");
            if($list){
                //$datas=array();
                foreach($list as $value){
                    $app_screen = explode( ',', $value->meta_value );
                    $data = array();
                    for ($i=0;$i<count($app_screen);$i++) {
                        $data[] = array(
                            'img'=>wp_get_attachment_image_src($app_screen[$i], 'full')[0]
                        );
                    }
                    update_post_meta( $value->post_id, '_screenshot', $data );
                    //$datas[] = array( $value->post_id, '_screenshot', maybe_serialize($data)); 
                }
                //$wpdb->query($iodb->multArrayInsert($wpdb->postmeta, array("post_id","meta_key","meta_value"),$datas));
            }
            $rewrite = true;
        }
        if ( version_compare( $version, '3.1918', '<' ) && version_compare( $version, '3.0330', '>=' ) ) {
            update_option( 'onenav_version', '3.1918' );
            global $wpdb;
            if (!column_in_db_table($wpdb->iocustomterm, 'parent')) {
                $wpdb->query("ALTER TABLE $wpdb->iocustomterm ADD `parent` bigint(20) NOT NULL DEFAULT 0 AFTER `user_id`");
            }
            $rewrite = true;
        }
        if( version_compare( $version, '3.2139', '<' ) ){
            update_option( 'onenav_version', '3.2139' );
            global $wpdb;
            $list = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE `user_email` REGEXP '^io.*@io.com$'");
            if($list ){
                foreach($list as $value){
                    $wpdb->query("UPDATE $wpdb->users SET `user_email`='' WHERE `ID`=$value->ID");
                }
            }
            //开始更新
            io_update_post_purview();
            $rewrite = true;
        }
        if($rewrite){
            wp_cache_flush();
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
        }
    }
    return $rewrite;
}

/**
 * 为文章添加自定义字段
 * 
 * @param mixed $type
 * @return void
 */
function io_update_post_purview($key='_user_purview_level', $val='all', $WHERE="`post_type` IN ('sites','post','app','book')"){
    global $wpdb;
    set_time_limit(0);

    //开始更新
    $result = $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->posts` WHERE $WHERE");
    $term_count = (int)$result;
    $result = $wpdb->get_var("SELECT MAX(`ID`) FROM `$wpdb->posts` ");
    $max_id = (int)$result;

    $term_last_id = 0;
    $do_count     = 0;
    $step         = 1000; //每次从数据库取多少
    while ($do_count < $term_count && $term_last_id < $max_id) { 
        if ($term_last_id < $max_id) {
            $obj = $wpdb->get_results("SELECT ID FROM `$wpdb->posts` WHERE `ID`> {$term_last_id} AND $WHERE ORDER BY `ID` LIMIT " . $step);
            $values_pre = "";
            $values     = array();
            foreach ($obj as $row) {
                $id           = $row->ID;
                $term_last_id = $id;

                $values_pre .= "(%d, %s, %s),";
                $values[]   = $id;
                $values[]   = $key;
                $values[]   = $val;
                $do_count++;
            }
            $sql = $wpdb->prepare("INSERT INTO `{$wpdb->postmeta}` (`post_id`, `meta_key`, `meta_value`) VALUES " . substr($values_pre, 0, -1), $values);
            $wpdb->query($sql);
        }
    }
}
function io_update_theme_after_update_db() {
    $current_v = '3.2139';
    $v = get_option('onenav_version', false);
    if(!$v){
        update_option( 'onenav_version', $current_v );
    }
    $v_html = '';
    $nonce= wp_create_nonce('io_up_db');
    if( $v != $current_v ){
        $v_html = '<p><a class="button ajax-up-get" href="' . add_query_arg(array('action' => 'io_update_theme_v', '_wpnonce' => $nonce), admin_url('admin-ajax.php')) . '">立即更新</a></p>';
    }
    global $wpdb;

    $db = array();
    if(!column_in_db_table($wpdb->iocustomurl,'post_id')){
        $db[] = 1;
    }
    if(!column_in_db_table($wpdb->iocustomurl,'summary')){
        $db[] = 2;
    }
    if(!column_in_db_table($wpdb->iomessages,'meta')){
        $db[] = 3;
    }
    if(!column_in_db_table($wpdb->iocustomterm,'parent')){
        $db[] = 4;
    }
    $db_html = '';
    if($db){
        $db_html = '<div>检查到数据库缺少字段(如果点击后没效果，请切换一下主题再点)。</div>';
        $db_html .= '<p><a class="button ajax-up-get" href="' . add_query_arg(array('action' => 'io_update_theme_db', 'type' => implode('-',$db), '_wpnonce' => $nonce), admin_url('admin-ajax.php')) . '">立即补缺</a></p>';
    }

    $do_action = $db_html;
    if($v_html){
        $do_action = $v_html;
    }
    if ($do_action) {
        $js = '<script type="text/javascript">
        (function ($) {
        $(".ajax-up-get").click( function () {
            var _this = $(this);
            if(_this.attr("disabled")){
                return !1;
            }
            var _notice = _this.parents(".notice-error").find(".ajax-notice");
            var _tt = _this.html();
            var ajax_url = _this.attr("href");
            var spin = "<i class=\'fa fa-spinner fa-spin fa-fw\'></i> "
            var n_type = "warning";
            var n_msg = spin + "正在处理，请稍候...";
            _this.attr("disabled", true);
            _this.html(spin + "请稍候...");
            $.ajax({
                type: "GET",
                url: ajax_url,
                dataType: "json",
                error: function (n) {
                    var n_con = "<div style=\'padding: 10px;margin: 0;\' class=\'notice notice-error\'><b>" + "网络异常或者操作失败，请稍候再试！ " + n.status + "|" + n.statusText + "</b></div>";
                    _notice.html(n_con);
                    _this.attr("disabled", false);
                    _this.html( _tt );
                },
                success: function (n) {
                    if (n.msg) {
                        n_type = n.error_type || (n.error ? "error" : "info");
                        var n_con = "<div style=\'padding: 10px;margin: 0;\' class=\'notice notice-" + n_type + "\'><b>" + n.msg + "</b></div>";
                        _notice.html(n_con);
                    }
                    _this.attr("disabled", false);
                    _this.html( _tt );
                    if (n.reload) {
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                }
            });
            return !1;
        });
    })(jQuery);
    </script>';
        $html = '<div class="notice notice-error is-dismissible">';
        $html .= '<h3>onenav 数据库需更新！</h3>';
        $html .= $do_action;
        $html .= '<div class="ajax-notice" style="margin-bottom:10px"></div>';
        $html .= '</div>';
        echo $html.$js;
    }
}
add_action('admin_notices', 'io_update_theme_after_update_db');

/**
 * 判断表中是否有字段
 * @param mixed $table 完整表名
 * @param mixed $column 字段名称
 * @return bool true 为字段已经存在
 */
function column_in_db_table($table, $column){
    global $wpdb;
    $column_exists = $wpdb->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    if ($column_exists) {
        return true;
    } else {
        return false;
    }
}
/**
 * 判断是否有表
 * @param mixed $table 完整表名
 * @return bool true 为表名已经存在
 */
function io_is_table($table){
    global $wpdb;
    if($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table) {
        return true;
    } else {
        return false;
    }
}
function io_update_theme_v_ajax(){
    if( !is_super_admin() ){
        echo (json_encode(array('error' => 1, 'msg' => '权限不足！')));
        exit();
    }
    if (!wp_verify_nonce($_GET['_wpnonce'],"io_up_db")){
        echo (json_encode(array('error' => 1, 'msg' => '对不起!您没有通过安全检查！')));
        exit();
    }
    if(get_transient( 'onenav_manual_update_version' )){
        echo (json_encode(array('error' => 1, 'msg' => '正在后台更新，请3分钟后刷新窗口，如果窗口消失，说明更新成功！')));
        exit();
    }
    set_transient('onenav_manual_update_version', 1, 3 * MINUTE_IN_SECONDS);
    if (!updateDB()) {
        if (update_option('onenav_version', '3.1539')) {
            updateDB();
            echo (json_encode(array('error' => 0, 'msg' => '更新成功！', 'reload' => 1)));
            exit();
        }
    }
    echo (json_encode(array('error' => 0, 'msg' => '更新成功！', 'reload' => 1)));
    exit();
}
add_action('wp_ajax_io_update_theme_v', 'io_update_theme_v_ajax');

function io_update_post_purview_ajax(){
    if( !is_super_admin() ){
        echo (json_encode(array('error' => 1, 'msg' => '权限不足！')));
        exit();
    }
    if(get_option( 'onenav_manual_post_purview' , 0 )){
        echo (json_encode(array('error' => 1, 'msg' => '已经执行了，不要重复点击！')));
        exit();
    }
    update_option('onenav_manual_post_purview', 1);

    io_update_post_purview('_user_purview_level', 'all', "`post_type` = 'post'");

    echo (json_encode(array('error' => 0, 'msg' => '更新成功！', 'reload' => 1)));
    exit();
}
add_action('wp_ajax_io_update_post_purview', 'io_update_post_purview_ajax');

function io_update_theme_db_ajax(){
    if( !is_super_admin() ){
        echo (json_encode(array('error' => 1, 'msg' => '权限不足！')));
        exit();
    }
    if (!wp_verify_nonce($_GET['_wpnonce'],"io_up_db")){
        echo (json_encode(array('error' => 1, 'msg' => '对不起!您没有通过安全检查！')));
        exit();
    }
    $db = $_GET['type'];
    $type = explode('-', $db);
    global $wpdb;
    foreach ($type as $v) {
        switch ($v) {
            case '1':
                if(!column_in_db_table($wpdb->iocustomurl,'post_id')){
                    $wpdb->query("ALTER TABLE $wpdb->iocustomurl ADD `post_id` bigint(20)");
                }
                break;
            case '2':
                if(!column_in_db_table($wpdb->iocustomurl,'summary')){
                    $wpdb->query("ALTER TABLE $wpdb->iocustomurl ADD `summary` varchar(255) DEFAULT NULL");
                }
                break;
            case '3':
                if(!column_in_db_table($wpdb->iomessages,'meta')){
                    $wpdb->query("ALTER TABLE $wpdb->iomessages ADD `meta` text DEFAULT NULL");
                }
                break;
            case '4':
                if(!column_in_db_table($wpdb->iocustomterm,'parent')){
                    $wpdb->query("ALTER TABLE $wpdb->iocustomterm ADD `parent` bigint(20) NOT NULL DEFAULT 0 AFTER `user_id`");
                }
                break;
        }
    }
    echo (json_encode(array('error' => 0, 'msg' => '插入成功！','reload' => 1)));
    exit();
}
add_action('wp_ajax_io_update_theme_db', 'io_update_theme_db_ajax');
