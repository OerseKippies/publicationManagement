<?php

declare(strict_types=1);

namespace PubM\Domain\Service;

final class RenewalEngineService
{
    /** @param list<array<string, mixed>> $renewals */
    /** @return array{today: int, thisWeek: int, overdue: int} */
    public function summarize(array $renewals): array
    {
        $today = 0;
        $thisWeek = 0;
        $overdue = 0;
        $now = time();
        $weekEnd = strtotime('+7 days', strtotime('today'));

        foreach ($renewals as $renewal) {
            $date = strtotime((string) ($renewal['renewalDate'] ?? ''));
            if ($date === false) {
                continue;
            }
            if ($date < strtotime('today')) {
                ++$overdue;
            } elseif ($date < strtotime('tomorrow')) {
                ++$today;
            } elseif ($date <= $weekEnd) {
                ++$thisWeek;
            }
        }

        return ['today' => $today, 'thisWeek' => $thisWeek, 'overdue' => $overdue];
    }
}
