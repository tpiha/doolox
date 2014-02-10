<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DooloxCron extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'doolox:cron';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
        $dir = base_path() . '/';
        $local = base_path() . '/latest.zip';
        $remote = 'http://wordpress.org/latest.zip';

        $dirPath = $dir . 'wordpress/';

        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
        }

        rmdir($dirPath);

        file_put_contents($local, fopen($remote, 'r'));

        $zip = new ZipArchive;
        $res = $zip->open($local);
        $zip->extractTo($dir);
        $zip->close();

        unlink($local);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
