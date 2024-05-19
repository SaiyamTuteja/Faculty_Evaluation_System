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

    use DateInterval;
    use InvalidArgumentException;

    /**
     * Represents an email validation request to be submitted against the Verifalia API.
     */
    class ValidationRequest
	{
        /**
         * @var ValidationRequestEntry[] One or more `ValidationRequestEntry` containing the email addresses to validate.
         */
		public $entries;

        /**
         * @var ?string A reference to the expected results quality level for this request. Quality levels determine how Verifalia validates
         * email addresses, including whether and how the automatic reprocessing logic occurs (for transient statuses) and the
         * verification timeouts settings.
         * Use one of `Standard`, `High` or `Extreme` or a custom quality level ID if you have one (custom quality levels
         * are available to premium plans only).
         * @see QualityLevelName for a list of the advertised quality levels names.
         */
		public $quality;

        /**
         * @var ?string The strategy employed by Verifalia to identify duplicate email addresses during a multiple items
         * validation process. Duplicated items (after the first occurrence) will have the `Duplicate` status.
         * @see DeduplicationMode for a list of the supported deduplication strategies.
         */
		public $deduplication;

        /**
         * @var ?int Specifies the priority (speed) of a validation job relative to the parent Verifalia account. If
         * there are multiple concurrent validation jobs in an account, this value allows you to adjust the processing
         * speed of a specific job in comparison to others. The valid range for this priority spans from 0 (lowest) to
         * 255 (highest), with 127 representing normal priority. If not specified, Verifalia processes all concurrent
         * validation jobs for an account at the same speed.
         */
        public $priority;

        /**
         * @var ?DateInterval Defines the data retention period for this verification job in Verifalia. After
         * this specified period, the job will be automatically deleted. If set to null, the service defaults to the
         * retention period associated with the user or browser app submitting the job. A verification job can be
         * deleted at any time before its retention period using the `delete()` function. The configured retention
         * period, if specified, must be within the range of 5 minutes to 30 days.
         */
		public $retention;

        /**
         * @var ?string Allows to assign an optional custom name to the validation job for personal reference. This name
         * will be included in subsequent API calls and displayed in the Verifalia client area.
         */
        public $name;

        /**
         * @var ?CompletionCallback Allows to define an optional URL which Verifalia will invoke once the results for
         * this job are ready.
         */
        public $completionCallback;

        /**
         * Initializes a `ValidationRequest` to be submitted to the Verifalia email validation engine.
         * @param string|string[]|ValidationRequestEntry[]|ValidationRequestEntry $entries Represents one or more
         * entries to be validated. An entry can be either a `string` containing the email address to be validated or a
         * complete `ValidationRequestEntry` instance. To validate multiple entries, provide an array containing either
         * `string` or `ValidationRequestEntry` instances.
         * @see ValidationRequestEntry
         */
		public function __construct($entries)
		{
			$this->entries = array();

			if (is_array($entries)) {
				for ($x = 0; $x < count($entries); $x++) {
					$this->addEntry($entries[$x]);
				}
			} else {
				$this->addEntry($entries);
			}
		}

        /**
         * @param string|ValidationRequestEntry $entry
         * @return void
         */
		private function addEntry($entry)
		{
			if (is_string($entry)) {
				$this->entries[] = new ValidationRequestEntry($entry);
			} else if ($entry instanceof ValidationRequestEntry) {
				$this->entries[] = $entry;
			} else {
				throw new InvalidArgumentException('Invalid input entry: it must be either a string representing the email address or a ValidationRequestEntry instance.');
			}
		}
	}

}