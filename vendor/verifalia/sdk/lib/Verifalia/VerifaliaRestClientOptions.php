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

    use Verifalia\Security\AuthenticationProvider;
    use Verifalia\Security\BearerAuthenticationProvider;
    use Verifalia\Security\ClientCertificateAuthenticationProvider;
    use Verifalia\Security\UsernamePasswordAuthenticationProvider;

    /**
     * This class contains a list of Verifalia client options.
     */
    class VerifaliaRestClientOptions
	{
		/**
		 * username: (string) The username of the Verifalia user to authenticate with. While authenticating with your
		 * Verifalia main account credentials is possible, it is strongly advised to create one or more users (formerly
		 * known as sub-accounts) with just the required permissions, for improved security. To create a new user or
		 * manage existing ones, please visit https://verifalia.com/client-area#/users
		 */
		const USERNAME = 'username';

		/**
		 * password: (string) The password of the Verifalia user to authenticate with. While authenticating with your
		 * Verifalia main account credentials is possible, it is strongly advised to create one or more users (formerly
		 * known as sub-accounts) with just the required permissions, for improved security. To create a new user or
		 * manage existing ones, please visit https://verifalia.com/client-area#/users
		 */
		const PASSWORD = 'password';

		/**
		 * baseUris: (string[]) The base URIs of the Verifalia API - please do *NOT* set these unless you have been
		 * instructed to do so by the Verifalia support team.
		 */
		const BASE_URIS = 'baseUris';

        /**
         * certificate: (string|array) Set to a string to specify the path to a file containing a PEM formatted X.509
         * client certificate.
         * If a password is required, then set to an array containing the path to the PEM file in the first array element
         * followed by the password required for the certificate in the second array element.
         * To learn more about X.509 client certificate authentication in Verifalia, see:
         * https://verifalia.com/help/sub-accounts/how-to-create-self-signed-client-certificate-for-tls-mutual-authentication
         */
        const CERTIFICATE = 'certificate';

        /**
         * authentication-provider: (AuthenticationProvider) Allows to specify an `AuthenticationProvider` to use while
         * authenticating against the Verifalia API.
         * @see AuthenticationProvider
         * @see UsernamePasswordAuthenticationProvider
         * @see BearerAuthenticationProvider
         * @see ClientCertificateAuthenticationProvider
         */
        const AUTHENTICATION_PROVIDER = 'authentication-provider';
	}
}
