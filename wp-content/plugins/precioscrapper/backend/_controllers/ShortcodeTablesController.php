<?php

require_once '_helpers.php';
global $wpdb;
global $db_prefix;
require_once __DIR__ . '/../_models/ShortcodeTables.php';
$ST = new ShortcodeTables($wpdb, $db_prefix);

add_action('rest_api_init', function () {
  global $api_prefix;
  // /wp-json/cs/v1/tabla/infaoliva
  register_rest_route($api_prefix, 'tabla/infaoliva', [
    'methods' => 'GET',
    'callback' => 'tabla_infaoliva'
  ]);
  register_rest_route($api_prefix, 'tabla/poolred', [
    'methods' => 'GET',
    'callback' => 'tabla_poolred'
  ]);
  register_rest_route($api_prefix, 'tabla/almazaras', [
    'methods' => 'GET',
    'callback' => 'tabla_almazaras'
  ]);

  // tablas historicos de precios
  register_rest_route($api_prefix, 'tabla/historico/precios/infaoliva', [
    'methods' => 'GET',
    'callback' => 'tabla_historico_precios_infaoliva'
  ]);
  register_rest_route($api_prefix, 'tabla/historico/precios/poolred', [
    'methods' => 'GET',
    'callback' => 'tabla_historico_precios_poolred'
  ]);
  register_rest_route($api_prefix, 'tabla/historico/precios/almazaras', [
    'methods' => 'GET',
    'callback' => 'tabla_historico_precios_almazaras'
  ]);

});

function tabla_infaoliva() {
  global $ST;

  return api_response($ST->tabla_infaoliva());
}

function tabla_poolred() {
  global $ST;

  return api_response($ST->tabla_poolred());
}

function tabla_almazaras() {
  global $ST;

  return api_response($ST->tabla_almazaras());
}


function tabla_historico_precios_infaoliva(){
	global $ST;
	return api_response($ST->tabla_historico_precios_infaoliva());
}

function tabla_historico_precios_poolred(){
	global $ST;
	return api_response($ST->tabla_historico_precios_poolred());
}

function tabla_historico_precios_almazaras(){
	global $ST;
	return api_response($ST->tabla_historico_precios_almazaras());
}


function tabla_historico_tipos_extra_virgen(){
    global $ST;
    return api_response($ST->tabla_historico_tipos_extra_virgen());
}

function tabla_historico_tipos_virgen(){
    global $ST;
    return api_response($ST->tabla_historico_tipos_virgen());
}

function tabla_historico_tipos_lampante(){
    global $ST;
    return api_response($ST->tabla_historico_tipos_lampante());
}