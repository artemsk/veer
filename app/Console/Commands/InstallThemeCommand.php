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

        /* Default path for theme files */
        protected $path = '/vendor/artemsk/veer-themes';

        protected $actually_did_it = false;

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
                
                $siteId = $this->argument('siteId');
                $theme = $this->argument('theme');

                if($this->option('files') == true) $this->copyThemeFiles($theme);

                if($this->option('config') == true) $this->copyThemeConfigs($siteId, $theme);

                if($this->option('enable') == true) {
                    $this->info('* Enable theme.');

                    $this->enableTheme($siteId, $theme);

                    $this->actually_did_it = true;
                }

                if($this->actually_did_it != true) $this->info('ERROR: Nothing to do. Please choose at least one option.');

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
                        ['files', null, InputOption::VALUE_NONE, 'Copy theme files to folders.', null],
                        ['config', null, InputOption::VALUE_NONE, 'Install theme configuration & components.', null],
			['enable', null, InputOption::VALUE_NONE, 'Enable theme after install.', null],
                        ['path', null, InputOption::VALUE_OPTIONAL, 'Path to theme files', null],
		];
	}

        protected function failed($error)
        {
            $message = [
                "empty" => "ERROR: install.json is empty",
                "404" => "ERROR: install.json not found",
            ];

            if(array_key_exists($error, $message)) $this->info($message[$error]);
        }


        protected function copyThemeFiles($theme)
        {
            $this->info('* Copy theme files to app folders: ');

            if($this->option('path')) $this->path = $this->option('path');

            $themePath = base_path().$this->path.'/'.$theme;

            $this->info('  - components');
            \File::copyDirectory($themePath.'/app/Components', base_path().'/app/Components');
            $this->info('  - events');
            \File::copyDirectory($themePath.'/app/Events', base_path().'/app/Events');
            $this->info('  - queues');
            \File::copyDirectory($themePath.'/app/Queues', base_path().'/app/Queues');
            $this->info('  - assets');
            \File::copyDirectory($themePath.'/assets', base_path().'/public/assets/themes/'.$theme);
            $this->info('  - views');
            \File::copyDirectory($themePath.'/views', base_path().'/resources/themes/'.$theme);
            $this->info('  - language files');
            \File::copyDirectory($themePath.'/lang', base_path().'/resources/lang');

            $this->actually_did_it = true;
        }

        protected function copyThemeConfigs($siteId, $theme)
        {
            // get json
            if(!file_exists(public_path().'/'.config('veer.assets_path').'/'.$theme."/install.json")) return $this->failed('404');
            
            $settingsFile = json_decode(
                \File::get(public_path().'/'.config('veer.assets_path').'/'.$theme."/install.json")
                );

            if(empty($settingsFile)) return $this->failed('empty');

            $this->info('* Configuration cards.');

            \Eloquent::unguard();

            $this->setThemeConfiguration(data_get($settingsFile, 'config', array()), $siteId, $theme);

            $this->info('* Components cards.');

            $this->setThemeComponents(data_get($settingsFile, 'components', array()), $siteId, $theme);

            $this->info('* Events cards.');

            $this->setThemeEvents(data_get($settingsFile, 'events', array()), $siteId, $theme);

            $this->info('* Jobs.');

            $this->setThemeJobs(data_get($settingsFile, 'queues', array()));

            $this->actually_did_it = true;
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
            } // TODO: $config - array
        }

        protected function setThemeComponents($components, $siteId, $theme)
        {
            foreach($components as $key => $component)
            {
                foreach(is_array($component)? $component : array($component) as $oneComponent) {
                    $c = \Veer\Models\Component::firstOrCreate(["route_name" => $oneComponent,
                        "components_type" => "functions", "components_src" => $key, "sites_id" => $siteId]);

                    $c->theme = $theme;
                    $c->save();
                }
            }
        }

        protected function setThemeEvents($events, $siteId, $theme)
        {
            foreach($events as $key => $event)
            {
                foreach(is_array($event)? $event : array($event) as $oneEvent) {
                    $c = \Veer\Models\Component::firstOrCreate(["route_name" => $oneEvent,
                        "components_type" => "events", "components_src" => $key, "sites_id" => $siteId]);

                    $c->theme = $theme;
                    $c->save();
                }
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
