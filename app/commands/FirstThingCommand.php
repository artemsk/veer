<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FirstThingCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'veer:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'First things firsts.';

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
		$this->info('Veer is starting up...');
		$this->info('');
		
		// Run migrations
		if($this->option('migrate') == true) {
			$this->info('- Setting up database & tables...');
			$this->call('migrate');
		}
		
		$this->info('');
		$this->info('- Adding url to sites table...');
		$this->info('');
		
		$site = new \Veer\Models\Site();
		$site->url = $this->argument('url');
		$site->on_off = 1;
		$site->save();
		
		// TODO: default configuration?
		
		$this->info('- Registering administrator...');
		$this->info('');
		
		$email = $this->ask('What is your email?');
		
		$password = $this->secret('What is the password?');
		
		$this->info('');
		
		$user = new \Veer\Models\User;
        $user->email = $email;
        $user->password = $password;		
		$user->sites_id = $site->id;
		$user->save();
		
		$admin = new Veer\Models\UserAdmin;
        $admin->save();
        $user->administrator()->save($admin); 
		
		$this->call('cache:clear');
		
		$this->info('Congratulations. Everything is done.');
		$this->info('Continue here: ');
		$this->info('');
		
		$this->info($this->argument('url').'/admin/sites');
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
			array('url', InputArgument::REQUIRED, 'Installation url to start things up. with http://'),
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
			array('migrate', null, InputOption::VALUE_NONE, 'Run migrations if you have not already done it.', null),
		);
	}

}
