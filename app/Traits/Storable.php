<?php

namespace App\Traits;

trait Storable
{
	/**
	 * Store details about a web page.
	 *
	 * @param  \GuzzleHttp\Psr7\Response $response
	 * @param  string 				     $page
	 * @return void
	 */
	private function storePage($response, $page)
	{
		if ($response->getStatusCode() == 200 && !in_array($page, $this->pagesCrawled)) {
			$body = $this->getBodyContents($response);

			$this->site->pages()->create([
				'name' => $page,
				'body' => trim(preg_replace('/\s\s+|\t|\n/', '', $body)),
			]);

			$this->pagesCrawled[] = $page;

			$links = $this->getPageLinks($body);

			$this->followLinkedPages('store', $links);
		}
	}
}
