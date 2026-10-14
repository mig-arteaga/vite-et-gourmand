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
}