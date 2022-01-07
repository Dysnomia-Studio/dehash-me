const crypto = require('crypto');

process.on('uncaughtException', function (exception) {
	console.log(exception);
});

process.on('unhandledRejection', (reason, p) => {
	console.log('Unhandled Rejection at: Promise ', p, ' reason: ', reason);
});

const { Pool } = require('pg');
let pool;

const hashTypeList = [
	'blake2b512', 'blake2s256',
	'md4', 'md5',
	'mdc2',
	'ripemd', 'ripemd160', 'rmd160',
	'sha1', 'sha224', 'sha256', 'sha384', 'sha512', 'sha512-224', 'sha512-256',
	'sha3-224', 'sha3-256', 'sha3-384', 'sha3-512',
	'shake128', 'shake256',
	'sm3',
	'whirlpool'
];

/* Available: ( require('crypto').getHashes() )
[ 'RSA-MD4',
  'RSA-MD5',
  'RSA-MDC2',
  'RSA-RIPEMD160',
  'RSA-SHA1',
  'RSA-SHA1-2',
  'RSA-SHA224',
  'RSA-SHA256',
  'RSA-SHA3-224',
  'RSA-SHA3-256',
  'RSA-SHA3-384',
  'RSA-SHA3-512',
  'RSA-SHA384',
  'RSA-SHA512',
  'RSA-SHA512/224',
  'RSA-SHA512/256',
  'RSA-SM3',
  'blake2b512',
  'blake2s256',
  'id-rsassa-pkcs1-v1_5-with-sha3-224',
  'id-rsassa-pkcs1-v1_5-with-sha3-256',
  'id-rsassa-pkcs1-v1_5-with-sha3-384',
  'id-rsassa-pkcs1-v1_5-with-sha3-512',
  'md4',
  'md4WithRSAEncryption',
  'md5',
  'md5-sha1',
  'md5WithRSAEncryption',
  'mdc2',
  'mdc2WithRSA',
  'ripemd',
  'ripemd160',
  'ripemd160WithRSA',
  'rmd160',
  'sha1',
  'sha1WithRSAEncryption',
  'sha224',
  'sha224WithRSAEncryption',
  'sha256',
  'sha256WithRSAEncryption',
  'sha3-224',
  'sha3-256',
  'sha3-384',
  'sha3-512',
  'sha384',
  'sha384WithRSAEncryption',
  'sha512',
  'sha512-224',
  'sha512-224WithRSAEncryption',
  'sha512-256',
  'sha512-256WithRSAEncryption',
  'sha512WithRSAEncryption',
  'shake128',
  'shake256',
  'sm3',
  'sm3WithRSAEncryption',
  'ssl3-md5',
  'ssl3-sha1',
  'whirlpool' ]
*/

const MAX_SIZE = 16;
let connected = false;

module.exports = function() {
	/**
	 * Used to connect to database
	 */
	this.connectDb = function(callback) {
		pool = new Pool({
			user: '***REMOVED***',
			host: '***REMOVED***',
			database: '***REMOVED***',
			password: '***REMOVED***',
			port: 5432,
		});

		pool.connect((err, client, done) => {
			if (err) throw err;
			if(connected) { return; }
			connected = true;
			console.log('Successfuly connected to database server.');
			callback();
		});
	};

	this.generateRandomPass = function() {
		var size = Math.round(4 + Math.random()*(MAX_SIZE - 1));
		var text = "";

		for(var i=0; i<size; i++) {
			// ASCII 33 à 126
			text += String.fromCharCode(33 + Math.round(Math.random()*97));
		}

		console.log("Hashing: " + text);

		calcAndSaveHash(text, function() {	
			generateRandomPass();
		});
	};

	this.findText = function(text, callback) {
		pool.query('SELECT * FROM "hashLists" WHERE text = $1', [text])
			.then(res => callback(res.rows))
			.catch(e => console.error(e.stack));
	};

	this.calcAndSaveHash = function(text,callback) {
		findText(text, function (obj) {
			var types = [];

			// If we return only one element, security
			if(obj != undefined && obj.hash != undefined) {
				obj = [obj];
			}

			// Check if in database
			if(obj!=undefined && obj[0]!=undefined) {
				for(var hash in hashTypeList) {
					var find = false;
					for(var id in obj) {
						if(obj[id].type == hashTypeList[hash]) {
							find = true;
							break;
						}
					}

					if(!find) {
						types.push(hashTypeList[hash]);
					}
				}
			} else {
				types = hashTypeList;
			}

			//console.log("Hashing " + text + " in " + types.length + " hashes ...");
			if(types.length > 0 ) {
				saveToDb(text,types,callback);
			} else {
				callback();
			}
		});
	};

	this.saveToDb = function(text,types,callback) {
		let done = 0;
		function doIt() {
			if(++done >= types.length) {
				callback();
			}
		}

		for(let i=0; i<types.length; i++) {
			pool.query('INSERT INTO "hashLists" (text, type, hash) VALUES($1,$2,$3)', 
					[
						text,
						types[i],
						crypto.createHash(types[i]).update(text).digest("hex")
					])
				.then(() => doIt())
				.catch(e => console.error(e.stack));
		}
	};
};