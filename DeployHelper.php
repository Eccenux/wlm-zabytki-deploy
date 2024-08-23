<?php

/**
	Deploy WLM.
	
	Two main steps:
	1. Check type and token separately.
	2. Run deploy function with given type.
*/
class DeployHelper {
	private $config;
	private $repoDir = '/data/project/zabytki/repo';  // Working directory for the cloned repository

	public function __construct($configPath = '.config.php') {
		$this->config = require $configPath;
	}

	private function execCommand($command, $info="") {
		echo empty($info) ? "Executing: $command" : $info;
		echo "\n";
		$output = shell_exec($command);
		echo $output;
		echo "\n";
		return $output;
	}

	public function validateToken($deployType, $providedToken) {
		if ($deployType === 'test' && $providedToken === $this->config['token_testing']) {
			return true;
		} elseif ($deployType === 'main' && $providedToken === $this->config['token_main']) {
			return true;
		}
		return false;
	}

	public function deploy($deployType) {
		$deployPath = $this->getDeployPath($deployType);

		// Step 1: Clone or pull the repository
		$this->cloneOrPullRepo();
		if (!is_dir("{$this->repoDir}/.git")) {
			return "Failed to get repo files?";
		}
		if (!is_dir("{$this->repoDir}/app-prod/assets")) {
			return "The repo doesn't contain assets!";
		}

		// Step 2: Create the deploy directory if it doesn't exist
		$this->createDeployDirectory($deployPath);

		// Step 3: Remove the assets subdirectory in the deploy path
		$this->execCommand("rm -rf $deployPath/assets", "Cleanup deploy dir.");

		// Step 4: Copy files from the repo's app-prod directory to the deploy path
		$this->execCommand("cp -r {$this->repoDir}/app-prod/* $deployPath/", "Copy new files.");
		if (!is_dir("$deployPath/assets")) {
			return "Failed to copy files?";
		}
		
		return true;
	}

	private function createDeployDirectory($deployPath) {
		if (!is_dir($deployPath)) {
			if (mkdir($deployPath, 0777, true)) {
				echo "Successfully created deploy directory: $deployPath\n";
			} else {
				throw new Exception("Failed to create deploy directory: $deployPath");
			}
		}
	}	

	private function cloneOrPullRepo() {
		if (!is_dir($this->repoDir)) {
			// Clone the repository if it doesn't exist
			$this->execCommand("git clone {$this->config['git_address']} $this->repoDir");
		} else {
			// Pull the latest changes if the repository already exists
			$this->execCommand("cd $this->repoDir && git pull");
		}
	}

	private function getDeployPath($deployType) {
		if ($deployType === 'test') {
			return $this->config['deploy_path_testing'];
		} elseif ($deployType === 'main') {
			return $this->config['deploy_path_main'];
		} else {
			throw new Exception("Invalid deploy type: $deployType");
		}
	}
}
