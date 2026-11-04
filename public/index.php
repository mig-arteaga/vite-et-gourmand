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
use App\Controllers\HoraireController;
use App\Controllers\AdminController;
use App\Controllers\CompteController;
use App\Repositories\MenuRepository;
use App\Repositories\ThemeRepository;
use App\Repositories\RegimeRepository;
use App\Repositories\CommandeRepository;
use App\Repositories\UtilisateurRepository;
use App\Repositories\HoraireRepository;
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

    $horaireController = new HoraireController(
        new HoraireRepository($pdo)
    );

    $reviews = $avisController->getValidatedReviews();
    $horaires = $horaireController->getAll();

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

    $horaireController = new HoraireController(
        new HoraireRepository($pdo)
    );

    $menuService = new \App\Services\MenuService();

    $menus = $menuController->getAll();
    $horaires = $horaireController->getAll();

    require __DIR__ . '/../views/pages/menus.php';
});

$router->get('/contact', function () {
    \App\Core\Session::start();

    $pdo = Database::connect();
    $horaires = (new HoraireController(new HoraireRepository($pdo)))->getAll();

    require __DIR__ . '/../views/pages/contact.php';
});

$router->get('/nous', function () {
    \App\Core\Session::start();

    $pdo = Database::connect();
    $horaires = (new HoraireController(new HoraireRepository($pdo)))->getAll();

    require __DIR__ . '/../views/pages/nous.php';
});

$router->get('/login', function () {
    \App\Core\Session::start();

    $authFlash = $_SESSION['auth_flash'] ?? null;
    unset($_SESSION['auth_flash']);

    $oldEmail = $_SESSION['auth_old_email'] ?? '';
    unset($_SESSION['auth_old_email']);

    $pdo = Database::connect();
    $horaires = (new HoraireController(new HoraireRepository($pdo)))->getAll();

    require __DIR__ . '/../views/pages/login.php';
});

$router->get('/register', function () {
    \App\Core\Session::start();

    if (\App\Core\Session::isLoggedIn()) {
        header('Location: /compte');
        exit;
    }

    $authFlash = $_SESSION['auth_flash'] ?? null;
    unset($_SESSION['auth_flash']);

    $oldInput = $_SESSION['auth_old_input'] ?? [];
    unset($_SESSION['auth_old_input']);

    $pdo = Database::connect();
    $horaires = (new HoraireController(new HoraireRepository($pdo)))->getAll();

    require __DIR__ . '/../views/pages/register.php';
});

$router->get('/forgot-password', function () {
    \App\Core\Session::start();

    $authFlash = $_SESSION['auth_flash'] ?? null;
    unset($_SESSION['auth_flash']);

    $pdo = Database::connect();
    $horaires = (new HoraireController(new HoraireRepository($pdo)))->getAll();

    require __DIR__ . '/../views/pages/forgot-password.php';
});

$router->get('/reset-password', function () {
    \App\Core\Session::start();

    $authFlash = $_SESSION['auth_flash'] ?? null;
    unset($_SESSION['auth_flash']);

    $resetToken = trim((string) ($_GET['token'] ?? ''));

    $pdo = Database::connect();
    $utilisateurRepository = new UtilisateurRepository($pdo);
    $authController = new AuthController(
        new AuthService($utilisateurRepository),
        $utilisateurRepository
    );

    $isValidToken = $authController->isResetTokenValid($resetToken);
    $horaires = (new HoraireController(new HoraireRepository($pdo)))->getAll();

    require __DIR__ . '/../views/pages/reset-password.php';
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
    $horaires = (new HoraireController(new HoraireRepository($pdo)))->getAll();

    require __DIR__ . '/../views/pages/commande.php';
});

