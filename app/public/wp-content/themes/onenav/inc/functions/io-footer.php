<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-07-22 18:01:47
 * @LastEditors: iowen
 * @LastEditTime: 2023-02-11 15:41:46
 * @FilePath: \onenav\inc\functions\io-footer.php
 * @Description: 
 */

function io_copyright($class='', $simple = false){
    $aff = io_get_option('io_aff', true) ? '由<a href="https://www.iotheme.cn/?aff=' . io_get_option('io_id', '') . '" title="一为主题-精品wordpress主题" target="_blank" class="'.$class.'" rel="noopener"><strong> OneNav </strong></a>强力驱动&nbsp' : '';
    if (io_get_option('footer_copyright','') && !$simple) {
        echo io_get_option('footer_copyright','') . "&nbsp;&nbsp;" . $aff . io_get_option('footer_statistics','');
    } else {
        $copy   = 'Copyright © ' . date('Y') . ' <a href="' . esc_url(home_url()) . '" title="' . get_bloginfo('name') . '" class="'.$class.'" rel="home">' . get_bloginfo('name') . '</a>&nbsp;';
        $icp    = io_get_option('icp', false) ? '<a href="https://beian.miit.gov.cn/" target="_blank" class="'.$class.'" rel="link noopener">' . io_get_option('icp','') . '</a>&nbsp;' : '';
        $p_icp  = '';
        if ($police_icp = io_get_option('police_icp','')) {
            if (preg_match('/\d+/', $police_icp, $arr)) {
                $p_icp = ' <a href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=' . $arr[0] . '" target="_blank" class="'.$class.'" rel="noopener"><img style="margin-bottom: 3px;" src="' . get_theme_file_uri('/images/gaba.png') . '"> ' . $police_icp . '</a>&nbsp;';
            }
        }
        echo $copy . $icp . $p_icp . $aff;
        echo io_get_option('footer_statistics','');
        unset($copy, $icp, $p_icp, $aff);
    } 
}