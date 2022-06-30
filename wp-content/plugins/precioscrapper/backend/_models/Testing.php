<?php
require_once 'PoolredReader.php';
require_once 'InfaolivaReader.php';
require_once 'AlmazarasReader.php';
require_once 'Model.php';
require_once 'iModel.php';
require_once 'Main.php';

class Testing extends Model implements iModel {
  protected $wpdb;
  protected $db_prefix;

  function __construct($wpdb, $db_prefix) {
    $this->wpdb = $wpdb;
    $this->db_prefix = $db_prefix;
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

  //methods to test specific functions
  // FRONTEND METHODS
  public function plans($request) {
    $Plan = new Plan($this->wpdb, $this->db_prefix);
    $errors = [];

    return [
      "errors" => $errors,
      "plans" => $Plan->plans(),
    ];
  }

  public function plan_details($request) {
    $data = json_decode($request->get_body(), true);
    $id_plan = $data["id_plan"];
    $errors = [];

    $Plan = new Plan($this->wpdb, $this->db_prefix);

    return [
      "errors" => $errors,
      "plan_details" => $Plan->get_plan_details($id_plan),
      "id_plan" => $id_plan
    ];
  }

  public function logic_details($request) {
    $Enabler = new Enabler($this->wpdb, $this->db_prefix);
    $errors = [];

    return [
      "errors" => $errors,
      "logic_details" => $Enabler->get_logic_details()
    ];
  }

  public function sandbox() {
//  	return (new PoolredReader())->get_composition();
//	  return (new ShortcodeTables())->consulta_fuente('poolred');
//		return (new ShortcodeTables())->consulta_diferencial('poolred');
//		return (new ShortcodeTables())->compose_result('infaoliva');

    return (new Main())->run_tasks();
  }
}

