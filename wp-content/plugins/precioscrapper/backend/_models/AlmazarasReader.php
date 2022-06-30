<?php

require_once 'Reader.php';

class AlmazarasReader extends Reader {

	public function __construct() {
		parent::__construct( "https://almazarasfederadas.es/" );
		$this->source = "almazaras";
		date_default_timezone_set( 'Europe/Madrid' );
	}

	public function transform_date( $date_reversed ) {
		$date_fields = explode( ' ', $date_reversed );

		return implode( '-', array_reverse( $date_fields ) ) . ' 00:00:00';
	}

	//  poolred la fecha de visita mostrada en la página
	public function get_price_date() {
		$date_text   = $this->filter( ".tablasPA" );
		$date_text   = count( $date_text ) ? $date_text[0] : "";
		$date_fields = explode( "/", $date_text );
		if ( count( $date_fields ) >= 3 ) {
			return $this->transform_date( substr( $date_fields[0], - 2 ) . " " . $date_fields[1] . " " . $date_fields[2] );
		}

		return "";
	}

	public function format_price( $price ) {
		return str_replace( ",", ".", $price );
	}

	public function get_extra_virgen_price() {
		$price = $this->filter( "tr:nth-child(2) > td:nth-child(2)" );
		$price = count( $price ) ? substr( utf8_decode( $price[0] ), 1 ) : "";

		return ( $price == "" || $price == "SC" ) ? "0" : $this->format_price( $price );
	}

	public function get_virgen_price() {
		$price = $this->filter( "tr:nth-child(3) > td:nth-child(2)" );

		return count( $price ) ? $this->format_price( $price[0] ) : "0";
	}

	public function get_lampante_price() {
		$price = $this->filter( "tr:nth-child(4) > td:nth-child(2)" );

		return count( $price ) ? $this->format_price( $price[0] ) : "0";
	}

	public function get_extra_virgen_arbequina_price() {
		$price = $this->filter( "tr:nth-child(2) > td:nth-child(3)" );
		$price = count( $price ) ? substr( utf8_decode( $price[0] ), 1 ) : "";

		return ( $price == "" || $price == "SC" ) ? "0" : $this->format_price( $price );
	}

	public function get_virgen_arbequina_price() {
		$price = $this->filter( "tr:nth-child(3) > td:nth-child(3)" );
		$price = count( $price ) ? trim( $price[0] ) : "";

		return ( $price == "" || $price == "SC" ) ? "0" : $this->format_price( $price );
	}

	public function get_lampante_arbequina_price() {
		$price = $this->filter( "tr:nth-child(4) > td:nth-child(3)" );
		$price = count( $price ) ? trim( $price[0] ) : "";

		return ( $price == "" || $price == "SC" ) ? "0" : $this->format_price( $price );
	}

	public function get_composition() {
		$fecha_precio = $this->get_price_date();

		return [
			"lectura" => [
				"leido_el"         => date( "Y-m-d H:i:s" ),
				"codigo_respuesta" => $this->status
			],
			"precios" => [
				[
					"aceite"             => "extra_virgen",
					"precio"             => $this->get_extra_virgen_price(),
					"precio_historico"   => $this->get_extra_virgen_price(),
					"precio_arbequina"   => $this->get_extra_virgen_arbequina_price(),
					"fecha_precio"       => $fecha_precio,
					"fecha_precio_rango" => $fecha_precio,
					"fuente"             => $this->source
				],
				[
					"aceite"             => "virgen",
					"precio"             => $this->get_virgen_price(),
					"precio_historico"   => $this->get_virgen_price(),
					"precio_arbequina"   => $this->get_virgen_arbequina_price(),
					"fecha_precio"       => $fecha_precio,
					"fecha_precio_rango" => $fecha_precio,
					"fuente"             => $this->source
				],
				[
					"aceite"             => "lampante",
					"precio"             => $this->get_lampante_price(),
					"precio_historico"   => $this->get_lampante_price(),
					"precio_arbequina"   => $this->get_lampante_arbequina_price(),
					"fecha_precio"       => $fecha_precio,
					"fecha_precio_rango" => $fecha_precio,
					"fuente"             => $this->source
				]
			]
		];
	}
}
