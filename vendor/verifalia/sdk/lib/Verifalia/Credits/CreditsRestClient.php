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

namespace Verifalia\Credits {

    use Verifalia\Internal\ParserUtils;
    use Verifalia\Exceptions\VerifaliaException;
	use Verifalia\Internal\Rest\MultiplexedRestClient;

	/**
	 * Manages credit packs, daily free credits and usage consumption for the Verifalia account.
	 */
	class CreditsRestClient
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
         * Returns the current credits balance for the Verifalia account.
         *
         * @return Balance The credits balance for the Verifalia account.
         * @throws VerifaliaException
         */
		public function getBalance(): Balance
        {
			$response = $this->restClient->invoke(MultiplexedRestClient::HTTP_METHOD_GET, "credits/balance");
			$statusCode = $response->getStatusCode();
			$body = $response->getBody();

			switch ($statusCode) {
				case MultiplexedRestClient::HTTP_STATUS_OK: {
                    $balance = json_decode($body);

                    // Mapping

                    if (!empty($balance->freeCreditsResetIn)) {
                        $balance->freeCreditsResetIn = ParserUtils::timeSpanStringToDateInterval($balance->freeCreditsResetIn);
                    }

                    return new Balance($balance->creditPacks,
                        $balance->freeCredits,
                        $balance->freeCreditsResetIn);
                }

				default:
					throw new VerifaliaException("Unexpected HTTP status code $statusCode. Body: $body");
			}
		}
	}
}
