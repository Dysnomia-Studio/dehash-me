// Hash count
async function hashCount() {
	const countHashResponse = await fetch('/count');

	if (countHashResponse.status !== 200) {
		return;
	}
	let countHash = await countHashResponse.text();

	const text = [];
	let i = countHash.length;
	while (i > 2) {
		i -= 3;
		text.unshift(countHash.slice(i));
		countHash = countHash.slice(0, i);
	}
	text.unshift(countHash);

	document.getElementById('hashCount').innerText = text.filter((d) => d !== '');
}

// SVG Background
let count = 0;

function ready(fn) {
	if (document.readyState != 'loading') {
		fn();
	} else {
		document.addEventListener('DOMContentLoaded', fn);
	}
}

ready(() => {
	setInterval(function () {
		const i = count;
		count++;
		count %= 10;

		let bgElt = document.getElementById('background-' + i);
		bgElt.innerHTML = bgElt.innerHTML + '<circle id="bubble-' + i + '" fill="#27ae60" fill-opacity="0.1" class="background-bubble" cx="' +
			Math.floor(Math.random() * window.innerWidth) + '" cy="' +
			Math.floor(Math.random() * window.innerHeight) + '" r="0" />'

		setTimeout(function () {
			document.getElementById('bubble-' + i).remove();
			bgElt.innerHTML = bgElt.innerHTML;
		}, 3000);
	}, 300);

	window.addEventListener('resize', function () {
		for (const element of document.querySelectorAll('#background svg')) {
			element.setAttribute('tabindex', 'width', window.innerWidth);
			element.setAttribute('tabindex', 'height', window.innerHeight);
		}
	});

	hashCount();
});
