<?php

namespace App\Traits;

trait Helpers
{
	/**
	 * Follows all linked web pages.
	 *
	 * @param string $action
	 * @param array  $links
	 * @return void
	 */
	private function followLinkedPages($action, $links)
	{
		if (!empty($links)) {
			foreach($links as $url) {
				preg_match_all('/href="(.*?)"/s', $url, $matches);

				$href = array_pop($matches[1]);

				if ($href != '/') {
					if (empty(parse_url($href)['host'])) {
						$href = 'https://' . $this->site->url . $href;
					}

					// dump($href);

					$response = $this->getPage($href);

					$this->bar->advance();

					if (!empty($response) && $response->getStatusCode() == 200 && $action == 'store') {
						$this->storePage($response, parse_url($href)['path']);
					} elseif (!empty($response) && $response->getStatusCode() == 200 && $action == 'check') {
						$this->checkIfPageUpdated($response, parse_url($href)['path']);
					}
				}
			}
		}
	}

	/**
	 * Return <body> contents.
	 *
	 * @param  \GuzzleHttp\Psr7\Response $response
	 * @return string
	 */
	private function getBodyContents($response)
	{
		$html = $response->getBody()->getContents();

		// $head = substr($html, 0, strpos($html, "<body>"));

		// $body = substr($html, strpos($html, "<body>"));

		preg_match("/<body[^>]*>(.*?)<\/body>/is", $html, $matches);

		return $matches[0];
	}

	/**
	 * Follow links and store page.
	 *
	 * @param  string $body
	 * @return array
	 */
	private function getPageLinks($body)
	{
		preg_match_all('/<a [^>]+>/i', $body, $links);

		return $links[0];
	}

	/**
	 * Fetch page if valid.
	 *
	 * @param  string $href
	 * @return mixed
	 */
	private function getPage($href)
	{
		return $this->isValidHref($href) ? $this->client->get($href) : null;
	}
}
