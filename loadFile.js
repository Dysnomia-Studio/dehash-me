require('./passCalc')();

const passes = [];

const countBase = 7000;
let count = countBase;

connectDb(function() {
	const lineReader = require('readline').createInterface({
		input: require('fs').createReadStream('10_million_password_list_top_100000.txt')
	});

	lineReader.on('line', function(line) {
		passes.push(line);
	});

	function genHash() {
		if(passes[count] === undefined) {
			console.log('reported');
			setTimeout(function() {
				genHash();
			},1000);
			return;
		}

		//console.log("Hashing " + passes[countBase]);

		calcAndSaveHash(passes[countBase], function() {
			if(++count % 100 === 0) {
				console.log('************** Hashed " + count + " passes **************');
			}

			passes.splice(0,1);

			//setTimeout(genHash, 100);
			genHash();
		});
	}
	genHash();
});