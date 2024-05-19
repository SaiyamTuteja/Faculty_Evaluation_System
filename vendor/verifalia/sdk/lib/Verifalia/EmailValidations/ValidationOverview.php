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
 */ /** @noinspection PhpUnused */

namespace Verifalia\EmailValidations {

    use DateInterval;
    use DateTime;

    /**
     * Provides an overview of an email validation job.
     * @see Validation
     */
    class ValidationOverview
	{
        /**
         * @var string The unique identifier assigned to the validation job.
         */
		public $id;

        /**
         * @var DateTime The date and time this validation job has been submitted to Verifalia.
         */
		public $submittedOn;

        /**
         * @var ?DateTime The date and time when this validation job was ultimately completed, if applicable.
         */
        public $completedOn;

        /**
         * @var ?int The priority (speed) of a validation job relative to the parent Verifalia account.
         * If there are multiple concurrent validation jobs in an account, this value allows you to adjust the processing
         * speed of a specific job in comparison to others. The valid range for this priority spans from `0` (lowest) to
         * `255` (highest), with `127` representing normal priority. If not specified, Verifalia processes all concurrent
         * validation jobs for an account at the same speed.
         */
        public $priority;

        /**
         * @var string An optional custom name to the validation job for personal reference.
         */
        public $name;

        /**
         * @var string The unique identifier of the Verifalia user who submitted the validation job.
         */
        public $owner;

        /**
         * @var string The IP address of the client that submitted the validation job.
         */
        public $clientIP;

        /**
         * @var DateTime The date and time when the validation job was created.
         */
        public $createdOn;

        /**
         * @var string A reference to the quality level against which this job was validated. Quality levels determine how Verifalia validates
         *  email addresses, including whether and how the automatic reprocessing logic occurs (for transient statuses) and the
         *  verification timeouts settings.
         *  Use one of `Standard`, `High` or `Extreme` or a custom quality level ID if you have one (custom quality levels
         *  are available to premium plans only).
         * @see QualityLevelName for a list of the advertised quality levels names.
         */
        public $quality;

        /**
         * @var DateInterval the data retention period for this verification job in Verifalia. After
         * this specified period, the job will be automatically deleted. If set to null, the service defaults to the
         * retention period associated with the user or browser app submitting the job. A verification job can be
         * deleted at any time before its retention period using the `delete()` function. The configured retention
         * period, if specified, must be within the range of 5 minutes to 30 days.
         */
        public $retention;

        /**
         * @var string The strategy employed by Verifalia to identify duplicate email addresses during a multiple items
         * validation process. Duplicated items (after the first occurrence) will have the `Duplicate` status.
         * @see DeduplicationMode for a list of the supported deduplication strategies.
         */
        public $deduplication;

        /**
         * @var string The processing status for the validation job.
         * @see ValidationStatus for a list of the supported deduplication strategies.
         */
        public $status;

        /**
         * @var int The number of entries the validation job contains.
         */
        public $noOfEntries;

        /**
         * @var ?ValidationProgress The completion progress of the validation job, if available.
         */
        public $progress;
	}
}
