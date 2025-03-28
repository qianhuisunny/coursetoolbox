<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-07-04 21:26:44
 * @LastEditors: iowen
 * @LastEditTime: 2023-03-12 00:02:43
 * @FilePath: \onenav\inc\functions\io-tool.php
 * @Description: 
 */

/**
 * 是否为博客页
 * @return bool 
 */
function is_blog(){
	return is_page_template('template-blog.php');
}
/**
 * 是否为用户管理页
 * @return bool 
 */
function is_io_user(){
	return '' !== get_query_var('user_child_route');
}
/**
 * 是否为次级导航页
 * @return bool 
 */
function is_mininav(){
	return is_page_template('template-mininav.php');
}
/**
 * 是否为排行榜页
 * @return bool 
 */
function is_rankings(){
	return is_page_template('template-rankings.php');
}
/**
 * 是否为投稿页
 * @return bool 
 */
function is_contribute(){
	return is_page_template('template-contribute.php');
}
/**
 * 是否为登录页
 * @return bool 
 */
function is_io_login(){
	return get_query_var('custom_action') === 'login';
}
/**
 * 是否为书签页
 * @return bool 
 */
function is_bookmark(){
	return  get_query_var('bookmark_id');
}

/**
 * 删除内容或者数组的两端空格
 * 
 * @param array|string $input
 * @return array|string
 */
function io_trim($input){
    if (!is_array($input)) {
        return trim($input);
    }
    return array_map('io_trim', $input);
}

/**
 * is url
 * 
 * @param mixed $url
 * @return bool
 */
