require('./passCalc')();

let j = 1,
	m = 1,
	y = 1915,
	total = false;

function birthPass() {
	let text = "";

	if(j < 10)	{ text += "0" + j; }
	else 		{ text += j; }

	if(m < 10)	{ text += "0" + m; }
	else 		{ text += m; }

	if(total)	{ text += y; } 
	else		{ 
		if(y%100 < 10)	 { text += "0" + y%100; }
		else		 { text += y%100; }
	}

	calcAndSaveHash(text, function() {
		if(total) {
			j++;
		}
		total = !total;

		if(j>31) { j = 1; m++; }
		if(m>31) { m = 1; y++; console.log(y); }

		birthPass();
	});
}

connectDb(function() {
	birthPass();
});