$router->get('/compte', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé',
        'Utilisateur'
    ]);

    $activeTab = $_GET['tab'] ?? 'commandes';
    $allowedTabs = ['commandes', 'profil'];

    if (!in_array($activeTab, $allowedTabs, true)) {
        $activeTab = 'commandes';
    }

    $userId = (int) ($_SESSION['user_id'] ?? 0);

    $pdo = Database::connect();

    $compteController = new CompteController(
        new UtilisateurRepository($pdo),
        new CommandeRepository($pdo),
        new MenuRepository($pdo),
        new CommandeService(),
        new \App\Repositories\AvisRepository($pdo)
    );

    $currentUser = $compteController->getUser($userId);
    $clientOrders = $compteController->getUserOrders($userId);
    $clientReviews = $compteController->getUserReviews($userId);
    $reviewedOrderIds = $compteController->getReviewedOrderIds($userId);
    $compteFlash = $_SESSION['compte_flash'] ?? null;
    unset($_SESSION['compte_flash']);

    $horaires = (new HoraireController(new HoraireRepository($pdo)))->getAll();
    $csrfToken = \App\Core\Csrf::generate();

    require __DIR__ . '/../views/pages/compte.php';
});

$router->get('/employe', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé'
    ]);

    $activeTab = $_GET['tab'] ?? 'commandes';
    $allowedTabs = ['commandes', 'avis', 'menus', 'horaires'];

    if (!in_array($activeTab, $allowedTabs, true)) {
        $activeTab = 'commandes';
    }

    $pdo = Database::connect();

    $avisController = new \App\Controllers\AvisController(
        new \App\Repositories\AvisRepository($pdo)
    );

    $commandeController = new CommandeController(
        new MenuRepository($pdo),
        new UtilisateurRepository($pdo),
        new CommandeRepository($pdo),
        new CommandeService()
    );

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo),
        new \App\Repositories\PlatRepository($pdo),
        new \App\Repositories\AllergeneRepository($pdo),
        new \App\Repositories\PhotoMenuRepository($pdo)
    );

    $horaireController = new HoraireController(
        new HoraireRepository($pdo)
    );

    $reviews = $avisController->getAllReviews();
    $orders = $commandeController->getAllOrders();
    $allowedOrderStatuses = $commandeController->getAllowedStatuses();
    $menus = $menuController->getAll();
    $dishes = $menuController->getAllDishes();
    $themes = $menuController->getAllThemes();
    $regimes = $menuController->getAllRegimes();
    $allergens = $menuController->getAllAllergens();
    $availablePhotos = $menuController->getAvailablePhotos();
    $horaires = $horaireController->getAll();
    $csrfToken = \App\Core\Csrf::generate();

    require __DIR__ . '/../views/pages/employe.php';
});

$router->get('/admin', function () {
    \App\Core\Session::requireRoles(['Administrateur']);

    $activeTab = $_GET['tab'] ?? 'employes';
    $allowedTabs = ['employes', 'statistiques'];

    if (!in_array($activeTab, $allowedTabs, true)) {
        $activeTab = 'employes';
    }

    $pdo = Database::connect();

    $adminController = new AdminController(
        new UtilisateurRepository($pdo),
        new CommandeRepository($pdo),
        new \App\Services\MailService(),
        new \App\Repositories\StatsNoSqlRepository($pdo)
    );

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo)
    );

    $filterMenuId = filter_var(
        $_GET['menu_id'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $filterStartDate = is_string($_GET['date_debut'] ?? null)
        ? trim($_GET['date_debut'])
        : null;

    $filterEndDate = is_string($_GET['date_fin'] ?? null)
        ? trim($_GET['date_fin'])
        : null;

    $employees = $adminController->getEmployees();
    $menus = $menuController->getAll();
    $analytics = $adminController->getAnalyticsData(
        $filterMenuId !== false ? (int) $filterMenuId : null,
        $filterStartDate,
        $filterEndDate
    );

    $adminFlash = $_SESSION['admin_flash'] ?? null;
    unset($_SESSION['admin_flash']);

    $horaires = (new HoraireController(new HoraireRepository($pdo)))->getAll();
    $csrfToken = \App\Core\Csrf::generate();

    require __DIR__ . '/../views/pages/admin.php';
});

