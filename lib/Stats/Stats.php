<?php
abstract class Stats extends MongoInterface {
    public static function newVisit() {
        $_SERVER['HTTP_USER_AGENT'] = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';

        if(!isset($_SESSION['visited']) || $_SESSION['visited']!=$_SERVER['REQUEST_URI']) {
            $manager = new MongoDB\Driver\Manager(
                '***REMOVED***');

            $visit = [];
            if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'googlebot')) {
                $visit['user-agent'] = 'Google Bot';
            } elseif(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'baidu')) {
                $visit['user-agent'] = 'Baidu Bot';
            } elseif(substr($_SERVER['HTTP_USER_AGENT'],0,31)=='Mozilla/5.0 (compatible; Yandex') {
                $visit['user-agent'] = 'Yandex Bot';
            } else {
                $visit['user-agent'] = $_SERVER['HTTP_USER_AGENT'];
            }
            
            $visit['url'] = Web::get_full_url($_SERVER);
            $visit['origin'] = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:null;
            $visit['time'] = time();

            $bulkVisit = new MongoDB\Driver\BulkWrite(['ordered' => true]);
            $bulkVisit->insert($visit);

            $manager->executeBulkWrite('stats.visits', $bulkVisit);

            $_SESSION['visited'] = $_SERVER['REQUEST_URI'];
        }
    }
}
