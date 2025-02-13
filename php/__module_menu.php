<?php
namespace SIM\PROJECTS;
use SIM;

const MODULE_VERSION		= '8.0.7';
//module slug is the same as grandparent folder name
DEFINE(__NAMESPACE__.'\MODULE_SLUG', strtolower(basename(dirname(__DIR__))));

DEFINE(__NAMESPACE__.'\MODULE_PATH', plugin_dir_path(__DIR__));

// check for dependicies
add_filter('sim_submenu_projects_description',  __NAMESPACE__.'\moduleDescription');
function moduleDescription($description){
	ob_start();

	?>
	<p>
		<strong>Auto created page:</strong><br>
		<a href='<?php echo home_url('/projects');?>'>Projects</a><br>
	</p>
	<?php

	return $description.ob_get_clean();
}

//run on module activation
add_filter('sim_module_projects_after_save',  __NAMESPACE__.'\moduleUpdated');
function moduleUpdated($options){
	flush_rewrite_rules( true );

	return $options;
}