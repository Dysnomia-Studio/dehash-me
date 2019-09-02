// MongoDB
const mongoose = require('mongoose');
const Schema = mongoose.Schema;

const hashListSchema = new Schema({
	text: String,
	hash: String,
	type: String,
});
const hashList = mongoose.model('hashList', hashListSchema);

// Postgres
const { Pool } = require('pg');
let pool;

let current = 0;

/**
 * Used to connect to database
 */
function connectMongoDb() {
	mongoose.connect("***REMOVED***");
	MongoDB = mongoose.connection;
	MongoDB.on('error', function(err) { console.err(err.message); });
	MongoDB.once('open', function() {
		console.log('Successfuly connected to MongoDB database server.');
		
		iterate();
	});
}

function connectSQLDb() {
	pool = new Pool({
		user: '***REMOVED***',
		host: '***REMOVED***',
		database: '***REMOVED***',
		password: '***REMOVED***',
		port: 5432,
	});

	pool.connect((err, client, done) => {
		if (err) throw err;

		console.log('Successfuly connected to PostgreSQL database server.');
		connectMongoDb();
	});
}

function removeAfterIterate(doc) {
	//console.log('Removing old document');
	doc.remove(function(err) {
		if(err) {
			console.error('INTERUPTING REMOVE: ' + err.message);
		} else {
			iterate();
		}
	});
}

/**
 * Iterate on items: find, insert in pgsql then remove
 */
function iterate() {
	hashList.find({}, function(err, data) {
		if(err) {
			console.error(err);
			return;
		}

		//console.log(data);
		if(current%100 === 0) {
			console.log('Migration status: ' + current + ' items');
		}
		current++;

		pool.query('INSERT INTO "hashLists" (text, type, hash) VALUES($1,$2,$3)', 
			[
				data[0].text,
				data[0].type,
				data[0].hash
			])
		.then(() => {
			console.log('Not existing in old db');
			removeAfterIterate(data[0]);
		})
		.catch((e) => {
			if(e.message === 'duplicate key value violates unique constraint "pk_hashlist"') {
				//console.log('Duplicated key detected');
				removeAfterIterate(data[0]);
			} else {
				console.error('INTERUPTING INSERT: ' + e.message);
			}
		});
	}).limit(1);
}

connectSQLDb();