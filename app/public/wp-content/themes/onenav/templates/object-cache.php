<?php
/**
 * Memcached Object Cache
 * 
   在PHP中安装了 Memcached 扩展，然后复制此文件到 wp-content 目录下，WordPress 将使用 Memcached 作为对象缓存。
 *
 * @package WordPress
 */

if ((isset($_GET['debug']) && $_GET['debug'] == 'sql')) {
	return;
}

// 如果 memcached 服务器列表未定义或不是数组，则定义为空数组
if (!isset($memcached_servers) || !is_array($memcached_servers)) {
	$memcached_servers = array();
}

// 如果 memcached 服务器列表为空，则添加默认值
if (empty($memcached_servers)) {
	$memcached_servers = array('127.0.0.1:11211');
}

// 如果多个安装程序共用一个 wp-config.php 或 $table_prefix，
// 用户可以使用此功能来保证此对象缓存生成的密钥的唯一性。
if (!defined('WP_CACHE_KEY_SALT')) {
	if (isset($table_prefix) && !empty($table_prefix)) {
		define('WP_CACHE_KEY_SALT', $table_prefix);
	} else {
		define('WP_CACHE_KEY_SALT', md5(__FILE__));
	}
}

if (class_exists('Memcached')) {
	function wp_cache_add($key, $data, $group = '', $expire = 0){
		global $wp_object_cache;
		return $wp_object_cache->add($key, $data, $group, $expire);
	}

	function wp_cache_cas($cas_token, $key, $data, $group = '', $expire = 0){
		global $wp_object_cache;
		return $wp_object_cache->cas($cas_token, $key, $data, $group, (int) $expire);
	}

	function wp_cache_close(){
		global $wp_object_cache;
		return $wp_object_cache->close();
	}

	function wp_cache_decr($key, $offset = 1, $group = ''){
		global $wp_object_cache;
		return $wp_object_cache->decr($key, $offset, $group);
	}

	function wp_cache_delete($key, $group = ''){
		global $wp_object_cache;
		return $wp_object_cache->delete($key, $group);
	}

	function wp_cache_flush(){
		global $wp_object_cache;
		return $wp_object_cache->flush();
	}

	function wp_cache_get($key, $group = '', $force = false, &$found = null){
		global $wp_object_cache;
		return $wp_object_cache->get($key, $group, $force, $found);
	}

	/**
	 * $keys_and_groups = array(
	 *      array( 'key', 'group' ),
	 *      array( 'key', '' ),
	 *      array( 'key', 'group' ),
	 *      array( 'key' )
	 * );
	 *
	 */
	function wp_cache_get_multiple($key_and_groups, $bucket = 'default'){
		global $wp_object_cache;
		return $wp_object_cache->get_multiple($key_and_groups, $bucket);
	}

	/**
	 * $items = array(
	 *      array( 'key', 'data', 'group' ),
	 *      array( 'key', 'data' )
	 * );
	 *
	 */
	function wp_cache_set_multiple($items, $group = 'default', $expire = 0){
		global $wp_object_cache;
		return $wp_object_cache->set_multiple($items, $group = 'default', $expire = 0);
	}

	function wp_cache_delete_multiple($keys, $group = ''){
		global $wp_object_cache;
		return $wp_object_cache->delete_multiple($keys, $group);
	}

	function wp_cache_get_with_cas($key, $group = '', &$cas_token = null){
		global $wp_object_cache;
		return $wp_object_cache->get_with_cas($key, $group, $cas_token);
	}

	function wp_cache_incr($key, $offset = 1, $group = ''){
		global $wp_object_cache;
		return $wp_object_cache->incr($key, $offset, $group);
	}

	if (!isset($_GET['debug']) || $_GET['debug'] != 'sql'):
	function wp_cache_init(){
		global $wp_object_cache;
		$wp_object_cache = new WP_Object_Cache();
	}
	endif;

	function wp_cache_replace($key, $data, $group = '', $expire = 0){
		global $wp_object_cache;
		return $wp_object_cache->replace($key, $data, $group, $expire);
	}

	function wp_cache_set($key, $data, $group = '', $expire = 0){
		global $wp_object_cache;

		if (defined('WP_INSTALLING') == false) {
			return $wp_object_cache->set($key, $data, $group, $expire);
		} else {
			return $wp_object_cache->delete($key, $group);
		}
	}

	function wp_cache_switch_to_blog($blog_id){
		global $wp_object_cache;
		return $wp_object_cache->switch_to_blog($blog_id);
	}

	function wp_cache_add_global_groups($groups){
		global $wp_object_cache;
		$wp_object_cache->add_global_groups($groups);
	}

	function wp_cache_add_non_persistent_groups($groups){
		global $wp_object_cache;
		$wp_object_cache->add_non_persistent_groups($groups);
	}

	function wp_cache_get_stats(){
		global $wp_object_cache;
		return $wp_object_cache->get_stats();
	}

	class WP_Object_Cache
	{
		public $global_groups = array();
		public $no_mc_groups = array();
		public $cache = array();
		public $mc = array();
		private $blog_prefix;
		private $global_prefix;
		public $stats = array('add' => 0, 'delete' => 0, 'get' => 0, 'get_multiple' => 0, );
		public $group_ops = array();
		private $default_expiration = 0;

		function add($id, $data, $group = 'default', $expire = 0){
			$key = $this->key($id, $group);

			if (is_object($data)) {
				$data = clone $data;
			}

			if (in_array($group, $this->no_mc_groups)) {
				$this->cache[$key] = $data;

				return true;
			} elseif (isset($this->cache[$key]) && $this->cache[$key] !== false) {
				return false;
			}

			$mc     = $this->get_mc($group);
			$expire = ($expire == 0) ? $this->default_expiration : $expire;
			$result = $mc->add($key, $data, $expire);

			if (false !== $result) {
				if (isset($this->stats['add'])) {
					++$this->stats['add'];
				}

				$this->group_ops[$group][] = "add $id";
				$this->cache[$key]         = $data;
			}

			return $result;
		}

		function cas($cas_token, $id, $data, $group = 'default', $expire = 0){
			$key = $this->key($id, $group);
			$mc  = $this->get_mc($group);
			unset($this->cache[$key]);
			return $mc->cas($cas_token, $key, $data, $expire);
		}

		function add_global_groups($groups){
			if (!is_array($groups)) {
				$groups = (array) $groups;
			}

			$this->global_groups = array_merge($this->global_groups, $groups);
			$this->global_groups = array_unique($this->global_groups);
		}

		function add_non_persistent_groups($groups){
			if (!is_array($groups)) {
				$groups = (array) $groups;
			}

			$this->no_mc_groups = array_merge($this->no_mc_groups, $groups);
			$this->no_mc_groups = array_unique($this->no_mc_groups);
		}

		function is_non_persistent_group($group){
			$group = $group ?: 'default';
			return isset($this->no_mc_groups[$group]);
		}

		function incr($id, $offset = 1, $group = 'default'){
			$key                 = $this->key($id, $group);
			$mc                  = $this->get_mc($group);
			$this->cache[$key] = $mc->increment($key, $offset);

			return $this->cache[$key];
		}

		function decr($id, $offset = 1, $group = 'default'){
			$key                 = $this->key($id, $group);
			$mc                  = $this->get_mc($group);
			$this->cache[$key] = $mc->decrement($key, $offset);

			return $this->cache[$key];
		}

		function close(){
			foreach ($this->mc as $bucket => $mc) {
				$mc->quit();
			}
		}

		function delete($id, $group = 'default'){
			$key = $this->key($id, $group);

			if (in_array($group, $this->no_mc_groups)) {
				unset($this->cache[$key]);

				return true;
			}

			$mc = $this->get_mc($group);

			$result = $mc->delete($key);

			if (isset($this->stats['delete'])) {
				++$this->stats['delete'];
			}
			$this->group_ops[$group][] = "delete $id";

			if (false !== $result) {
				unset($this->cache[$key]);
			}

			return $result;
		}

		function flush(){
			// 如果有多个博客，请不要刷新。
			if (function_exists('is_site_admin') || defined('CUSTOM_USER_TABLE') && defined('CUSTOM_USER_META_TABLE')) {
				return true;
			}

			$ret = true;
			foreach (array_keys($this->mc) as $group) {
				$ret &= $this->mc[$group]->flush();
			}

			return $ret;
		}

		function get($id, $group = 'default', $force = false, &$found = null){
			$key = $this->key($id, $group);
			$mc  = $this->get_mc($group);

			if (null !== $found) {
				$found = true;
			}

			if (isset($this->cache[$key]) && (!$force || in_array($group, $this->no_mc_groups))) {
				if (is_object($this->cache[$key])) {
					$value = clone $this->cache[$key];
				} else {
					$value = $this->cache[$key];
				}
			} else if (in_array($group, $this->no_mc_groups)) {
				$this->cache[$key] = $value = false;
			} else {
				$value = $mc->get($key);
				if ($mc->getResultCode() == Memcached::RES_NOTFOUND) {
					$value = false;
					if (null !== $found) {
						$found = false;
					}
				}

				$this->cache[$key] = $value;
			}

			if (isset($this->stats['get'])) {
				++$this->stats['get'];
			}

			$this->group_ops[$group][] = "get $id";

			if ('checkthedatabaseplease' === $value) {
				unset($this->cache[$key]);
				$value = false;
			}

			return $value;
		}

		function get_multiple($keys, $group = 'default'){
			$return = array();
			$gets   = array();
			foreach ($keys as $i => $values) {
				$mc     = $this->get_mc($group);
				$values = (array) $values;
				if (empty($values[1])) {
					$values[1] = 'default';
				}

				list($id, $group) = (array) $values;
				$key              = $this->key($id, $group);

				if (isset($this->cache[$key])) {

					if (is_object($this->cache[$key])) {
						$return[$key] = clone $this->cache[$key];
					} else {
						$return[$key] = $this->cache[$key];
					}

				} else if (in_array($group, $this->no_mc_groups)) {
					$return[$key] = false;

				} else {
					$gets[$key] = $key;
				}
			}

			if (!empty($gets)) {
				$null    = null;
				$results = $mc->getMulti($gets, $null, Memcached::GET_PRESERVE_ORDER);
				$joined  = array_combine(array_keys($gets), array_values($results));
				$return  = array_merge($return, $joined);
			}

			@++$this->stats['get_multiple'];
			$this->group_ops[$group][] = "get_multiple $id";
			$this->cache               = array_merge($this->cache, $return);

			return array_values($return);
		}

		function delete_multiple($ids, $group = 'default'){
			$keys = array();

			foreach ($ids as $id) {
				$keys[] = $key = $this->key($id, $group);

				unset($this->cache[$key]);
			}

			if ($this->is_non_persistent_group($group)) {
				return true;
			}

			$mc = $this->get_mc($group);

			return $mc->deleteMulti($keys);
		}

		function get_with_cas($id, $group = 'default', &$cas_token = null){
			$key = $this->key($id, $group);
			$mc  = $this->get_mc($group);

			if (defined('Memcached::GET_EXTENDED')) {
				$result = $mc->get($key, null, Memcached::GET_EXTENDED);

				if ($mc->getResultCode() == Memcached::RES_NOTFOUND) {
					return false;
				} else {
					$cas_token = $result['cas'];
					return $result['value'];
				}
			} else {
				$result = $mc->get($key, null, $cas_token);

				if ($mc->getResultCode() == Memcached::RES_NOTFOUND) {
					return false;
				} else {
					return $result;
				}
			}
		}

		function key($key, $group){
			if (empty($group)) {
				$group = 'default';
			}

			if (false !== array_search($group, $this->global_groups)) {
				$prefix = $this->global_prefix;
			} else {
				$prefix = $this->blog_prefix;
			}

			return preg_replace('/\s+/', '', WP_CACHE_KEY_SALT . "$prefix$group:$key");
		}

		function replace($id, $data, $group = 'default', $expire = 0){
			$key    = $this->key($id, $group);
			$expire = ($expire == 0) ? $this->default_expiration : $expire;
			$mc     = $this->get_mc($group);

			if (is_object($data)) {
				$data = clone $data;
			}

			$result = $mc->replace($key, $data, $expire);
			if (false !== $result) {
				$this->cache[$key] = $data;
			}

			return $result;
		}

		function set($id, $data, $group = 'default', $expire = 0){
			$key = $this->key($id, $group);
			if (isset($this->cache[$key]) && ('checkthedatabaseplease' === $this->cache[$key])) {
				return false;
			}

			if (is_object($data)) {
				$data = clone $data;
			}

			$this->cache[$key] = $data;

			if (in_array($group, $this->no_mc_groups)) {
				return true;
			}

			$expire = ($expire == 0) ? $this->default_expiration : $expire;
			$mc     = $this->get_mc($group);
			$result = $mc->set($key, $data, $expire);

			return $result;
		}

		function set_multiple($items, $group = 'default', $expire = 0){
			$sets   = array();
			$mc     = $this->get_mc($group);
			$expire = ($expire == 0) ? $this->default_expiration : $expire;

			foreach ($items as $i => $item) {
				if (empty($item[2])) {
					$item[2] = 'default';
				}

				list($id, $data, $group) = $item;

				$key = $this->key($id, $group);
				if (isset($this->cache[$key]) && ('checkthedatabaseplease' === $this->cache[$key])) {
					continue;
				}

				if (is_object($data)) {
					$data = clone $data;
				}

				$this->cache[$key] = $data;

				if (in_array($group, $this->no_mc_groups)) {
					continue;
				}

				$sets[$key] = $data;
			}

			if (!empty($sets)) {
				$mc->setMulti($sets, $expire);
			}
		}

		function switch_to_blog($blog_id){
			if (is_multisite()) {
				$blog_id = (int) $blog_id;

				$this->blog_prefix = $blog_id . ':';
			} else {
				global $table_prefix;

				$this->blog_prefix = $table_prefix . ':';
			}
		}

		function colorize_debug_line($line){
			$colors = array(
				'get'    => 'green',
				'set'    => 'purple',
				'add'    => 'blue',
				'delete' => 'red'
			);

			$cmd = substr($line, 0, strpos($line, ' '));

			$cmd2 = "<span style='color:{$colors[$cmd]}'>$cmd</span>";

			return $cmd2 . substr($line, strlen($cmd)) . "\n";
		}

		function get_stats(){
			$items = [];
			foreach ($this->mc as $mc) {
				$stats = $mc->getStats();
				foreach ($stats as $key => $details) {
					foreach ($details as $name => $value) {
						$items[$key][$name] = $value;
					}
				}
			}

			return $items;
		}

		function get_mc($group = ''){
			if (isset($this->mc[$group])) {
				return $this->mc[$group];
			}

			return $this->mc['default'];
		}

		function failure_callback($host, $port){
		}

		function __construct(){
			global $memcached_servers;

			if (isset($memcached_servers)) {
				$buckets = $memcached_servers;
			} else {
				$buckets = array('127.0.0.1:11211');
			}

			reset($buckets);
			if (is_int(key($buckets))) {
				$buckets = array('default' => $buckets);
			}

			foreach ($buckets as $bucket => $servers) {
				$this->mc[$bucket] = new Memcached();

				$instances = array();
				foreach ($servers as $server) {
					@list($node, $port) = explode(':', $server);
					if (empty($port)) {
						$port = ini_get('memcache.default_port');
					}
					$port = intval($port);
					if (!$port) {
						$port = 11211;
					}

					$instances[] = array($node, $port, 1);
				}
				$this->mc[$bucket]->addServers($instances);
			}

			global $blog_id, $table_prefix;
			$this->global_prefix = '';
			$this->blog_prefix   = '';
			if (function_exists('is_multisite')) {
				$this->global_prefix = (is_multisite() || (defined('CUSTOM_USER_TABLE') && defined('CUSTOM_USER_META_TABLE'))) ? '' : $table_prefix;
				$this->blog_prefix   = (is_multisite() ? $blog_id : $table_prefix) . ':';
			}

			$this->cache_hits   =& $this->stats['get'];
			$this->cache_misses =& $this->stats['add'];
		}


	}
} else {
	// 没有 Memcached 类
	if (function_exists('wp_using_ext_object_cache')) {
		wp_using_ext_object_cache(false);
	} else {
		wp_die('Memcached 类不可用。');
	}
}
