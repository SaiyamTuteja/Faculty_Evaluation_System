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

namespace Verifalia\Security {

    use GuzzleHttp\RequestOptions;
    use Verifalia\Exceptions\VerifaliaException;
    use Verifalia\Internal\Rest\MultiplexedRestClient;

    /**
     * Allows to authenticate against the Verifalia API using HTTP basic auth.
     */
    class UsernamePasswordAuthenticationProvider implements AuthenticationProvider
    {
        private $username;
        private $password;

        /**
         * Initializes the authentication provider using the specified username-password credentials.
         *
         * While authenticating with your Verifalia main account credentials is possible, it is strongly advised
         *  to create one or more users (formerly known as sub-accounts) with just the required permissions, for improved
         *  security. To create a new user or manage existing ones, please visit https://verifalia.com/client-area#/users
         *
         * @param string $username The username of the Verifalia user to authenticate with.
         * @param string $password The password of the Verifalia user to authenticate with.
         */
        public function __construct(string $username, string $password)
        {
            $this->username = $username;
            $this->password = $password;
        }

        public function authenticate(MultiplexedRestClient $restClient, &$requestOptions)
        {
            $requestOptions = array_merge($requestOptions, [
                RequestOptions::AUTH => [
                    $this->username,
                    $this->password
                ]
            ]);
        }

        /**
         * @throws VerifaliaException
         */
        public function handleUnauthorizedRequest(MultiplexedRestClient $restClient)
        {
            throw new VerifaliaException("Can't authenticate to Verifalia using the provided username and password: please check your credentials and retry.");
        }
    }
}
