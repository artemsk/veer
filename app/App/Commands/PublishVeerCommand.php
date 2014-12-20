<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PublishVeerCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'veer:publish';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish all necessary files after install or update.';

	
	protected $packageName = "artemsk/veer-core";
	
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->info('');
		$this->info('Updating Veer...');
		$this->info('');
		
		$source = base_path()."/vendor/".$this->packageName."/src";
		
		$only = $this->option('only');
		
		// Publish config
		if($this->option('only') == "config" || empty($only)) {
			
			$this->info('- Publishing config.');

			$destination = app_path()."/config";
			
			app('files')->copyDirectory($source."/config", $destination);
		}
		
		// Publish views
		if($this->option('only') == "views" || empty($only)) {
			
			$this->info('- Publishing views.');

			$destination = app_path()."/views";
			
			app('files')->copyDirectory($source."/views", $destination);
		}
		
		// Publish assets
		if($this->option('only') == "assets" || empty($only)) {
			
			$this->info('- Publishing assets.');

			$destination = public_path();
			
			app('files')->copyDirectory($source."/assets", $destination);
		}		
		
		// Publish assets
		if($this->option('only') == "migrations" || empty($only)) {
			
			$this->info('- Publishing migrations.');

			$this->call('migrate:publish', array($this->packageName));
			
			// Run migrations
			$this->info('- Run migrations.');
			
			$this->call('migrate');
		}			
		
		$this->info('Done.');
		$this->info('');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('only', null, InputOption::VALUE_OPTIONAL, 'Publish only views|migrations|config|assets.', null),
		);
	}

}
