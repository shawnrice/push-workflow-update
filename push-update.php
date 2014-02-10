<?php

namespace CFPropertyList;

/**
 * Require CFPropertyList
 */
require_once(__DIR__.'/Libraries/CFPropertyList/classes/CFPropertyList/CFPropertyList.php');
// require_once(__DIR__.'/check-server-status.php');
/**
 * Require David Ferguson's Workflows class
 */
require_once('Libraries/workflows.php');

// Avoid collisions
if (! isset( $push ) ) {
	// Escape the new Workflow object so it doesn't collide withthe CFPropertyList namespace
	$push = new \Workflows();	
}

$cache = $push->cache();
$data  = $push->data();

if (! file_exists($cache) ) {
	mkdir($cache);
}

if (! file_exists($data) ) {
	mkdir($data);
}

if (! file_exists($data . "/settings.json")) {

} else {
	$tmp 	= json_decode(file_get_contents( $data . "/settings.json" ), TRUE);
	$user 	= $tmp['user'];
	$key 	= $tmp['key'];
	unset($tmp);
}



$workflow_directory = realpath(dirname(__DIR__));
$workflows = scandir($workflow_directory);

// Ignore these files
$ignore = array('.' , '..' , '.DS_Store');
$w = array();

foreach ($workflows as $dir) {

	if ( ! is_dir( '../' . $dir) || ( in_array( $dir , $ignore ) ) ) {
		continue;
	}
	
	$values = readPlist( '../' . $dir . '/info.plist' );

	$w[$values['name']] = $values;
	$w[$values['name']]['dir'] = $dir;	
}

// Sort the workflows by workflow name.
// The SORT_CASE_FLAG is available starting in 5.4, so run the workflow without
// it in OS X Mountain Lion or lesser.
if (PHP_MINOR_VERSION >= 4) {
	ksort($w , SORT_NATURAL | SORT_FLAG_CASE );
} else {
	ksort($w , SORT_NATURAL );
}




function readPlist($plist) {
	if (! file_exists($plist) ) {
		return 1; // Error Code #1 is info file doesn't exist
	}
	// The files exist.

	// Construct the workflow plist objects
	$workflow = new CFPropertyList( $plist );

	// Declare an array to store the data about the info plist in.
	$info = array();

	// Convert plist object to usable array for processing
	$tmp = $workflow->toArray();

	$info['bundleid'] 		= $tmp['bundleid'];
	$info['name']	 		= $tmp['name'];
	$info['createdby'] 		= $tmp['createdby'];
	$info['disabled'] 		= $tmp['disabled'];
	$info['readme'] 		= $tmp['readme'];
	$info['webaddress'] 	= $tmp['webaddress'];
	$info['description']	= $tmp['description'];

	return $info;

}






?>