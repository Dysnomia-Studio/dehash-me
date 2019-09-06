<?php
class Stats extends SQLInterface {
    public function __construct() {
        SQLInterface::__construct('***REMOVED***', 'stats', '***REMOVED***', '***REMOVED***');
    }

    public function insertVisit($visits) {
        $this->addContent(
            'visits',
            [$visits]
        );
    }

    public static function newVisit() {
        $_SERVER['HTTP_USER_AGENT'] = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';

        if(!isset($_SESSION['visited']) || $_SESSION['visited']!=$_SERVER['REQUEST_URI']) {
            $visit = [];
            if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'googlebot')) {
                $visit['userAgent'] = 'Google Bot';
            } elseif(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'baidu')) {
                $visit['userAgent'] = 'Baidu Bot';
            } elseif(substr($_SERVER['HTTP_USER_AGENT'],0,31)=='Mozilla/5.0 (compatible; Yandex') {
                $visit['userAgent'] = 'Yandex Bot';
            } else {
                $visit['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
            }
            
            $visit['url'] = Web::full_url($_SERVER);
            $visit['origin'] = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:null;
            $visit['time'] = time();
            $visit['ip'] = Web::get_client_ip();

            $stats = new Stats();
            $stats->insertVisit($visit);

            $_SESSION['visited'] = $_SERVER['REQUEST_URI'];
        }
    }
}
