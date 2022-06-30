<?php

class Helper {
	public static function mysql_escape( $inp ) {
		if ( is_array( $inp ) ) {
			return array_map( __METHOD__, $inp );
		}

		if ( ! empty( $inp ) && is_string( $inp ) ) {
			return str_replace( array( '\\', "\0", "\n", "\r", "'", '"', "\x1a" ), array(
				'\\\\',
				'\\0',
				'\\n',
				'\\r',
				"\\'",
				'\\"',
				'\\Z'
			), $inp );
		}

		return $inp;
	}

	public static function sqlNow(){
		return date("Y-m-d H:i:s", time());
	}
}
