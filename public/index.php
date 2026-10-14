<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Database;
use App\Controllers\MenuController;
use App\Controllers\AuthController;
use App\Controllers\ContactController;
use App\Controllers\CommandeController;
use App\Controllers\DistanceController;
use App\Repositories\MenuRepository;
use App\Repositories\ThemeRepository;
use App\Repositories\RegimeRepository;
use App\Repositories\CommandeRepository;
use App\Repositories\UtilisateurRepository;
use App\Services\AuthService;
use App\Services\DistanceService;
use App\Services\CommandeService;

$router = new Router();

$router->get('/', function () {
    \App\Core\Session::start();

    $pdo = Database::connect();

    $avisController = new \App\Controllers\AvisController(
        new \App\Repositories\AvisRepository($pdo)
    );

    $reviews = $avisController->getValidatedReviews();

    require __DIR__ . '/../views/pages/index.php';
});

$router->get('/menus', function () {
    \App\Core\Session::start();

    $pdo = Database::connect();

    $menuController = new \App\Controllers\MenuController(
        new \App\Repositories\MenuRepository($pdo),
        new \App\Repositories\ThemeRepository($pdo),
        new \App\Repositories\RegimeRepository($pdo)
    );

    $menuService = new \App\Services\MenuService();

    $menus = $menuController->getAll();

    require __DIR__ . '/../views/pages/menus.php';
});

$router->get('/contact', function () {
    \App\Core\Session::start();

    require __DIR__ . '/../views/pages/contact.php';
});

$router->get('/nous', function () {
    \App\Core\Session::start();

    require __DIR__ . '/../views/pages/nous.php';
});

$router->get('/login', function () {
    \App\Core\Session::start();

    require __DIR__ . '/../views/pages/login.php';
});

$router->get('/logout', function () {
    \App\Core\Session::start();

    $pdo = Database::connect();

    $authController = new AuthController(
        new AuthService(
            new UtilisateurRepository($pdo)
        )
    );

    $redirect = $authController->logout();

    header("Location: $redirect");
    exit;
});

$router->get('/commande', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé',
        'Utilisateur'
    ]);

    $menuId = $_GET['menu'] ?? null;

    if ($menuId === null || filter_var($menuId, FILTER_VALIDATE_INT) === false || (int) $menuId <= 0) {
        http_response_code(400);
        echo 'Menu invalide ou manquant';
        return;
    }

    $userId = $_SESSION['user_id'];

    $pdo = Database::connect();

    $commandeController = new CommandeController(
        new MenuRepository($pdo),
        new UtilisateurRepository($pdo),
        new CommandeRepository($pdo),
        new CommandeService()
    );

    $detail = $commandeController->getOrderData(
        (int) $menuId,
        (int) $userId
    );

    $menu = $detail['menu'];
    $user = $detail['user'];
    $menuList = $detail['menuList'];

    require __DIR__ . '/../views/pages/commande.php';
});

$router->get('/compte', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé',
        'Utilisateur'
    ]);

    require __DIR__ . '/../views/pages/compte.php';
});

$router->get('/employe', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé'
    ]);

    require __DIR__ . '/../views/pages/employe.php';
});

$router->get('/admin', function () {
    \App\Core\Session::requireRoles(['Administrateur']);

    require __DIR__ . '/../views/pages/admin.php';
});

$router->get('/cgv', function () {
    \App\Core\Session::start();

    require __DIR__ . '/../views/pages/cgv.php';
});

$router->get('/legal', function () {
    \App\Core\Session::start();

    require __DIR__ . '/../views/pages/legal.php';
});

$router->get('/menus/filter-values', function () {
    $pdo = Database::connect();

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo)
    );

    $result = $menuController->getFilterValues();

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result);
});

$router->post('/login', function () {
    \App\Core\Session::start();

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $pdo = Database::connect();

    $authController = new \App\Controllers\AuthController(
        new \App\Services\AuthService(
            new \App\Repositories\UtilisateurRepository($pdo)
        )
    );

    $redirect = $authController->login(
        $_POST['email'] ?? '',
        $_POST['password'] ?? ''
    );

    if ($redirect !== null) {
        header("Location: $redirect");
        exit;
    }

    echo "Mail ou mot de passe incorrect";
});

