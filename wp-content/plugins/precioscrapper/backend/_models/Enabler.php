<?php
require_once 'Model.php';
require_once 'iModel.php';
require_once 'Enabler.php';

class Enabler extends Model implements iModel {
	protected $wpdb;
	protected $table_enablers;
	protected $Service;
	protected $Question;

	function __construct( $wpdb, $db_prefix ) {
		$this->wpdb      = $wpdb;
		$this->db_prefix = $db_prefix;

		$this->table_enablers  = $db_prefix . 'enablers';
		$this->table_services  = $db_prefix . 'services';
		$this->table_questions = $db_prefix . 'questions';
		$this->table_answers   = $db_prefix . 'answers';

		$this->Service  = new Service( $wpdb, $db_prefix );
		$this->Question = new Question( $wpdb, $db_prefix );
	}

	public function store_rules() {
		return [];
	}

	public function update_rules() {
		return [];
	}

	public function validation_messages() {
		return [];
	}

	public function init_enablers() {
		$Category = new Category( $this->wpdb, $this->db_prefix );

		return [
			"categories" => $Category->get_enablers_composition(),
			"enablers"   => $this->get_enablers()
		];
	}

	public function enablers() {
		return [
			"enablers" => $this->get_enablers()
		];
	}

	public function get_enablers() {
		return $this->wpdb->get_results(
			"SELECT * FROM $this->table_enablers ORDER BY id"
		);
	}

	public function update_enabler( $request ) {
		$data = json_decode( $request->get_body(), true );
		$item = $data["item"];

		$errors = [];

		// buscar el enabler exacto
		$found_enabler = $this->find_enabler( $item );
		$found_item    = count( $found_enabler ) == 1 ? $found_enabler[0] : null;

		$inserted = false;
		$deleted  = false;

		if ( ! $found_item ) {
			// si no se encuentra se inserta
			$inserted = $this->insert_enabler( $item );
		} else {
			// si se encuentra se borra
			$deleted = $this->delete_enabler( $found_item );
		}

		if ( $inserted === false && $deleted === false ) {
			$errors[] = "No se pudo actualizar";
		}

		return [ 'errors' => $errors ];
	}

	// métodos específicos
	public function find_enabler( $item ) {
		$id_service     = $item["id_service"];
		$desired_answer = $item["desired_answer"];
		$id_question    = $item["id_question"];

		return $this->wpdb->get_results(
			"SELECT * FROM $this->table_enablers
				WHERE id_service = $id_service
				AND id_question = $id_question
				AND desired_answer = $desired_answer
			"
		);
	}

	public function insert_enabler( $item ) {
		return $this->wpdb->insert(
			$this->table_enablers,
			[
				'id_service'     => $item["id_service"],
				'desired_answer' => $item["desired_answer"],
				'id_question'    => $item["id_question"]
			],
			[
				'%d',
				'%d',
				'%d'
			]
		);
	}

	public function delete_enabler( $item ) {
		return $this->wpdb->delete(
			$this->table_enablers,
			[
				'id' => $item->id
			],
			[
				'%d'
			]
		);
	}

	public function get_enablers_by_service( $id_service ) {
		return $this->wpdb->get_results(
			"SELECT * FROM $this->table_enablers
				WHERE id_service = $id_service
				ORDER BY id"
		);
	}

	/**
	 * @param $answer ["id_answer", "answer"]
	 *
	 * @return null|id_service
	 */
	public function get_services_to_enable( $answer ) {
		$enablers = $this->wpdb->get_results(
			"SELECT id_service FROM $this->table_enablers
				WHERE 
				      id_question = {$answer["id_question"]}
				AND 
				      desired_answer = {$answer["answer"]}									  
			"
		);

		return $enablers;
	}

	public function get_logic_details() {
		$Category   = new Category( $this->wpdb, $this->db_prefix );
		$Question   = new Question( $this->wpdb, $this->db_prefix );
		$Services   = new Service( $this->wpdb, $this->db_prefix );
		$categories = $Category->categories();
		$services   = $Services->services_by_priority();
		$enablers   = $this->wpdb->get_results(
			"SELECT *
			FROM $this->table_services s,
			     $this->table_enablers e,
			     $this->table_questions q			     
			WHERE s.id = e.id_service
			  AND q.id = e.id_question			  
		" );

		$possible_answers = [ 1, 0 ];

		foreach ( $categories as $category ) {
			$category->questions = $Question->questions();
			foreach ( $category->questions as $question ) {
				$question->answers = [];
				foreach ( $possible_answers as $pa ) {
					$this->write_log( $pa );
					$ansObj              = new stdClass();
					$ansObj->answer      = $pa;
					$ansObj->services    = $this->get_services_with_status( [
						"desired_answer" => $pa,
						"enablers"       => $enablers,
						"services"       => $services,
						"id_question"    => $question->id
					] );
					$question->answers[] = clone $ansObj;
				}
			}
		}

		return $categories;
	}

	public function get_services_with_status( $params ) {
		$desired_answer = $params["desired_answer"];
		$enablers       = $params["enablers"];
		$services       = $params["services"];
		$id_question    = $params["id_question"];

		foreach ( $services as $service ) {
			$flag_enabled = 0;
			foreach ( $enablers as $enabler ) {
				// compare id_service, its desired answer
				// if all found then set to is_enabled = true, false otherwise


				if ( $service->id == $enabler->id_service &&
				     $desired_answer == $enabler->desired_answer &&
				     $id_question == $enabler->id_question
				) {

					$service->is_enabled = 1;
					$flag_enabled        = 1;
					break;
				}
			}
			if ( $flag_enabled === 0 ) {
				$service->is_enabled = 0;
			}
		}

		return $services;
	}
}
