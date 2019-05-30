<?php
/************************************************
			       PHP SQL LIB
					BY ELANIS
************************************************/

class SQLInterface {
	private $bd;

	/**
	 * constructor
	 *
	 * @param      string  $host      The host
	 * @param      string  $db        The database
	 * @param      string  $id        The username
	 * @param      string  $password  The password
	 */
	public function __construct($host='',$db='',$id='',$password='',$driver='pgsql',$port=5432) {

		if($host=='') { $host='localhost'; }

		if($id==''||$db==''||$password=='') { //Wrong Use !
			global $config;

			if(isset($config['SQLCredentials'])) {
				$host 		= $config['SQLCredentials']['host'];
				$db 		= $config['SQLCredentials']['db'];
				$id 		= $config['SQLCredentials']['id'];
				$password 	= $config['SQLCredentials']['password'];
				$driver 	= $config['SQLCredentials']['driver'];
				$port 		= $config['SQLCredentials']['port'];
			} else {
				die('DATABASE ERROR: All needed informations are not given !');
			}
		}

		try {
			$this->bd = new PDO($driver.':host='.$host.';port='.$port.';dbname='.$db,$id,$password);
		}
		catch (Exception $e) {
			die('Erreur : ' . $e->getMessage());
		}
    }

	/**
	 * Gets the content of a table
	 *
	 * @param      table           $table  The table name
	 * @param      integer		   $min    The minimum index
	 * @param      integer         $size   The size
	 * @param      string          $order  The order type
	 *
	 * @return     array           The content.
	 */
	public function getContent($table,$min=0,$size=1000000,$order='') {
		if(is_string($table) && is_int($min) && is_int($size) && is_string($order)) {

			if($order != '') {
				$order = ' ORDER BY '.$order;
			}

			$query = $this->bd->prepare('SELECT * FROM "'.$table.'"'.$order.' OFFSET '.$min.' LIMIT '.$size);
			$query->execute();

			$data = $query->fetchAll(PDO::FETCH_ASSOC);
			$query->CloseCursor();

			return $data;
		}
		else {
			return [];
		}
	}

	private function bindValues($query, $bindValue, $suffixe='') {
		foreach($bindValue as $name => $value) {
			if(is_int($value)) {
				$pdoType = PDO::PARAM_INT;
			} else {
				$pdoType = PDO::PARAM_STR;
			}

			$query->bindValue((':'.$name.$suffixe), $value, $pdoType);
		}
	}

	/**
	 * Gets content of a table depends on specified conditions.
	 *
	 * @param      array           $table  The table name
	 * @param      array           $where  The condition
	 * @param      integer		   $min    The minimum index
	 * @param      integer         $size   The size
	 * @param      string          $order  The order type
	 *
	 * @return     array           The condition content.
	 */
	public function getCondContent($table,$where,$min=0,$size=1000000,$order='') {
		if(is_string($table) && is_array($where) && is_int($min)  && is_int($size) && is_string($order)) {
			$where_cond = '';

			foreach($where as $name => $value) {
				if($where_cond != '') { $where_cond .= ' AND '; } //@ADD : OR/NOT/AND
				$where_cond .= '"'.$name.'" = :'.$name;
			}

			if($order != '') {
				$order = ' ORDER BY '.$order;
			}

			$query = $this->bd->prepare('SELECT * FROM "'.$table.'" WHERE '.$where_cond.$order.' OFFSET '.$min.' LIMIT '.$size);

			$this->bindValues($query, $where);

			$query->execute();

			$data = $query->fetchAll();
			$query->CloseCursor();

			return $data;
		}
		else {
			return [];
		}
	}

	/**
	 * Gets content of a table depends on specified conditions.
	 *
	 * @param      array           $table  The table name
	 * @param      array           $where  The condition
	 * @param      integer		   $min    The minimum index
	 * @param      integer         $size   The size
	 * @param      string          $order  The order type
	 *
	 * @return     array           The condition content.
	 */
	public function getILIKECondContent($table,$where,$min=0,$size=1000000,$order='') {
		if(is_string($table) && is_array($where) && is_int($min)  && is_int($size) && is_string($order)) {
			$where_cond = '';

			foreach($where as $name => $value) {
				if($where_cond != '') { $where_cond .= ' AND '; } //@ADD : OR/NOT/AND
				$where_cond .= $name.' ILIKE :'.$name;

				$where[$name] = "%".$value."%";
			}

			if($order != '') {
				$order = ' ORDER BY '.$order;
			}

			$query = $this->bd->prepare('SELECT * FROM "'.$table.'" WHERE '.$where_cond.$order.' OFFSET '.$min.' LIMIT '.$size);

			$this->bindValues($query, $where);

			$query->execute();

			$data = $query->fetchAll();
			$query->CloseCursor();

			return $data;
		}
		else {
			return [];
		}
	}

