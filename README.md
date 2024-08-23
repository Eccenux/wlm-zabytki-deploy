# Deployment Script

This project provides a PHP-based deployment script that allows you to automate the deployment of WLM project.

The script is designed to securely deploy files by validating access tokens. Separe access tokens are provided for a test installation and main installation.

## Prerequisites

- PHP installed on the server.
- Git installed on the server.
- Access to a web server that can execute PHP scripts.
- A public GitHub repository.

## Configuration

### `.config.php`

The deployment script requires a configuration file named `.config.php` in the root directory. This file should contain the following information:

```php
<?php
return [
	'git_address' => 'https://github.com/yourusername/your-repository.git',
	'deploy_path_testing' => '/path/to/your/testing/environment',
	'deploy_path_main' => '/path/to/your/production/environment',
	'token_testing' => 'your_testing_token',
	'token_main' => 'your_production_token'
];
