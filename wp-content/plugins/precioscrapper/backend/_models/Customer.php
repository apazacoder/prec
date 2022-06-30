<?php
require_once 'Model.php';
require_once 'iModel.php';
require_once 'Customer.php';
require_once 'Helper.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;

class Customer extends Model implements iModel {
	protected $wpdb;
	protected $db_prefix;
	protected $table_customers;
	protected $table_categories;
	protected $Customer;

	function __construct( $wpdb, $db_prefix ) {
		$this->db_prefix        = $db_prefix;
		$this->wpdb             = $wpdb;
		$this->table_customers  = $db_prefix . 'customers';
		$this->table_answers    = $db_prefix . 'answers';
		$this->table_questions  = $db_prefix . 'questions';
		$this->table_categories = $db_prefix . 'categories';
		$this->Category         = new Category( $wpdb, $db_prefix );
	}

	public function store_rules() {
		return [
			'id_category' => 'required',
			'fullname'    => 'required',
			'email'       => 'required',
			'phone'       => 'required',
		];
	}

	public function update_rules() {
		return [
			'id_category' => 'required',
			'fullname'    => 'required',
			'email'       => 'required',
			'phone'       => 'required',
		];
	}

	public function validation_messages() {
		return [
			'id_category.required' => 'Por favor seleccione la categoría',
			'fullname.required'    => 'Por favor introduzca el nombre del cliente',
			'email.required'       => 'Por favor introduzca el e-mail del cliente',
			'phone.required'       => 'Por favor introduzca el teléfono del cliente',
		];
	}

	public function init_customers( $request ) {
		return [
			"customers"  => $this->customers_with_categories( $request ),
			"categories" => $this->Category->categories()
		];
	}

	public function customers() {
		return $this->wpdb->get_results(
			"SELECT * FROM $this->table_customers ORDER BY created_at ASC"
		);
	}

	public function store_customer_and_answers( $request ) {
		$errors      = [];
		$customer_id = "";
		$plans       = [];
		$result      = $this->store_customer( $request );
		// if no errors obtain the customer id
		$errors = $result["errors"];
		if ( ! $errors ) {
			$customer_id = $result["item_id"];

			// store all answers of this customer based on filtered_questions data
			$Answer = new Answer( $this->wpdb, $this->db_prefix );

			$data               = json_decode( $request->get_body(), true );
			$item               = $data["item"];
			$filtered_questions = $item["filtered_questions"];

			$errors = $Answer->store_answers_by_customer( $request, $customer_id );

			$id_custom_plan = 0;
			if ( ! $errors ) {
				$Plan = new Plan ( $this->wpdb, $this->db_prefix );

				// create a new unique plan for the customer based on his/her answers
				$id_custom_plan = $Plan->store_custom_plan( $request )["id_plan"];

				// use the data gathered to make the calculations required and return all plans
				$plans = $Plan->calculate_plans( $id_custom_plan );

				// update the customer to store references to the calculated plans:
				// - id_custom_plan
				// - id_inferior_plan
				// - id_best_plan
				// - id_superior_plan
				$errors = $this->store_plans_references( $customer_id, $id_custom_plan, $item["id_category"] );

			}
		}

		return [
			"errors"         => $errors,
			"item_id"        => $customer_id,
			"plans"          => $plans,
			"id_custom_plan" => $id_custom_plan
		];
	}

	public function store_plans_references( $customer_id, $id_custom_plan, $id_category ) {
		$Plan      = new Plan ( $this->wpdb, $this->db_prefix );
		$plans_ids = $Plan->calculate_plans_ids( $id_custom_plan, $id_category );
		// TODO store plans

		$id_inferior_plan = null;
		$id_best_plan     = null;
		$id_superior_plan = null;
		foreach ( $plans_ids as $plan_id ) {
			switch ( $plan_id["type"] ) {
				case "inferior":
					$id_inferior_plan = $plan_id["id"];
					break;
				case "best":
					$id_best_plan = $plan_id["id"];
					break;
				case "superior":
					$id_superior_plan = $plan_id["id"];
					break;
			}
		}

		// update
		$now     = Helper::sqlNow();
		$updated = false;
		$errors  = [];
		try {
			$updated = $this->wpdb->update(
				$this->table_customers,
				[
					'id_custom_plan'   => $id_custom_plan,
					'id_inferior_plan' => $id_inferior_plan,
					'id_best_plan'     => $id_best_plan,
					'id_superior_plan' => $id_superior_plan,
					'updated_at'       => $now
				],
				[
					'id' => $customer_id
				],
				[
					'%d',
					'%d',
					'%d',
					'%d',
					'%s'
				],
				[
					'%d'
				]
			);
		} catch ( Exception $e ) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}


