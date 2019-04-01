<?php
class HashManager extends SQLInterface {
	public function countHash() {
		$queryResult = $this->selectQuery('SELECT reltuples::BIGINT AS approximate_row_count FROM pg_class WHERE relname = \'hashLists\'', []);

		if($queryResult !== false && isset($queryResult[0]) && isset($queryResult[0][0])) {
			return $queryResult[0][0];
		} else {
			return -1;
		}
	}

	private function cmp($a, $b) {
		$aArray = json_decode(json_encode($a), true);
		$bArray = json_decode(json_encode($b), true);

		if($aArray['type'] == $bArray['type']) {
			if($aArray['text'] == $bArray['text']) {
				return strcasecmp($aArray['hash'], $bArray['hash']);
			}

			return strcasecmp($aArray['text'], $bArray['text']);
		}

		return strcasecmp($aArray['type'], $bArray['type']);
	}

	private function sort($hashes) {
		usort($hashes, array('HashManager','cmp'));
		return $hashes;
	}

	public function getByHash($hash) {
		return $this->sort($this->getCondContent(
			'hashLists',
			["hash" => strtolower($hash)]));
	}

	public function getByWord($word) {
		// Hashes List
		$hashList = ["md2","md4","md5","sha1","sha256","sha384","sha512","ripemd128","ripemd160","ripemd256","ripemd320","whirlpool"];

		$drawHashes = [];

		// Query and fetch already hashed data
		$alreadyHashedRaw = $this->getCondContent(
			'hashLists',
			["text" => $word]);

		foreach ($alreadyHashedRaw as $hash) {
			$hash = (array) $hash;
			if(isset($hash['type']) && !empty($hash['type']) 
				&& isset($hash['hash']) && !empty($hash['hash'])) {
				$alreadyHashed[$hash['type']]=true;
				
				array_push($drawHashes, $hash);
			}
		}

		// Generate not already hashed data
		$insertData = [];

		foreach($hashList as $hashType) {
			if(!isset($alreadyHashed[$hashType])) {
				$hashedText = hash($hashType,$word);

				$hash = [
					"text" => $word,
					"type" => $hashType,
					"hash" => $hashedText,
				];

				array_push($insertData, $hash);

				// Text
				array_push($drawHashes, [
					"hash" => $hashedText,
					"type" => $hashType
				]);
			}
		}

		// Save it to database
		if(count($insertData) > 0) {
			$this->addContent('hashLists', $insertData);
		}

		// Return hashes
		return $this->sort($drawHashes);
	}
}