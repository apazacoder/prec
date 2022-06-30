<?php
/*
Plugin Name: Scrapper de precios
Plugin URI: https://apazim.com
Description: Permite agregar vistas resultado del scrapper con el shortcode [scrapper]
Version: 1.0
Author: Alcides Apaza Yanarico
Author URI: apaza.alcides@gmail.com
License: GPLv2
*/

date_default_timezone_set('Europe/Madrid');

global $cs_db_version;
$cs_db_version = '1.0'; // plugin database version
global $db_prefix;
global $wpdb;
$db_prefix = $wpdb->prefix . 'cs_'; //wp_cs_

// to disable new breaking feature of WP 5.6
//add_filter( 'wp_is_application_passwords_available', '__return_false' );

function cs_install()
{
    global $wpdb;
    global $cs_db_version;
    global $db_prefix;
    $charset_collate = $wpdb->get_charset_collate();

    $queries = [];

    $table_name = $db_prefix . 'lecturas';
    array_push($queries, "CREATE TABLE $table_name (
		id int(10) NOT NULL AUTO_INCREMENT,												
		leido_el varchar(240) NOT NULL,
		codigo_respuesta int(10) NOT NULL,						
		resultado varchar(30),						
		PRIMARY KEY  (id)		        
	) $charset_collate;");

    $table_name = $db_prefix . 'precios';
    array_push($queries, "CREATE TABLE $table_name (
		id int(10) NOT NULL AUTO_INCREMENT,										
		aceite varchar(120) NOT NULL,
		precio decimal(10,9) NOT NULL,
		precio_historico decimal(10,9) NOT NULL,
		precio_arbequina decimal(10,9) NOT NULL,
		fecha_precio TIMESTAMP,
		fecha_precio_rango TIMESTAMP,
		fuente varchar(64) NOT NULL,		
		id_lectura int(10) NULL, 					
		PRIMARY KEY  (id),
		KEY (id_lectura)        
	) $charset_collate;");

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // standard database creation
    foreach ($queries as $query) {
        dbDelta($query);
    }

    // create foreign keys
    $queries = [];

    $table_main = $db_prefix . 'precios';
    $table_foreign_1 = $db_prefix . 'lecturas';

    array_push($queries, "ALTER TABLE $table_main ADD CONSTRAINT FOREIGN KEY fk_{$table_main}_{$table_foreign_1}(id_lectura) REFERENCES $table_foreign_1(id) ON UPDATE CASCADE ON DELETE RESTRICT");
    foreach ($queries as $query) {
        $wpdb->query($query);
    }

    // store database version
    add_option('cs_db_version', $cs_db_version);
}

function cs_install_data()
{
    global $wpdb;
    global $app_prefix;
}

register_activation_hook(__FILE__, 'cs_install');
//register_activation_hook( __FILE__, 'cs_install_data' );

// SHORTCODES
function shortcode_tabla_poolred()
{
    $pageCode = <<< PAGE
<div class="scrapper-app" id="scrapper-tabla-poolred">
	<div class="fade-in-from-top">
	    <tabla-poolred></tabla-poolred>
	</div>
</div>
PAGE;
    return $pageCode;
}
add_shortcode('tabla-poolred', 'shortcode_tabla_poolred');


function shortcode_tabla_infaoliva()
{
    $pageCode = <<< PAGE
<div class="scrapper-app" id="scrapper-tabla-infaoliva">
	<div class="fade-in-from-top">
	    <tabla-infaoliva></tabla-infaoliva>
	</div>
</div>
PAGE;
    return $pageCode;
}
add_shortcode('tabla-infaoliva', 'shortcode_tabla_infaoliva');

function shortcode_tabla_almazaras()
{
    $pageCode = <<< PAGE
<div class="scrapper-app" id="scrapper-tabla-almazaras">
	<div class="fade-in-from-top">
	    <tabla-almazaras></tabla-almazaras>
	</div>
</div>
PAGE;
    return $pageCode;
}
add_shortcode('tabla-almazaras', 'shortcode_tabla_almazaras');


// SHORTCODES
function shortcode_tabla_historico_precios_poolred()
{
    $pageCode = <<< PAGE
<div class="scrapper-app" id="scrapper-tabla-historico-precios-poolred">
	<div class="fade-in-from-top">
	    <tabla-historico-precios-poolred></tabla-historico-precios-poolred>
	</div>
</div>
PAGE;
    return $pageCode;
}
add_shortcode('tabla-historico-precios-poolred', 'shortcode_tabla_historico_precios_poolred');


function shortcode_tabla_historico_precios_infaoliva()
{
    $pageCode = <<< PAGE
<div class="scrapper-app" id="scrapper-tabla-historico-precios-infaoliva">
	<div class="fade-in-from-top">
	    <tabla-historico-precios-infaoliva></tabla-historico-precios-infaoliva>
	</div>
</div>
PAGE;
    return $pageCode;
}
add_shortcode('tabla-historico-precios-infaoliva', 'shortcode_tabla_historico_precios_infaoliva');

function shortcode_tabla_historico_precios_almazaras()
{
    $pageCode = <<< PAGE
<div class="scrapper-app" id="scrapper-tabla-historico-precios-almazaras">
	<div class="fade-in-from-top">
	    <tabla-historico-precios-almazaras></tabla-historico-precios-almazaras>
	</div>
</div>
PAGE;
    return $pageCode;
}
add_shortcode('tabla-historico-precios-almazaras', 'shortcode_tabla_historico_precios_almazaras');