	public function store_customer( $request ) {
		$data    = json_decode( $request->get_body(), true );
		$item    = $data["item"];
		$errors  = $this->get_store_errors( $item );
		$item_id = "";

		if ( ! $errors ) {
			$now     = Helper::sqlNow();
			$created = false;
			try {
				$created = $this->wpdb->insert(
					$this->table_customers,
					[
						'id_category'             => $item["id_category"],
						'fullname'                => $item["fullname"],
						'email'                   => $item["email"],
						'phone'                   => $item["phone"],
						'newsletter_subscription' => $item["newsletter_subscription"],
						'notifications'           => $item["notifications"],
						'created_at'              => $now,
						'updated_at'              => $now
					],
					[
						'%d',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s'
					]
				);
				// compute the best plan
			} catch ( Exception $e ) {
				$errors[] = $e->getMessage();
			}

			if ( $created === false ) {
				$errors[] = "Error de base de datos";
			} else {
				$item_id = $this->wpdb->insert_id;
			}
		}

		return [
			'errors'  => $errors,
			'item_id' => $item_id
		];
	}

	public function update_customer( $request ) {
		$data   = json_decode( $request->get_body(), true );
		$item   = $data["item"];
		$errors = $this->get_update_errors( $item );

		if ( ! $errors ) {
			$now     = Helper::sqlNow();
			$updated = false;
			try {
				$updated = $this->wpdb->update(
					$this->table_customers,
					[
						'id_category'                  => $item["id_category"],
						'fullname'                     => $item["fullname"],
						'email'                        => $item["email"],
						'phone'                        => $item["phone"],
						'newsletter_subscription'      => $item["newsletter_subscription"],
						'notifications'                => $item["notifications"],
						'is_contact_request_processed' => $item["is_contact_request_processed"],
						'updated_at'                   => $now
					],
					[
						'id' => $item["id"]
					],
					[
						'%d',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%d',
						'%s'
					],
					[
						'%d'
					]
				);
			} catch ( Exception $e ) {
				$errors[] = $e->getMessage();
			}

			if ( $updated === false ) {
				$errors[] = "Error de base de datos";
			}
		}

		return [ 'errors' => $errors ];
	}

	public function delete_customer( $request ) {
		$data   = json_decode( $request->get_body(), true );
		$item   = $data["item"];
		$errors = [];

		$deleted = false;
		try {
			$deleted = $this->wpdb->delete(
				$this->table_customers,
				[
					'id' => $item["id"]
				],
				[
					'%d'
				]
			);
		} catch ( Exception $e ) {
			$errors[] = $e->getMessage();
		}

		if ( $deleted === false ) {
			$errors[] = "Error de base de datos";
		}

		return [ 'errors' => $errors ];
	}

	// model exlusive functions
	public function customers_with_categories( $request ) {
		$data = json_decode( $request->get_body(), true );
		if ( trim( $data["term"] ) == "" ) {
			return $this->customers_paginated( $request );
		} else {
			return $this->customers_filtered( $request );
		}
	}

	public function customers_paginated( $request ) {
		$data   = json_decode( $request->get_body(), true );
		$page   = $data["page"];
		$length = $data["length"];
		$offset = $length * ( $page - 1 );

		$query = "SELECT cu.id, cu.fullname, cu.email, cu.phone, cu.newsletter_subscription, cu.notifications, cu.is_contact_requested, cu.is_contact_request_processed, cu.created_at, cu.updated_at, cu.id_category, ca.name as category, cu.id_best_plan, cu.id_chosen_plan, cu.id_custom_plan
				FROM $this->table_customers cu
				LEFT JOIN $this->table_categories ca
				ON cu.id_category = ca.id
				ORDER BY cu.created_at ASC";

		$notFiltered = $this->wpdb->get_results( $query );
		$filtered    = $this->wpdb->get_results( "$query LIMIT $offset, $length" );

		$itemsNumber = count( $notFiltered );
		$pagesNumber = $itemsNumber ? (integer) ( ceil( $itemsNumber / $length ) ) : 1;
		$currentPage = $pagesNumber > 1 ? $page : 1;

		return [
			"itemsNumber" => $itemsNumber,
			"itemsRows"   => $filtered,
			"pagesNumber" => $pagesNumber,
			"currentPage" => $currentPage,
		];
	}

