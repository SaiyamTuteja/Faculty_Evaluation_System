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

namespace Verifalia {

    use InvalidArgumentException;
    use Verifalia\Credits\CreditsRestClient;
    use Verifalia\EmailValidations\EmailValidationsRestClient;
    use Verifalia\Internal\Rest\MultiplexedRestClient;
    use Verifalia\Security\ClientCertificateAuthenticationProvider;
    use Verifalia\Security\UsernamePasswordAuthenticationProvider;

    /**
     * HTTPS-based REST client for Verifalia. Once initialized, use
     * the `emailValidations` field to verify email addresses and manage email verification jobs and the `credits`
     * field to manage the credits for the Verifalia account.
     */
    class VerifaliaRestClient
    {
        const DEFAULT_BASE_URIS = [
            'https://api-1.verifalia.com/',
            'https://api-2.verifalia.com/',
            'https://api-3.verifalia.com/'
        ];

        const DEFAULT_CCA_BASE_URIS = [
            'https://api-cca-1.verifalia.com/',
            'https://api-cca-2.verifalia.com/',
            'https://api-cca-3.verifalia.com/'
        ];

        /**
         * Allows to manage the credits for the Verifalia account.
         */
        public $credits;

        /**
         * Allows to verify email addresses and manage email verification jobs using the Verifalia service.
         */
        public $emailValidations;

        /**
         * Initializes a new HTTPS-based REST client for Verifalia with the specified options. Once initialized, use
         * the `emailValidations` field to verify email addresses and manage email verification jobs and the `credits`
         * field to manage the credits for the Verifalia account.
         *
         * #### Username-password authentication
         *
         * Here is an example showing how to initialize a `VerifaliaRestClient` instance using a username/password credentials
         * pair for a user:
         *
         *     $verifalia = new VerifaliaRestClient([
         *         'username' => 'your-username-here',
         *         'password' => 'your-password-here'
         *     ]);
         *
         * While authenticating with your Verifalia main account credentials is possible, it is strongly advised
         * to create one or more users (formerly known as sub-accounts) with just the required permissions, for improved
         * security. To create a new user or manage existing ones, please visit https://verifalia.com/client-area#/users
         *
         * #### X.509 client-certificate authentication
         *
         * Here is an example showing how to initialize a `VerifaliaRestClient` instance using an X.590 client-certificate
         * for a user:
         *
         *      $verifalia = new VerifaliaRestClient([
         *          'certificate' => '/home/gfring/Documents/lospollos.full.pem'
         *      ]);
         *
         * #### Other options
         * Configuration settings include the following options:
         *
         * - username: The username of the Verifalia user to authenticate with.
         * - password: The password of the Verifalia user to authenticate with.
         * - baseUris: The base URIs of the Verifalia API.
         * - authentication-provider: An implementation of `AuthenticationProvider` used to authenticate against the
         * Verifalia API.
         *
         * @param array $options VerifaliaRestClient configuration settings.
         * @see VerifaliaRestClientOptions for a list of available options.
         */
        public function __construct(array $options)
        {
            // Check the provided options

            if ($options === null) {
                throw new InvalidArgumentException('options is null');
            }

            // Custom base URIs

            if (array_key_exists(VerifaliaRestClientOptions::BASE_URIS, $options)) {
                $baseUris = $options[VerifaliaRestClientOptions::BASE_URIS];
            }

            // Authentication settings

            if (array_key_exists(VerifaliaRestClientOptions::AUTHENTICATION_PROVIDER, $options)) {
                $authenticationProvider = $options[VerifaliaRestClientOptions::AUTHENTICATION_PROVIDER];
            }
            else {
                if (array_key_exists(VerifaliaRestClientOptions::CERTIFICATE, $options)) {
                    $authenticationProvider = new ClientCertificateAuthenticationProvider(
                        $options[VerifaliaRestClientOptions::CERTIFICATE]
                    );

                    // Default base URIs for client-certificate authentication

                    if (empty($baseUris)) {
                        $baseUris = self::DEFAULT_CCA_BASE_URIS;
                    }
                } else {
                    if (!array_key_exists(VerifaliaRestClientOptions::USERNAME, $options)) {
                        throw new InvalidArgumentException("username is null or empty: please visit https://verifalia.com/client-area to set up a new user, if you don't have one.");
                    }

                    if (!array_key_exists(VerifaliaRestClientOptions::PASSWORD, $options)) {
                        throw new InvalidArgumentException("password is null or empty: please visit https://verifalia.com/client-area to set up a new user, if you don't have one.");
                    }

                    $authenticationProvider = new UsernamePasswordAuthenticationProvider(
                        $options[VerifaliaRestClientOptions::USERNAME],
                        $options[VerifaliaRestClientOptions::PASSWORD]
                    );
                }
            }

            if (empty($baseUris)) {
                $baseUris = self::DEFAULT_BASE_URIS;
            }

            // Set up the underlying REST client

            $restClient = new MultiplexedRestClient(
                $baseUris,
                $authenticationProvider
            );

            $this->credits = new CreditsRestClient($restClient);
            $this->emailValidations = new EmailValidationsRestClient($restClient);
        }
    }
}
