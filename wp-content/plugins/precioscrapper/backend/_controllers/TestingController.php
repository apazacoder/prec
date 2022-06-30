<?php
require_once '_helpers.php';
global $wpdb;
global $db_prefix;
require_once __DIR__ . '/../_models/Testing.php';
$Testing = new Testing( $wpdb, $db_prefix );

add_action( 'rest_api_init', function () {
	global $api_prefix;
	// /wp-json/cs/v1/test_plans

	register_rest_route( $api_prefix, 'test_plans', [
		'methods'  => 'GET',
		'callback' => 'test_plans'
	] );

	register_rest_route( $api_prefix, 'test_plan_details', [
		'methods'  => 'POST',
		'callback' => 'test_plan_details'
	] );

	register_rest_route( $api_prefix, 'test_logic_details', [
		'methods'  => 'GET',
		'callback' => 'test_logic_details'
	] );

	register_rest_route( $api_prefix, 'sandbox', [
		'methods'  => 'GET',
		'callback' => 'sandbox'
	] );
} );

function test_plans( $request ) {
	global $Testing;

	return api_response( $Testing->plans( $request ) );
}

function test_plan_details( $request ) {
	global $Testing;

	return api_response( $Testing->plan_details( $request ) );
}

function test_logic_details( $request ) {
	global $Testing;

	return api_response( $Testing->logic_details( $request ) );
}

function sandbox() {
	global $Testing;
	return api_response( $Testing->sandbox() );
}

