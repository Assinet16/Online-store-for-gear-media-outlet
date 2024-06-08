<?php


// Vérifie si le champ 'type' a été envoyé avec le formulaire
if (isset($_POST['type'])) {
    // Récupère la valeur du champ 'type' et la stocke dans la variable $type
    $type = $_POST['type'];
    
    // Redirige en fonction de la valeur du champ 'type'
    if ($type === 'administrateur') {
        header("Location: mot.html");
        exit();
    } elseif ($type === 'achteur') {
        header("Location: achteur.html");
        exit();
    }
} else {
    // Si le champ 'type' n'est pas défini, redirige vers 'safha.html'
    header("Location: safha.html");
    exit();
}
?>

