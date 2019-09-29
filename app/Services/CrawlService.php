<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Traits\{Valitable,Storable,Checkable,Helpers};
use App\{Site,Page};
use Log;

class CrawlService
{
	use Valitable,
		Storable,
		Checkable,
		Helpers;

    /**
     * Instance of GuzzleHttp Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Instance of Site.
     *
     * @var \App\Site
     */
    protected $site;

    /**
     * Instance of Page.
     *
     * @var \App\Page
     */
    protected $page;

    /**
     * Pages crawled.
     *
     * @var array
     */
    protected $pagesCrawled = [];

    /**
     * Pages that are stored.
     *
     * @var array
     */
    protected $storedPages = [];

    /**
     * Pages that are updated.
     *
     * @var array
     */
    protected $updatedPages = [];

    /**
     * Instance of ProgressBar.
     *
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    protected $bar;

	/**
	 * Initialize service.
	 *
	 * @param \GuzzleHttp\Client $client
	 * @param \App\Site 	     $site
     * @param \App\Page          $page
	 */
    public function __construct(Client $client, Site $site, Page $page)
    {
    	$this->client = $client;

    	$this->site = $site;

    	$this->page = $page;
    }

    /**
     * Make an HTTP request to provided url and crawl page(s).
     *
     * @param  string $url
     * @param  \Symfony\Component\Console\Helper\ProgressBar $bar
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return void
     */
	public function crawlSite($url, $bar)
	{
		try {
			$response = $this->client->get($url);

			if ($response->getStatusCode() == 200) {

				$this->site = $this->site->create([
					// 'url' => parse_url(preg_replace('#^https?://#', '', $url))['host']
					'url' => parse_url($url)['host'],
				]);

				$this->bar = $bar;

				$this->storePage($response, '/');
			}
		} catch (GuzzleException $e) {
    		Log::error('GuzzleHttp Error: ' . $e->getMessage());

    		throw $e;
    	}
	}

	/**
	 * Crawl site for updated web pages.
	 *
	 * @param  string $url
	 * @param  \Symfony\Component\Console\Helper\ProgressBar $bar
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @return void
	 */
	public function checkSiteForUpdates($url, $bar)
	{
		try {
			$this->storedPages = $this->site->retrievePages($url);

			$response = $this->client->get($url);

			if ($response->getStatusCode() == 200) {
				$this->site = Site::whereUrl($url)->first();

				$this->bar = $bar;

				$this->checkIfPageUpdated($response, '/');
			}
		} catch (GuzzleException $e) {
    		Log::error('GuzzleHttp Error: ' . $e->getMessage());

    		throw $e;
    	}
	}
}
