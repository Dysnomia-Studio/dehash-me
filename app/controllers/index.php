<?php
$text = (!isset($_POST['input-text']) || empty($_POST['input-text']))?"":$_POST['input-text'];
$text = htmlentities(htmlspecialchars(trim($text), ENT_QUOTES | ENT_HTML5, "UTF-8"));

/**
 * Anti-Bot System
 */
if(!isset($_SESSION['lastTime'])) {
	$_SESSION['lastTime'] = 0;
}
$maxTime = (time() - 2); // Antispam measure: Prevent more than one call per 2 second

if(!isset($_SESSION['ip']) || $_SESSION['ip'] != Web::get_client_ip() || $_SESSION['lastTime'] > $maxTime)  { // Bots are not allowed here
		$text = "";
}

$type = 1;

/**
 * Gestionnaire de resultats
 */
if($text != "" && isset($_POST['dehash-button'])) {
	$hashMngr = new HashManager();
	$pageData['results'] = $hashMngr->getByHash($text);

} else if($text != "" && isset($_POST['hash-button'])) {
	$hashMngr = new HashManager();
	$pageData['results'] = $hashMngr->getByWord($text);

	$type = 2;
} else {
	$pageData['results'] = [];
}

// Store ip & add time to session
$_SESSION['ip'] = Web::get_client_ip();

if($text != "") {
	$_SESSION['lastTime'] = time();
}

// Don't use cache
$pageData['writeCache'] = false;
$pageData['readCache'] = false;	