$router->post('/menus/filter', function () {
    $pdo = Database::connect();

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo)
    );

    $menuService = new \App\Services\MenuService();

    $filters = [
        'minPrice' => (int) ($_POST['minPrice'] ?? 0),
        'maxPrice' => (int) ($_POST['maxPrice'] ?? 0),
        'theme' => $_POST['theme'] ?? '',
        'diet' => $_POST['diet'] ?? '',
        'people' => (int) ($_POST['people'] ?? 0)
    ];

    $menus = $menuController->getFiltered($filters);

    if (count($menus) === 0) {
        require __DIR__ . '/../views/menus/no-results.php';
        return;
    }

    foreach ($menus as $menu) {
        require __DIR__ . '/../views/menus/menu.php';
    }
});

$router->post('/menus/detail', function () {
    $menuId = (int) ($_POST['menu'] ?? 0);

    $pdo = Database::connect();

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo)
    );

    $detail = $menuController->getDetailData($menuId);

    header('Content-Type: application/json; charset=utf-8');

    if ($detail === null) {
        http_response_code(404);
        echo json_encode([
            'error' => 'Menu introuvable'
        ]);
        return;
    }

    echo json_encode($detail);
});

$router->post('/commande/change-menu', function () {
    $menuId = (int) ($_POST['menuId'] ?? 0);

    $pdo = Database::connect();

    $commandeController = new CommandeController(
        new MenuRepository($pdo),
        new UtilisateurRepository($pdo),
        new CommandeRepository($pdo),
        new CommandeService()
    );

    $detail = $commandeController->getChangeMenuData($menuId);

    header('Content-Type: application/json; charset=utf-8');

    if ($detail === null) {
        http_response_code(404);
        echo json_encode([
            'error' => 'Menu introuvable'
        ]);
        return;
    }

    echo json_encode($detail);
});

$router->post('/commande/distance', function () {
    \App\Core\Session::start();

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'error' => 'Requête invalide (CSRF)'
        ]);

        return;
    }

    $now = time();

    $_SESSION['distance_requests'] = array_filter(
        $_SESSION['distance_requests'] ?? [],
        fn($timestamp) => $timestamp > $now - 60
    );

    if (count($_SESSION['distance_requests']) >= 10) {
        http_response_code(429);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'error' => 'Trop de demandes. Veuillez patienter une minute.'
        ]);
        return;
    }

    $_SESSION['distance_requests'][] = $now;

    $address = is_string($_POST['address'] ?? null)
        ? trim($_POST['address'])
        : '';

    $city = is_string($_POST['city'] ?? null)
        ? trim($_POST['city'])
        : '';

    $zipcode = is_string($_POST['zipcode'] ?? null)
        ? trim($_POST['zipcode'])
        : '';

    $distanceController = new DistanceController(
        new DistanceService()
    );

    header('Content-Type: application/json; charset=utf-8');

    try {
        $result = $distanceController->getDistance(
            $address,
            $city,
            $zipcode
        );

        echo json_encode($result);
    } catch (\InvalidArgumentException $e) {
        http_response_code(400);

        echo json_encode([
            'error' => $e->getMessage()
        ]);
    } catch (\RuntimeException $e) {
        http_response_code(502);

        echo json_encode([
            'error' => 'Impossible de calculer la distance'
        ]);
    }
});

