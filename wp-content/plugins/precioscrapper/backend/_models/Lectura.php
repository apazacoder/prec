<?php
require_once 'Model.php';
require_once 'iModel.php';
require_once 'Lectura.php';

class Lectura extends Model implements iModel {
	protected $wpdb;
	protected $table_lecturas;
	protected $db_prefix;

	function __construct( $wpdb, $db_prefix ) {
		$this->wpdb           = $wpdb;
		$this->db_prefix      = $db_prefix;
		$this->table_lecturas = $db_prefix . 'lecturas';
	}

	public function store_rules() {
		return [
			'leido_el'         => 'required',
			'codigo_respuesta' => 'required'
		];
	}

	public function update_rules() {
		return [
			'leido_el'         => 'required',
			'codigo_respuesta' => 'required'
		];
	}

	public function validation_messages() {
		return [
			'leido_el.required'         => 'Por favor introduzca la fecha de lectura',
			'codigo_respuesta.required' => 'Defina el código de respuesta'
		];
	}


	public function init_lecturas($data)
	{
		return [
			"lecturas" => $this->paginated_lecturas($data)
		];
	}

	public function paginated_lecturas($data)
	{
		$page = $data["page"];
		$term = $data["term"] ? Helper::mysql_escape(trim($data["term"])) : '';
		$length = $data["length"];
		$offset = $length * ($page - 1);

		$mainQuery = "SELECT * from $this->table_lecturas";
		$filterQuery = "WHERE leido_el like '%$term%' OR codigo_respuesta like '%$term%' OR resultado like '%$term%'";
		$order = "ORDER BY id ASC";

		$query = $term ? $mainQuery . ' ' . $filterQuery . ' ' . $order : $mainQuery . ' ' . $order;

		$notFiltered = $this->wpdb->get_results($query);
		$filtered = $this->wpdb->get_results("$query LIMIT $offset, $length");

		$itemsNumber = count($notFiltered);
		$pagesNumber = $itemsNumber ? (integer)(ceil($itemsNumber / $length)) : 1;
		$currentPage = $pagesNumber > 1 ? $page : 1;

		return [
			"itemsNumber" => $itemsNumber,
			"itemsRows" => $filtered,
			"pagesNumber" => $pagesNumber,
			"currentPage" => $currentPage,
			"term" => $term,
		];
	}


	public function lecturas() {
		return $this->wpdb->get_results(
			"SELECT * FROM $this->table_lecturas ORDER BY id ASC"
		);
	}

	public function store_lectura( $data ) {
		$item   = $data["item"];
		$errors = $this->get_store_errors( $item );
		$id     = 0;


		if ( ! $errors ) {
			$created = false;
			try {
				$created = $this->wpdb->insert(
					$this->table_lecturas,
					[
						'leido_el'         => $item["leido_el"],
						'codigo_respuesta' => $item["codigo_respuesta"]
					],
					[
						'%s',
						'%s',
					]
				);
				$id      = $this->wpdb->insert_id;
			} catch ( Exception $e ) {
				$errors[] = $e->getMessage();
			}

			if ( $created === false ) {
				$errors[] = "Error de base de datos";
			}
		}

		return [
			'errors' => $errors,
			'id'     => $id
		];
	}

	public function update_lectura( $data ) {
		$item   = $data["item"];
		$errors = $this->get_update_errors( $item );

		if ( ! $errors ) {
			$updated = false;
			try {
				$updated = $this->wpdb->update(
					$this->table_lecturas,
					[
						'leido_el'         => $item["leido_el"],
						'codigo_respuesta' => $item["codigo_respuesta"],
						'resultado' => $item["resultado"]
					],
					[
						'id' => $item["id"]
					],
					[
						'%s',
						'%s',
						'%s'
					],
					[
						'%d'
					]
				);
			} catch ( Exception $e ) {
				$errors[] = $e->getMessage();
			}

			if ( $updated === false ) {
				$errors[] = "Error de base de datos";
			}
		}

		return [ 'errors' => $errors ];
	}

	public function delete_lectura( $data ) {
		$item   = $data["item"];
		$errors = [];

		$deleted = false;
		if ( ! $errors ) {
			try {
				$deleted = $this->wpdb->delete(
					$this->table_lecturas,
					[
						'id' => $item["id"]
					],
					[
						'%d'
					]
				);
			} catch ( Exception $e ) {
				$errors[] = $e->getMessage();
			}
		}

		if ( $deleted === false ) {
			$errors[] = "Error al borrar el registro de lectura";
		}

		return [ 'errors' => $errors ];
	}

	// FRONTEND METHODS
	public function init_frontend() {
		$Lectura = new Lectura( $this->wpdb, $this->db_prefix );

		return [
			"lecturas" => $this->lecturas(),
		];
	}
}

