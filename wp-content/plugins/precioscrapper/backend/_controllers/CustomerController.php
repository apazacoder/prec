<?php

require_once '_helpers.php';
global $wpdb;
global $db_prefix;
require_once __DIR__ . '/../_models/Customer.php';
$Customer = new Customer( $wpdb, $db_prefix );

add_action( 'rest_api_init', function () {
	global $api_prefix;
	// /wp-json/cs/v1/customers
	register_rest_route( $api_prefix, 'init_customers', [
		'methods'  => 'POST',
		'callback' => 'init_customers'
	] );
	register_rest_route( $api_prefix, 'customers', [
		'methods'  => 'GET',
		'callback' => 'customers'
	] );
	register_rest_route( $api_prefix, 'customer', [
		'methods'  => 'POST',
		'callback' => 'store_customer'
	] );

	register_rest_route( $api_prefix, 'customer', [
		'methods'  => 'PUT',
		'callback' => 'update_customer'
	] );

	register_rest_route( $api_prefix, 'customer', [
		'methods'  => 'DELETE',
		'callback' => 'delete_customer'
	] );

	register_rest_route( $api_prefix, 'customer_and_answers', [
		'methods'  => 'POST',
		'callback' => 'store_customer_and_answers'
	] );

	register_rest_route( $api_prefix, 'customer_pdf/(?P<id_custom_plan>[\d]+)', [
		'methods'  => 'GET',
		'callback' => 'customer_pdf'
	] );

	register_rest_route( $api_prefix, 'request_contact', [
		'methods'  => 'POST',
		'callback' => 'request_contact'
	] );

	register_rest_route( $api_prefix, 'customer_recommended_plans/(?P<id_customer>[\d]+)', [
		'methods'  => 'GET',
		'callback' => 'customer_recommended_plans'
	] );

	register_rest_route( $api_prefix, 'customer_questions_and_answers/(?P<id_customer>[\d]+)', [
		'methods'  => 'GET',
		'callback' => 'customer_questions_and_answers'
	] );

	register_rest_route( $api_prefix, 'contact_requests_pending_qty' ,
	[
		'methods'  => 'GET',
		'callback' => 'contact_requests_pending_qty'
	] );
} );

function init_customers($request) {
	global $Customer;

	return api_response( $Customer->init_customers($request) );
}

function customers() {
	global $Customer;

	return api_response( $Customer->customers() );
}

function store_customer( $request ) {
	global $Customer;

	return api_response( $Customer->store_customer( $request ) );
}

function update_customer( $request ) {
	global $Customer;

	return api_response( $Customer->update_customer( $request ) );
}

function delete_customer( $request ) {
	global $Customer;

	return api_response( $Customer->delete_customer( $request ) );
}

function store_customer_and_answers( $request ) {
	global $Customer;

	return api_response( $Customer->store_customer_and_answers( $request ) );
}

function customer_pdf( $request ) {
	global $Customer;

	return api_response( $Customer->customer_pdf( $request ) );
}

function request_contact( $request ) {
	global $Customer;

	return api_response( $Customer->request_contact( $request ) );
}

function customer_recommended_plans( $request ) {
	global $Customer;

	return api_response( $Customer->customer_recommended_plans( $request ) );
}

function customer_questions_and_answers( $request ) {
	global $Customer;

	return api_response( $Customer->customer_questions_and_answers( $request ) );
}

function contact_requests_pending_qty( $request ) {
	global $Customer;

	return api_response( $Customer->get_contact_requests_pending_qty( $request ) );
}
