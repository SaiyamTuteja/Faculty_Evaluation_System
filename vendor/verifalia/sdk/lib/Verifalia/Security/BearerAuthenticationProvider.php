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

namespace Verifalia\Security {

    use GuzzleHttp\RequestOptions;
    use Verifalia\Exceptions\VerifaliaException;
    use Verifalia\Internal\Rest\InvocationOptions;
    use Verifalia\Internal\Rest\MultiplexedRestClient;

    /**
     * Allows to authenticate against the Verifalia API using bearer authentication.
     */
    class BearerAuthenticationProvider implements AuthenticationProvider
    {
        const MAX_NO_OF_ATTEMPTS = 3;

        private $username;
        private $password;
        private $totpTokenProvider;
        private $accessToken;

        /**
         * Initializes the authentication provider using the specified username-password credentials.
         *
         * @param string $username The username of the Verifalia user to authenticate with.
         * @param string $password The password of the Verifalia user to authenticate with.
         */
        public function __construct(string $username, string $password /*, TotpTokenProvider $totpTokenProvider = null */)
        {
            $this->username = $username;
            $this->password = $password;
            // $this->totpTokenProvider = $totpTokenProvider;
        }

        /**
         * @throws VerifaliaException
         */
        public function authenticate(MultiplexedRestClient $restClient, &$requestOptions)
        {
            if ($this->accessToken === null) {
                $authData = array(
                    'username' => $this->username,
                    'password' => $this->password
                );

                $options = new InvocationOptions();
                $options->skipAuthentication = true;

                $authResponse = $restClient->invoke(MultiplexedRestClient::HTTP_METHOD_POST,
                    'auth/tokens',
                    null,
                    $authData,
                    $options);

                if ($authResponse->getStatusCode() == MultiplexedRestClient::HTTP_STATUS_OK) {
                    $this->accessToken = json_decode($authResponse->getBody())->accessToken;

                    // TODO: Add support for MFA

//                    // Decode the JWT token, see https://www.converticacommerce.com/support-maintenance/security/php-one-liner-decode-jwt-json-web-tokens/
//
//                    $claims = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $this->accessToken)[1]))));
//
//                    // Handle the multi-factor auth (MFA) request, if needed
//
//                    if (property_exists($claims, 'verifalia:mfa')) {
//                        $totpAuthData = $this->provideAdditionalAuthFactor($restClient);
//                        $this->accessToken = $totpAuthData->accessToken;
//                    }
                }
                else {
                    throw new VerifaliaException('Invalid credentials used while attempting to retrieve a bearer auth token.');
                }
            }

            $this->addBearerAuth($requestOptions);
        }

        public function handleUnauthorizedRequest(MultiplexedRestClient $restClient)
        {
            $this->accessToken = null;
        }

        private function addBearerAuth(&$requestOptions) {
            $requestOptions = array_merge($requestOptions, [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer '.$this->accessToken
                ]
            ]);
        }

        /**
         * @throws VerifaliaException
         */
        private function provideAdditionalAuthFactor(MultiplexedRestClient $restClient) {
            if ($this->totpTokenProvider === null) {
                throw new VerifaliaException('A multi-factor authentication is required but no token provider has been provided.');
            }

            for ($idxAttempt = 0; $idxAttempt < self::MAX_NO_OF_ATTEMPTS; $idxAttempt++) {
                // Retrieve the one-time token from the configured device

                $totp = $this->totpTokenProvider->provideTotpToken();

                // Validates the provided token against the Verifalia API

                try {
                    $options = new InvocationOptions();
                    $options->skipAuthentication = true;
                    $options->requestOptions = [];

                    $this->addBearerAuth($options->requestOptions);

                    $authResponse = $restClient->invoke(MultiplexedRestClient::HTTP_METHOD_POST,
                        'auth/totp/verifications',
                        null,
                        [
                            'passCode' => $totp
                        ],
                        $options);

                    if ($authResponse->getStatusCode() == MultiplexedRestClient::HTTP_STATUS_OK) {
                        return json_decode($authResponse->getBody());
                    }
                }
                catch(VerifaliaException $ex) {
                    // Having an authorization issue is allowed here, as we are working on an TOTP token validation attempt.
                    // We will re-throw a VerifaliaException below in the even all the configured TOTP validation attempts fail.
                }
            }

            throw new VerifaliaException("Invalid TOTP token provided after ".self::MAX_NO_OF_ATTEMPTS." attempt(s): aborting the authentication.");
        }
    }

}
