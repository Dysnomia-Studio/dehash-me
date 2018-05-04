<?php
abstract class Security {
	private $hashCount=1;
	/**
	 * Hash a text with my custom algorithms
	 *
	 * @param      string  $data   Text need to be hash
	 *
	 * @return     string  Hashed text
	 */
	static function hashPassword($hashType=-1) {
		if($hashType < 1 || $hashType > $hashCount) {
			$hashType = $hashCount;
		}

		$hashType = "hashV$hashType";

		return $this->$hashType;
	}

	static function hashV1($data) {
		$token = ***REMOVED***;
		$part1 = ***REMOVED***;
		$part2 = ***REMOVED***;
		$part3 = ***REMOVED***;
		$part4 = ***REMOVED***;

		return $part4;
	}
}
