<?php
namespace SIM\PROJECTS;
use SIM;

// register custom meta tag field
add_action( 'init',  __NAMESPACE__.'\blockInit' );
function blockInit(){
	register_block_type(
		__DIR__ . '/metadata/build',
		array(
			"attributes"	=>  [
				"lock"	=> [
					"type"	=> "object",
					"default"	=> [
						"move"		=> true,
						"remove"	=> true
					]
				],
				'number'	=> [
					'type'	=> 'int',
					'default'	=> ''
				],
				'url'	=> [
					'type'	=> 'string',
					'default'	=> ''
				],
				'manager'	=> [
					'type'		=> 'string',
					'default'	=> '{"userid":"","name":"","tel":"","email":"","":""}'
				],
				'ministry'	=> [
					'type'	=> 'int',
					'default'	=> ''
				]
			]
		)
	);

	register_post_meta( 'project', 'number', array(
        'show_in_rest' 		=> true,
        'single' 			=> true,
        'type' 				=> 'string',
		'sanitize_callback' => 'sanitize_text_field'
    ) );

	register_post_meta( 'project', 'url', array(
        'show_in_rest' 		=> true,
        'single' 			=> true,
        'type' 				=> 'string',
		'sanitize_callback' => 'sanitize_text_field'
    ) );

	register_post_meta( 'project', 'manager', array(
        'show_in_rest' 		=> true,
        'single' 			=> true,
        'type' 				=> 'string',
		'sanitize_callback' => 'sanitize_text_field'
    ) );

	register_post_meta( 'project', 'ministry', array(
        'show_in_rest' 		=> true,
        'single' 			=> true,
        'type' 				=> 'string',
		'sanitize_callback' => 'sanitize_text_field'
    ) );
}


add_action( 'rest_api_init',  __NAMESPACE__.'\blockRestApiInit');
function blockRestApiInit() {	
	//Route for notification messages
	register_rest_route( 
		RESTAPIPREFIX.'/projects', 
		'/ministries',
		array(
			'methods' => 'GET',
			'callback' => __NAMESPACE__.'\getPosts',
			'permission_callback' => '__return_true',
		)
	);
}
function getPosts($request){
			return get_posts([
				'numberposts' => -1,
				'post_type'   => 'location',
				'orderby'     => 'title',
				'order'		  => 'ASC',
				'tax_query'	  => [
					[
						'taxonomy' 			=> 'locations',
						'include_children' 	=> true,
						'field'    			=> 'slug',
						'operator'         	=> 'IN',
						'terms'    			=> [$request['slug']],
					]
				]
			]);
		}