<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-06-08 18:32:05
 * @LastEditors: iowen
 * @LastEditTime: 2023-04-23 10:43:24
 * @FilePath: \onenav\inc\framework\framework.php
 * @Description: 
 */
/**
 * Version: 2.0.3
 * Text Domain: csf
 * Domain Path: /languages
 */
require_once plugin_dir_path( __FILE__ ) .'classes/setup.class.php';
require_once plugin_dir_path( __FILE__ ) .'customize/options-function.php';
require_once plugin_dir_path( __FILE__ ) .'customize/iosf.class.php';

$io_get_option = false;
function io_get_option($option, $default = null, $key = ''){
    global $io_get_option;
    if ($io_get_option) {
        $options = $io_get_option;
    } else {
        $options       = get_option('io_get_option');
        $io_get_option = $options;
    }
    $_v = $default;
    if (isset($options[$option])) {
        if ($key) {
            $_v = isset($options[$option][$key]) ? $options[$option][$key] : $default;
        } else {
            $_v = $options[$option];
        }
    }
    $_v = _iol($_v, $option, isset($options['m_language']) ? $options['m_language'] : false);
    return $_v;
}

/**
 * 多语言选项输出
 * 支持数组类型和字符串类型
 * 
 * zh==服务内容--en==Service Content
 * 
 * @param mixed $option   选项内容
 * @param mixed $key      选项名称
 * @param mixed $is_multi 是否开启多语言
 * @return mixed
 */
function _iol($option, $key = '', $is_multi = null){
    if (empty($option)) {
        return $option;
    }

    if($is_multi === null){
        $is_multi = io_get_option('m_language', false);
    }

    $data = array();
    if ( $key != '' && strpos($key, 'multi') !== false) {
        $content = $option[0]['content'];
        if (!$is_multi) {
            return $content;
        }

        foreach ($option as $value) {
            $data[$value['language']] = $value['content'];
        }
    } elseif (is_array($option)) {
        return $option;
    }

    if ( empty($data) && strpos($option, '|*|') !== false) {
        if (!$is_multi) {
            return $option;
        }

        $_data = explode('|*|', $option);
        foreach ($_data as $value) {
            $d = explode('=*=', $value);
            $data[$d[0]] = $d[1];
        }
    }

    $language = determine_locale();
    if (isset($data[$language])) {
        $content = $data[$language];
    } else {
        $language = explode('_', $language)[0];
        $content  = isset($data[$language]) ? $data[$language] : $option;
    }
    return $content;
}