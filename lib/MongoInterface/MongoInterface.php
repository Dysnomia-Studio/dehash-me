<?php 
/************************************************
			      PHP MONGO LIB
					BY ELANIS
************************************************/

class MongoInterface {
	protected $manager;

	/**
	 * Connect to a database
	 *
	 * @param      string  $dbURL  The database url
	 */
	protected function connect($dbURL) {
		global $config; 

		if(!isset($dbURL) || $dbURL=="") {
			if(!isset($config['mongoURL']) || $config['mongoURL']=="") { 
				die('DATABASE ERROR: All needed informations are not given !');
			}

			$dbURL = $config['mongoURL'];
		}
		
		if(!extension_loaded("mongodb")) {
			die('ERROR: MONGO EXTENSION NOT LOADED !');
		} else {
			try {
				$this->manager = new MongoDB\Driver\Manager($dbURL);
			}
			catch (Exception $e) {
			        die('Erreur : ' . $e->getMessage());
			}
		}
	}

	/**
	 * constructor
	 *
	 * @param      string  $dbURL  The database url
	 */
	public function __construct($dbURL="") {
		$this->connect($dbURL); //Il faut se connecter à la BDD
    }

	/**
	 * Gets the content depends on a condition
	 *
	 * @param      string  $db      The database name
	 * @param      string  $doc     The document name
	 * @param      string  $filter  The filter
	 *
	 * @return     array   Query return
	 */
	public function getCondContent($db,$doc,$filter) {
		if(is_string($db) && is_string($doc) && is_array($filter)) {
			$query = new MongoDB\Driver\Query($filter);
			$cursor = $this->manager->executeQuery($db.'.'.$doc, $query);

			return $cursor->toArray();
		} else {
			return [];
		}
	}

	/**
	 * Update content of a database
	 *
	 * @param      string  $db       The database
	 * @param      <type>  $doc      The document
	 * @param      <type>  $filter   The filter
	 * @param      <type>  $content  The content
	 */
	public function updateContent($db,$doc,$filter,$content) {
		if(is_string($db) && is_string($doc) && is_array($filter) && is_array($content)) {
			$bulk = new MongoDB\Driver\BulkWrite();
			$bulk->update($filter,$content);

			$this->manager->executeBulkWrite($db.'.'.$doc, $bulk);
		}
	}

	/**
	 * Adds content to database
	 *
	 * @param      string  $db       The database name
	 * @param      string  $doc      The document name
	 * @param      array   $content  The content needs to be inserted
	 */
	public function addContent($db,$doc,$content) {
		if(is_string($db) && is_string($doc) && is_array($content)) {
			$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
			$bulk->insert($content);

			$this->manager->executeBulkWrite($db.'.'.$doc, $bulk);
		}
	}

	/**
	 * Removes content from a database
	 *
	 * @param      string  $db      The database
	 * @param      <type>  $doc     The document
	 * @param      <type>  $filter  The filter
	 */
	public function removeContent($db,$doc,$filter) {
		if(is_string($db) && is_string($doc) && is_array($filter)) {
			$bulk = new MongoDB\Driver\BulkWrite();
			$bulk->delete($filter);

			$this->manager->executeBulkWrite($db.'.'.$doc, $bulk);
		} 
	}

	/**
	 * Count a collection
	 *
	 * @param      <type>  $collection  The collection
	 * @param      <type>  $query       The query selector
	 *
	 * @return     <type>  count
	 */
	protected function count($collection, $query) {
		if(is_string($collection) && is_array($query)) {
			$cmd = new MongoDB\Driver\Command(["count" => $collection, "query" => $query ]);
			$result = $this->manager->executeCommand('***REMOVED***', $cmd);

			return json_decode(json_encode($result->toArray()[0]), true)['n'];	
		} 
	}
}