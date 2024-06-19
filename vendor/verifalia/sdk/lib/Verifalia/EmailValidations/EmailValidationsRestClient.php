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

    use DateTime;
    use Exception;
    use Verifalia\Internal\ParserUtils;
    use Verifalia\Exceptions\VerifaliaException;
	use Verifalia\Internal\Rest\MultiplexedRestClient;
	use Verifalia\Common\ListingCursor;
	use Verifalia\Common\Direction;

	/**
	 * Allows to submit and manage email validations using the Verifalia service.
	 */
	class EmailValidationsRestClient
	{
        /**
         * @var MultiplexedRestClient
         */
		private $restClient;

		public function __construct(MultiplexedRestClient $restClient)
		{
			$this->restClient = $restClient;
		}

        /**
         * Submits a new email validation for processing. By default, this method **waits** for the completion of the email
         * validation job. You can pass a `$waitOptions` parameter to request a different waiting behavior.
         *
         * @param string|string[]|ValidationRequestEntry[]|ValidationRequest|ValidationRequestEntry $entries One or more
         * entries (email addresses) to validate.
         * @param ?WaitOptions $waitOptions Defines the options that specify how to wait for the completion of the email
         * validation job. It can be set to `null` to wait for completion using the default options provided by the SDK,
         * or set to an instance of `WaitOptions` for advanced scenarios and progress tracking.
         * @return ?Validation An object describing the validation job.
         * @throws VerifaliaException
         */
		public function submit($entries, WaitOptions $waitOptions = null)
		{
			// Builds the input json structure

			if ($entries instanceof ValidationRequest) {
				$validation = $entries;
			} else {
				$validation = new ValidationRequest($entries);
			}

			$data = array();
            $mappedEntries = array();

            foreach ($validation->entries as $entry) {
                $mappedEntry = array('inputData' => $entry->inputData);

                if ($entry->custom !== null) {
                    $mappedEntry['custom'] = $entry->custom;
                }

                $mappedEntries[] = $mappedEntry;
            }

            $data['entries'] = $mappedEntries;

            if ($validation->name !== null) {
                $data['name'] = $validation->name;
            }

			if ($validation->deduplication !== null) {
				$data['deduplication'] = $validation->deduplication;
			}

			if ($validation->quality !== null) {
				$data['quality'] = $validation->quality;
			}

			if ($validation->priority !== null) {
				$data['priority'] = $validation->priority;
			}

			if ($validation->retention !== null) {
				$data['retention'] = ParserUtils::dateIntervalToTimeSpanString($validation->retention);
			}

            if ($validation->completionCallback !== null) {
                $callback = array("url" => $validation->completionCallback->url);

                if ($validation->completionCallback->version !== null) {
                    $callback["version"] = $validation->completionCallback->version;
                }

                if ($validation->completionCallback->skipServerCertificateValidation !== null) {
                    $callback["skipServerCertificateValidation"] = $validation->completionCallback->skipServerCertificateValidation;
                }

                $data['callback'] = $callback;
            }

            if ($waitOptions === null) {
                $waitOptions = WaitOptions::$default;
            }

			// Sends the request to the Verifalia servers

			$response = $this->restClient->invoke(
				MultiplexedRestClient::HTTP_METHOD_POST,
				"email-validations",
				[
                    'waitTime' => $waitOptions->submissionWaitTime
                ],
				$data
			);

			$statusCode = $response->getStatusCode();
			$body = $response->getBody();

			switch ($statusCode) {
				case MultiplexedRestClient::HTTP_STATUS_OK:
				case MultiplexedRestClient::HTTP_STATUS_ACCEPTED: {
						$partialValidation = $this->buildPartialValidation(json_decode($body));

						// Returns immediately if the validation has been completed or if we should not wait for it

						if ($waitOptions === WaitOptions::$noWait || $partialValidation->overview->status === ValidationStatus::COMPLETED) {
							return $this->retrieveValidationFromPartialValidation($partialValidation);
						}

						return $this->waitValidationForCompletion($partialValidation->overview, $waitOptions);
					}

				case MultiplexedRestClient::HTTP_STATUS_PAYMENT_REQUIRED:
					throw new VerifaliaException("Verifalia was unable to accept your request because of low account credit. Body: $body");

				default:
					throw new VerifaliaException("Unexpected HTTP status code $statusCode. Body: $body");
			}
		}

        /**
         * Returns an email validation job that was previously submitted for processing. By default, this method **waits**
         * for the completion of the email validation job, if applicable. You can pass a `$waitOptions` parameter to
         * request a different waiting behavior.
         *
         * @param string $id The unique identifier of the validation job to retrieve.
         * @param ?WaitOptions $waitOptions Defines the options that specify how to wait for the completion of the email
         * validation job. It can be set to `null` to wait for completion using the default options provided by the SDK,
         * or set to an instance of `WaitOptions` for advanced scenarios and progress tracking.
         * @return ?Validation An object describing the validation job.
         * @throws VerifaliaException
         * @throws Exception
         */
		public function get(string $id, WaitOptions $waitOptions = null)
		{
            if ($waitOptions === null) {
                $waitOptions = WaitOptions::$default;
            }

			$response = $this->restClient->invoke(MultiplexedRestClient::HTTP_METHOD_GET,
                "email-validations/$id",
                [
                    'waitTime' => $waitOptions->pollWaitTime
                ]);

			$statusCode = $response->getStatusCode();
			$body = $response->getBody();

			switch ($statusCode) {
				case MultiplexedRestClient::HTTP_STATUS_OK:
				case MultiplexedRestClient::HTTP_STATUS_ACCEPTED: {
						$partialValidation = $this->buildPartialValidation(json_decode($body));

						// Returns immediately if the validation has been completed or if we should not wait for it

						if ($waitOptions === WaitOptions::$noWait || $partialValidation->overview->status === ValidationStatus::COMPLETED) {
							return $this->retrieveValidationFromPartialValidation($partialValidation);
						}

						return $this->waitValidationForCompletion($partialValidation->overview, $waitOptions);
					}

				case MultiplexedRestClient::HTTP_STATUS_NOT_FOUND:
				case MultiplexedRestClient::HTTP_STATUS_GONE:
					return null;

				default:
					throw new VerifaliaException("Unexpected HTTP status code $statusCode. Body: $body");
			}
		}

        /**
         * @throws VerifaliaException
         */
        private function waitValidationForCompletion($validationOverview, WaitOptions $waitOptions)
		{
			$resultOverview = $validationOverview;

			do {
				// Fires a progress, since we are not yet completed

				if ($waitOptions->progress !== null) {
					call_user_func($waitOptions->progress, $resultOverview);
				}

				// Wait for the next polling schedule

				$waitOptions->waitForNextPoll($resultOverview);

				// Fetch the job from the API

				$result = $this->get($validationOverview->id, $waitOptions);

				if ($result === null) {
					// A null result means the validation has been deleted (or is expired) between a poll and the next one

					return null;
				}

				$resultOverview = $result->overview;

				// Returns immediately if the validation has been completed

				if ($resultOverview->status === ValidationStatus::COMPLETED) {
					return $result;
				}
			} while (true);
		}

        /**
         * @throws VerifaliaException
         * @throws Exception
         */
        private function retrieveValidationFromPartialValidation($partialValidation): Validation
        {
			$allEntries = array();

			if (property_exists($partialValidation, 'entries')) {
				$currentSegment = $partialValidation->entries;

				while ($currentSegment !== null && $currentSegment->data !== null) {
                    foreach($currentSegment->data as $entry){
                        $entry->index = (int) $entry->index;

                        if (!empty($entry->completedOn))
                        {
                            $entry->completedOn = new DateTime($entry->completedOn);
                        }

                        if (!empty($entry->hasInternationalDomainName))
                        {
                            $entry->hasInternationalDomainName = (bool) $entry->hasInternationalDomainName;
                        }

                        if (!empty($entry->hasInternationalMailboxName))
                        {
                            $entry->hasInternationalMailboxName = (bool) $entry->hasInternationalMailboxName;
                        }

                        if (!empty($entry->isDisposableEmailAddress))
                        {
                            $entry->isDisposableEmailAddress = (bool) $entry->isDisposableEmailAddress;
                        }

                        if (!empty($entry->isFreeEmailAddress))
                        {
                            $entry->isFreeEmailAddress = (bool) $entry->isFreeEmailAddress;
                        }

                        if (!empty($entry->isRoleAccount))
                        {
                            $entry->isRoleAccount = (bool) $entry->isRoleAccount;
                        }

                        if (!empty($entry->syntaxFailureIndex))
                        {
                            $entry->syntaxFailureIndex = (int) $entry->syntaxFailureIndex;
                        }

                        if (!empty($entry->duplicateOf))
                        {
                            $entry->duplicateOf = (int) $entry->duplicateOf;
                        }

                        $allEntries[] = $entry;
                    }

					if (!property_exists($currentSegment, 'meta') || !property_exists($currentSegment->meta, 'isTruncated') || $currentSegment->meta->isTruncated === false) {
						break;
					}

					$currentSegment = $this->listEntriesSegmented(
						$partialValidation->overview->id,
						new ListingCursor($currentSegment->meta->cursor)
					);
				}
			}

			return new Validation($partialValidation->overview, $allEntries);
		}

        /**
         * @throws VerifaliaException
         */
        private function listEntriesSegmented(string $id, ListingCursor $cursor)
		{
			// Generate the additional parameters, where needed

			$cursorParamName = $cursor->direction === Direction::FORWARD
				? "cursor"
				: "cursor:prev";

			$query = [
				$cursorParamName => $cursor->cursor
			];

			if ($cursor->limit > 0) {
				$query["limit"] = $cursor->limit;
			}

			$response = $this->restClient->invoke(
				MultiplexedRestClient::HTTP_METHOD_GET,
				"/email-validations/$id/entries",
				$query
			);

			$statusCode = $response->getStatusCode();
			$body = $response->getBody();

			if ($statusCode === MultiplexedRestClient::HTTP_STATUS_OK) {
				return json_decode($body)->data;
			}

			throw new VerifaliaException("Unexpected HTTP response: $statusCode $body");
		}

        /**
         * Deletes an email validation job that was previously submitted for processing.
         *
         * @param string $id The unique identifier of the email validation job to be deleted.
         * @return void
         * @throws VerifaliaException
         */
		public function delete(string $id)
		{
			// Sends the request to the Verifalia servers

			$response = $this->restClient->invoke(MultiplexedRestClient::HTTP_METHOD_DELETE, "email-validations/$id");
			$statusCode = $response->getStatusCode();

			if ($statusCode !== MultiplexedRestClient::HTTP_STATUS_OK) {
				$body = $response->getBody();

				throw new VerifaliaException("Unexpected HTTP status code $statusCode. Body: $body");
			}
		}

        /**
         * @throws Exception
         */
        private function buildPartialValidation($partialValidation)
        {
            $partialValidation->overview->createdOn = new DateTime($partialValidation->overview->createdOn);

            if (!empty($partialValidation->overview->submittedOn))
            {
                $partialValidation->overview->submittedOn = new DateTime($partialValidation->overview->submittedOn);
            }

            if (!empty($partialValidation->overview->completedOn))
            {
                $partialValidation->overview->completedOn = new DateTime($partialValidation->overview->completedOn);
            }

            if (!empty($partialValidation->overview->priority))
            {
                $partialValidation->overview->priority = (int) ($partialValidation->overview->priority);
            }

            $partialValidation->overview->noOfEntries = (int) ($partialValidation->overview->noOfEntries);
            $partialValidation->overview->retention = ParserUtils::timeSpanStringToDateInterval($partialValidation->overview->retention);

            if (!empty($partialValidation->overview->progress))
            {
                if (!empty($partialValidation->overview->progress->estimatedTimeRemaining))
                {
                    $partialValidation->overview->progress->estimatedTimeRemaining = ParserUtils::timeSpanStringToDateInterval($partialValidation->overview->progress->estimatedTimeRemaining);
                }
            }

            return $partialValidation;
        }
	}
}
