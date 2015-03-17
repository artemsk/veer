<?php namespace Veer\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallThemeCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'veer:theme';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install theme.';

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
		$this->info('Installing theme');

                // get json
                $settingsFile = json_decode(
                    \File::get(public_path().'/'.config('veer.assets_path').'/'.$this->argument('theme')."/install.json")
                    );

                if(empty($settingsFile)) return $this->failed('empty');

                $siteId = $this->argument('siteId');
                $theme = $this->argument('theme');

                $this->info('* Configuration cards.');

                \Eloquent::unguard();
                
                $this->setThemeConfiguration(data_get($settingsFile, 'config', array()), $siteId, $theme);

                $this->info('* Components cards.');

                $this->setThemeComponents(data_get($settingsFile, 'components', array()), $siteId, $theme);

                $this->info('* Events cards.');

                $this->setThemeEvents(data_get($settingsFile, 'events', array()), $siteId, $theme);

                $this->info('* Jobs.');

                $this->setThemeJobs(data_get($settingsFile, 'queues', array()));

                if($this->option('enable') == true) {
                    $this->info('* Enable theme.');

                    $this->enableTheme($siteId, $theme);
                }

                $this->call('cache:clear');

                $this->info('Done.');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['theme', InputArgument::REQUIRED, 'Theme name.'],
                        ['siteId', InputArgument::REQUIRED, 'Site #id.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['enable', null, InputOption::VALUE_NONE, 'Enable theme after install.', null],
		];
	}

        protected function failed($error)
        {
            if($error == 'empty') $this->info('Install.json is empty');
        }

        protected function setThemeConfiguration($configuration, $siteId, $theme)
        {
            foreach($configuration as $key => $config)
            {
                if($key == "TEMPLATE") continue;

                $c = \Veer\Models\Configuration::firstOrCreate(["conf_key" => $key,
                    "sites_id" => $siteId]);

                $c->conf_val = $config;
                $c->theme = $theme;
                $c->save();
            }
        }

        protected function setThemeComponents($components, $siteId, $theme)
        {
            foreach($components as $key => $component)
            {
                $c = \Veer\Models\Component::firstOrCreate(["route_name" => array_get($component, 0),
                    "components_type" => "functions", "components_src" => $key, "sites_id" => $siteId]);

                $c->theme = $theme;
                $c->save();
            }
        }

        protected function setThemeEvents($events, $siteId, $theme)
        {
            foreach($events as $key => $event)
            {
                $c = \Veer\Models\Component::firstOrCreate(["route_name" => array_get($event, 0),
                    "components_type" => "events", "components_src" => $key, "sites_id" => $siteId]);

                $c->theme = $theme;
                $c->save();
            }
        }

        protected function setThemeJobs($queues)
        {
            foreach($queues as $key => $job)
            {
                app('veeradmin')->saveJob(array(
                    "jobs.new.start" => array_get($job, 2),
                    "jobs.new.repeat" => array_get($job, 1),
                    "jobs.new.data" =>  json_encode(array_get($job, 0)),
                    "jobs.new.classname" =>  $key
                ));
            }
        }

        protected function enableTheme($siteId, $theme)
        {
             $c = \Veer\Models\Configuration::firstOrCreate(["conf_key" => "TEMPLATE",
                        "sites_id" => $siteId]);

            $c->conf_val = $theme;
            $c->theme = $theme;
            $c->save();
        }

}
