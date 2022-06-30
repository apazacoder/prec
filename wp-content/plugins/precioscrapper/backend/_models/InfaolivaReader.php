<?php

require_once 'Reader.php';

class InfaolivaReader extends Reader {

  public function __construct() {
    parent::__construct("http://www.infaoliva.com/paginas/ObservatorioFechas.asp");
    $this->source = "infaoliva";
    date_default_timezone_set('Europe/Madrid');
  }

  public function transform_date($date_reversed) {
    $date_fields = explode('-', $date_reversed);
    return implode('-', array_reverse($date_fields));
  }

  //  infaoliva  la fecha de la tabla, menos el
  public function get_price_date() {
    $date_text = $this->filter("tr.FondoNegro:nth-child(1) > th:nth-child(1) > div:nth-child(1)");
    $date_text = count($date_text) ? $date_text[0] : "";
    $date_text = utf8_decode($date_text);

    $date_fields = explode("/", $date_text);
    if (count($date_fields) >= 3) {
      return $date_fields[2] . "-" . $date_fields[1] . "-" . substr($date_fields[0], -2) . " 00:00:00";
    }
    return "";
  }

  //  fecha final de la tabla de rangos de infaoliva
  public function get_price_range_date() {
    $date_text = $this->filter("tr.FondoNegro:nth-child(17) > th:nth-child(1) > div:nth-child(1)");
    $date_text = count($date_text) ? $date_text[0] : "";
    $date_text = utf8_decode($date_text);

    $date_fields = explode("/", $date_text);
    if (count($date_fields) >= 3) {
      return $date_fields[2] . "-" . $date_fields[1] . "-" . substr($date_fields[0], -2) . " 00:00:00";
    }
    return "";
  }

  public function format_price($price){
    return preg_replace("/[^0-9]/", "", $price);
  }

  // obtain the average based on an initial index
  public function get_average_price($starting_index){
    $sum = 0;
    $qty = 0;
    for ($i = 0; $i <= 4; $i++){
      $price = $this->filter("tr.Fuente16M:nth-child(".($starting_index + $i*4).") > td:nth-child(3)");
      $price = count($price) ? $this->format_price($price[0]) : "";
      if ($price != "" && $price != "Sin cotización"){
        $qty += 1;
        $sum += $price;
      }
    }

    return $qty != 0 ? ($sum/$qty)/1000 : 0;
  }

  public function get_extra_virgen_price() {
    return "".$this->get_average_price(2);
  }

  public function get_virgen_price() {
    return "".$this->get_average_price(3);
  }

  public function get_lampante_price() {
    return "".$this->get_average_price(4);
  }

  public function process_historic_price($price){
	  $price = count($price) ? $this->format_price($price[0]) : "";
	  if ($price == "" || $price == "Sin cotización"){
		  $price = "0";
	  }else{
			$price = $price / 1000;
	  }
	  return $price;
  }

	public function get_extra_virgen_historic_price() {
		$price = $this->filter("tr.Fuente16M:nth-child(18) > td:nth-child(3)");
		return "".$this->process_historic_price($price);
	}

	public function get_virgen_historic_price() {
		$price = $this->filter("tr.Fuente16M:nth-child(19) > td:nth-child(3)");
		return "".$this->process_historic_price($price);
	}

	public function get_lampante_historic_price() {
		$price = $this->filter("tr.Fuente16M:nth-child(20) > td:nth-child(3)");
		return "".$this->process_historic_price($price);
	}


  public function get_composition() {
    $fecha_precio = $this->get_price_date();
    $fecha_precio_rango = $this->get_price_range_date();
    return [
      "lectura" => [
        "leido_el" => date("Y-m-d H:i:s"),
        "codigo_respuesta" => $this->status
      ],
      "precios" => [
        [
          "aceite" => "extra_virgen",
          "precio" => $this->get_extra_virgen_price(),
          "precio_historico" => $this->get_extra_virgen_historic_price(),
          "precio_arbequina" => "0",
          "fecha_precio" => $fecha_precio,
          "fecha_precio_rango" => $fecha_precio_rango,
          "fuente" => $this->source
        ],
        [
          "aceite" => "virgen",
          "precio" => $this->get_virgen_price(),
          "precio_historico" => $this->get_virgen_historic_price(),
          "precio_arbequina" => "0",
          "fecha_precio" => $fecha_precio,
          "fecha_precio_rango" => $fecha_precio_rango,
          "fuente" => $this->source
        ],
        [
          "aceite" => "lampante",
          "precio" => $this->get_lampante_price(),
          "precio_historico" => $this->get_lampante_historic_price(),
          "precio_arbequina" => "0",
          "fecha_precio" => $fecha_precio,
          "fecha_precio_rango" => $fecha_precio_rango,
          "fuente" => $this->source
        ]
      ]
    ];
  }
  //
  //
  //  //  returns all data for poolread source
  //  public function get_data(){
  //    return [
  //      "aceite" => "poolred",
  //      "precio" => ""
  //      "data" => $this->filter("tr.fSolucionNormal:nth-child(2) > td:nth-child(1)"),
  //      "code" => $this->client->getResponse()->getStatusCode(),
  //      "html" => $this->crawler->html()
  //    ];
  //  }
}
