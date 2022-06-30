<?php

require_once '_helpers.php';
global $wpdb;
global $db_prefix;
require_once __DIR__ . '/../_models/Precio.php';
$Precio = new Precio( $wpdb, $db_prefix );

add_action( 'rest_api_init', function () {
	global $api_prefix;
	// /wp-json/cs/v1/precios
	register_rest_route($api_prefix, 'init_precios', [
		'methods' => 'POST',
		'callback' => 'init_precios'
	]);
	register_rest_route( $api_prefix, 'precios', [
		'methods'  => 'GET',
		'callback' => 'precios'
	] );
	register_rest_route( $api_prefix, 'precio', [
		'methods'  => 'POST',
		'callback' => 'store_precio'
	] );

	register_rest_route( $api_prefix, 'precio', [
		'methods'  => 'PUT',
		'callback' => 'update_precio'
	] );

	register_rest_route( $api_prefix, 'precio', [
		'methods'  => 'DELETE',
		'callback' => 'delete_precio'
	] );
} );

function init_precios($request) {
	global $Precio;

	return api_response($Precio->init_precios(json_decode($request->get_body(), true)));
}

function precios() {
	global $Precio;

	return api_response( $Precio->precios() );
}

function store_precio( $request ) {
	global $Precio;

	return api_response( $Precio->store_precio( json_decode($request->get_body(), true) ) );
}

function update_precio( $request ) {
	global $Precio;

	return api_response( $Precio->update_precio( json_decode($request->get_body(), true) ) );
}

function delete_precio( $request ) {
	global $Precio;

	return api_response( $Precio->delete_precio( json_decode($request->get_body(), true) ) );
}