$router->get('/cgv', function () {
    \App\Core\Session::start();

    $pdo = Database::connect();
    $horaires = (new HoraireController(new HoraireRepository($pdo)))->getAll();

    require __DIR__ . '/../views/pages/cgv.php';
});

$router->get('/legal', function () {
    \App\Core\Session::start();

    $pdo = Database::connect();
    $horaires = (new HoraireController(new HoraireRepository($pdo)))->getAll();

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

    $email = (string) ($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $pdo = Database::connect();

    $authController = new \App\Controllers\AuthController(
        new \App\Services\AuthService(
            new \App\Repositories\UtilisateurRepository($pdo)
        )
    );

    $redirect = $authController->login($email, $password);

    if ($redirect !== null) {
        header("Location: $redirect");
        exit;
    }

    $_SESSION['auth_flash'] = [
        'success' => false,
        'message' => 'Adresse e-mail ou mot de passe incorrect (ou compte désactivé).'
    ];
    $_SESSION['auth_old_email'] = $email;

    header('Location: /login');
    exit;
});

$router->post('/register', function () {
    \App\Core\Session::start();

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $input = [
        'nom' => (string) ($_POST['nom'] ?? ''),
        'prenom' => (string) ($_POST['prenom'] ?? ''),
        'email' => (string) ($_POST['email'] ?? ''),
        'telephone' => (string) ($_POST['telephone'] ?? ''),
        'adresse' => (string) ($_POST['adresse'] ?? ''),
        'code_postal' => (string) ($_POST['code_postal'] ?? ''),
        'ville' => (string) ($_POST['ville'] ?? ''),
    ];

    $pdo = Database::connect();
    $utilisateurRepository = new UtilisateurRepository($pdo);

    $authController = new AuthController(
        new AuthService($utilisateurRepository),
        $utilisateurRepository
    );

    $result = $authController->register(
        $input['nom'],
        $input['prenom'],
        $input['email'],
        $input['telephone'],
        $input['adresse'],
        $input['code_postal'],
        $input['ville'],
        (string) ($_POST['password'] ?? ''),
        (string) ($_POST['password_confirm'] ?? '')
    );

    $_SESSION['auth_flash'] = $result;

    if ($result['success']) {
        $_SESSION['auth_old_email'] = $input['email'];
        header('Location: /login');
        exit;
    }

    $_SESSION['auth_old_input'] = $input;
    header('Location: /register');
    exit;
});

$router->post('/forgot-password', function () {
    \App\Core\Session::start();

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $email = (string) ($_POST['email'] ?? '');
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    $baseUrl = $scheme . '://' . $host;

    $pdo = Database::connect();
    $utilisateurRepository = new UtilisateurRepository($pdo);

    $authController = new AuthController(
        new AuthService($utilisateurRepository),
        $utilisateurRepository
    );

    $_SESSION['auth_flash'] = $authController->requestPasswordReset($email, $baseUrl);

    header('Location: /forgot-password');
    exit;
});

$router->post('/reset-password', function () {
    \App\Core\Session::start();

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $token = (string) ($_POST['token'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

    $pdo = Database::connect();
    $utilisateurRepository = new UtilisateurRepository($pdo);

    $authController = new AuthController(
        new AuthService($utilisateurRepository),
        $utilisateurRepository
    );

    $result = $authController->resetPassword($token, $password, $passwordConfirm);
    $_SESSION['auth_flash'] = $result;

    if ($result['success']) {
        header('Location: /login');
        exit;
    }

    header('Location: /reset-password?token=' . urlencode($token));
    exit;
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

    if (count($_SESSION['distance_requests']) >= 30) {
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

        (new \App\Repositories\StatsNoSqlRepository($pdo))->syncFromRelational();

        $mailService = new \App\Services\MailService();
        $mailService->sendOrderConfirmation(
            $user->getEmail(),
            $user->getFirstName(),
            $commandeId,
            $menu->getTitle(),
            $prices['totalPrice'],
            $date->format('d/m/Y'),
            $time
        );

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

$router->post('/employe/avis/statut', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé'
    ]);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $reviewId = filter_var(
        $_POST['id_avis'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $status = is_string($_POST['statut'] ?? null)
        ? trim($_POST['statut'])
        : '';

    if ($reviewId !== false) {
        $pdo = Database::connect();

        $avisController = new \App\Controllers\AvisController(
            new \App\Repositories\AvisRepository($pdo)
        );

        $avisController->moderateReview((int) $reviewId, $status);
    }

    header('Location: /employe?tab=avis');
    exit;
});

$router->post('/employe/commandes/statut', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé'
    ]);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $orderId = filter_var(
        $_POST['id_commande'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $newStatus = is_string($_POST['statut'] ?? null)
        ? trim($_POST['statut'])
        : '';

    $returned = isset($_POST['restitution']) && $_POST['restitution'] === '1';

    if ($orderId !== false) {
        $pdo = Database::connect();

        $commandeController = new CommandeController(
            new MenuRepository($pdo),
            new UtilisateurRepository($pdo),
            new CommandeRepository($pdo),
            new CommandeService()
        );

        $commandeController->updateOrderStatusByEmployee(
            (int) $orderId,
            $newStatus,
            $returned
        );
    }

    header('Location: /employe?tab=commandes');
    exit;
});

$router->post('/employe/commandes/annuler', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé'
    ]);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $orderId = filter_var(
        $_POST['id_commande'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $contactMode = is_string($_POST['mode_contact'] ?? null)
        ? trim($_POST['mode_contact'])
        : '';

    $reason = is_string($_POST['motif_annulation'] ?? null)
        ? trim($_POST['motif_annulation'])
        : '';

    if ($orderId !== false) {
        $pdo = Database::connect();

        $commandeController = new CommandeController(
            new MenuRepository($pdo),
            new UtilisateurRepository($pdo),
            new CommandeRepository($pdo),
            new CommandeService()
        );

        $commandeController->cancelOrderByEmployee(
            (int) $orderId,
            $contactMode,
            $reason
        );
    }

    header('Location: /employe?tab=commandes');
    exit;
});

$router->post('/employe/menus/create', function () {
    \App\Core\Session::requireRoles(['Administrateur', 'Employé']);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $pdo = Database::connect();

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo),
        new \App\Repositories\PlatRepository($pdo),
        new \App\Repositories\AllergeneRepository($pdo),
        new \App\Repositories\PhotoMenuRepository($pdo)
    );

    $dishIds = is_array($_POST['plats'] ?? null) ? $_POST['plats'] : [];

    $menuController->createMenu(
        (string) ($_POST['titre'] ?? ''),
        (string) ($_POST['description'] ?? ''),
        (int) ($_POST['theme'] ?? 0),
        (int) ($_POST['regime'] ?? 0),
        (int) ($_POST['min_personnes'] ?? 0),
        (float) ($_POST['prix_personne'] ?? 0),
        (int) ($_POST['jours_avant'] ?? 0),
        (int) ($_POST['stock'] ?? 0),
        $dishIds,
        (string) ($_POST['photo'] ?? 'images/menu-1-1.jpg')
    );

    header('Location: /employe?tab=menus');
    exit;
});

$router->post('/employe/menus/update', function () {
    \App\Core\Session::requireRoles(['Administrateur', 'Employé']);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $pdo = Database::connect();

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo),
        new \App\Repositories\PlatRepository($pdo),
        new \App\Repositories\AllergeneRepository($pdo),
        new \App\Repositories\PhotoMenuRepository($pdo)
    );

    $dishIds = is_array($_POST['plats'] ?? null) ? $_POST['plats'] : [];

    $menuController->updateMenu(
        (int) ($_POST['id_menu'] ?? 0),
        (string) ($_POST['titre'] ?? ''),
        (string) ($_POST['description'] ?? ''),
        (int) ($_POST['theme'] ?? 0),
        (int) ($_POST['regime'] ?? 0),
        (int) ($_POST['min_personnes'] ?? 0),
        (float) ($_POST['prix_personne'] ?? 0),
        (int) ($_POST['jours_avant'] ?? 0),
        (int) ($_POST['stock'] ?? 0),
        $dishIds,
        (string) ($_POST['photo'] ?? 'images/menu-1-1.jpg')
    );

    header('Location: /employe?tab=menus');
    exit;
});

