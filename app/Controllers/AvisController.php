<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\AvisRepository;

class AvisController {
    private AvisRepository $avisRepository;

    public function __construct(AvisRepository $avisRepository) {
        $this->avisRepository = $avisRepository;
    }

    public function getValidatedReviews(): array {
        return $this->avisRepository->findByStatus('Validé');
    }

    public function getAllReviews(): array {
        return $this->avisRepository->findAll();
    }

    public function moderateReview(int $reviewId, string $status): bool {
        if ($reviewId <= 0) {
            return false;
        }

        $allowedStatuses = ['Validé', 'Refusé'];

        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        return $this->avisRepository->updateStatus($reviewId, $status);
    }
}
