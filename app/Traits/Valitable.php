<?php

namespace App\Traits;

use Log;

trait Valitable
{
	/**
	 * Check if that site is previously crawled.
	 *
	 * @param  string $url
	 * @throws \Symfony\Component\HttpKernel\Exception\HttpException
	 * @return void
	 */
	public function checkIfAlreadyCrawled($url)
	{
		$domain = $this->site->where('url', '=', preg_replace('#^https?://#', '', $url))->first();

		// if (!empty($domain)) {
		// 	Log::error('Error: ' . $domain->url . ' is already crawled.');

		// 	return true;
		// }

		return !empty($domain) ? Log::error('Error: ' . $domain->url . ' is already crawled.') || true : false;

		// return false;
	}

	/**
	 * Check if link's destination is valid.
	 *
	 * @param  string  $href
	 * @return boolean
	 */
	private function isValidHref($href)
	{
		return $href != 'https://' . $this->site->url
				&& $href != 'http://' . $this->site->url
				&& $href != 'https://' . $this->site->url . '#'
				&& $href != 'http://' . $this->site->url . '#'
				&& strpos($href, '#') === false
				&& parse_url($href)['host'] == $this->site->url;
	}
}
