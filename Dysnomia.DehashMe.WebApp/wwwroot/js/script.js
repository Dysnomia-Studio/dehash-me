// Hash count
async function hashCount() {
	const countHashResponse = await fetch('/count');

	if (countHashResponse.status !== 200) { return; }
	let countHash = await countHashResponse.text();

	const text = [];
	let i = countHash.length;
	while (i > 2) {
		i -= 3;
		text.unshift(countHash.slice(i));
		countHash = countHash.slice(0, i);
	}
	text.unshift(countHash);

	$('#hashCount').text(text.filter((d) => d !== ''));
}

// SVG Background
let count = 0;
$(document).ready(() => {
	$('#background svg').attr('width', $(window).innerWidth());
	$('#background svg').attr('height', $(window).innerHeight());

	setInterval(function () {
		const i = count++;
		count %= 10;
		$('#background-' + i).append('<circle id="bubble-' + i + '" fill="#27ae60" fill-opacity="0.1" class="background-bubble" cx="' +
			Math.floor(Math.random() * $(window).innerWidth()) + '" cy="' +
			Math.floor(Math.random() * $(window).innerHeight()) + '" r="0" />');

		$('#background-' + i).html($('#background-' + i).html());

		setTimeout(function () {
			$('#bubble-' + i).remove();
			$('#background-' + i).html($('#background-' + i).html());
		}, 3000);
	}, 300);

	$(window).resize(function () {
		$('#background svg').attr('width', $(window).innerWidth());
		$('#background svg').attr('height', $(window).innerHeight());
	});

	hashCount();
});