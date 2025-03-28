<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2023-01-24 15:33:40
 * @LastEditors: iowen
 * @LastEditTime: 2023-03-30 01:14:47
 * @FilePath: \onenav\inc\functions\io-oauth.php
 * @Description: 
 */


/***
 * oauth登录页路由
 **/
function io_oauth_rewrite_rules($wp_rewrite){
    if (get_option('permalink_structure')) {
        $new_rules['oauth/([A-Za-z]+)$']          = 'index.php?oauth=$matches[1]';
        $new_rules['oauth/([A-Za-z]+)/callback$'] = 'index.php?oauth=$matches[1]&oauth_callback=1';

        $lang = io_get_lang_rules(); 
        if($lang){
            $new_rules[$lang . 'oauth/([A-Za-z]+)$']          = 'index.php?lang=$matches[1]&oauth=$matches[2]';
            $new_rules[$lang . 'oauth/([A-Za-z]+)/callback$'] = 'index.php?lang=$matches[1]&oauth=$matches[2]&oauth_callback=1';
        }

        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
    return $wp_rewrite;
}
//add_action('generate_rewrite_rules', 'io_oauth_rewrite_rules');
function io_add_oauth_query_vars($public_query_vars)
{
    if (!is_admin()) {
        $public_query_vars[] = 'oauth'; 
        $public_query_vars[] = 'oauth_callback';
    }
    return $public_query_vars;
}
add_filter('query_vars', 'io_add_oauth_query_vars');
function io_oauth_template()
{
    $type     = strtolower(get_query_var('oauth'));
    $callback = get_query_var('oauth_callback');
    $oauth_list = array( 
        'qq', 'sina', 
        'wechat', 'gzh', 'dyh', 
        'gitee', 'alipay',
        'baidu', 'github'
    );
    if ($type) {
        if (in_array($type, $oauth_list)):
            global $wp_query;
            $wp_query->is_home = false;
            $wp_query->is_page = true;
            $template          = $callback ? get_theme_file_path("/inc/auth/{$type}-callback.php") : get_theme_file_path("/inc/auth/{$type}.php");
            load_template($template);
            exit;
        else:
            // 非法路由处理
            unset($oauth);
            set404();
            return;
        endif;
    }
}
add_action('template_redirect', 'io_oauth_template', 5);

/**
 * 获取回调地址
 * @param mixed $type
 * @return string
 */
function get_oauth_callback_url($type){
    if('weibo'==$type){
        $type = 'sina';
    }
    //return esc_url(home_url().'/oauth/' . $type . '/callback');
    return esc_url(get_theme_file_uri("/inc/auth/{$type}-callback.php"));
}
/**
 * 获取社交登录的链接
 * @param mixed $type
 * @param mixed $rurl
 * @return bool|string
 */
function io_get_oauth_login_url($type, $rurl = ''){
    if (!$rurl) {
        $rurl = !empty($_GET['redirect_to']) ? $_GET['redirect_to'] : home_url();
    }

    if (io_get_option('open_' . $type, false)) {
        if ('weixin_gzh' == $type) {
            $type = io_get_option('open_weixin_gzh_key', 'gzh', 'type');
        }
        if('weibo'==$type){
            $type = 'sina';
        }
        //$url = home_url().'/oauth/' . $type;
        $url = get_theme_file_uri("/inc/auth/{$type}.php");
        return add_query_arg('loginurl', $rurl, $url);
    }
    return false;
}
/**
 * 输出微信二维码js
 * @param mixed $type
 * @param mixed $is_popup 弹窗
 * @param mixed $echo
 * @return mixed
 */
function get_weixin_qr_js($type = 'gzh',$is_popup = false, $echo = true){
    $callback = get_oauth_callback_url($type);
    $js = '<script type="text/javascript">';
    $js .= 'var _state="";
    $(document).on("click", ".qrcode-signin", function () {
        if ($("#user_agreement")[0] && !$("#user_agreement").is(":checked")) {
            ioPopupTips(2, localize.userAgreement);
            return false;
        }
        var _this = $(this);
        var url = _this.attr("href");
        var container = $("#wp_login_form").parent();
        _this.addClass("disabled");
        $.post(url, null, function (n) {
            if(n){
                if(n.msg){
                    console.log(n.msg);
                }
                if(n.html){'
                    .($is_popup?'ioPopup("small",n.html,"","");':'container.html("<div class=\'sign-header h4 mb-3 mb-md-5\'>扫码登录</div>"+n.html+n.but);'). '
                    _state = n.state;'
                    .('gzh' === $type?'checkLogin();':'').'
                }
            }else{
                console.log("二维码获取失败，请稍后再试");
            }
            _this.removeClass("disabled");
        }, "json");
        return false;
        });';
    if('gzh' === $type):
    $js .= 'function checkLogin() {
            var url = "'.$callback.'";
            if (!url || !_state) return;
            $.post(url, {
                state: _state,
                loginurl: window.location.href,
                action: "check_callback"
            }, function (n) {
                if (n && n.goto) {
                    window.location.href = n.goto;
                    window.location.reload;
                } else {
                    setTimeout(function () {
                        checkLogin();
                    }, 2000);
                }
            }, "json");
        }';
    elseif('dyh' === $type):
    $js .= '$(document).on("click",".io-wx-btn",function(){
        var _this = $(this),
            url = "'.$callback.'",
            code = _this.prev().val();
        if(code){
            if(!_this.hasClass("disabled")){
                _this.text("验证中...");
                _this.addClass("disabled");
                $.post(url, {
                    action: "check_callback",
                    code: code,
                    loginurl: window.location.href
                }, function(n) {
                    if(n.status == "1"){
                        window.location.href = n.goto;
                        window.location.reload; 
                    }else{
                        _this.removeClass("disabled");
                        _this.text("验证登录");
                        alert("登录失败！请检查是否验证码已过期～");
                    }
                }, "json");
            }
        }else{
            alert("请输入验证码～");
        }
        return false;
    });';
    endif;
    $js .= '</script>';
    if($echo){
        echo $js;
    }else{
        return $js;
    }
}
/**
 * 获取社交登录信息
 * 
 * 'name'  => 'QQ',
 * 'type'  => 'qq',
 * 'class' => 'openlogin-qq-a',
 * 'n_key' => 'nickname',
 * 'icon'  => 'icon-qq',
 * @return array
 */
