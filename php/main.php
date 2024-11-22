<?php
namespace SIM\PROJECTS;
use SIM;

// Create the location custom post type 
add_action('init', __NAMESPACE__.'\loadAssets', 999);
function loadAssets(){
	SIM\registerPostTypeAndTax('project', 'projects');
}
