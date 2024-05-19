<?php

/*
 * MIT License
 *
 * Copyright (c) 2005-2024 Cobisi Research - https://verifalia.com/
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Verifalia\EmailValidations {

    /**
     * Provides optional configuration settings for waiting on the completion of an email validation job.
     */
	class WaitOptions
	{
        /**
         * @var int Defines how much time (in milliseconds) to ask the Verifalia API to wait for the completion of the job
         * on the server side, during the initial job submission request.
         */
        public $submissionWaitTime = 30 * 1000;

        /**
         * @var int Defines how much time (in milliseconds) to ask the Verifalia API to wait for the completion of the job
         * on the server side, during any of the polling requests.
         */
        public $pollWaitTime = 30 * 1000;

        /**
         * @var ?callable A callable which eventually receives completion progress updates for an email validation job.
         * It accepts a ValidationOverview, with the current email validation job overview.
         * @see ValidationOverview
         */
        public $progress = null;

        /**
         * @var WaitOptions Indicates that the library should automatically wait for the email validation to complete, using the default
         * wait times.
         */
        public static $default;

        /**
         * @var WaitOptions Indicates that the library should not wait for the email validation to complete.
         */
        public static $noWait;

        /**
         * DO NOT CALL THIS FUNCTION DIRECTLY. Static constructor for `WaitOptions` (sort of).
         */
        public static function __constructStatic() {
            self::$default = new WaitOptions();

            self::$noWait = new WaitOptions();
            self::$noWait->submissionWaitTime = 0;
            self::$noWait->pollWaitTime = 0;
        }

        /**
         * Initializes new configuration settings for waiting on the completion of an email validation job.
         *
         * @param callable|null $progress A callable which eventually receives completion progress updates for an email
         * validation job. It accepts a ValidationOverview, with the current email validation job overview.
         * @see ValidationOverview
         */
		public function __construct(callable $progress = null)
		{
			$this->progress = $progress;
		}

		function waitForNextPoll($validationOverview)
		{
			// Observe the ETA if we have one, otherwise a delay given the formula: max(5, min(30, 2^(log(noOfEntries, 10) - 1)))

			$delay = max(5, min(30, pow(2, log10($validationOverview->noOfEntries) - 1)));

			if (property_exists($validationOverview, 'progress') && property_exists($validationOverview->progress, 'estimatedTimeRemaining')) {
				preg_match("/^(?:(\d*?)\.)?(\d{2})\:(\d{2})\:(\d{2})(?:\.(\d*?))?$/", $validationOverview->progress->estimatedTimeRemaining, $timespanMatch);

				if (!empty($timespanMatch)) {
					$hours = $timespanMatch[2];
					$minutes = $timespanMatch[3];
					$seconds = $timespanMatch[4];
	
					// Calculate the delay (in seconds)
	
					$delay = $seconds;
					$delay += $minutes * 60;
					$delay += $hours * 3600;

					// TODO: Follow the ETA more precisely: as a safety net, we are constraining it to a maximum of 30s for now.
	
					$delay = max(5, min(30, $delay));
				}
			}

			sleep($delay);
		}
	}

    // Invokes the static constructor for this class (sort of)

    WaitOptions::__constructStatic();
}
