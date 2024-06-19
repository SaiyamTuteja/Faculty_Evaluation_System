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

namespace Verifalia\Internal\Rest {

    use GuzzleHttp\Client;
    use GuzzleHttp\RequestOptions;
    use Psr\Http\Message\ResponseInterface;
    use Throwable;
    use Verifalia\Exceptions\VerifaliaException;
    use Verifalia\Security\AuthenticationProvider;

    /**
     * FOR INTERNAL USE ONLY. Represents a multiplexed REST client for the Verifalia API.
     */
    class MultiplexedRestClient
    {
        const PACKAGE_VERSION = '3.0';
        const DEFAULT_API_VERSION = 'v2.5';

        // Supported HTTP status codes

        const HTTP_STATUS_OK = 200;
        const HTTP_STATUS_ACCEPTED = 202;
        const HTTP_STATUS_UNAUTHORIZED = 401;
        const HTTP_STATUS_PAYMENT_REQUIRED = 402;
        const HTTP_STATUS_FORBIDDEN = 403;
        const HTTP_STATUS_NOT_FOUND = 404;
        const HTTP_STATUS_GONE = 410;

        // Supported HTTP methods

        const HTTP_METHOD_GET = 'GET';
        const HTTP_METHOD_POST = 'POST';
        const HTTP_METHOD_PUT = 'PUT';
        const HTTP_METHOD_DELETE = 'DELETE';

        private $authenticationProvider;
        private $shuffledBaseUris;

        public function __construct(array $baseUris, AuthenticationProvider $authenticationProvider)
        {
            $this->shuffledBaseUris = $baseUris;
            shuffle($this->shuffledBaseUris);

            $this->authenticationProvider = $authenticationProvider;
        }

        /**
         * Invokes the specified REST resource.
         *
         * @param string $method
         * @param string $relativePath
         * @param string|array|null $query
         * @param ?mixed $data
         * @param InvocationOptions|null $options
         * @return ResponseInterface
         * @throws VerifaliaException
         */
        public function invoke(string $method, string $relativePath, $query = null, $data = null, InvocationOptions $options = null): ResponseInterface
        {
            $errors = [];

            // Cycle among the base URIs

            foreach ($this->shuffledBaseUris as $baseUri) {
                $underlyingClient = new Client([
                    'base_uri' => $baseUri . self::DEFAULT_API_VERSION . '/',
                ]);

                $requestOptions = [
                    RequestOptions::ALLOW_REDIRECTS => false,
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Accept-Encoding' => 'gzip',
                        'User-Agent' => 'verifalia-rest-client/php/' . self::PACKAGE_VERSION . '/' . phpversion()
                    ],
                    RequestOptions::HTTP_ERRORS => false
                ];

                if ($options === null || !$options->skipAuthentication) {
                    $this->authenticationProvider->authenticate($this, $requestOptions);
                }

                if ($query !== null) {
                    $requestOptions[RequestOptions::QUERY] = $query;
                }

                if ($method === self::HTTP_METHOD_POST || $method === self::HTTP_METHOD_PUT) {
                    if ($data !== null) {
                        $requestOptions[RequestOptions::JSON] = $data;
                    }
                }

                // Additional options

                if ($options !== null && $options->requestOptions !== null) {
                    $requestOptions = array_merge($requestOptions, $options->requestOptions);
                }

                // Execute the request against the current API endpoint

                $response = null;

                try {
                    $response = $underlyingClient->request(
                        $method,
                        $relativePath,
                        $requestOptions
                    );
                } catch (Throwable $e) {
                    // Records the error and continue cycling, hoping the next endpoint will handle the request

                    $errors[] = $e;
                    continue;
                }

                // Records an error (and continue cycling) in the event the status code is a 5xx

                if ($response->getStatusCode() >= 500 && $response->getStatusCode() <= 599) {
                    $errors[] = new VerifaliaException('Status code is ' . $response->getStatusCode());
                    continue;
                }

                // If the request is unauthorized, give the authentication provider a chance to remediate (on a subsequent attempt)

                if ($response->getStatusCode() == self::HTTP_STATUS_UNAUTHORIZED) {
                    $this->authenticationProvider->handleUnauthorizedRequest($this);

                    $errors[] = new VerifaliaException("Can't authenticate to Verifalia using the provided credentials (will retry in the next attempt).");
                    continue;
                }

                // Fails on the first occurrence of an HTTP 403 status code

                if ($response->getStatusCode() === self::HTTP_STATUS_FORBIDDEN) {
                    throw new VerifaliaException('Authentication error (HTTP ' . $response->getStatusCode() . '): ' . $response->getBody());
                }

                return $response;
            }

            // We have iterated all the base URIs at this point, so we should report the issue

            throw new VerifaliaException('All the endpoints are unreachable. ' . join(',', $errors));
        }
    }
}
