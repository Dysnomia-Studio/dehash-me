require('./passCalc')();

connectDb(function() {
	generateRandomPass();
});