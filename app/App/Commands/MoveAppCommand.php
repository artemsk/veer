<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MoveAppCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'veer:move';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Move app from vendors folder.';

	
	protected $packageName = "artemsk/veer";
	
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
		$this->info('Moving...');
		$this->info('');
		
		$source = base_path()."/vendor/".$this->packageName;
		
		$only = $this->option('only');
		
		if(empty($only)) {
		
		 $destination = base_path();
		 app('files')->deleteDirectory($source."/.git");
		 app('files')->move(base_path()."/composer.json", base_path()."/composer-backup.json");
		 app('files')->copyDirectory($source, $destination);
		 app('files')->move(base_path()."/composer-backup.json", base_path()."/composer.json");	
		 
		} 
		
		// Publish config
		if($this->option('only') == "config") {
			
			$this->info('- Publishing config file.');

			$destination = app_path()."/config/veer.php";
			
			app('files')->copy($source."/app/config/veer.php", $destination);
		}
		
		// Publish views
		if($this->option('only') == "views") {
			
			$this->info('- Publishing views.');

			$destination = app_path()."/views";
			
			app('files')->copyDirectory($source."/app/views", $destination);
		}
		
		// Publish assets
		if($this->option('only') == "assets") {
			
			$this->info('- Publishing assets.');

			$destination = public_path();
			
			app('files')->copyDirectory($source."/assets", $destination);
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
