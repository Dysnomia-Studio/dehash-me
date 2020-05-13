require('./passCalc')();

let j = 1,
	m = 1,
	y = 0;

function birthPass() {
	let text = '';

	if(j < 10)	{ text += '0' + j; }
	else 		{ text += j; }

	if(m < 10)	{ text += '0' + m; }
	else 		{ text += m; }

	if(y < 10)	 { text += '0' + y; }
	else 		{ text += y; }

	calcAndSaveHash(text, function() {
		j++;

		if(j>31) { j = 1; m++; }
		if(m>31) { m = 1; y++; console.log(y); }

		if(y > 3000) {
			return;
		}

		birthPass();
	});
}

connectDb(function() {
	birthPass();
});