$router->post('/commande/create', function () {
    \App\Core\Session::start();

    header('Content-Type: application/json; charset=utf-8');

    // Vérification CSRF
    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);

        echo json_encode([
            'error' => 'Requête invalide (CSRF)'
        ]);

        return;
    }

    // Validation du jeton de commande
    $orderToken = $_POST['order_token'] ?? '';

    if (!is_string($orderToken) || !preg_match('/\A[a-f0-9]{32}\z/', $orderToken)) {
        http_response_code(422);
        echo json_encode(['error' => 'Jeton de commande invalide']);
        return;
    }

    // Vérification de la connexion
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        http_response_code(401);

        echo json_encode([
            'error' => 'Utilisateur non connecté'
        ]);

        return;
    }

    // Vérification du jeton de commande dans la base de données
    $pdo = \App\Core\Database::connect();
    $commandeRepository = new CommandeRepository($pdo);

    $existingOrderId = $commandeRepository->findIdByOrderToken(
        $orderToken,
        (int) $userId
    );

    if ($existingOrderId !== null) {
        echo json_encode([
            'success' => true,
            'message' => 'Commande déjà enregistrée',
            'commandeId' => $existingOrderId
        ]);
        return;
    }

    // Champs attendus
    $requiredFields = [
        'address',
        'city',
        'date',
        'hour',
        'people',
        'menu'
    ];

    foreach ($requiredFields as $field) {
        if (
            !isset($_POST[$field]) ||
            !is_string($_POST[$field]) ||
            trim($_POST[$field]) === ''
        ) {
            http_response_code(422);

            echo json_encode([
                'error' => "Champ obligatoire invalide : $field"
            ]);

            return;
        }
    }

    // Validation du menu
    $menuId = filter_var(
        $_POST['menu'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    // Validation du nombre de personnes
    $people = filter_var(
        $_POST['people'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($menuId === false || $people === false) {
        http_response_code(422);

        echo json_encode([
            'error' => 'Menu ou nombre de personnes invalide'
        ]);

        return;
    }

    // Récupérer le menu depuis la base de données
    $menuRepository = new MenuRepository($pdo);
    $menu = $menuRepository->findById($menuId);

    if ($menu === null) {
        http_response_code(404);

        echo json_encode([
            'error' => 'Menu introuvable'
        ]);

        return;
    }

    // Vérifier le nombre de personnes
    $commandeService = new CommandeService();

    if (!$commandeService->isPeopleCountValid(
        $people,
        $menu->getMinPeople(),
        $menu->getStock()
    )) {
        http_response_code(422);

        echo json_encode([
            'error' => 'Nombre de personnes invalide ou stock insuffisant'
        ]);

        return;
    }

    // Validation du format de la date
    $date = DateTimeImmutable::createFromFormat(
        '!Y-m-d',
        $_POST['date']
    );

    if (
        $date === false ||
        $date->format('Y-m-d') !== $_POST['date']
    ) {
        http_response_code(422);
        echo json_encode([
            'error' => 'Format de date invalide'
        ]);
        return;
    }

    // Validation du délai de livraison
    if (!$commandeService->isDeliveryDateValid(
        $date,
        $menu->getDaysBefore()
    )) {
        http_response_code(422);
        echo json_encode([
            'error' => 'Date de livraison non autorisée pour ce menu'
        ]);
        return;
    }

    // Validation du format de l'heure
    $time = $_POST['hour'];

    if (
        !preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $time) ||
        !$commandeService->isDeliveryTimeValid($time)
    ) {
        http_response_code(422);

        echo json_encode([
            'error' => 'Heure de livraison invalide (08:30 - 22:30)'
        ]);

        return;
    }

    // Récupération des informations de livraison
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $zipcode = trim($_POST['zipcode'] ?? '');

    // Validation de l'adresse et calcul de la distance
    try {
        $distanceService = new DistanceService();

        $distanceKm = $distanceService->getDistanceKm(
            $address,
            $city,
            $zipcode
        );

    } catch (\InvalidArgumentException $e) {
        http_response_code(422);

        echo json_encode([
            'error' => $e->getMessage()
        ]);

        return;

    } catch (\RuntimeException $e) {
        error_log($e->getMessage());

        http_response_code(502);

        echo json_encode([
            'error' => 'Impossible de vérifier l’adresse de livraison'
        ]);

        return;
    }

    // Calcul du prix définitif côté serveur
    $prices = $commandeService->calculatePrice(
        $menu->getUnitPrice(),
        $people,
        $menu->getMinPeople(),
        $distanceKm
    );

    // Récupération de l'utilisateur connecté
    $utilisateurRepository = new UtilisateurRepository($pdo);

    $user = $utilisateurRepository->findById((int) $userId);

    if ($user === null) {
        http_response_code(404);

        echo json_encode([
            'error' => 'Utilisateur introuvable'
        ]);

        return;
    }

    // Informations complémentaires de la commande
    $equipmentLoan = isset($_POST['equipmentLoan'])
        && $_POST['equipmentLoan'] === '1';

    $returned = false;

    $status = 'En attente';

    $createdAt = new \DateTimeImmutable();

    // Création de l'objet Commande
    $commande = new \App\Models\Commande(
        0,                              // ID attribué par MySQL
        $orderToken,                    // Jeton de commande
        $user,                          // Utilisateur connecté
        $menu,                          // Menu sélectionné
        $createdAt,                     // Date de création
        $date,                          // Date de livraison
        $time,                          // Heure de livraison
        $address,                       // Adresse
        $city,                          // Ville
        $zipcode,                       // Code postal (facultatif)
        $people,                        // Nombre de personnes
        $prices['menuPrice'],           // Prix des menus
        $prices['deliveryFee'],         // Frais de livraison
        $prices['groupDiscount'],       // Réduction de groupe
        $prices['distanceFee'],         // Frais kilométriques
        $prices['totalPrice'],          // Prix total
        $equipmentLoan,                 // Prêt de matériel
        $returned,                      // Matériel restitué
        $status                         // Statut initial
    );

    try {
        // Début de la transaction
        $pdo->beginTransaction();

        // Réservation du stock
        if (!$menuRepository->reserveStock($menuId, $people)) {
            $pdo->rollBack();

            http_response_code(409);
            echo json_encode([
                'error' => 'Stock insuffisant pour ce menu'
            ]);
            return;
        }

        // Enregistrement de la commande
        $commandeRepository = new CommandeRepository($pdo);
        $commandeId = $commandeRepository->create($commande);

        // Validation de la transaction
        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Commande enregistrée avec succès',
            'commandeId' => $commandeId
        ]);

    } catch (\Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        // Une autre requête a peut-être enregistré
        // cette commande avec le même token.
        if (
            $e instanceof \PDOException
            && $e->getCode() === '23000'
            && isset($e->errorInfo[1])
            && (int) $e->errorInfo[1] === 1062
        ) {
            $existingOrderId = $commandeRepository->findIdByOrderToken(
                $orderToken,
                (int) $userId
            );

            if ($existingOrderId !== null) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Commande déjà enregistrée',
                    'commandeId' => $existingOrderId
                ]);
                return;
            }
        }

        error_log($e->getMessage());

        http_response_code(500);
        echo json_encode([
            'error' => 'Erreur lors de l’enregistrement de la commande'
        ]);
    }
});

