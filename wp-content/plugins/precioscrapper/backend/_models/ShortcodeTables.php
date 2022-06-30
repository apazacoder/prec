<?php
require_once 'Precio.php';

class ShortcodeTables {
  public $wpdb;
  public $db_prefix;

  public function __construct() {
    global $wpdb;
    global $db_prefix;

    $this->wpdb = $wpdb;
    $this->db_prefix = $db_prefix;
  }

  public function consulta_fuente($fuente) {
    return "(SELECT * FROM wp_cs_precios WHERE fuente='$fuente' AND aceite='extra_virgen' ORDER BY id desc LIMIT 1) UNION
(SELECT * FROM wp_cs_precios WHERE fuente='$fuente' AND aceite='virgen' ORDER BY id desc LIMIT 1) UNION
(SELECT * FROM wp_cs_precios WHERE fuente='$fuente' AND aceite='lampante' ORDER BY id desc LIMIT 1)";
  }

  // notar que los diferenciales de cero no serán correctos
  public function consulta_diferencial($fuente) {
    return "(SELECT * FROM wp_cs_precios WHERE fuente='$fuente' AND aceite='extra_virgen' AND precio_historico!=0.000000000 ORDER BY id desc LIMIT 1,1) UNION
(SELECT * FROM wp_cs_precios WHERE fuente='$fuente' AND aceite='virgen' AND precio_historico!=0.000000000 ORDER BY id desc LIMIT 1,1) UNION
(SELECT * FROM wp_cs_precios WHERE fuente='$fuente' AND aceite='lampante' AND precio_historico!=0.000000000 ORDER BY id desc LIMIT 1,1)";
  }

  public function compose_result($source) {
    $result = $this->wpdb->get_results($this->consulta_fuente($source));
    $diferencial = $this->wpdb->get_results($this->consulta_diferencial($source));

    foreach ($result as $price) {
      $price->precio = number_format($price->precio, 3, '.', ',');
      $price->precio_historico = number_format($price->precio_historico, 3, '.', ',');
      $price->precio_arbequina = number_format($price->precio_arbequina, 3, '.', ',');
    }

    foreach ($diferencial as $price) {
      $price->precio = number_format($price->precio, 3, '.', ',');
      $price->precio_historico = number_format($price->precio_historico, 3, '.', ',');
      $price->precio_arbequina = number_format($price->precio_arbequina, 3, '.', ',');
    }

    // diferenciales
    if ($diferencial) {
      // two ways of compute diferentials
      if ($source == 'infaoliva') {
        // only for infaoliva we use the historic prices for diferentials
        $result[0]->diferencial = number_format($result[0]->precio_historico - $diferencial[0]->precio_historico, 3, '.', ',');
        $result[1]->diferencial = number_format($result[1]->precio_historico - $diferencial[1]->precio_historico, 3, '.', ',');
        $result[2]->diferencial = number_format($result[2]->precio_historico - $diferencial[2]->precio_historico, 3, '.', ',');
      } else {
        // for the others we use the average prices for diferentiasl
        $result[0]->diferencial = number_format($result[0]->precio - $diferencial[0]->precio, 3, '.', ',');
        $result[1]->diferencial = number_format($result[1]->precio - $diferencial[1]->precio, 3, '.', ',');
        $result[2]->diferencial = number_format($result[2]->precio - $diferencial[2]->precio, 3, '.', ',');
      }
    } else {
      // first times, there will be no diferenciales at all
      $result[0]->diferencial = number_format(0, 3, '.', ',');
      $result[1]->diferencial = number_format(0, 3, '.', ',');
      $result[2]->diferencial = number_format(0, 3, '.', ',');
    }

    $date_from = count($result) ? $result[0]->fecha_precio : '';
    $date_from = date_format(date_create($date_from), "d/m/y");

    $date = count($result) ? $result[0]->fecha_precio_rango : '';
    $date = date_format(date_create($date), "d/m/y");

    return [
      "precios" => $result,
      "fecha_desde" => $date_from,
      "fecha_hasta" => $date,
      "origen" => ucfirst($source)
    ];
  }

  public function tabla_infaoliva() {
    return $this->compose_result("infaoliva");
  }

  public function tabla_poolred() {
    return $this->compose_result("poolred");
  }

  public function tabla_almazaras() {
    return $this->compose_result("almazaras");
  }


  public function consulta_historico_precios($fuente, $aceite) {
    return "SELECT * FROM wp_cs_precios WHERE fuente='$fuente' AND aceite='$aceite' ORDER BY id desc LIMIT 30";
  }

  public function compose_historico_precios($fuente) {
    $extra_virgen_precios = $this->wpdb->get_results($this->consulta_historico_precios($fuente, 'extra_virgen'));
    $virgen_precios = $this->wpdb->get_results($this->consulta_historico_precios($fuente, 'virgen'));
    $lampante_precios = $this->wpdb->get_results($this->consulta_historico_precios($fuente, 'lampante'));

    // format dates and prices
    foreach($extra_virgen_precios as $index => $precio){
      $extra_virgen_precios[$index]->fecha_precio_rango =  date_format(date_create($extra_virgen_precios[$index]->fecha_precio_rango), "d/m/y");
      $virgen_precios[$index]->fecha_precio_rango = date_format(date_create($virgen_precios[$index]->fecha_precio_rango), "d/m/y");
      $lampante_precios[$index]->fecha_precio_rango = date_format(date_create($lampante_precios[$index]->fecha_precio_rango), "d/m/y");

      $extra_virgen_precios[$index]->fecha_precio =  date_format(date_create($extra_virgen_precios[$index]->fecha_precio), "d/m/y");
      $virgen_precios[$index]->fecha_precio = date_format(date_create($virgen_precios[$index]->fecha_precio), "d/m/y");
      $lampante_precios[$index]->fecha_precio = date_format(date_create($lampante_precios[$index]->fecha_precio), "d/m/y");


      $extra_virgen_precios[$index]->precio = number_format($extra_virgen_precios[$index]->precio, 3, '.', ',');
      $virgen_precios[$index]->precio = number_format($virgen_precios[$index]->precio, 3, '.', ',');
      $lampante_precios[$index]->precio = number_format($lampante_precios[$index]->precio, 3, '.', ',');

      $extra_virgen_precios[$index]->precio_arbequina = number_format($extra_virgen_precios[$index]->precio_arbequina, 3, '.', ',');
      $virgen_precios[$index]->precio_arbequina = number_format($virgen_precios[$index]->precio_arbequina, 3, '.', ',');
      $lampante_precios[$index]->precio_arbequina = number_format($lampante_precios[$index]->precio_arbequina, 3, '.', ',');

      $extra_virgen_precios[$index]->precio_historico = number_format($extra_virgen_precios[$index]->precio_historico, 3, '.', ',');
      $virgen_precios[$index]->precio_historico = number_format($virgen_precios[$index]->precio_historico, 3, '.', ',');
      $lampante_precios[$index]->precio_historico = number_format($lampante_precios[$index]->precio_historico, 3, '.', ',');
    }

    return [
      "extra_virgen" => $extra_virgen_precios,
      "virgen" => $virgen_precios,
      "lampante" => $lampante_precios
    ];
  }

  public function tabla_historico_precios_infaoliva() {
    // para 30 días
    return $this->compose_historico_precios('infaoliva');
  }

  public function tabla_historico_precios_poolred() {
    return $this->compose_historico_precios('poolred');
  }

  public function tabla_historico_precios_almazaras() {
    return $this->compose_historico_precios('almazaras');
  }
}
