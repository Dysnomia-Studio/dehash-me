<?php
	$hashList = ["md2","md4","md5","sha1","sha256","sha384","sha512","ripemd128","ripemd160","ripemd256","ripemd320","whirlpool"];
	$manager = new MongoDB\Driver\Manager($config['mongo_host']);

	$table_content = "";

	$text = (!isset($_POST['input-text']) || empty($_POST['input-text']))?"":$_POST['input-text'];
	$text = htmlspecialchars(trim($text), ENT_QUOTES | ENT_HTML5, "UTF-8");

	$drawResults = true;
?>
<div id="background">
	<svg id="background-0" class="backgrounds"></svg>
	<svg id="background-1" class="backgrounds"></svg>
	<svg id="background-2" class="backgrounds"></svg>
	<svg id="background-3" class="backgrounds"></svg>
	<svg id="background-4" class="backgrounds"></svg>
	<svg id="background-5" class="backgrounds"></svg>
	<svg id="background-6" class="backgrounds"></svg>
	<svg id="background-7" class="backgrounds"></svg>
	<svg id="background-8" class="backgrounds"></svg>
	<svg id="background-9" class="backgrounds"></svg>
</div>
<div class="corps">
	<header>
		<h1><a href="/">Dehash.me</a></h1>
	</header>
	<section class="disclaimer">
		This website was created to raise awareness on basic hashing weaknesses. This website own and generate its own dictionary. Some tips to developers if you don't want critical data being revealed (and/or dehashed):<br/><br/>
		1) Hash your critical data (like passwords), and all data needed to check forms and not needed as is.<br/>
		2) Use multiple and strong hashes (like whirlpool or bcrypt)<br/>
		3) Salt your hashes with long and alphanumericals "words", you can generate your salt by hashing the username for example.<br/>
		<br/><br/>
		<b><u>Warning:</u></b> Do not use this website in illegal or millitary purposes.<br/>
		<b><u>Note</u>:</b> We don't keep any data on your requests.<br/><br/>
	</section>
	<form class="hash-form" action="/" method="post">
		<textarea id="input-text" name="input-text" placeholder="Some input text to hash or dehash ..."><?php echo $text; ?></textarea>
		<div id="button-group">
			<input type="submit" id="hash-button" name="hash-button" value="Hash me !" />
			<input type="submit" id="dehash-button" name="dehash-button" value="Dehash me !" />
		</div>
	</form>
	<div class="ad">
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<!-- Dehash.me - Responsive -->
		<ins class="adsbygoogle"
		     style="display:block"
		     data-ad-client="***REMOVED***"
		     data-ad-slot="***REMOVED***"
		     data-ad-format="auto"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script>
	</div>
	<?php
	if(!isset($_SESSION['ip']) || $_SESSION['ip'] != get_client_ip()) { // Bots are not allowed here
		$drawResults = false;
	} else if(isset($_POST['hash-button']) && !(empty($text))) {
		// Verif du client sur mongo
		$manager = new MongoDB\Driver\Manager("***REMOVED***");
		$bulkHash = new MongoDB\Driver\BulkWrite(['ordered' => true]);
		$insertCount = 0;
		$alreadyHashed = [];
		$drawHashes = [];

		$filter = ["text" => $text];

		$query = new MongoDB\Driver\Query($filter);
		$cursor = $manager->executeQuery('***REMOVED***.hashlists', $query);

		foreach ($cursor->toArray() as $key => $value) {
			$value = (array) $value;
			if(isset($value['text']) && !empty($value['text']) 
				&& isset($value['type']) && !empty($value['type']) 
				&& isset($value['hash']) && !empty($value['hash'])) {
				$alreadyHashed[$value['type']]=true;
				
				array_push($drawHashes, [$value['hash'],$value['type']]);
			}
		}

		foreach($hashList as $key => $value) {
			$hashedText = hash($value,$text);

			$hash = [
				"text" => $text,
				"type" => $value,
				"hash" => $hashedText,
			];

			if(!isset($alreadyHashed[$value])) {
				$insertCount++;
				$bulkHash->insert($hash);

				// Text
				array_push($drawHashes, [$hashedText,$value]);
			}
		}

		function cmp($a, $b) {
			if ($a[1] == $b[1]) {
			    return 0;
			}
			return ($a[1] < $b[1]) ? -1 : 1;
		}
		usort($drawHashes,"cmp");

		foreach($drawHashes as $key => $value) {
			$table_content .= "<tr><td>".$value[0]."</td><td>".$value[1]."</td>";
		}

		if($insertCount > 0) {
			$manager->executeBulkWrite('***REMOVED***.hashlists', $bulkHash);
		}

	} elseif(isset($_POST['dehash-button'])) {
		// Verif du client sur mongo
		$manager = new MongoDB\Driver\Manager("***REMOVED***");

		$filter = ["hash" => strtolower($text)];

		$query = new MongoDB\Driver\Query($filter);
		$cursor = $manager->executeQuery('***REMOVED***.hashlists', $query);

		foreach ($cursor->toArray() as $key => $value) {
			$value = (array) $value;
			$table_content .= "
		<tr><td>".$value['text']."</td><td>".$value['type']."</td>";
		}
	} else {
		$drawResults = false;
	}

	if($drawResults) {
		if($table_content!="") {
			echo '<div style="text-align: center;"><h3>Results</h3></div>
		<table class="hash-results">
			<tr><th>Text</th><th>Hash Algorithm</th></tr>'.$table_content.'
		</table>';
		} else {
			echo '<div style="text-align: center;">No results found in database.</div>';
		}
	}
	?>
</div>
<script src="https://01.cdn.elanis.eu/portfolio/js/jquery.min.js"></script>
<script>
var count = 0;
$(document).ready(function(){
		$('#background svg').attr("width",$(window).innerWidth());
		$('#background svg').attr("height",$(window).innerHeight());

	setInterval(function() {
		let i = count++;
		count%=10;
		$('#background-'+ i).append('<circle id="bubble-'+ i + '" fill="#27ae60" fill-opacity="0.1" class="background-bubble" cx="'
			+ Math.floor(Math.random()*$(window).innerWidth()) + '" cy="'
			+ Math.floor(Math.random()*$(window).innerHeight()) + '" r="0" />');

		$('#background-'+ i).html($('#background-'+ i).html());

		setTimeout(function() {
			$('#bubble-'+i).remove();
			$('#background-'+ i).html($('#background-'+ i).html());
		},3000);
	}, 300);

	$(window).resize(function(){
		$('#background svg').attr("width",$(window).innerWidth());
		$('#background svg').attr("height",$(window).innerHeight());
	});
});
</script>
<footer>
Created by <a href="https://elanis.eu">Elanis</a> - Copyright 2017 - <a href="https://elanis.eu/contact">Contact</a><br/>
<span id="hashCount">About 2,000,000</span> hash stored !
</footer>
<script type="text/javascript">
	$(document).ready(function() {
		$('#count').on('load', function() {
			var countHash = parseInt($("#count").contents().find("body").text());
			if(isNaN(countHash)) { return; }
			var countHash = "" + countHash;
			var text = [];
			var i = countHash.length;
			while (i > 0) {
				i -= 3;
				text.unshift(countHash.slice(i));
				countHash = countHash.slice(0, i)
			}

			$('#hashCount').text(text);
		});
		$('#count').attr('src', function ( i, val ) { return val; });
	});
</script>
<iframe id="count" src="count" width="1" height="1" style="display: none;"></iframe>
<?php
// Store ip & add time to session
$_SESSION['ip'] = get_client_ip();
?>
