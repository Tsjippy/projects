<?php
namespace SIM\PROJECTS;
use SIM;

const MODULE_VERSION		= '8.0.3';
//module slug is the same as grandparent folder name
DEFINE(__NAMESPACE__.'\MODULE_SLUG', strtolower(basename(dirname(__DIR__))));

// check for dependicies
add_filter('sim_submenu_description', function($description, $moduleSlug){
	//module slug should be the same as the constant
	if($moduleSlug != MODULE_SLUG)	{
		return $description;
	}

	ob_start();

	?>
	<p>
		<strong>Auto created page:</strong><br>
		<a href='<?php echo home_url('/projects');?>'>Projects</a><br>
	</p>
	<?php

	return $description.ob_get_clean();
}, 10, 2);

//run on module activation
add_filter('sim_module_updated', function($options, $moduleSlug){
	//module slug should be the same as grandparent folder name
	if($moduleSlug != MODULE_SLUG){
		return $options;
	}

	flush_rewrite_rules( true );

	return $options;
}, 10, 2);