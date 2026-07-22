<?php
require_once "session.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        require_once "../config/database.php";
    
        $sql = 
            "SELECT 
                u.id_utilisateur,
                u.prenom,
                u.nom,
                u.email,
                u.mot_de_passe,
                u.photo,
                r.libelle AS role
            FROM utilisateurs u
            INNER JOIN roles r ON u.role = r.id_role
            WHERE u.email = ?
        ";
    
        $stmt = $pdo->prepare($sql);
    
        $stmt->execute([$_POST['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['password'], $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['firstname'] = $user['prenom'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['photo'] = $user['photo'];

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accueil - Vite & Gourmand</title>
    <!--Font awesome CDN-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.0/css/all.min.css">
    <!--Scroll reveal CDN-->
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Main section -->
    <section class="main-section">
        <img src="../images/hero.jpg" alt="" class="parallax">
        <div class="container">
            <!-- Login form -->
             <div class="login-box">
                 <form class="login-form" method="POST" action="login.php">
                    <div class="login-head">
                        <a href="../index.php">
                            <img src="../images/vg-logo-2.png" alt="">
                        </a>
                        <h4>Connexion</h4>
                    </div>
                    <div class="login-content">
                         <h4>Email</h4>
                         <input
                             type="email"
                             name="email"
                             required
                         >
     
                         <h4>Mot de passe</h4>
                         <input
                             type="password"
                             name="password"
                             required
                         >
     
                        <div class="login-buttons">
                            <button type="submit" class="btn body-btn btn-underline">
                                Se connecter
                            </button>
                            <a href="../index.php" class="btn body-btn btn-underline">
                                Accueil
                            </a>
                        </div> 
                     </div>
                 </form>
             </div>
        </div>
    </section>

    <script type="module" src="../js/script.js"></script>
</body>
</html>