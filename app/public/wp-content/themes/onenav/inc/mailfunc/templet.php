<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2023-03-27 23:00:47
 * @LastEditors: iowen
 * @LastEditTime: 2023-06-09 20:55:47
 * @FilePath: \onenav\inc\mailfunc\templet.php
 * @Description: 
 */

/**
 * 评论待管理员审核
 * 
 * @param mixed $data
 * @return string
 */
function io_templet_comment_admin($data){
    $defaults = array(
        'postTitle'      => '',
        'commentAuthor'  => '',
        'commentContent' => '',
        'commentLink'    => '',
        'verify'         => '1'
    );
    $data = wp_parse_args((array) $data, $defaults);
    $message = '<p>'.$data['commentAuthor'].'在文章《<a href="'.$data['commentLink'].'" target="_blank">'.$data['postTitle'].'</a>》发表了评论, 快去看看吧</p>
    <p style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">'.$data['commentContent'].'</p>';
    if($data['verify']){
    $message .= '<p>此条评论待审核。</p>';
    }
    return $message;
}

/**
 * 评论被回复通知
 * 
 * @param mixed $data
 * @return string 
 */
function io_templet_reply($data){
    $message = '<p>'.$data['parentAuthor'].', 您好!</p>
    <p>您于'.$data['parentCommentDate'].'在文章《'.$data['postTitle'].'》上发表评论: </p>
    <p style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">'.$data['parentCommentContent'].'</p>
    <p>'.$data['commentAuthor'].' 于'.$data['commentDate'].' 给您的回复如下: </p>
    <p style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">'.$data['commentContent'].'</p>
    <p>您可以点击 <a style="color:#00bbff;text-decoration:none" href="'.$data['commentLink'].'" target="_blank">查看回复的完整內容</a></p>';

    return $message;
}

/**
 * 被评论给文章作者的通知
 * 
 * @param mixed $data
 * @return string 
 */
function io_templet_comment($data){
    $message = '<p>'.$data['commentAuthor'].'在文章<a href="'.$data['commentLink'].'" target="_blank">'.$data['postTitle'].'</a>中发表了回复，快去看看吧：<br></p>
    <p style="padding:10px 15px;background-color:#f4f4f4;margin-top:10px;color:#000;border-radius:6px;">'.$data['commentContent'].'</p>';

    return $message;
}


function io_templet_add_links($data){
    $message = '<p>网站有新的链接提交：</p>
    <p style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">
    链接名称：'.$data['link_name'].'<br>
    链接地址：'.$data['link_url'].'<br>
    链接简介：'.$data['link_description'].'<br>
    链接Logo：'.$data['link_image'].'
    </p>
    <p>您可以点击 <a style="color:#00bbff;text-decoration:none" href="'.$data['link_admin'].'" target="_blank">以审核该链接</a></p>';

    return $message;
}



function io_templet_comment_pass($data){
    $message = '<p>'.$data['parentAuthor'].', 您好!</p>
    <p>您于'.$data['parentCommentDate'].'在文章《'.$data['postTitle'].'》上发表的评论: </p>
    <p style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">'.$data['parentCommentContent'].'</p>
    <p>管理员已通过审核并显示。</p>
    <p>您可在此查看您的评论：<a style="color:#00bbff;text-decoration:none" href="'.$data['commentLink'].'" target="_blank">前往查看完整內容</a></p>';

    return $message;
}

/**
 * 投稿审核
 * 
 * @param mixed $data
 * @return string
 */
function io_templet_contribute_post($data){
    $message = '<h3>有投稿需要审核。</h3>
    <div style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">
    <p>文章标题《'.$data['postTitle'].'》</p>
    <p>内容摘要：'.$data['summary'].'</p>
    <p>投稿时间：'.$data['time'].'</p>
    </div>
    <p>您可以打开下方链接以审核投稿文章：<a style="color:#00bbff;text-decoration:none" href="'.$data['link'].'" target="_blank">前往审核</a></p>';

    return $message;
}
/**
 * 重设密码
 * 
 * @param mixed $data
 * @return string
 */
function io_templet_findpass($data){
    $message = '<p>有人要求重设以下帐号的密码：</p>
    <br>
    <p>网站: '.$data['home'].'</p>
    <p>用户名: '.$data['userLogin'].'</p>
    <p>若这不是您本人要求的，请忽略本邮件，一切如常</p>
    <p>要重置您的密码，请打开下面的链接:<br><a href="'.$data['resetPassLink'].'" style="word-break: break-all;">'.$data['resetPassLink'].'</a></p>';

    return $message;
}
/**
 * 登录失败
 * 
 * @param mixed $data
 * @return string
 */
