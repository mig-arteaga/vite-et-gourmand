<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\HoraireRepository;

class HoraireController {
    private HoraireRepository $horaireRepository;

    public function __construct(HoraireRepository $horaireRepository) {
        $this->horaireRepository = $horaireRepository;
    }

    public function getAll(): array {
        return $this->horaireRepository->findAll();
    }

    public function updateAll(array $submittedHoraires): void {
        foreach ($submittedHoraires as $rawId => $data) {
            $id = filter_var(
                $rawId,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if ($id === false || !is_array($data)) {
                continue;
            }

            $closed = isset($data['ferme']) && (string) $data['ferme'] === '1';

            $opening = isset($data['heure_ouverture']) && is_string($data['heure_ouverture'])
                ? trim($data['heure_ouverture'])
                : '';

            $closing = isset($data['heure_fermeture']) && is_string($data['heure_fermeture'])
                ? trim($data['heure_fermeture'])
                : '';

            if (!$closed) {
                if (
                    !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $opening) ||
                    !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $closing)
                ) {
                    continue;
                }

                if (strlen($opening) === 5) {
                    $opening .= ':00';
                }

                if (strlen($closing) === 5) {
                    $closing .= ':00';
                }
            } else {
                $opening = null;
                $closing = null;
            }

            $this->horaireRepository->update(
                (int) $id,
                $opening,
                $closing,
                $closed
            );
        }
    }
}

