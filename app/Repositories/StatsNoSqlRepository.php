<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use Throwable;

/**
 * Composant NoSQL pour le stockage et l'agrégation des statistiques de commandes par menu.
 *
 * Utilise MongoDB (`MongoDB\Driver\Manager`) en priorité lorsque le serveur NoSQL
 * est joignable, avec persistance documentaire JSON de secours afin de garantir
 * la continuité de service sur les hébergements PaaS (Heroku).
 */
class StatsNoSqlRepository {
    private PDO $pdo;
    private string $mongoUri;
    private string $mongoDatabase;
    private string $fallbackFile;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->mongoUri = (string) (getenv('MONGODB_URI') ?: 'mongodb://mongo:27017');
        $this->mongoDatabase = (string) (getenv('MONGODB_DATABASE') ?: 'vite_et_gourmand_stats');
        $this->fallbackFile = __DIR__ . '/../../storage/nosql/orders_stats.json';
    }

    /**
     * Synchronise les documents de commandes dans la collection NoSQL (MongoDB + fichier JSON documentaire).
     *
     * @return array{engine: string, syncedCount: int}
     */
    public function syncFromRelational(): array {
        $documents = $this->fetchRelationalDocuments();

        $this->writeFallbackDocuments($documents);

        if ($this->syncToMongo($documents)) {
            return [
                'engine' => 'MongoDB (' . $this->mongoDatabase . '.orders_stats)',
                'syncedCount' => count($documents)
            ];
        }

        return [
            'engine' => 'NoSQL Document Store (JSON)',
            'syncedCount' => count($documents)
        ];
    }

    /**
     * Calcule les statistiques de commandes et de chiffre d'affaires par menu à partir de la base NoSQL.
     *
     * @return array{
     *     engine: string,
     *     stats: array<int, array{
     *         id_menu: int,
     *         titre: string,
     *         prix_personne: float,
     *         nb_commandes: int,
     *         total_personnes: int,
     *         chiffre_affaires: float
     *     }>
     * }
     */
    public function getRevenueStats(
        ?int $menuId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $syncInfo = $this->syncFromRelational();
        $menus = $this->fetchAllMenusCatalog();

        $mongoDocs = $this->readFromMongo();
        if ($mongoDocs !== null) {
            $documents = $mongoDocs;
            $engine = 'MongoDB (' . $this->mongoDatabase . '.orders_stats)';
        } else {
            $documents = $this->readFallbackDocuments();
            $engine = $syncInfo['engine'];
        }

        $aggregated = [];
        foreach ($menus as $idMenu => $menuMeta) {
            if ($menuId !== null && $menuId > 0 && $idMenu !== $menuId) {
                continue;
            }

            $aggregated[$idMenu] = [
                'id_menu' => $idMenu,
                'titre' => $menuMeta['titre'],
                'prix_personne' => $menuMeta['prix_personne'],
                'nb_commandes' => 0,
                'total_personnes' => 0,
                'chiffre_affaires' => 0.0
            ];
        }

        foreach ($documents as $doc) {
            $docMenuId = (int) ($doc['menu_id'] ?? 0);
            $docStatus = (string) ($doc['statut'] ?? '');
            $docDeliveryDate = (string) ($doc['date_livraison'] ?? '');

            if ($docStatus === 'Annulée') {
                continue;
            }

            if ($menuId !== null && $menuId > 0 && $docMenuId !== $menuId) {
                continue;
            }

            if ($startDate !== null && $startDate !== '' && $docDeliveryDate < $startDate) {
                continue;
            }

            if ($endDate !== null && $endDate !== '' && $docDeliveryDate > $endDate) {
                continue;
            }

            if (!isset($aggregated[$docMenuId])) {
                $aggregated[$docMenuId] = [
                    'id_menu' => $docMenuId,
                    'titre' => (string) ($doc['menu_titre'] ?? 'Menu #' . $docMenuId),
                    'prix_personne' => (float) ($doc['prix_personne'] ?? 0.0),
                    'nb_commandes' => 0,
                    'total_personnes' => 0,
                    'chiffre_affaires' => 0.0
                ];
            }

            $aggregated[$docMenuId]['nb_commandes']++;
            $aggregated[$docMenuId]['total_personnes'] += (int) ($doc['qte_personnes'] ?? 0);
            $aggregated[$docMenuId]['chiffre_affaires'] = round(
                $aggregated[$docMenuId]['chiffre_affaires'] + (float) ($doc['prix_total'] ?? 0.0),
                2
            );
        }

        $stats = array_values($aggregated);

        usort($stats, static function (array $a, array $b): int {
            if ($b['chiffre_affaires'] !== $a['chiffre_affaires']) {
                return $b['chiffre_affaires'] <=> $a['chiffre_affaires'];
            }
            if ($b['nb_commandes'] !== $a['nb_commandes']) {
                return $b['nb_commandes'] <=> $a['nb_commandes'];
            }
            return $a['id_menu'] <=> $b['id_menu'];
        });

        return [
            'engine' => $engine,
            'stats' => $stats
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchRelationalDocuments(): array {
        $sql = '
            SELECT
                c.id_commande,
                c.menu AS menu_id,
                m.titre AS menu_titre,
                m.prix_personne,
                c.date_create,
                c.date_livraison,
                c.qte_personnes,
                c.prix_menus,
                c.prix_livraison,
                c.reduction_groupe,
                c.frais_kilometriques,
                c.prix_total,
                c.statut
            FROM commandes c
            INNER JOIN menus m
                ON c.menu = m.id_menu
            ORDER BY c.id_commande ASC
        ';

        $stmt = $this->pdo->query($sql);
        $documents = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $documents[] = [
                '_id' => (int) $row['id_commande'],
                'order_id' => (int) $row['id_commande'],
                'menu_id' => (int) $row['menu_id'],
                'menu_titre' => (string) $row['menu_titre'],
                'prix_personne' => (float) $row['prix_personne'],
                'date_create' => (string) $row['date_create'],
                'date_livraison' => (string) $row['date_livraison'],
                'qte_personnes' => (int) $row['qte_personnes'],
                'prix_menus' => (float) $row['prix_menus'],
                'prix_livraison' => (float) $row['prix_livraison'],
                'reduction_groupe' => (float) $row['reduction_groupe'],
                'frais_kilometriques' => (float) $row['frais_kilometriques'],
                'prix_total' => (float) $row['prix_total'],
                'statut' => (string) $row['statut'],
                'synced_at' => gmdate('c')
            ];
        }

        return $documents;
    }

    /**
     * @return array<int, array{titre: string, prix_personne: float}>
     */
    private function fetchAllMenusCatalog(): array {
        $sql = '
            SELECT id_menu, titre, prix_personne
            FROM menus
            ORDER BY id_menu ASC
        ';

        $stmt = $this->pdo->query($sql);
        $catalog = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $catalog[(int) $row['id_menu']] = [
                'titre' => (string) $row['titre'],
                'prix_personne' => (float) $row['prix_personne']
            ];
        }

        return $catalog;
    }

    /**
     * @param array<int, array<string, mixed>> $documents
     */
    private function syncToMongo(array $documents): bool {
        if (!class_exists('\MongoDB\Driver\Manager') || !class_exists('\MongoDB\Driver\BulkWrite')) {
            return false;
        }

        try {
            $managerClass = '\MongoDB\Driver\Manager';
            $bulkClass = '\MongoDB\Driver\BulkWrite';

            $manager = new $managerClass($this->mongoUri, [
                'connectTimeoutMS' => 1500,
                'serverSelectionTimeoutMS' => 1500
            ]);

            $bulk = new $bulkClass();
            $bulk->delete([]);

            foreach ($documents as $doc) {
                $bulk->insert($doc);
            }

            $namespace = $this->mongoDatabase . '.orders_stats';
            $manager->executeBulkWrite($namespace, $bulk);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @return ?array<int, array<string, mixed>>
     */
    private function readFromMongo(): ?array {
        if (!class_exists('\MongoDB\Driver\Manager') || !class_exists('\MongoDB\Driver\Query')) {
            return null;
        }

        try {
            $managerClass = '\MongoDB\Driver\Manager';
            $queryClass = '\MongoDB\Driver\Query';

            $manager = new $managerClass($this->mongoUri, [
                'connectTimeoutMS' => 1500,
                'serverSelectionTimeoutMS' => 1500
            ]);

            $query = new $queryClass([]);
            $namespace = $this->mongoDatabase . '.orders_stats';
            $cursor = $manager->executeQuery($namespace, $query);

            $documents = [];
            foreach ($cursor as $document) {
                $documents[] = (array) $document;
            }

            return $documents;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param array<int, array<string, mixed>> $documents
     */
    private function writeFallbackDocuments(array $documents): void {
        $dir = dirname($this->fallbackFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $payload = json_encode(
            [
                'collection' => 'orders_stats',
                'updated_at' => gmdate('c'),
                'documents' => $documents
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        if ($payload !== false) {
            @file_put_contents($this->fallbackFile, $payload);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readFallbackDocuments(): array {
        if (!is_file($this->fallbackFile)) {
            return [];
        }

        $raw = @file_get_contents($this->fallbackFile);
        if (!is_string($raw) || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || !isset($decoded['documents']) || !is_array($decoded['documents'])) {
            return [];
        }

        return $decoded['documents'];
    }
}

