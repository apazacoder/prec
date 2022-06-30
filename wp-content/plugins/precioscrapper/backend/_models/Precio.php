<?php
require_once 'Model.php';
require_once 'iModel.php';
require_once 'Precio.php';

class Precio extends Model implements iModel {
  protected $wpdb;
  protected $table_precios;
  protected $table_lecturas;

  function __construct($wpdb, $db_prefix) {
    $this->wpdb = $wpdb;
    $this->table_precios = $db_prefix . 'precios';
    $this->table_lecturas = $db_prefix . 'lecturas';
  }

  public function store_rules() {
    return [
      'aceite' => 'required',
      'precio' => 'required',
      'precio_historico' => 'required',
      'precio_arbequina' => 'required',
      'fecha_precio' => 'required',
      'fuente' => 'required'
    ];
  }

  public function update_rules() {
    return [
      'aceite' => 'required',
      'precio' => 'required',
      'precio_historico' => 'required',
      'precio_arbequina' => 'required',
      'fecha_precio' => 'required',
      'fuente' => 'required'
    ];
  }

  public function validation_messages() {
    return [
      'aceite.required' => 'Por favor defina el aceite',
      'precio.required' => 'Por favor defina el precio',
      'precio_historico.required' => 'Por favor defina el precio histórico',
      'precio_arbequina.required' => 'Por favor defina el precio de arbequina',
      'fecha_precio.required' => 'Por favor defina la fecha del precio',
      'fuente.required' => 'Por favor defina la fuente'
    ];
  }

  public function init_precios($data) {
    return [
      "precios" => $this->paginated_precios($data)
    ];
  }

