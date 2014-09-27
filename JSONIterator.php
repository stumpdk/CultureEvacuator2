<?php
	class JSONIterator{
		/**
		* Returns a JSON iterator based on an array of JSON data
		*/
		public static function getIterator($jsonArray){
			$jsonIterator = new RecursiveIteratorIterator(
		    new RecursiveArrayIterator(json_decode($jsonArray, TRUE)),
		    RecursiveIteratorIterator::SELF_FIRST);
            return $jsonIterator;
		}
	}
?>