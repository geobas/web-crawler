<?php

namespace App;

use Log;

class Timer
{
	/**
	 * Time in seconds.
	 *
	 * @var null
	 */
    private $time = null;

    /**
     * Initialize timer.
     */
    public function __construct() {
        $this->time = time();

        echo "\n    ".  'Working - please wait...' . PHP_EOL;
    }

    /**
     * Calculates elapsed time when shutting down.
     */
    public function __destruct() {
        Log::info('Operation took ' . (time() - $this->time) . ' second(s).');
    }
}