function io_is_url($url){
	if (preg_match("/^http[s]?:\/\/.*$/", $url)) {
	//if (false !== filter_var($url, FILTER_VALIDATE_URL)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 生成二维码
 * @param mixed $text 内容
 * @param mixed $size 尺寸
 * @param mixed $margin 边距
 * @param mixed $level 容错级别
 * @return bool|string
 */
function io_get_qrcode($text, $size = 256, $margin = 10, $level = 'L', $cache = true){
	if ($cache) {
		$cache_key = 'qr_' . strtolower(substr(md5($text),8,16)) . $size . $margin . $level;
		$_cache = wp_cache_get($cache_key);
		if (false !== $_cache) {
			return $_cache;
		}
	}
    //引入phpqrcode类库
    require_once get_theme_file_path('/inc/classes/phpqrcode.php');
    ob_start();
    QRcode::png($text, false, $level, $size, $margin);
    $data = ob_get_contents();
    ob_end_clean();
	if ($cache) {
		wp_cache_set($cache_key, $data);
	}
	return $data;
}
/**
 * 获取二维码数据
 *
 * @param string $text
 * @return string
 */
function io_get_qrcode_base64($text){

	$imageString = base64_encode(io_get_qrcode($text, 256, 10, 'L', false));
    header("content-type:application/json; charset=utf-8");
    return 'data:image/jpeg;base64,' . $imageString;
}
/**
 * 输出二维码图片
 * @param mixed $text 内容
 * @param mixed $size 尺寸
 * @param mixed $margin 边距
 * @param mixed $level 容错级别
 * @return void
 */
function io_show_qrcode($text, $size = 256, $margin = 10, $level = 'L'){
	$headers = array(
		'X-Robots-Tag: noindex, nofollow',
		'Content-type: image/png',
		'cache-control: public, max-age=86400'
	);
	foreach ($headers as $header) {
        header($header);
    }
	echo io_get_qrcode($text, $size, $margin, $level);
}
/**
 * 获取时间
 * @param string $format
 * @param string $offset  日期偏移 如前一天 '-1day'
 * @return string
 */
function io_get_time($format='', $offset=''){
	$format = $format ?: 'Y-m-d H:i:s';
	if($offset)
		$time = date($format, strtotime($offset,current_time( 'timestamp' )));
	else
		$time = date($format, current_time('timestamp'));
	return $time;
}
/**
 * 文字计数
 *
 * @param string $str
 * @param string $charset
 * @return int
 */
function io_strlen($str, $charset = 'utf-8'){
    //中文算一个，英文算半个
    return (int) ((strlen($str) + mb_strlen($str, $charset)) / 4);
}

function set404(){
    global $wp_query;
    $wp_query->is_home = false;
    $wp_query->is_404 = true;
    $wp_query->query = array('error'=>'404');
    $wp_query->query_vars['error'] = '404';
    get_template_part('404');
    exit();
}
/**
 * 网址查重
 *
 * @param string $link
 * @return bool
 */
function link_exists($link){
    global $wpdb;
    $link = str_replace(array('http://','https://'), '', $link);
    if(!empty($link)){
        $sql = "SELECT `post_id` FROM $wpdb->postmeta WHERE `meta_value` REGEXP '^http(s)?://{$link}(/)?$' AND `meta_key`='_sites_link'";
        if($wpdb->get_row($sql)) {
            return true;
        } 
    }
    return false;
}

/**
 * 文章标题查重
 *
 * @param string $title
 * @param string $type
 * @return bool
 */
function title_exists($title, $type='sites'){
    global $wpdb;
    if(!empty($title)){
        $sql = "SELECT `ID` FROM $wpdb->posts WHERE `post_status` IN ('pending','publish') AND `post_type` = '{$type}' AND `post_title` = '{$title}'";
        if($wpdb->get_row($sql)) {
            return true;
        } 
    }
    return false;
}

/**
 * 将任意编码的字符串转换为 UTF-8
 *
 * @param string $string 要编码的字符串
 * @param string $encoding  编码；默认值：ISO-8859-1
 * @param bool $safe_mode 安全模式：如果设置为TRUE，则在出现错误时返回原始字符串
 * @return  string 
 */
function io_encode_utf8( $string = '', $encoding = 'iso-8859-1', $safe_mode = false ) {
	$safe = ( $safe_mode ) ? $string : false;
	if ( strtoupper( $encoding ) == 'UTF-8' || strtoupper( $encoding ) == 'UTF8' ) {
		return $string;
	} elseif ( strtoupper( $encoding ) == 'ISO-8859-1' ) {
		return utf8_encode( $string );
	} elseif ( strtoupper( $encoding ) == 'WINDOWS-1252' ) {
		return utf8_encode( io_map_w1252_iso8859_1( $string ) );
	} elseif ( strtoupper( $encoding ) == 'UNICODE-1-1-UTF-7' ) {
		$encoding = 'utf-7';
	}
	if ( function_exists( 'mb_convert_encoding' ) ) {
		$conv = @mb_convert_encoding( $string, 'UTF-8', strtoupper( $encoding ) );
		if ( $conv ) {
			return $conv;
		}
	}
	if ( function_exists( 'iconv' ) ) {
		$conv = @iconv( strtoupper( $encoding ), 'UTF-8', $string );
		if ( $conv ) {
			return $conv;
		}
	}
	if ( function_exists( 'libiconv' ) ) {
		$conv = @libiconv( strtoupper( $encoding ), 'UTF-8', $string );
		if ( $conv ) {
			return $conv;
		}
	}
	return $safe;
}
/**
 * 特殊模式
 * Windows-1252 基本上是 ISO-8859-1 ——除了一些例外
 * @param string $string 
 * @return  string 
 */
function io_map_w1252_iso8859_1( $string = '' ) {
	if ( '' == $string ) {
		return '';
	}
	$return = '';
	for ( $i = 0; $i < strlen( $string ); ++$i ) {
		$c = ord( $string[ $i ] );
		switch ( $c ) {
			case 129:
				$return .= chr( 252 );
				break;
			case 132:
				$return .= chr( 228 );
				break;
			case 142:
				$return .= chr( 196 );
				break;
			case 148:
				$return .= chr( 246 );
				break;
			case 153:
				$return .= chr( 214 );
				break;
			case 154:
				$return .= chr( 220 );
				break;
			case 225:
				$return .= chr( 223 );
				break;
			default:
				$return .= chr( $c );
				break;
		}
	}
	return $return;
}
/**
 * 获取链接根域名
 * @param string $url 
 * @return string 
 */
function get_url_root($url){
	if (!$url) {
		return $url;
	}
	if (!preg_match("/^http/is", $url)) {
		$url = "http://" . $url;
	}
	$url = parse_url($url)["host"];
	$url_arr   = explode(".", $url);
	if (count($url_arr) <= 2) {
		$host = $url;
	} else {
		$last   = array_pop($url_arr);
		$last_1 = array_pop($url_arr);
		$last_2 = array_pop($url_arr);
		$host   = $last_1 . '.' . $last;
		if (in_array($host, DUALHOST))
			$host = $last_2 . '.' . $last_1 . '.' . $last;
	}
	return $host;
}
/**
 * 获取随机数
 * @param int $counts 随机数位数
 * @return string
 */
function io_get_captcha($counts = 6){
    $original = '0,1,2,3,4,5,6,7,8,9';
    $original = explode(',', $original);
    $code      = "";
    for ($j = 0; $j < $counts; $j++) {
        $code .= $original[rand(0, count($original) - 1)];
    }
    return strtolower($code);
}
/**
 * 
 * 
 * @param mixed $abc
 * @return float|int
 */
function char_to_num($abc){
    $ten = 0;
    $len = strlen($abc);
    for($i=1;$i<=$len;$i++){
		$char = substr($abc,0-$i,1);//反向获取单个字符
        $int = ord($char);
        $ten += ($int-65)*pow(26,$i-1);
    }
    return $ten;
}

/**
 * 多久后
 * 
 * @param mixed $time
 * @return bool|string
 */
function io_friend_after_date($time){
	if (!$time)
		return false;
	if (!is_numeric($time)) {
		$time = strtotime($time);
	}
	$today      	= strtotime(date("Y-m-d", current_time('timestamp'))); //今天
	$tomorrow       = $today + 3600 * 24; //明天
	$after_tomorrow = $tomorrow + 3600 * 24; //后天
	$three_days     = $after_tomorrow + 3600 * 24; //大后天

	$date = '';

	switch ($time) {
		case $time > $three_days:
			$date = date(__('m月d日 H:i', 'i_theme'), $time);
			break;
		case $time > $after_tomorrow:
			$date = __('后天', 'i_theme') . date('H:i', $time);
			break;
		case $time > $tomorrow:
			$date = __('明天', 'i_theme') . date('H:i', $time);
			break;
		case $time > $today:
			$date = __('今天', 'i_theme') . date('H:i', $time);
			break;
		default:
			$date = date(__('m月d日 H:i', 'i_theme'), $time);
			break;
	}
	return $date;
}
