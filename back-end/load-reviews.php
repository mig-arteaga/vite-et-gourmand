<?php
    try {
        require_once "config/database.php";
        
        $sql = "SELECT 
            a.date_avis as review_date,
            a.note as score,
            a.message,
            u.nom as surname,
            u.prenom as name,
            u.photo
        FROM avis a
        INNER JOIN utilisateurs u ON a.utilisateur = u.id_utilisateur
        WHERE a.statut = 'Validé';";
        
        foreach ($pdo->query($sql, PDO::FETCH_ASSOC) as $review) {
            include "assets/review.php";
        }
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
?>