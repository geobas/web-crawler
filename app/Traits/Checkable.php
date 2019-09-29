<?php

namespace App\Traits;

trait Checkable
{
	/**
	 * Check if one or more pages were updated.
	 *
	 * @param  \GuzzleHttp\Psr7\Response $response
	 * @param  string 					 $page
	 * @return void
	 */
	private function checkIfPageUpdated($response, $page)
	{
		if ($response->getStatusCode() == 200 && !in_array($page, $this->pagesCrawled)) {
			$body = $this->getBodyContents($response);

			$this->pagesCrawled[] = $page;

			if ($this->storedPages[$page] !== trim(preg_replace('/\s\s+|\t|\n/', '', $body))) {
				$this->updatedPages[] = $page;
			}

			$links = $this->getPageLinks($body);

			$this->followLinkedPages('check', $links);
		}
	}

	/**
	 * Retrieve all updated pages.
	 *
	 * @return array
	 */
	public function retrieveUpdatedPages()
	{
		return $this->updatedPages;
	}
}
