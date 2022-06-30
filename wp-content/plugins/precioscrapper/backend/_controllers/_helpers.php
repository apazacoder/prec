<?php

function api_response($response){
	$response = new WP_REST_Response($response);
	$response->set_status(200);
	return $response;
}
