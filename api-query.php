<?php

/**
 * Prepares and sends the cURL request to the API endpoint
 * @param  string $username The Packal Username
 * @param  string $key      The API Key
 * @param  string $bundle   The BundleID
 * @param  file   $workflow The workflow file
 * @return array           	An array of headers, errors, and content response
 */
function curl_request( $username, $key, $bundle, $workflow) {
	// Create the curl object at our endpoint
	$ch = curl_init("https://apidev.packal.org");

	// Create an array of options
	$options = array(
				CURLOPT_HEADER => false,
				CURLOPT_POST => true,
				CURLOPT_FRESH_CONNECT => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
				CURLOPT_POST => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_USERAGENT => "push-update-workflow-packal",
				CURLOPT_AUTOREFERER => true
				);



	// Look into CURLOPT_PROGRESSFUNCTION for possible notifications.

	// Package the workflow file as a curl file
	$file = curl_file_create($workflow, 'application/zip');

	// Create post fields
	$data = array(
			'workflow' => $file,
			'username' => $username,
			'key'      => $key,
			'bundle'   => $bundle
			);

	// Set the options
	curl_setopt_array ( $ch, $options );
	// Set the post data
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	// Execute the curl request
	$content 	= curl_exec($ch);
	// Grab any error
	$err 		= curl_errno ( $ch );
	// Grab any error message
	$errmsg 	= curl_error ( $ch );
	// Grab the headers
	$header 	= curl_getinfo ( $ch );
	// Grab the HTTP Code
	$httpCode 	= curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
	// Close the curl request
	curl_close($ch);

	// Package everything into an array
	$header 			= array();
	$header['errno'] 	= $err;
	$header['errmsg'] 	= $errmsg;
	$header['content'] 	= $content;


	return $header['content'];

}