// SHORTCODES
function shortcode_tabla_historico_tipos_extra_virgen()
{
    $pageCode = <<< PAGE
<div class="scrapper-app" id="scrapper-tabla-historico-tipos-extra-virgen">
	<div class="fade-in-from-top">
	    <tabla-historico-tipos-extra-virgen></tabla-historico-tipos-extra-virgen>
	</div>
</div>
PAGE;
    return $pageCode;
}
add_shortcode('tabla-historico-tipos-extra-virgen', 'shortcode_tabla_historico_tipos_extra_virgen');


function shortcode_tabla_historico_tipos_virgen()
{
    $pageCode = <<< PAGE
<div class="scrapper-app" id="scrapper-tabla-historico-tipos-virgen">
	<div class="fade-in-from-top">
	    <tabla-historico-tipos-virgen></tabla-historico-tipos-virgen>
	</div>
</div>
PAGE;
    return $pageCode;
}
add_shortcode('tabla-historico-tipos-virgen', 'shortcode_tabla_historico_tipos_virgen');

function shortcode_tabla_historico_tipos_lampante()
{
    $pageCode = <<< PAGE
<div class="scrapper-app" id="scrapper-tabla-historico-tipos-lampante">
	<div class="fade-in-from-top">
	    <tabla-historico-tipos-lampante></tabla-historico-tipos-lampante>
	</div>
</div>
PAGE;
    return $pageCode;
}
add_shortcode('tabla-historico-tipos-lampante', 'shortcode_tabla_historico_tipos_lampante');




function shortcode2_scrapper()
{
//
    $pageCode = <<< PAGE
<div class="scrapper-app" id="scrapper-app">
	<div class="fade-in-from-top">
	  <tabla-poolred></tabla-poolred>	  	 
	  <tabla-infaoliva></tabla-infaoliva>	  	 
	  <tabla-almazaras></tabla-almazaras>
	  <tabla-historico-precios-poolred></tabla-historico-precios-poolred>	  	 
      <tabla-historico-precios-infaoliva></tabla-historico-precios-infaoliva>	  	 
	  <tabla-historico-precios-almazaras></tabla-historico-precios-almazaras>	 	  	 
	  <grafico-historico-precios-infaoliva></grafico-historico-precios-infaoliva> 		
	</div>
</div>
PAGE;

    return $pageCode;
}

add_shortcode('scrapper', 'shortcode2_scrapper');

// public files
add_action('wp_enqueue_scripts', 'cs_enqueue_scripts');
function cs_enqueue_scripts($hook)
{

    wp_register_script('cs_wfront_chunk_vendors', plugins_url('frontend/dist/js/chunk-vendors.js', __FILE__), '', true);
    wp_enqueue_script('cs_wfront_chunk_vendors');

    wp_register_script('cs_wfront_app', plugins_url('frontend/dist/js/app.js', __FILE__), '', true);
    wp_enqueue_script('cs_wfront_app');
}

// admin files
add_action('admin_enqueue_scripts', 'cs_admin_enqueue_scripts', 10000);
function cs_admin_enqueue_scripts($hook)
{
    // add conditional to load only in our own menu
//	if ( $hook == "toplevel_page_admin_cotizador" ) {
    // webpack assets
    wp_register_script('cs_wadmin_chunk_vendors', plugins_url('backend/dist/js/chunk-vendors.js', __FILE__), '', true);
    wp_enqueue_script('cs_wadmin_chunk_vendors');

    wp_register_script('cs_wadmin_app', plugins_url('backend/dist/js/app.js', __FILE__), '', true);
    wp_enqueue_script('cs_wadmin_app');
//	}
}


// add admin page
add_action('admin_menu', 'cs_cotizador_menu');
function cs_cotizador_menu()
{
    add_menu_page('Precios', 'Precios', 'manage_options', 'admin_cotizador', 'render_admin_precios', plugins_url('cs_icon.png', __FILE__), 2);
}

// render admin page
function render_admin_precios()
{
    echo <<<PAGE
<div id="backend">
	<h1 class="has-text-centered">Scrapper de precios</h1>
	<div class="columns nav is-multiline is-mobile">
		<div class="column is-four-quarters-mobile is-two-quarters-tablet is-two-quarters-desktop">			
			<router-link to="/" class="button is-active is-link">Precios y lecturas</router-link>
    </div>
    <div class="column is-four-quarters-mobile is-two-quarters-tablet is-two-quarters-desktop">
      <router-link to="/lecturas" 
      class="button is-active is-link">Todas las lecturas</router-link>
    </div>    
  </div>
  <transition name="router-anim" mode="out-in">
    <router-view></router-view>
  </transition>
</div>
PAGE;
}

// API Endpoints
global $api_prefix;
$api_prefix = "cs/v1";

require_once 'backend/_controllers/TestingController.php';
require_once 'backend/_controllers/LecturaController.php';
require_once 'backend/_controllers/PrecioController.php';
require_once 'backend/_controllers/ShortcodeTablesController.php';
//require_once 'backend/_controllers/QuestionController.php';
//require_once 'backend/_controllers/PlanController.php';
//require_once 'backend/_controllers/ServiceController.php';
//require_once 'backend/_controllers/EnablerController.php';
//require_once 'backend/_controllers/CustomerController.php';
//require_once 'backend/_controllers/AnswerController.php';