	public function customers_filtered( $request ) {
		$data   = json_decode( $request->get_body(), true );
		$page   = $data["page"];
		$term   = $data["term"] ? Helper::mysql_escape( $data["term"] ) : '';
		$length = $data["length"];
		$offset = $length * ( $page - 1 );

		$baseQuery = "SELECT cu.id, cu.fullname, cu.email, cu.phone, cu.newsletter_subscription, cu.notifications, cu.is_contact_requested, cu.is_contact_request_processed, cu.created_at, cu.updated_at, cu.id_category, ca.name as category, cu.id_best_plan, cu.id_chosen_plan, cu.id_custom_plan
				FROM $this->table_customers cu
				LEFT JOIN $this->table_categories ca
				ON cu.id_category = ca.id ";

		$normalQuery = "WHERE cu.fullname like '%$term%' OR cu.email like '%$term%' OR cu.phone like '%$term%' OR cu.created_at like '%$term%' OR cu.updated_at like '%$term%' OR ca.name like '%$term%'
				ORDER BY cu.created_at ASC";

		$pendingsQuery = "WHERE cu.is_contact_requested = '1' AND cu.is_contact_request_processed = '0'";

		switch ( $term ) {
			case "pendientes":
				$query = $baseQuery . $pendingsQuery;
				break;
			default:
				$query = $baseQuery . $normalQuery;
				break;
		}

		$notFiltered = $this->wpdb->get_results( $query );
		$filtered    = $this->wpdb->get_results( "$query LIMIT $offset, $length" );

		$itemsNumber = count( $notFiltered );
		$pagesNumber = $itemsNumber ? (integer) ( ceil( $itemsNumber / $length ) ) : 1;
		$currentPage = $pagesNumber > 1 ? $page : 1;

		return [
			"itemsNumber" => $itemsNumber,
			"itemsRows"   => $filtered,
			"pagesNumber" => $pagesNumber,
			"currentPage" => $currentPage,
			"term"        => $term,
		];
	}

	// model specific
	public function get_customers_by_category( $id_category ) {
		return $this->wpdb->get_results(
			"SELECT * FROM $this->table_customers
				WHERE id_category = $id_category
				ORDER BY id"
		);
	}

