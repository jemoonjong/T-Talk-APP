<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Framework Function Pack - packed js css
 * @author jodalpo
 * @since 2009-01-23
 */

/*
 * directory 생성.
 */
function folderCreate($pathname, $mode = '0755') {
	if (is_dir($pathname) || empty($pathname)) {
		return true;
	}

	if (is_file($pathname)) {
		return true;
	}
	$nextPathname = substr($pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR));

	if(folderCreate($nextPathname, $mode)) {
		if(!file_exists($pathname)) {
			if(mkdir($pathname, intval($mode, 8))) {
				chmod($pathname, intval($mode, 8));
				return true;
			} else {
				return false;
			}
		}
	}
	return true;
}

/**
 * javascript packed
 * @param $file javscript files
 */
function packedJavascript($file)
{
	$Asset = new Asset;
	$Asset->_js_dir = JS_ROOT;
	$Asset->_cache_path = CACHE_ASSET;
	folderCreate($Asset->_cache_path);

	$Asset->_link = CACHE_URL;
	$file = array_chunk($file, 7);
	foreach($file as $javascript) {
		$Asset->_files['javascript'] = $javascript;
		$Asset->_count_js = count($javascript);
		$Asset->js();
	}
}

/**
 * css packed
 * @param $file css files
 */
function packedCss($file)
{
	$Asset = new Asset;
	$Asset->_css_dir = CSS_ROOT;
	$Asset->_cache_path = CACHE_ASSET;
	folderCreate($Asset->_cache_path);

	$Asset->_link = CACHE_URL;
	$Asset->_files['css'] = $file;
	$Asset->_count_css = count($file);

	$Asset->css();
}

/**
 * javascript + css both packed
 *
 * @param array() $data javascript, css files
 * @param string $javascriptPath javascript directory path
 * @param string $cssPath css directory path
 */
function scriptForLayout($data)
{
	if (!empty($data['css'])) packedCss($data['css']);
	if (!empty($data['javascript'])) packedJavascript($data['javascript']);
}

require_once(APPPATH.'helpers/csstidy/class.csstidy.php');
require_once(APPPATH.'helpers/jsmin/jsmin.php');

class Asset {
	var $_css_dir;
	var $_js_dir;
	var $_files;
	var $_cache_path;
	var $is_working = PACKED;
	var $_count_js;
	var $_count_css;

	public function js() {
		if ($this->is_working && $this->_count_js) {
			$fileName = $this->__find($this->__generateFileName($this->_files['javascript']).'_([0-9]{10}).js');

			if ($fileName) {
				$fileName = $fileName[0];
			}

			if ($fileName) {
				$packed_ts = filemtime($this->_cache_path.$fileName);
				$latest_ts = 0;
				foreach ($this->_files['javascript'] as $script) {
					$latest_ts = max($latest_ts, filemtime($this->_js_dir.$script.'.js'));
				}

				if ($latest_ts > $packed_ts) {
					unlink($this->_cache_path.$fileName);
					$fileName = null;
				}
			}

			if (!$fileName) {
				$ts = time();
				$scriptBuffer = '';
				foreach ($this->_files['javascript'] as $script) {
					if (file_exists($this->_js_dir.$script.'.js')) {
						$buffer = file_get_contents($this->_js_dir.$script.'.js');
						if (PHP5) {
							$buffer = trim(JSMin::minify($buffer));
						}
						$scriptBuffer .= "\n/* {$script}.js */\n" . $buffer;
					}
				}

				$fileName = $this->__generateFileName($this->_files['javascript']).'_'.$ts.'.js';
				file_put_contents($this->_cache_path.$fileName, $scriptBuffer);
			}

			echo '<script type="text/javascript" src="'.$this->_link.$fileName.'"></script>'."\n";
		} else {
			echo $this->normal_js();
		}
	}

	public function css() {
		if ($this->is_working && $this->_count_css) {
			$fileName = $this->__find($this->__generateFileName($this->_files['css']).'_([0-9]{10}).css');

			if ($fileName) {
				$fileName = $fileName[0];
			}

			if ($fileName) {
				$packed_ts = filemtime($this->_cache_path.$fileName);
				$latest_ts = 0;
				foreach ($this->_files['css'] as $script) {
					$latest_ts = max($latest_ts, filemtime($this->_css_dir.$script.'.css'));
				}

				if ($latest_ts > $packed_ts) {
					unlink($this->_cache_path.$fileName);
					$fileName = null;
				}
			}

			if (!$fileName) {
				$ts = time();
				$scriptBuffer = '';
				foreach ($this->_files['css'] as $script) {
					if (file_exists($this->_css_dir.$script.'.css')) {
						$buffer = file_get_contents($this->_css_dir.$script.'.css');
						$tidy = new csstidy();
						$tidy->load_template('high_compression');
						$tidy->parse($buffer);
						$buffer = $tidy->print->plain();
						$scriptBuffer .= "\n/* {$script}.css */\n" . $buffer;
					}
				}

				$fileName = $this->__generateFileName($this->_files['css']).'_'.$ts.'.css';
				file_put_contents($this->_cache_path.$fileName, $scriptBuffer);
			}

			echo '<link href="'.$this->_link.$fileName.'" rel="stylesheet" type="text/css" media="all" />'."\n";
		} else {
			echo $this->normal_css();
		}
	}

	public function normal_js() {
		if ($this->_count_js) {
			$temp = '';
			foreach ($this->_files['javascript'] as $javascript) {
				if (file_exists($this->_js_dir.$javascript.'.js'))
					$temp .= '<script type="text/javascript" src="'.JS_URL.'/'.$javascript.'.js" charset="'.CHAR_SET.'"></script>'."\n";
			}
			return $temp;
		} else {
			return null;
		}
	}

	public function normal_css() {
		if ($this->_count_css) {
			$temp = '';
			foreach ($this->_files['css'] as $css) {
				if (file_exists($this->_css_dir.$css.'.css'))
					$temp .= '<link href="'.CSS_URL.'/'.$css.'.css" rel="stylesheet" type="text/css" media="all" />'."\n";
			}
			return $temp;
		} else {
			return null;
		}
	}

	public function __generateFileName($names) {
		$file_name = md5(str_replace('.', '-', implode('_', $names)));
		return $file_name;
	}

	public function __find($fileName) {
		if (PHP5) {
			$files = scandir($this->_cache_path);
		} else {
			$dh  = opendir($this->_cache_path);
			while (false !== ($filename = readdir($dh))) {
			    $files[] = $filename;
			}

			sort($files);
		}

		$found = array();
		foreach ($files as $file) {
			if (preg_match("/^{$fileName}$/i", $file)) {
				$found[] = $file;
			}
		}
		return $found;
	}
}
?>