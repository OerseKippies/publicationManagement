<?php

declare(strict_types=1);

namespace PubM\Domain\Service;

final class HealthEngineService
{
    /** @param array<string, mixed> $registry */
    /** @return list<array<string, mixed>> */
    public function analyze(array $registry): array
    {
        $findings = [];
        $status = strtoupper((string) ($registry['registryStatus'] ?? 'DRAFT'));
        $url = trim((string) ($registry['publicationUrl'] ?? ''));
        $adRef = trim((string) ($registry['advertisementReference'] ?? ''));
        $renewalDate = $registry['renewalDate'] ?? null;

        if ($status === 'PUBLISHED' && $url === '') {
            $findings[] = $this->finding('MISSING_URL', 'HIGH', 'Missing publication URL', 'Published listing has no URL — add Marktplaats/2dehands link.');
        }

        if ($renewalDate !== null && strtotime((string) $renewalDate) < time() && in_array($status, ['PUBLISHED', 'STALE'], true)) {
            $findings[] = $this->finding('RENEWAL_OVERDUE', 'HIGH', 'Renewal overdue', 'Renewal date has passed — renew or archive the publication.');
        }

        if ($status === 'EXPIRED') {
            $findings[] = $this->finding('EXPIRED_PUBLICATION', 'MEDIUM', 'Publication expired', 'Listing is expired — renew or remove from platform.');
        }

        if ($adRef === '') {
            $findings[] = $this->finding('MISSING_ADVERTISEMENT', 'HIGH', 'Missing advertisement reference', 'Publication has no linked adM advertisement.');
        }

        if ($status === 'PUBLISHED' && $renewalDate === null) {
            $findings[] = $this->finding('ORPHAN_PUBLICATION', 'LOW', 'No renewal schedule', 'Published listing has no renewal date set.');
        }

        $updatedAt = strtotime((string) ($registry['updatedAt'] ?? 'now'));
        if ($status === 'PUBLISHED' && $updatedAt !== false && (time() - $updatedAt) > 45 * 86400) {
            $findings[] = $this->finding('STALE_PUBLICATION', 'MEDIUM', 'Publication stale', 'No updates in 45+ days — review listing content and photos.');
        }

        return $findings;
    }

    /** @return array<string, mixed> */
    private function finding(string $code, string $severity, string $title, string $detail): array
    {
        return ['code' => $code, 'severity' => $severity, 'title' => $title, 'detail' => $detail];
    }
}
