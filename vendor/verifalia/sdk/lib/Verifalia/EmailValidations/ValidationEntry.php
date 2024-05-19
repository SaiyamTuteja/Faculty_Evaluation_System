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

    /**
     * Represents a single validated entry within a completed email validation job.
     * @see Validation
     */
    class ValidationEntry
	{
        /**
         * @var int The index of this entry within its `Validation` container. This property is particularly useful when
         * the API returns a filtered view of the items.
         * @see Validation
         */
        public $index;

        /**
         * @var string The input string being validated.
         */
        public $inputData;

        /**
         * @var ?string A custom, optional string which is passed back upon completing the validation. To pass back and
         * forth a custom value, use the `custom` property of `ValidationRequestEntry`.
         * @see ValidationRequestEntry
         */
        public $custom;

        /**
         * @var ?DateTime The date this entry has been completed, if available.
         */
        public $completedOn;

        /**
         * @var ?string The email address without any comments or folding white space. Returns null if the input data is
         * not a syntactically valid email address.
         */
        public $emailAddress;

        /**
         * @var ?string Gets the domain part of the email address, converted to ASCII if needed and with comments and folding
         * white spaces stripped off.
         * The ASCII encoding is performed using the standard <a href="http://en.wikipedia.org/wiki/Punycode">punycode algorithm</a>.
         * To get the domain part without any ASCII encoding, use `emailAddressDomainPart`.
         */
        public $asciiEmailAddressDomainPart;

        /**
         * @var ?string Gets the local part of the email address, without comments and folding white spaces.
         */
        public $emailAddressLocalPart;

        /**
         * @var ?string Gets the domain part of the email address, without comments and folding white spaces.
         * If the ASCII-only (punycode) version of the domain part is needed, use `asciiEmailAddressDomainPart`.
         */
        public $emailAddressDomainPart;

        /**
         * @var ?bool If true, the email address has an international domain name.
         */
        public $hasInternationalDomainName;

        /**
         * @var ?bool If true, the email address has an international mailbox name.
         */
        public $hasInternationalMailboxName;

        /**
         * @var ?bool If true, the email address comes from a disposable email address (DEA) provider.
         * <a href="https://verifalia.com/help/email-validations/what-is-a-disposable-email-address-dea">Learn more about
         * disposable email addresses</a>.
         */
        public $isDisposableEmailAddress;

        /**
         * @var ?bool If true, the email address comes from a free email address provider (e.g. gmail, yahoo, outlook / hotmail, ...).
         */
        public $isFreeEmailAddress;

        /**
         * @var ?bool If true, the local part of the email address is a well-known role account.
         */
        public $isRoleAccount;

        /**
         * @var string The validation status for this entry.
         * @see ValidationEntryStatus for a list of the validation statuses supported by this SDK.
         */
        public $status;

        /**
         * @var string The classification for the status of this email address. Standard values include `Deliverable`,
         * `Undeliverable`, `Risky` and `Unknown`.
         * @see ValidationEntryClassification for a list of the classifications supported by this SDK.
         */
        public $classification;

        /**
         * @var ?int The position of the character in the email address that led to the syntax validation failure.
         * Returns null if there is no syntax failure.
         */
        public $syntaxFailureIndex;

        /**
         * @var ?int The zero-based index of the first occurrence of this email address in the parent `Validation`.
         * This information is relevant when the status for this entry is `Duplicate`; duplicated items only provide
         * details such as this index and any possible `custom` values.
         * @see Validation
         */
        public $duplicateOf;

        /**
         * @var ?string[] The potential corrections for the input data, in the event Verifalia identified potential
         * typos during the verification process.
         */
        public $suggestions;
	}
}