$router->post('/employe/menus/delete', function () {
    \App\Core\Session::requireRoles(['Administrateur', 'Employé']);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $pdo = Database::connect();

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo)
    );

    $menuController->deleteMenu((int) ($_POST['id_menu'] ?? 0));

    header('Location: /employe?tab=menus');
    exit;
});

$router->post('/employe/plats/create', function () {
    \App\Core\Session::requireRoles(['Administrateur', 'Employé']);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $pdo = Database::connect();

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo),
        new \App\Repositories\PlatRepository($pdo)
    );

    $allergenIds = is_array($_POST['allergenes'] ?? null) ? $_POST['allergenes'] : [];

    $menuController->createDish(
        (string) ($_POST['titre'] ?? ''),
        (string) ($_POST['description'] ?? ''),
        (string) ($_POST['type'] ?? ''),
        $allergenIds
    );

    header('Location: /employe?tab=menus');
    exit;
});

$router->post('/employe/plats/update', function () {
    \App\Core\Session::requireRoles(['Administrateur', 'Employé']);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $pdo = Database::connect();

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo),
        new \App\Repositories\PlatRepository($pdo)
    );

    $allergenIds = is_array($_POST['allergenes'] ?? null) ? $_POST['allergenes'] : [];

    $menuController->updateDish(
        (int) ($_POST['id_plat'] ?? 0),
        (string) ($_POST['titre'] ?? ''),
        (string) ($_POST['description'] ?? ''),
        (string) ($_POST['type'] ?? ''),
        $allergenIds
    );

    header('Location: /employe?tab=menus');
    exit;
});

