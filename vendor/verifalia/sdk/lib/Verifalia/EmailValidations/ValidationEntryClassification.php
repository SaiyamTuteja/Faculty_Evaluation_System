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
     * A classification of the validation status of an email validation entry.
     */
    class ValidationEntryClassification
    {
        /**
         * An entry marked as `Deliverable` refers to an email address which is deliverable.
         */
        const DELIVERABLE = 'Deliverable';

        /**
         * An entry marked as `Risky` refers to an email address which could be no longer valid.
         */
        const RISKY = 'Risky';

        /**
         * An entry marked as `Undeliverable` refers to an email address which is either invalid or no longer deliverable.
         */
        const UNDELIVERABLE = 'Undeliverable';

        /**
         * An entry marked as `Unknown` contains an email address whose deliverability is unknown.
         */
        const UNKNOWN = 'Unknown';
    }
}