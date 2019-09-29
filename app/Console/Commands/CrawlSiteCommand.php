<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CrawlService;
use App\Timer;
use Log;

class CrawlSiteCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "crawl:site {url?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Crawls a web site";

    /**
     * Instance of GuzzleHttp.
     *
     * @var \App\Services\CrawlService
     */
    protected $service;

    /**
     * Instance of Timer.
     *
     * @var \App\Timer
     */
    protected $timer;

    /**
     * Create a new command instance.
     *
     * @param \App\Services\CrawlService $service
     * @return void
     */
    public function __construct(CrawlService $service, Timer $timer)
    {
    	$this->service = $service;

    	$this->timer = $timer;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	try {
	    	if (!empty($this->argument('url'))) {
	    		$this->crawlSite($this->argument('url'));

				unset($this->timer);
	    	} else {
	    		$this->error('Please provide a valid url.');
	    	}
    	} catch (\Throwable $t) {
    		$this->error($this->argument('url') . ' was not successfully crawled.');

    		Log::error('An error occured while communicating to the server.');
    	}
   	}

   	/**
   	 * Determine whether the site is crawled for the first time
   	 * or it will be crawled for updated pages.
   	 *
   	 * @param  string $url
   	 * @return void
   	 */
   	private function crawlSite($url)
   	{
   		$this->service->checkIfAlreadyCrawled(parse_url($url)['host']) ? $this->crawlAgainCheckForUpdates($url) : $this->crawlSiteFirstTime($url);
   	}

   	/**
   	 * Crawl site and store its pages.
   	 *
   	 * @param  string $url
   	 * @return void
   	 */
   	private function crawlSiteFirstTime($url)
   	{
   		$bar = $this->output->createProgressBar();

		$bar->start();

		$this->service->crawlSite($url, $bar);

		$bar->finish();

		$this->info("\n\n" . $url . ' was successfully crawled.');
   	}

   	/**
   	 * Crawl site and check for updated pages.
   	 *
   	 * @param  string $url
   	 * @return void
   	 */
   	private function crawlAgainCheckForUpdates($url)
   	{
		if ($this->confirm('This site is already crawled. Do you wish to crawl it again? [y|N]')) {
			$bar = $this->output->createProgressBar();

			$bar->start();

			$this->service->checkSiteForUpdates(parse_url($url)['host'], $bar);

			$updatedPages = $this->service->retrieveUpdatedPages();

			if (!empty($updatedPages)) {
				$this->info("\n\n" . 'The following pages were updated:');

				foreach($updatedPages as $page) {
					$this->info($page);
				}
			} else {
				$this->info("\n\n" . 'Pages were not updated.');
			}

			// $bar->finish();
		} else {
			$this->info($url . ' wasn\'t crawled again.');
		}
   	}
}