	public function compose_html( $id_custom_plan ) {
		$Plan     = new Plan( $this->wpdb, $this->db_prefix );
		$result   = $Plan->calculate_plans( $id_custom_plan );
		$plans    = $result["plans"];
		$services = $result["services"];

		$html = <<<HTML
<style>
body{
	background: #fff;
	font-family: "Helvetica", sans-serif;
	font-size: 1em;	
}
table{
	table-layout:fixed;
	width: 100%;
	border-collapse: collapse;
	margin: 20px 10px;	
}
table th, table td{
	border: 1px solid #D4DBE3;
}
table th.no-top-border{
	border-top: none;
}
table th.no-bottom-border{
	border-bottom: none;
}
table th.best, table td.best{
	background: #FAFCFE;
}

table th.custom, table td.custom{
	background: #F7F9FB;
}
.theader{
	position: relative;
}
.theader .ribbon{
	width: 68%;
	left: 16%; 
	position: relative;
	top: -16px;			
    border: 1px solid #D4DBE3;
    text-transform: uppercase;
    background: #fff;
    font-size: .7rem;
    font-weight: bold;
    border-radius: 5px;
    padding: 4px 0 4px;                  
}
.theader .ribbon.hidden{
    visibility:hidden;
}
.theader .ribbon.custom{
    color: #0d47a1;
}
.theader .ribbon.reco{
	color: #2979ff;
}

.theader .name, .theader .price {
  color: #37474f;
}

.theader .name {
  font-weight: bold;
}

.theader .price {
	padding: 15px 0 15px;
  	line-height: 2rem;  
}
.theader .price .money-symbol {
    vertical-align: top;
    line-height: 3rem;
    font-size: .85rem;    
    font-weight: normal;
}

.theader .price .amount {
    font-size: 1.6rem;
    font-weight: bold;
}
.theader .price .decimal-month{
	margin-left: -10px;
	font-weight: normal;
}

.theader .price  .month {
    font-size: .85rem;    
    margin-left: -3px;
    font-weight: normal;
}

tbody td:nth-child(n+2){
	text-align: center;
}

tbody td{
	padding: 10px 10px;
	vertical-align: middle;
}
tbody img{
	width: 15px;
	height: auto;
}
tbody .service{
	font-size: .9rem;
	color: #37474f;
}
tbody td:nth-child(1), thead th:nth-child(1){
	width: 30%;
}

</style>
HTML;

		$html .= '<body>';
		$html .= '<table>';

		$html .= <<<HTML
 <thead>
  	<tr>
   		<th class="no-bottom-border"></th>   		
HTML;
		foreach ( $plans as $index => $plan ) {
			if ( $plan->type == "best" ) {
				$html .= '<th class="no-bottom-border best">';
			} else if ( $plan->type == "custom" ) {
				$html .= '<th class="no-bottom-border custom">';
			} else {
				$html .= '<th class="no-bottom-border">';
			}
			$html .= '<div class="theader">';
			if ( $plan->type == 'best' ) {
				$html .= '<div class="ribbon reco">';
				$html .= 'recomendado';
				$html .= '</div>';
			} else if ( $plan->type == 'custom' ) {
				$html .= '<div class="ribbon custom">';
				$html .= 'a tu medida';
				$html .= '</div>';
			} else {
				$html .= '<div class="ribbon hidden">&nbsp; </div>';
			}
			$html .= '<div class="name">';
			$html .= ( $plan->type != 'custom' ) ? $plan->name : 'Personalizado';
			$html .= '</div>';
			$html .= '</div>'; //theader end
			$html .= '</th>';
		}
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th class="no-top-border"></th>';
		foreach ( $plans as $index => $plan ) {
			if ( $plan->type == "best" ) {
				$html .= '<th class="no-top-border best">';
			} else if ( $plan->type == "custom" ) {
				$html .= '<th class="no-top-border custom">';
			} else {
				$html .= '<th class="no-top-border">';
			}
			$html   .= '<div class="theader">';
			$html   .= '<div class="price">';
			$html   .= '    <span class="money-symbol">US$</span>';
			$html   .= '    <span class="amount">';
			$amount = explode( ".", number_format( $plan->total_price, 2 ) );
			$html   .= $amount[0];
			$html   .= '    </span';
			$html   .= '    ><span class="decimal-month">';
			$html   .= '      <span class="decimal">';
			$html   .= $amount[1];
			$html   .= '      </span';
			$html   .= '      ><span class="month">/mes</span>';
			$html   .= '    </span>';
			$html   .= '</div>';
			$html   .= '</div>'; // theader end
			$html   .= '</th>';
		}

		$html .= <<<HTML
   	</tr>
 </thead>
HTML;
		$html .= '<tbody>';
		foreach ( $services as $si => $service ) {
			$html .= '<tr>';
			$html .= '<td class="service">' . $service->description . '</td>';
			foreach ( $plans as $pi => $plan ) {
				if ( $plan->type == "best" ) {
					$html .= '<td class="best">';
				} else if ( $plan->type == "custom" ) {
					$html .= '<td class="custom">';
				} else {
					$html .= '<td>';
				}
				if ( $plan->services[ $si ]->is_enabled ) {
					if ( $plan->type == "best" ) {
						$check_image = '<img src="' . __DIR__ . '/checkmark-best.jpg">';
					} else if ( $plan->type == "custom" ) {
						$check_image = '<img src="' . __DIR__ . '/checkmark-custom.jpg">';
					} else {
						$check_image = '<img src="' . __DIR__ . '/checkmark.jpg">';
					}


					$html .= $check_image;
				}
				$html .= '</td>';
			}
			$html .= '</tr>';
		}
		$html .= '<tbody>';
		$html .= '</table>';

		$html .= '</body>';

		return $html;
	}