function get_social_type_data(){
    $args = array(
        'qq' => array(
            'name'  => 'QQ',
            'type'  => 'qq',
            'class' => 'openlogin-qq-a',
            'n_key' => 'nickname',
            'icon'  => 'icon-qq',
        ),
        'wechat' => array(
            'name'  => __('微信', 'i_theme'),
            'type'  => 'wechat',
            'class' => 'openlogin-wechat-a',
            'n_key' => 'nickname',
            'icon'  => 'icon-wechat',
        ),
        'weixin_gzh' => array(
            'name'  => __('微信', 'i_theme'),
            'type'  => 'weixin_gzh',
            'class' => 'openlogin-wechat-gzh-a',
            'n_key' => 'nickname',
            'icon'  => 'icon-wechat',
        ),
        'weibo' => array(
            'name'  => __('微博', 'i_theme'),
            'type'  => 'weibo',
            'class' => 'openlogin-weibo-a',
            'n_key' => 'screen_name',
            'icon'  => 'icon-weibo',
        ),
        'baidu' => array(
            'name'  => __('百度', 'i_theme'),
            'type'  => 'baidu',
            'class' => 'openlogin-baidu-a',
            'n_key' => 'username',
            'icon'  => 'icon-baidu',
        ),
        'alipay' => array(
            'name'  => __('支付宝', 'i_theme'),
            'type'  => 'alipay',
            'class' => 'openlogin-alipay-a',
            'n_key' => 'username',
            'icon'  => 'icon-alipay',
        ),
        'github' => array(
            'name'  => 'GitHub',
            'type'  => 'github',
            'class' => 'openlogin-github-a',
            'n_key' => 'name',
            'icon'  => 'icon-github',
        ),
        'gitee' => array(
            'name'  => __('码云', 'i_theme'),
            'type'  => 'gitee',
            'class' => 'openlogin-gitee-a',
            'n_key' => 'name',
            'icon'  => 'icon-gitee',
        ),
        'huawei' => array(
            'name'  => __('华为', 'i_theme'),
            'type'  => 'huawei',
            'class' => 'openlogin-huawei-a',
            'n_key' => '',
            'icon'  => 'icon-huawei',
        ),
        'google' => array(
            'name'  => __('谷歌', 'i_theme'),
            'type'  => 'google',
            'class' => 'openlogin-google-a',
            'n_key' => '',
            'icon'  => 'icon-google',
        ),
        'microsoft' => array(
            'name'  => __('微软', 'i_theme'),
            'type'  => 'microsoft',
            'class' => 'openlogin-microsoft-a',
            'n_key' => '',
            'icon'  => 'icon-microsoft',
        ),
        'facebook' => array(
            'name'  => 'Facebook',
            'type'  => 'facebook',
            'class' => 'openlogin-facebook-a',
            'n_key' => '',
            'icon'  => 'icon-facebook',
        ),
        'twitter' => array(
            'name'  => 'Twitter',
            'type'  => 'twitter',
            'class' => 'openlogin-twitter-a',
            'n_key' => '',
            'icon'  => 'icon-twitter',
        ),
        'dingtalk' => array(
            'name'  => __('钉钉', 'i_theme'),
            'type'  => 'dingtalk',
            'class' => 'openlogin-dingtalk-a',
            'n_key' => '',
            'icon'  => 'icon-dingtalk',
        )
    );
    return apply_filters('io_social_type_data_filters', $args);
}
