require('./passCalc')();

function orderPass(current) {
	let i = 0;
	while(current[i] != undefined) {
		if(current[i]>93) {
			if(current[i+1]==undefined) {
				current[i+1] = -1;
			}
			current[i+1]++;
			current[i] = 0;
		}

		i++;
	}

	let text = "";
	for(let i=0; i<current.length; i++) {
		text += String.fromCharCode(33 + current[i]);
	}

	if(current[0] == 0) {
		console.log("*********** " + current.join('-') + " ***********");
	}

	calcAndSaveHash(text, function() {
		current[0]++;

		orderPass(current);
	});
}

connectDb(function() {
	orderPass([0, 74, 26, 0]);
});