<?php

// each model must implement methods and messages to be validable
interface iModel {

	function update_rules();

	function store_rules();

	function validation_messages();
}
