<?php
$data = [];
$maxTime = (time() - 2); // Antispam measure: Prevent more than one call per 2 second

if(!isset($_SESSION['ip']) && (isset($_GET['cookies']) && $_GET['cookies'] == 'true')) {
	$data = [ 'errorMessage' => 'Cookies are disabled on your browser, our API can\'t work without cookies for some security reasons.' ];

	$pageData['writeCache'] = false;

} else if(!isset($_SESSION['ip']) && !isset($_GET['cookies'])) {
	$_SESSION['ip'] = Web::get_client_ip();

	$url = 'api?cookies=true';
	if(isset($_GET['hash'])) {
		$url = 'api?cookies=true&hash='.$_GET['hash'];
	} else if(isset($_GET['dehash'])) {
		$url = 'api?cookies=true&dehash='.$_GET['dehash'];
	}

	header('Location: '.$url);
	exit();

} else if($_SESSION['ip'] != Web::get_client_ip() || (isset($_SESSION['lastTime']) && $_SESSION['lastTime'] > $maxTime)) {
	$data = [ 'errorMessage' => 'An antispam measure has stop this api call, please retry later.' ];

	$pageData['writeCache'] = false;
} else {
	$text = '';

	if(isset($_GET['hash'])) {
		$text = (!isset($_GET['hash']) || empty($_GET['hash']))?'':$_GET['hash'];
		$text = htmlentities(htmlspecialchars(trim($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

		$hashMngr = new HashManager();
		$data = $hashMngr->getByWord($text);

	} else if(isset($_GET['dehash'])) {
		$text = (!isset($_GET['dehash']) || empty($_GET['dehash']))?'':$_GET['dehash'];
		$text = htmlentities(htmlspecialchars(trim($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

		$hashMngr = new HashManager();
		$data = $hashMngr->getByHash($text);
	}

	if($text != '') {
		$_SESSION['lastTime'] = time();

		$dataOut = [];
		foreach ($data as $line) {
			$line = json_decode(json_encode($line), true);

			$hashRetour = [];
			$hashRetour['text'] = $line['text'];
			$hashRetour['type'] = $line['type'];
			$hashRetour['hash'] = $line['hash'];
			
			array_push($dataOut, $hashRetour);
		}

		$data = $dataOut;
	}
}

if(!isset($_SESSION['lastTime'])) {
	$_SESSION['lastTime'] = 0;
} 

echo json_encode($data);
?>