<?php

    // Effectue la connexion à la base de données
    $connexion = mysqli_connect("localhost", "root", "", "Selling_electronic_devices");
    // Vérifie si la connexion est établie
    if (!$connexion) {
        die("Erreur de connexion à la base de données : " . mysqli_connect_error());
    }

// Vérifie si la méthode de requête est POST et si le formulaire de modification a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" ) { 
    // Récupère les données du formulaire
    $id_produit = $_POST['id_produit'];
    $nouveau_nom = $_POST['nouveau_nom'];
    $nouvelle_description = $_POST['nouvelle_description'];
    $nouveau_prix = $_POST['nouveau_prix'];
    $nouvelle_categorie = $_POST['nouvelle_categorie'];
    $nouveau_stock = $_POST['nouveau_stock'];
    
    $sql_verifier_produit = "SELECT * FROM produits WHERE IDProduit = '$id_produit'";
    $result_verifier_produit = mysqli_query($connexion, $sql_verifier_produit);

    if (!$result_verifier_produit|| mysqli_num_rows($result_verifier_produit) ==0 ) {
        echo "Aucun produit trouvé avec cet ID.";
        exit; 
    }

    // Prépare la requête de mise à jour du produit avec les nouvelles données
    $sql_modifier_produit = "UPDATE produits SET NomProduit = '$nouveau_nom', Description = '$nouvelle_description', Prix = '$nouveau_prix', Categorie = '$nouvelle_categorie', StockDisponible = '$nouveau_stock' WHERE IDProduit = '$id_produit'";
    // Exécute la requête de mise à jour
    $result_modifier_produit = mysqli_query($connexion, $sql_modifier_produit);
    // Vérifie si la requête de mise à jour s'est bien déroulée
    if ($result_modifier_produit) {
        echo "Le produit a été modifié avec succès.";
    } else {
        echo "Erreur lors de la modification du produit : " . mysqli_error($connexion);
    }
    // Ferme la connexion à la base de données
    mysqli_close($connexion);
}
?>



