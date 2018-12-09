<?php
class Router {
	private $cacheBlacklist = array();
	private $cacheTime = 86400; // in seconds

	public function __construct() {
		global $config;
		$this->cacheBlacklist = $config['cacheBlacklist'];
	}

	public function get($pagename) {
		// Default values
		$data = array();
		$data['writeCache'] = false;
		$data['readCache'] = false;
		$data['pageName'] = $pagename.'.php';
		$data['cacheName'] = $this->buildCacheName($pagename);

		if(file_exists($data['cacheName']) &&
			filemtime($data['cacheName']) > (time() - $this->cacheTime) &&
			!in_array($pagename, $this->cacheBlacklist)) {

			$data['readCache'] = true;
		} else if(!in_array($pagename, $this->cacheBlacklist)) {
			$data['writeCache'] = true;
		}

		return $data;
	}

	private function buildCacheName($page) {
		foreach ($_GET as $key => $value) {
			if($key == 'route' || $key=='' || $value=='') { continue; }

			$key   = htmlentities(htmlspecialchars($key));
			$value = htmlentities(htmlspecialchars($value));

			$page.= '_'.$key.'-'.$value;
		}

		return DIR_CACHE.$page.'.html';
	}
}