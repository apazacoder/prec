<?php

require_once '_helpers.php';
global $wpdb;
global $db_prefix;
require_once __DIR__ . '/../_models/Lectura.php';

$Lectura = new Lectura($wpdb, $db_prefix);

add_action('rest_api_init', function () {
  global $api_prefix;

	register_rest_route($api_prefix, 'init_lecturas', [
		'methods' => 'POST',
		'callback' => 'init_lecturas'
	]);
  register_rest_route($api_prefix, 'lecturas', [
    'methods' => 'GET',
    'callback' => 'lecturas'
  ]);
  register_rest_route($api_prefix, 'lectura', [
    'methods' => 'POST',
    'callback' => 'store_lectura'
  ]);
  register_rest_route($api_prefix, 'lectura', [
    'methods' => 'PUT',
    'callback' => 'update_lectura'
  ]);
  register_rest_route($api_prefix, 'lectura', [
    'methods' => 'DELETE',
    'callback' => 'delete_lectura'
  ]);
  // FRONTEND ENDPOINTS
  register_rest_route($api_prefix, 'init_frontend', [
    'methods' => 'GET',
    'callback' => 'init_frontend'
  ]);
});

function init_lecturas($request)
{
	global $Lectura;

	return api_response($Lectura->init_lecturas(json_decode($request->get_body(), true)));
}

function lecturas() {
  global $Lectura;

  return api_response($Lectura->lecturas());
}

function store_lectura($request) {
  global $Lectura;

  return api_response($Lectura->store_lectura(json_decode($request->get_body(), true)));
}

function update_lectura($request) {
  global $Lectura;

  return api_response($Lectura->update_lectura(json_decode($request->get_body(), true)));
}

function delete_lectura($request) {
  global $Lectura;

  return api_response($Lectura->delete_lectura(json_decode($request->get_body(), true)));
}

function init_frontend() {
  global $Lectura;

  return api_response($Lectura->init_frontend());
}

