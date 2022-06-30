<?php

require_once 'Reader.php';

class PoolredReader extends Reader {

  public function __construct() {
    parent::__construct("http://www.poolred.com/Publico/PreciosActualizados.aspx");
    $this->source = "poolred";
    date_default_timezone_set('Europe/Madrid');
  }

  public function transform_date($date_reversed) {
    $date_fields = explode('-', $date_reversed);
    return implode('-', array_reverse($date_fields));
  }

  //  poolred la fecha de visita mostrada en la página
  public function get_price_date() {
    $date_text = $this->filter("#lbDesdeD");
    $date_text = count($date_text) ? $date_text[0] : "";

    return ($this->transform_date($date_text)) ? $this->transform_date($date_text) . " 00:00:00" : "";
  }

  public function get_price_range_date() {
    $date_text = $this->filter("#lbHastaD");
    $date_text = count($date_text) ? $date_text[0] : "";

    return ($this->transform_date($date_text)) ? $this->transform_date($date_text) . " 00:00:00" : "";
  }

  public function format_price($price) {
//    $price = str_replace(".", "", $price);
    $price = str_replace(",", "", $price);
    return $price;
  }


  public function get_extra_virgen_price() {
    $price = $this->filter("#lbPrecioE_0D");

    return count($price) ? $this->format_price($price[0]) : "0";
  }

  public function get_virgen_price() {
    $price = $this->filter("#lbPrecioB_0D");

    return count($price) ? $this->format_price($price[0]) : "0";
  }

  public function get_lampante_price() {
    $price = $this->filter("#lbPrecioR_0D");
    return count($price) ? $this->format_price($price[0]) : "0";
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
          "precio_historico" => $this->get_extra_virgen_price(),
          "precio_arbequina" => "0",
          "fecha_precio" => $fecha_precio,
          "fecha_precio_rango" => $fecha_precio_rango,
          "fuente" => $this->source
        ],
        [
          "aceite" => "virgen",
          "precio" => $this->get_virgen_price(),
          "precio_historico" => $this->get_virgen_price(),
          "precio_arbequina" => "0",
          "fecha_precio" => $fecha_precio,
          "fecha_precio_rango" => $fecha_precio_rango,
          "fuente" => $this->source
        ],
        [
          "aceite" => "lampante",
          "precio" => $this->get_lampante_price(),
          "precio_historico" => $this->get_lampante_price(),
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
