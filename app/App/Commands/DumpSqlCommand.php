<?php

use Illuminate\Console\Command;

class DumpData extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dump:data';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate a dump.sql in storage path';

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
		$mysql = [];

		$handle = fopen("php://stdin", "r");

		echo "Database Host [localhost]:";

		$mysql[0] = trim(fgets($handle));

		$mysql[0] = (empty($mysql[0])) ? 'localhost' : $mysql[0];

		echo "Database User [root]:";

		$mysql[1] = trim(fgets($handle));

		$mysql[1] = (empty($mysql[1])) ? 'root' : $mysql[1];

		echo "Database Password:";

		$mysql[2] = trim(fgets($handle));

		echo "Database Name:";

		$mysql[3] = trim(fgets($handle));

		exec("mysqldump -h " . $mysql[0] . " -u " . $mysql[1] . " -p " . $mysql[2] . " --no-create-info " . $mysql[3] . " > " . storage_path() . "/dump.sql");

		echo "Dump file has been created at. " . storage_path() . "/dump.sql";
	}

}