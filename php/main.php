<?php
namespace TSJIPPY\PROJECTS;
use TSJIPPY;

// Create the location custom post type 
add_action('init', __NAMESPACE__.'\loadAssets', 999);
function loadAssets(){
	TSJIPPY\registerPostTypeAndTax('project', 'projects');
}