function io_templet_login_fail($data){
    $message = '<p>你好！你的博客空间「'.get_bloginfo('name').'」有失败登录!</p>
    <p>请确定是您自己的登录失误, 以防别人攻击! 登录信息如下: </p>
    <div style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">
        登录名: '.$data['loginName'].'
        <br>登录密码: ******
        <br>登录时间: '. date("Y-m-d H:i:s",current_time( 'timestamp' )).'
        <br>登录IP: '.$data['ip'].' [' . io_get_ip_location($data['ip']) . ']
    </div>';

    return $message;
}
/**
 * 登录成功
 * 
 * @param mixed $data
 * @return string
 */
function io_templet_login($data){
    $message = '<p>你好！你的博客空间「'.get_bloginfo('name').'」有成功登录！</p>
    <p>请确定是您自己的登录, 以防别人攻击! 登录信息如下: </p>
    <div style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">
        登录名: '.$data['loginName'].'
        <br>登录密码: ******
        <br>登录时间: '. date("Y-m-d H:i:s",current_time( 'timestamp' )).'
        <br>登录IP: '.$data['ip'].' [' . io_get_ip_location($data['ip']) . ']
    </div>';

    return $message;
}
/**
 * 新用户注册通知管理员
 * 
 * @param mixed $data
 * @return string
 */
function io_templet_register_admin($data){
    $message = '<p>您的站点「'.get_bloginfo('name').'」有新用户注册:</p>
    <div style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">
        用户名: '.$data['loginName'].'
        <br>注册邮箱: '.$data['email'].'
        <br>注册时间: '. date("Y-m-d H:i:s",current_time( 'timestamp' )).'
        <br>注册IP: '.$data['ip'].' [' . io_get_ip_location($data['ip']) . ']
    </div>';

    return $message;
}
/**
 * 新用户注册通知账号信息
 * 
 * @param mixed $data
 * @return string
 */
function io_templet_register($data){
    $message = '<p>您的注册用户名和密码信息如下:</p>
    <div style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">
        用户名: '.$data['loginName'].'
        <br>登录密码: '.$data['password'].'
        <br>登录链接: <a href="'.$data['loginLink'].'">'.$data['loginLink'].'</a>
    </div>';

    return $message;
}
/**
 * 邮箱验证码
 * 
 * @param mixed $data
 * @return string
 */
function io_templet_verification_code($data){
    $message = '<p>您好!</p>
    <p>您于'.$data['date'].'在「'.get_bloginfo('name').'」申请的邮箱验证码是:  </p>
    <p style="color: #e83d26;font-size:24px;padding:10px 20px;background-color: #ffeaea;margin:15px 0px;border-radius:6px;">'.$data['code'].'</p>';

    return $message;
}
/**
 * 自动广告入驻通知
 * 
 * @param mixed $data
 * @return string
 */
function io_templet_add_auto_ad_url($data){
    $unit = iopay_get_auto_unit_name(io_get_option('auto_ad_config', 'hour', 'unit'));
    $tips = '有新自动广告入驻:';
    if ($data['check']) {
        $tips = '有新自动广告需要审核:';
    }
    $message = '<p>您的站点「'.get_bloginfo('name').'」'.$tips.'</p>
    <div style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">
        名称: '.$data['name'].'
        <br>URL: '.$data['url'].'
        <br>时间: '. date("Y-m-d H:i:s",current_time( 'timestamp' )).'
        <br>有效期: '.$data['limit'].$unit.'
    </div>';
    if(isset($data['msg'])){
        $message .= '<p style="font-size:12px">'.$data['msg'].'</p>';
    }

    return $message;
}


/**
 * 自动广告审核通知
 * 
 * @param mixed $data
 * @return string
 */
function io_templet_auto_ad_url_check($data){
    $unit = iopay_get_auto_unit_name(io_get_option('auto_ad_config', 'hour', 'unit'));
    $message = '<p>'.$data['title'].'</p>
    <div style="color: #0050ff;background-color: #f3f3f3;padding:10px 15px;font-size: 12px;line-height: 1.8;border-radius: 6px;">
        名称: '.$data['name'].'
        <br>URL: '.$data['url'].'
        <br>有效期: '.$data['limit'].$unit.'
    </div>';
    if(isset($data['msg'])){
        $message .= '<p style="font-size:12px">'.$data['msg'].'</p>';
    }

    return $message;
}