$router->post('/employe/plats/delete', function () {
    \App\Core\Session::requireRoles(['Administrateur', 'Employé']);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $pdo = Database::connect();

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo),
        new \App\Repositories\PlatRepository($pdo)
    );

    $menuController->deleteDish((int) ($_POST['id_plat'] ?? 0));

    header('Location: /employe?tab=menus');
    exit;
});

$router->post('/employe/horaires/update', function () {
    \App\Core\Session::requireRoles(['Administrateur', 'Employé']);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $submittedHoraires = is_array($_POST['horaires'] ?? null)
        ? $_POST['horaires']
        : [];

    if (!empty($submittedHoraires)) {
        $pdo = Database::connect();

        $horaireController = new HoraireController(
            new HoraireRepository($pdo)
        );

        $horaireController->updateAll($submittedHoraires);
    }

    header('Location: /employe?tab=horaires');
    exit;
});

$router->post('/admin/employes/create', function () {
    \App\Core\Session::requireRoles(['Administrateur']);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $pdo = Database::connect();

    $adminController = new AdminController(
        new UtilisateurRepository($pdo)
    );

    $_SESSION['admin_flash'] = $adminController->createEmployee(
        (string) ($_POST['nom'] ?? ''),
        (string) ($_POST['prenom'] ?? ''),
        (string) ($_POST['email'] ?? ''),
        (string) ($_POST['password'] ?? ''),
        (string) ($_POST['telephone'] ?? '')
    );

    header('Location: /admin?tab=employes');
    exit;
});

