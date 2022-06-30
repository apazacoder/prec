<?php
//global $wpdb;
//global $db_prefix;
require_once 'PoolredReader.php';
require_once 'InfaolivaReader.php';
require_once 'AlmazarasReader.php';
require_once 'Lectura.php';

class Main {
  public $wpdb;
  public $db_prefix;

  public function __construct() {
    global $wpdb;
    global $db_prefix;

    $this->wpdb = $wpdb;
    $this->db_prefix = $db_prefix;
  }

  public function compose_all() {
    $poolred_reader = new PoolredReader();
    $infaoliva_reader = new InfaolivaReader();
    $almazaras_reader = new AlmazarasReader();

    return [
      "poolred" => $poolred_reader->get_composition(),
      "infaoliva" => $infaoliva_reader->get_composition(),
      "almazaras" => $almazaras_reader->get_composition(),
    ];
  }

  /**
   * @param $single_composition
   * Store single source composition
   * @return array // empty: not stored, many records: stored some
   */
  public function store_single($single_composition) {
    $lectura = new Lectura($this->wpdb, $this->db_prefix);

    $store_lectura = $single_composition["lectura"];
    $result = $lectura->store_lectura([
      "item" => $store_lectura
    ]);
    $id_lectura = $result["id"];
    $precios = $single_composition["precios"];

    $results = [];
    $precio = new Precio($this->wpdb, $this->db_prefix);


    foreach ($precios as $store_precio) {
      // check if its repeated for each source, search for records in store_precio
      $repeated = !!count($precio->find_duplicate($store_precio));
      $resultado_lectura = "";

      if (!$repeated){
	      $store_precio["id_lectura"] = $id_lectura;

	      // check if price already exists in the db
      	$same_date_precio = $precio->find_same_date($store_precio);
      	$same_date_precio = count($same_date_precio) ? $same_date_precio[0] : [];

      	// a register with same date has found
      	if ($same_date_precio){
      		// update the id to update the modified element
      		$store_precio["id"] = $same_date_precio->id;
      		$results[] = $precio->update_precio(["item" => $store_precio]);

      		$resultado_lectura = "actualizado";
	      }else{
		      $results[] = $precio->store_precio(["item" => $store_precio]);
		      $resultado_lectura = "guardado";
	      }
      }else{
      	$resultado_lectura = 'repetido/ignorado';
      }

      // update the lecture according to the result of the operation
	    $store_lectura["id"] = $id_lectura;
      $store_lectura["resultado"] = $resultado_lectura;
      $lectura->update_lectura([
      	"item" => $store_lectura
      ]);
    }
    return $results;
  }


  public function run_tasks() {
    $results = [];
    $compositions = $this->compose_all();

    $results["poolred"] = $this->store_single($compositions["poolred"]);
    $results["infaoliva"] = $this->store_single($compositions["infaoliva"]);
    $results["almazaras"] = $this->store_single($compositions["almazaras"]);

    return $results;
  }
}
