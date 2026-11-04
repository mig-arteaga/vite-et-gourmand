<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Exécution réservée à la ligne de commande (CLI).');
}

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\Database;
use App\Repositories\StatsNoSqlRepository;

echo "Connexion à la base de données MySQL...\n";
$pdo = Database::connect();

$sqlFile = __DIR__ . '/01-init.sql';
if (!is_file($sqlFile)) {
    fwrite(STDERR, "Fichier introuvable : {$sqlFile}\n");
    exit(1);
}

$sql = (string) file_get_contents($sqlFile);

// Supprimer les commentaires SQL sur une ligne et l'instruction USE vite_et_gourmand
// afin d'utiliser la base active (Railway ou Docker local).
$lines = preg_split('/\R/', $sql) ?: [];
$cleanedLines = [];

foreach ($lines as $line) {
    $trimmed = trim($line);
    if ($trimmed === '' || str_starts_with($trimmed, '--')) {
        continue;
    }
    if (preg_match('/^USE\s+/i', $trimmed) === 1) {
        continue;
    }
    $cleanedLines[] = $line;
}

$cleanedSql = implode("\n", $cleanedLines);
$statements = array_filter(array_map('trim', explode(';', $cleanedSql)));

echo "Réinitialisation complète des tables et données initiales (" . count($statements) . " instructions)...\n";

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

foreach ($statements as $statement) {
    if ($statement !== '') {
        $pdo->exec($statement);
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

echo "Base MySQL réinitialisée avec succès à partir de docker/mysql/01-init.sql !\n";

echo "Synchronisation de la collection NoSQL MongoDB (orders_stats)...\n";
$statsRepo = new StatsNoSqlRepository($pdo);
$syncResult = $statsRepo->syncFromRelational();
echo "NoSQL synchronisé : {$syncResult['syncedCount']} documents ({$syncResult['engine']}).\n";