$router->post('/admin/employes/statut', function () {
    \App\Core\Session::requireRoles(['Administrateur']);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $userId = filter_var(
        $_POST['id_utilisateur'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $active = (string) ($_POST['actif'] ?? '0') === '1';

    if ($userId !== false) {
        $pdo = Database::connect();

        $adminController = new AdminController(
            new UtilisateurRepository($pdo)
        );

        $_SESSION['admin_flash'] = $adminController->toggleEmployeeStatus(
            (int) $userId,
            $active
        );
    }

    header('Location: /admin?tab=employes');
    exit;
});

$router->post('/compte/profil/update', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé',
        'Utilisateur'
    ]);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $userId = (int) ($_SESSION['user_id'] ?? 0);

    $pdo = Database::connect();

    $compteController = new CompteController(
        new UtilisateurRepository($pdo)
    );

    $_SESSION['compte_flash'] = $compteController->updateProfile(
        $userId,
        (string) ($_POST['nom'] ?? ''),
        (string) ($_POST['prenom'] ?? ''),
        (string) ($_POST['email'] ?? ''),
        (string) ($_POST['telephone'] ?? ''),
        (string) ($_POST['adresse'] ?? ''),
        (string) ($_POST['code_postal'] ?? ''),
        (string) ($_POST['ville'] ?? '')
    );

    header('Location: /compte?tab=profil');
    exit;
});

$router->post('/compte/commandes/update', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé',
        'Utilisateur'
    ]);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $orderId = (int) ($_POST['id_commande'] ?? 0);

    $pdo = Database::connect();

    $compteController = new CompteController(
        new UtilisateurRepository($pdo),
        new CommandeRepository($pdo),
        new MenuRepository($pdo)
    );

    $_SESSION['compte_flash'] = $compteController->updateOrderByClient(
        $userId,
        $orderId,
        (string) ($_POST['date_livraison'] ?? ''),
        (string) ($_POST['heure_livraison'] ?? ''),
        (string) ($_POST['adresse_livraison'] ?? ''),
        (string) ($_POST['code_postal_livraison'] ?? ''),
        (string) ($_POST['ville_livraison'] ?? ''),
        (int) ($_POST['qte_personnes'] ?? 0),
        isset($_POST['pret_materiel']) && (string) $_POST['pret_materiel'] === '1'
    );

    header('Location: /compte?tab=commandes');
    exit;
});

$router->post('/compte/commandes/cancel', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé',
        'Utilisateur'
    ]);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $orderId = (int) ($_POST['id_commande'] ?? 0);

    $pdo = Database::connect();

    $compteController = new CompteController(
        new UtilisateurRepository($pdo),
        new CommandeRepository($pdo),
        new MenuRepository($pdo)
    );

    $_SESSION['compte_flash'] = $compteController->cancelOrderByClient(
        $userId,
        $orderId
    );

    header('Location: /compte?tab=commandes');
    exit;
});

$router->post('/compte/avis/create', function () {
    \App\Core\Session::requireRoles([
        'Administrateur',
        'Employé',
        'Utilisateur'
    ]);

    if (!\App\Core\Csrf::validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo 'Requête invalide (CSRF)';
        return;
    }

    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $orderId = (int) ($_POST['id_commande'] ?? 0);
    $rating = (int) ($_POST['note'] ?? 0);
    $message = (string) ($_POST['message'] ?? '');

    $pdo = Database::connect();

    $compteController = new CompteController(
        new UtilisateurRepository($pdo),
        new CommandeRepository($pdo),
        new MenuRepository($pdo),
        new CommandeService(),
        new \App\Repositories\AvisRepository($pdo)
    );

    $_SESSION['compte_flash'] = $compteController->submitReview(
        $userId,
        $orderId,
        $rating,
        $message
    );

    header('Location: /compte?tab=commandes');
    exit;
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);