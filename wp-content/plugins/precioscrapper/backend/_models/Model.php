<?php

require_once 'Helper.php';

class Model {

	protected function get_update_errors( $item ) {
		return $this->get_errors( $item, $this->update_rules() );
	}

	protected function get_store_errors( $item ) {
		return $this->get_errors( $item, $this->store_rules() );
	}

	protected function test_value( $value, $rule ) {
		switch ( $rule ) {
			case 'required':
				return $this->required_test( $value );
				break;
		}
	}

	protected function get_error_message( $field, $rule ) {
		foreach ( $this->validation_messages() as $fieldRule => $message ) {
			if ( $field . '.' . $rule === $fieldRule ) {
				return $message;
			}
		}
	}

	function get_errors( $item, $operationRules ) {
		$errors      = [];
		$concatRules = $operationRules;
		foreach ( $concatRules as $field => $concatRule ) {
			$rules = explode( '|', $concatRule );
			foreach ( $rules as $rule ) {
				$testResult = $this->test_value( $item[ $field ], $rule );
				if ( ! $testResult ) {
					$errors[] = $this->get_error_message( $field, $rule );
				}
			}
		}

		return $errors;
	}

	// tests
	protected function required_test( $value ) {
		return trim( $value ) !== "";
	}

	// debug
	public function write_log( $log ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}
