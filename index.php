<?php
header('Content-Type: text/plain; charset=utf-8');

require 'DeployHelper.php';

// Get the deploy type and token from GET parameters
$deployType = $_GET['type'] ?? null;
$token = $_GET['token'] ?? null;

// Validate parameters
if (!$deployType || !$token) {
	http_response_code(400);
	die('[ERROR] Missing required parameters. Please provide type (test|main) and token.');
}

// Initialize DeployHelper
$deployHelper = new DeployHelper();

// Validate token
if (!$deployHelper->validateToken($deployType, $token)) {
	http_response_code(401);
	die('[ERROR] Unauthorized. Invalid token.');
}

try {
	// Run the deploy function for the given type
	$result = $deployHelper->deploy($deployType);
	if ($result !== true) {
		http_response_code(500);
		die("[ERROR] $result");
	}

	echo "Deployment completed for $deployType environment.\n";
} catch (Exception $e) {
	http_response_code(500);
	echo '[ERROR] ' . $e->getMessage();
}
