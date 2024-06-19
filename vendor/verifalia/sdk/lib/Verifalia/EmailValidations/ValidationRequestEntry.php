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

    /**
     * A single item of a `ValidationRequest` containing an email address to validate.
     * @see ValidationRequest
     */
	class ValidationRequestEntry
	{
        /**
         * @var string The input string to validate, which should represent an email address.
         */
		public $inputData;

        /**
         * @var ?string An optional, custom string which is passed back upon completing the validation job.
         * Setting this value is useful in the event you wish to have a custom reference of this `ValidationRequestEntry`
         * with something else (for example, a record in your database).
         * This value accepts a string with a maximum length of 50 characters.
         */
		public $custom = null;

        /**
         * @param string $inputData The input string to validate, which should represent an email address.
         * @param ?string $custom An optional, custom string which is passed back upon completing the validation job.
         */
		public function __construct(string $inputData, string $custom = null)
		{
			$this->inputData = $inputData;
			$this->custom = $custom;
		}
	}
}