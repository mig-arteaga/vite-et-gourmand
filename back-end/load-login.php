<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        require_once "config/database.php";
    
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
                header("Location: admin.php");
                exit;
            }
            elseif ($user['role'] === "Employé") {
                header("Location: employe.php");
                exit;
            }
            else {
                header("Location: index.php");
                exit;
            }
        } else {
            echo "Mail ou mot de passe incorrect";
        };
    
    } catch (PDOException $e) {
       echo $e->getMessage();
    }
    
    exit;
};
?>