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
		2) Use multiple and strong hashes (like whirlpool, bcrypt or argon2)<br/>
		3) Salt your hashes with long and alphanumericals "words", you can generate your salt by hashing multiple times the timestamp of account creation with username for example (but keep the recipe secret !).<br/>
		<br/><br/>
		<b><u>Warning:</u></b> Do not use this website in illegal or millitary purposes. We are not responsible of bad (or illegal) usage of this tool.<br/>
	</section>
	<form class="hash-form" method="post">
		<textarea id="input-text" name="input-text" placeholder="Some input text to hash or dehash ..."><?php echo $text; ?></textarea>
		<div id="button-group">
			<input type="submit" id="hash-button" name="hash-button" value="Hash me !" />
			<input type="submit" id="dehash-button" name="dehash-button" value="Dehash me !" />
		</div>
	</form>
	<div class="beforeResult">
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

		<div id="disclaimerMessage" class="disclaimerMessage">
			Hey you !<br/>
			Can you disable you adblocker please ?<br/>
			This is our only way to finance this website, we promise we won't display more than one ad per page :)<br/>
			Thanks and have fun with our tool !
		</div>

		<script type="text/javascript">
			window.addEventListener('load', () => {
				if(window.getComputedStyle(document.getElementsByClassName('beforeResult')[0]).height.charAt(0) === '0') {
					document.getElementById('disclaimerMessage').style.display = 'block';
				}
			});
		</script>
	</div>

	<?php
	if($text != "") {
		if(count($pageData['results']) == 0) {
			echo '<div style="text-align: center;">No results found in database.</div>';
		} else {
			$head = 'Text';
			$field = 'text';

			if($type == 2) {
				$head = 'Hash';
				$field = 'hash';
			}

			echo '<div style="text-align: center;">
					<h3>Results</h3>
					</div>
				<table class="hash-results">
					<tr><th>'.$head.'</th><th>Hash Algorithm</th></tr>';
			foreach ($pageData['results'] as $hash) {
				$hash = json_decode(json_encode($hash), true);
				
				echo '<tr><td>'.$hash[$field].'</td><td>'.$hash['type'].'</td></tr>';
			}

			echo '</table>';
		}
	}

	?>
	<script src="https://01.cdn.elanis.eu/portfolio/js/jquery.min.js"></script>
	<script>
	var count = 0;
	$(document).ready(function(){
			$('#background svg').attr("width",$(window).innerWidth());
			$('#background svg').attr("height",$(window).innerHeight());

		setInterval(function() {
			let i = count++;
			count%=10;
			$('#background-'+ i).append('<circle id="bubble-'+ i + '" fill="#27ae60" fill-opacity="0.1" class="background-bubble" cx="' + 
				Math.floor(Math.random()*$(window).innerWidth()) + '" cy="' +
				Math.floor(Math.random()*$(window).innerHeight()) + '" r="0" />');

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
</div>

<footer>
Created by <a href="https://dysnomia.studio" target="_blank">Dysnomia</a> - Copyright 2017-<?php echo date('Y'); ?><br/>
<span id="hashCount">About 65 million</span> hash stored !
</footer>
<script type="text/javascript">
	$(document).ready(function() {
		$('#count').on('load', function() {
			var countHash = parseInt($("#count").contents().find("body").text());
			if(isNaN(countHash)) { return; }
			countHash = "" + countHash;
			var text = [];
			var i = countHash.length;
			while(i>2) {
				i -= 3;
				text.unshift(countHash.slice(i));
				countHash = countHash.slice(0, i);
			}
			text.unshift(countHash);

			$('#hashCount').text(text);
		});
		$('#count').attr('src', function ( i, val ) { return val; });
	});
</script>
<iframe id="count" src="count" width="1" height="1" style="display: none;"></iframe>