	public function paginated_precios($data)
	{
		$page = $data["page"];
		$term = $data["term"] ? Helper::mysql_escape(trim($data["term"])) : '';
		$length = $data["length"];
		$offset = $length * ($page - 1);

		$mainQuery = "SELECT p.id as id, p.aceite, p.precio, p.precio_historico, p.precio_arbequina, p.fecha_precio, p.fecha_precio_rango, p.fuente, p.id_lectura, l.leido_el, l.codigo_respuesta, l.resultado FROM $this->table_precios p
				JOIN $this->table_lecturas l
				ON p.id_lectura = l.id";
		$filterQuery = "WHERE p.aceite like '%$term%' OR p.precio like '%$term%' OR p.precio_historico like '%$term%' OR p.precio_arbequina like '%$term%' OR p.fecha_precio like '%$term%' OR p.fecha_precio_rango like '%$term%' OR p.fuente like '%$term%' OR l.leido_el like '%$term%' OR l.codigo_respuesta like '%$term%' OR l.resultado like '%$term%'";
		$order = "ORDER BY p.id ASC";

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

	public function precios_con_lecturas() {
    return $this->wpdb->get_results(
      "SELECT p.id as id, p.aceite, p.precio, p.precio_historico, p.precio_arbequina, p.fecha_precio, p.fecha_precio_rango, p.fuente, p.id_lectura, l.leido_el, l.codigo_respuesta FROM $this->table_precios p
				JOIN $this->table_lecturas l
				ON p.id_lectura = l.id
				ORDER BY p.id ASC"
    );
  }

  public function precios() {
    return $this->wpdb->get_results(
      "SELECT * FROM $this->table_precios ORDER BY fecha_precio ASC"
    );
  }

  public function store_precio($data) {
    $item = $data["item"];
    $errors = $this->get_store_errors($item);


    if (!$errors) {
      $created = false;
      try {
        $to_save = [
          'id' => $item["id"],
          'aceite' => $item["aceite"],
          'precio' => $item["precio"],
          'precio_historico' => $item["precio_historico"],
          'precio_arbequina' => $item["precio_arbequina"],
          'fecha_precio' => $item["fecha_precio"],
          'fecha_precio_rango' => $item["fecha_precio_rango"],
          'fuente' => $item["fuente"],
        ];

        $types = [
          '%d',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s'
        ];

        if (array_key_exists("id_lectura", $item)) {
          $to_save['id_lectura'] = $item["id_lectura"];
          $types[] = '%d';
        }

        $created = $this->wpdb->insert(
          $this->table_precios,
          $to_save,
          $types
        );
      } catch (Exception $e) {
        $errors[] = $e->getMessage();
      }

      if ($created === false) {
        $errors[] = $this->wpdb->last_result;
        $errors[] = $this->wpdb->last_query;
        $errors[] = $this->wpdb->print_error();
        $errors[] = "Error de base de datos";
      }
    }

    return ['errors' => $errors];
  }

  public function update_precio($data) {
    $item = $data["item"];
    $errors = $this->get_update_errors($item);

    if (!$errors) {
      $updated = false;
      try {
      	$to_update = [
		      'aceite' => $item["aceite"],
		      'precio' => $item["precio"],
		      'precio_historico' => $item["precio_historico"],
		      'precio_arbequina' => $item["precio_arbequina"],
		      'fecha_precio' => $item["fecha_precio"],
		      'fecha_precio_rango' => $item["fecha_precio_rango"],
		      'fuente' => $item["fuente"]
	      ];

      	$update_types = [
		      '%s',
		      '%s',
		      '%s',
		      '%s',
		      '%s',
		      '%s',
		      '%s'
	      ];

	      if (array_key_exists("id_lectura", $item)) {
		      $to_update['id_lectura'] = $item["id_lectura"];
		      $update_types[] = '%d';
	      }

        $updated = $this->wpdb->update(
          $this->table_precios,
          $to_update,
          [
            'id' => $item["id"]
          ],
          $update_types,
          [
            '%d'
          ]
        );
      } catch (Exception $e) {
        $errors[] = $e->getMessage();
      }

      if ($updated === false) {
        $errors[] = "Error de base de datos";
      }
    }
    return ['errors' => $errors];
  }

  public function delete_precio($data) {
    $item = $data["item"];
    $errors = [];

    $deleted = false;
    if (!$errors) {
      try {
        $deleted = $this->wpdb->delete(
          $this->table_precios,
          [
            'id' => $item['id']
          ],
          [
            '%d'
          ]
        );
      } catch (Exception $e) {
        $errors[] = $e->getMessage();
      }
    }

    if ($deleted === false) {
      $errors[] = "Error de base de datos";
    }
    return ['errors' => $errors];
  }

  /*
   * $data["aceite"=> .., "precio_historico"=> .., "fecha_precio_rango"=> .., "fuente"=> ..]
   */
  public function find_duplicate($data) {
    $aceite = $data["aceite"];
    $precio_historico = $data["precio_historico"];
    $fecha_precio_rango = $data["fecha_precio_rango"];
    $fuente = $data["fuente"];
    $query = "SELECT * FROM $this->table_precios ";
    $query .= "WHERE aceite='$aceite' AND precio_historico=ROUND($precio_historico, 9) AND fecha_precio_rango='$fecha_precio_rango' AND fuente='$fuente'";
    return $this->wpdb->get_results($query);
  }

  // just find the record with same date
  public function find_same_date($data){
	  $aceite = $data["aceite"];
	  $fecha_precio_rango = $data["fecha_precio_rango"];
	  $fuente = $data["fuente"];
	  $query = "SELECT * FROM $this->table_precios ";
	  $query .= "WHERE aceite='$aceite' AND fecha_precio_rango='$fecha_precio_rango' AND fuente='$fuente'";
	  return $this->wpdb->get_results($query);
  }
}

//
//{
//	"poolred": {
//	"aceite": "extra_virgen",
//    "precio": "3.41423",
//    "precio_historico": "3.41423",
//    "precio_arbequina": "0",
//    "fecha_precio": "2021-05-18 00:00:00",
//    "fecha_precio_rango": "2021-05-24 00:00:00",
//    "fuente": "poolred",
//    "id_lectura": 57
//  }
//}
