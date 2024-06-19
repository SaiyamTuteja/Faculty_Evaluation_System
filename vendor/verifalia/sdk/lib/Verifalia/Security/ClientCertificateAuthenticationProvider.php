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
     * Allows to authenticate against the Verifalia API using an X.509 client certificate.
     * To learn more about X.509 client certificate authentication in Verifalia, see:
     * https://verifalia.com/help/sub-accounts/how-to-create-self-signed-client-certificate-for-tls-mutual-authentication
     */
    class ClientCertificateAuthenticationProvider implements AuthenticationProvider
    {
        /**
         * @var string|array Set to a string to specify the path to a file containing a PEM formatted client side certificate.
         * If a password is required, then set to an array containing the path to the PEM file in the first array element
         * followed by the password required for the certificate in the second array element.
         */
        private $certificate;

        /**
         * Initializes the authentication provider using the specified X.509 client certificate.
         *
         * @param string|array $certificate Set to a string to specify the path to a file containing a PEM formatted client
         * side certificate. If a password is required, then set to an array containing the path to the PEM file in the first
         * array element followed by the password required for the certificate in the second array element.
         * To learn more, see: https://verifalia.com/help/sub-accounts/how-to-create-self-signed-client-certificate-for-tls-mutual-authentication
         */
        public function __construct($certificate)
        {
            $this->certificate = $certificate;
        }

        public function authenticate(MultiplexedRestClient $restClient, &$requestOptions)
        {
            $requestOptions = array_merge($requestOptions, [
                RequestOptions::CERT => $this->certificate
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
