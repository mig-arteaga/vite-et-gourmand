<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=vite_et_gourmand',
            'root',
            ''
        );
    
        // Menu
        $sql = 
            "SELECT 
                u.id_utilisateur,
                u.prenom,
                u.nom,
                u.email,
                u.mot_de_passe,
                r.libelle AS role
            FROM utilisateurs u
            INNER JOIN roles r ON u.role = r.id_role
            WHERE u.email = ?
        ";
    
        $stmt = $pdo->prepare($sql);
        // $stmt->bindValue(':email', 'j.pecks@mail.com');
    
        $stmt->execute([$_POST['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['password'], $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['firstname'] = $user['prenom'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === "Administrateur") {
                header("Location: ../admin.php");
                exit;
            }
            elseif ($user['role'] === "Employé") {
                header("Location: ../employe.php");
                exit;
            }
            else {
                header("Location: ../index.php");
                exit;
            }
        } else {
            echo "Mail ou mot de passe incorrect";
        };
    
    } catch (PDOException $e) {
       echo $e->getMessage();
    }
    
    exit;

}
?>

<!-- Login form -->
<form method="POST" action="login.php">

    <input
        type="email"
        name="email"
        placeholder="Email"
        required
    >

    <input
        type="password"
        name="password"
        placeholder="Password"
        required
    >

    <button type="submit">
        Login
    </button>

</form>

<?php
// $pdo = new PDO(
//     'mysql:host=localhost;dbname=vite_et_gourmand',
//     'root',
//     ''
// );

// // Menu
// $sql = 
//     "SELECT 
//         id_utilisateur,
//         nom,
//         prenom,
//         email,
//         mot_de_passe,
//         role
//     FROM utilisateurs
//     WHERE email = :email
// ";

// $statement = $pdo->prepare($sql);
// $statement->bindValue(':email', 'j.pecks@mail.com');

// if ($statement->execute()) {
//     $user = $statement->fetch(PDO::FETCH_ASSOC);

//     if ($user === false) {
//         echo 'Identifiants invalides';
//     } else {
//         echo 'Bienvenue, '.$user['prenom'].' !';
//     }
// } else {
//     echo 'Impossible de récupérer l\'utilisateur';
// }
?>