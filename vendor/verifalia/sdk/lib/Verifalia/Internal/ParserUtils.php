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

namespace Verifalia\Internal {

    use DateInterval;

    /**
     * FOR INTERNAL USE ONLY. Contains helper functions for the JSON serializer / deserializer.
     */
    class ParserUtils
    {
        public static function timeSpanStringToDateInterval(string $timeSpan) : DateInterval
        {
            preg_match('/^(?:(\d*)\.)?(?:(\d{1,2}))\:(?:(\d{1,2}))\:(?:(\d{1,2}))$/', $timeSpan, $matches);

            $days = isset($matches[1]) ? (int)$matches[1] : 0;
            $hours = (int)$matches[2];
            $minutes = (int)$matches[3];
            $seconds = (int)$matches[4];

            return new DateInterval("P{$days}DT{$hours}H{$minutes}M{$seconds}S");
        }

        public static function dateIntervalToTimeSpanString(DateInterval $interval) : string
        {
            if ($interval->days > 0)
            {
                return $interval->format("%d.H:I:S");
            }

            return $interval->format("H:I:S");
        }
    }
}