$router->post('/commande/calculate-price', function () {
    $rawMenuId = $_POST['menuId'] ?? null;

    $menuId = filter_var(
        $rawMenuId,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($menuId === false) {
        http_response_code(422);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'error' => 'Identifiant du menu invalide'
        ]);

        return;
    }

    $rawPeople = $_POST['people'] ?? null;

    if (
        !is_string($rawPeople) ||
        !ctype_digit($rawPeople) ||
        filter_var($rawPeople, FILTER_VALIDATE_INT) === false
    ) {
        http_response_code(422);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'error' => 'Nombre de personnes invalide'
        ]);

        return;
    }

    $people = (int) $rawPeople;

    $rawDistanceKm = $_POST['distanceKm'] ?? null;

    if (
        !is_scalar($rawDistanceKm) ||
        !is_numeric($rawDistanceKm)
    ) {
        http_response_code(422);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'error' => 'Distance invalide'
        ]);

        return;
    }

    $distanceKm = (float) $rawDistanceKm;

    $pdo = Database::connect();

    $commandeController = new CommandeController(
        new MenuRepository($pdo),
        new UtilisateurRepository($pdo),
        new CommandeRepository($pdo),
        new CommandeService()
    );

    $result = $commandeController->calculatePrice(
        $menuId,
        $people,
        $distanceKm
    );

    http_response_code($result['status']);
    header('Content-Type: application/json; charset=utf-8');

    if (isset($result['error'])) {
        echo json_encode([
            'error' => $result['error']
        ]);
        return;
    }

    echo json_encode($result['prices']);
});

$router->post('/contact', function () {
    \App\Core\Session::start();

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $contactController = new ContactController();

    $isSent = $contactController->send(
        $_POST['name'] ?? '',
        $_POST['mail'] ?? '',
        $_POST['subject'] ?? '',
        $_POST['message'] ?? ''
    );

    header('Location: /contact');
    exit;
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);