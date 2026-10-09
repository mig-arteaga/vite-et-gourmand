<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Database;
use App\Controllers\MenuController;
use App\Controllers\CommandeController;
use App\Controllers\AuthController;
use App\Controllers\ContactController;
use App\Controllers\DistanceController;
use App\Repositories\MenuRepository;
use App\Repositories\RegimeRepository;
use App\Repositories\ThemeRepository;
use App\Repositories\UtilisateurRepository;
use App\Services\CommandeService;
use App\Services\AuthService;
use App\Services\DistanceService;

$router = new Router();

$router->get('/', function () {
    \App\Core\Session::start();

    $pdo = \App\Core\Database::connect();

    $avisController = new \App\Controllers\AvisController(
        new \App\Repositories\AvisRepository($pdo)
    );

    $reviews = $avisController->getValidatedReviews();

    require __DIR__ . '/../views/pages/index.php';
});

$router->get('/menus', function () {
    \App\Core\Session::start();

    $pdo = \App\Core\Database::connect();

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

    $pdo = \App\Core\Database::connect();

    $commandeController = new \App\Controllers\CommandeController(
        new \App\Repositories\MenuRepository($pdo),
        new \App\Repositories\UtilisateurRepository($pdo),
        new \App\Services\CommandeService()
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

$router->post('/login', function () {
    \App\Core\Session::start();

    $pdo = \App\Core\Database::connect();

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

$router->get('/menus/filter-values', function () {
    $pdo = Database::connect();

    $menuController = new MenuController(
        new MenuRepository($pdo),
        new ThemeRepository($pdo),
        new RegimeRepository($pdo)
    );

    $result = $menuController->getFilterValues();

    echo json_encode($result);
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
        new CommandeService()
    );

    $detail = $commandeController->getChangeMenuData($menuId);

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
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $zipcode = trim($_POST['zipcode'] ?? '');

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
    } catch (\RuntimeException $e) {
        http_response_code(502);

        echo json_encode([
            'error' => 'Impossible de calculer la distance'
        ]);
    }
});

$router->post('/commande/calculate-price', function () {
    $menuId = (int) ($_POST['menuId'] ?? 0);
    $people = (int) ($_POST['people'] ?? 0);
    $distanceKm = (float) ($_POST['distanceKm'] ?? 0);

    $pdo = Database::connect();

    $commandeController = new CommandeController(
        new MenuRepository($pdo),
        new UtilisateurRepository($pdo),
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
    $contactController = new ContactController();

    $isSent = $contactController->send(
        $_POST['name'] ?? '',
        $_POST['mail'] ?? '',
        $_POST['subject'] ?? '',
        $_POST['message'] ?? ''
    );

    header('Location: /contact');
    exit;

    header('Location: /');
    exit;
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);