	public function customer_pdf( $request ) {
//		$data       = json_decode( $request->get_body(), true );
		$get_params = $request->get_url_params();
		$dompdf     = new Dompdf();

		$options = $dompdf->getOptions();
		$options->setDefaultFont( 'Helvetica' );
		$dompdf->getOptions()->setChroot( __DIR__ );
		$dompdf->setOptions( $options );
		$root           = $dompdf->getOptions()->getChroot();
		$id_custom_plan = $get_params["id_custom_plan"];
		// compose the pdf with a parameter

		$dompdf->loadHtml( $this->compose_html( $id_custom_plan ) );
		$dompdf->setPaper( 'Letter', 'landscape' );
		$dompdf->render();
		$file_path = __DIR__ . '/../../../../../wp-content/uploads/plan_' . $id_custom_plan . '.pdf';
		$output    = $dompdf->output();
		file_put_contents( $file_path, $output );

		// send file directly to the browser
		if ( file_exists( $file_path ) ) {
			header( $_SERVER["SERVER_PROTOCOL"] . " 200 OK" );
			header( "Content-Type: application/pdf" );
			header( "Content-Transfer-Encoding: Binary" );
			header( "Content-Length:" . filesize( $file_path ) );
			header( "Content-Disposition: attachment; filename=myfile.pdf" );
			readfile( $file_path );
			die();
		} else {
			die( "Error: File not found." );
		}
	}

	public function get_customer_by_id( $id_customer ) {
		return $this->wpdb->get_results(
			"SELECT * FROM $this->table_customers
 				WHERE id = $id_customer
	 			LIMIT 1		
			"
		);
	}

	public function get_customer_by_custom_plan( $id_custom_plan ) {
		return $this->wpdb->get_results(
			"SELECT * FROM $this->table_customers
 				WHERE id_custom_plan = $id_custom_plan
	 			LIMIT 1		
			"
		);
	}

	public function request_contact( $request ) {
		$data           = json_decode( $request->get_body(), true );
		$id_custom_plan = $data["id_custom_plan"];
		$Customer       = new Customer( $this->wpdb, $this->db_prefix );
		$customer       = $Customer->get_customer_by_custom_plan( $id_custom_plan );
		$customer       = count( $customer ) ? $customer[0] : null;

		$errors = [];

		if ( $customer ) {
			$now     = Helper::sqlNow();
			$updated = false;
			try {
				$updated = $this->wpdb->update(
					$this->table_customers,
					[
						'is_contact_requested' => 1,
						'updated_at'           => $now
					],
					[
						'id' => $customer->id
					],
					[
						'%d',
						'%s'
					],
					[
						'%d'
					]
				);
			} catch ( Exception $e ) {
				$errors[] = $e->getMessage();
			}

			if ( $updated === false ) {
				$errors[] = "Error de base de datos";
			}
		}

		return [
			'errors' => $errors
		];
	}

	// returns plans or null
	public function customer_recommended_plans( $request ) {
		$get_params  = $request->get_url_params();
		$id_customer = $get_params["id_customer"];

		$Customer = new Customer( $this->wpdb, $this->db_prefix );
		$customer = $Customer->get_customer_by_id( $id_customer );
		$customer = count( $customer ) ? $customer[0] : null;

		$id_custom_plan = null;
		if ( $customer ) {
			$id_custom_plan = $customer->id_custom_plan;
		}

		$result = [];
		if ( $id_custom_plan ) {
			$Plan   = new Plan( $this->wpdb, $this->db_prefix );
			$result = $Plan->calculate_plans( $id_custom_plan );
		}

		return $result;
	}

	public function get_questions_and_answers_by_id( $id_customer ) {
		return $this->wpdb->get_results(
			"SELECT a.id_customer, a.id_question, a.id as id_answer, c.fullname, c.email, c.created_at, q.text, a.value
				FROM $this->table_answers a
				LEFT JOIN $this->table_customers c
				ON a.id_customer = c.id
				LEFT JOIN $this->table_questions q
				ON a.id_question = q.id
				WHERE id_customer = $id_customer
			" );
	}

	public function customer_questions_and_answers( $request ) {
		$get_params  = $request->get_url_params();
		$id_customer = $get_params["id_customer"];

		return $this->get_questions_and_answers_by_id( $id_customer );
	}

	public function get_contact_requests_pending_qty(){
		return count($this->wpdb->get_results(
			"SELECT * FROM $this->table_customers as cu
 				WHERE cu.is_contact_requested = '1' AND cu.is_contact_request_processed = '0'
			"
		));
	}
}