	/**
	 * Adds a content into database
	 *
	 * @param      string  $table  The table
	 * @param      array   $data   The data
	 */
	public function addContent($table,$data) {
		if(!is_string($table) ||
			!is_array($data) ||
			count($data) < 1) {

			return;
		}


		$content = ' (';

		foreach($data[0] as $name => $value) {
			if($content != ' (') { $content .= ', '; }
			$content .= '"'.$name.'"';
		}

		$content .= ' ) VALUES ';

		$firstRow = true;
		for($i=0; $i<count($data); $i++) {
			if(!$firstRow) { 
				$content .= ', ';
			}
			$firstRow = false;

			$content .= '( ';

			$firstCol = true;
			foreach($data[$i] as $name => $value) {
				if(!$firstCol) {
					$content .= ', '; 
				}
				$firstCol = false;

				$content .= ':'.$name.'_'.$i;
			}
			$content .= ' )';
		}

		$query = $this->bd->prepare('INSERT INTO "'.$table.'"'.$content);

		for($i=0; $i<count($data); $i++) {
			$this->bindValues($query, $data[$i], '_'.$i);
		}

		$query->execute();
		$query->CloseCursor();
	}

	/**
	 * Update database content
	 *
	 * @param      string  $table  The table
	 * @param      array   $data   The data
	 * @param      array   $where  The where
	 */
	public function updateContent($table,$data,$where) {
		if(is_string($table)&&is_array($data)&&is_array($where)) {

			$where_cond = '';
			foreach($where as $name => $value) {
				if($where_cond != '') { $where_cond .= ' AND '; } //@ADD : OR/NOT/AND
				$where_cond .= $name.' = :'.$name;
			}

			$setString = '';
			foreach($data as $name => $value) {
				if($setString != '') { $setString .= ' , '; }
				$setString .= $name.' = :'.$name;
			}

			var_dump('UPDATE "'.$table.'" SET '.$setString.' WHERE '.$where_cond);

			$query = $this->bd->prepare('UPDATE "'.$table.'" SET '.$setString.' WHERE '.$where_cond);

			$this->bindValues($query, $data);
			$this->bindValues($query, $where);

			$query->execute();
			$query->CloseCursor();
		}
	}

	/**
	 * Removes database content
	 *
	 * @param      string  $table  The table
	 * @param      array   $where  The where
	 */
	public function removeContent($table,$where) {
		if(is_string($table)&&is_array($where)) {

			$where_cond = '';
			foreach($where as $name => $value) {
				if($where_cond != '') { $where_cond .= ' AND '; } //@ADD : OR/NOT/AND
				$where_cond .= $name.' = :'.$name;
			}

			$query = $this->bd->prepare('DELETE FROM '.$table.' WHERE '.$where_cond);

			$this->bindValues($query, $where);

			$query->execute();
			$query->CloseCursor();
		}
	}

	/**
	 * Draws a table by content.
	 *
	 * @param      array    $header  The header
	 * @param      array    $rows    The rows
	 * @param      string   $table   The table
	 * @param      integer  $min     The minimum
	 * @param      integer  $size    The size
	 * @param      string   $order   The order
	 */
	public function drawTableByContent($header,$rows,$table,$min=0,$size=1000000,$order='') {
		echo '<table><tr>';
		for($i=0; $i<count($header); $i++) {
			echo '<th>'.$header[$i].'</th>';
		}
		echo '</tr>';

		foreach ($this->getContent($table,$min,$size,$order) as $data) {
			echo '<tr>';
			for($i=0; $i<count($rows); $i++) {
					echo '<td class="'.$rows[$i].'">';
					echo $data[$rows[$i]];
					echo '</td>';
			}
			echo '</tr>';
		}
	}

	/**
	 * Compte le nombre de lignes d'une table
	 *
	 * @param      array    $table  The table
	 *
	 * @return     integer          compte de lignes
	 */
	public function count($table) {
		$queryResult = $this->bd->query('SELECT COUNT(*) FROM "'.$table.'"');

		if($queryResult !== false) {
			return $queryResult->fetchColumn();
		} else {
			return -1;
		}
	}

	/**
	 * Query to select with complexe SQL setences
	 *
	 * @param      <type>         $query      The query the sql server will read
	 * @param      <type>         $bindValue  The binded value
	 * @param      boolean        $oneResult  Does we echo only one result ?
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public function selectQuery($query,$bindValue,$oneResult=false) {
		if(is_string($query) && is_array($bindValue) && is_bool($oneResult)) {
			$query = $this->bd->prepare($query);

			$this->bindValues($query, $bindValue);
			
			$query->execute();

			if($oneResult) {
				$data = $query->fetch();
				$query->CloseCursor();

				return $data;
			} else {
				$data = $query->fetchAll();
				$query->CloseCursor();

				return $data;
			}
		} else {
			return false;
		}
	}

	/**
	 * Update database content via a custom query
	 *
	 * @param      <type>  $query      The custom query
	 * @param      <type>  $bindValue  The binded value
	 */
	public function updateQuery($query,$bindValue) {
		if(is_string($query) && is_array($bindValue)) {

			$query = $this->bd->prepare($query);

			$this->bindValues($query, $bindValue);

			$query->execute();
			$query->CloseCursor();
		}
	}
}