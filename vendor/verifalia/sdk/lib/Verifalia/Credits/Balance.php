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
    use DateInterval;

    /**
     * The credits balance for the Verifalia account.
     */
    class Balance
    {
        /**
         * @var double The number of credit packs (non-expiring credits) available for the account.
         * Visit https://verifalia.com/client-area#/credits/add to add credit packs to your Verifalia account.
         */
        public $creditPacks;

        /**
         * @var ?double The number of free daily credits available for the account.
         * The allocation of free daily credits depends on the plan associated with your Verifalia account; visit
         * https://verifalia.com/client-area#/account/change-plan to change your plan.
         */
        public $freeCredits;

        /**
         * @var ?DateInterval The time it takes for the free daily credits to reset.
         */
        public $freeCreditsResetIn;

        /**
         * @param float $creditPacks
         * @param float|null $freeCredits
         * @param DateInterval|null $freeCreditsResetIn
         */
        public function __construct(float $creditPacks, float $freeCredits, DateInterval $freeCreditsResetIn)
        {
            $this->creditPacks = $creditPacks;
            $this->freeCredits = $freeCredits;
            $this->freeCreditsResetIn = $freeCreditsResetIn;
        }
    }
}