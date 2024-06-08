<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supprimer'])) {
    $id_produit = $_POST['id_produit'];

    $connexion = mysqli_connect("localhost", "root", "", "Selling_electronic_devices");

    if (!$connexion) {
        die("Erreur de connexion à la base de données : " . mysqli_connect_error());
    }
    $sql_verifier_produit = "SELECT * FROM produits WHERE IDProduit = '$id_produit'";
    $result_verifier_produit = mysqli_query($connexion, $sql_verifier_produit);
    //هذا الشرط يتحقق إذا كانت النتيجة المسترجعة من الاستعلام للتحقق من وجود المنتجات هي صفر
    if (!$result_verifier_produit || mysqli_num_rows($result_verifier_produit) == 0) {
        echo "Aucun produit trouvé avec cet ID.";
        exit; 
    }

    // Supprimer d'abord les enregistrements dans l'historique des commandes associées à ce produit
    $sql_supprimer_historique = "DELETE FROM historiquecommandes WHERE IDCommande IN (SELECT IDCommande FROM commandes WHERE IDProduit = '$id_produit')";
   //تصالًا بقاعدة البيانات
    $result_supprimer_historique = mysqli_query($connexion, $sql_supprimer_historique);

    if (!$result_supprimer_historique) {
        echo "Erreur lors de la suppression de l'historique des commandes associées au produit : " . mysqli_error($connexion);
        exit;
    }

    // Supprimer les expéditions associées à ce produit
    $sql_supprimer_expeditions = "DELETE FROM expeditions WHERE IDCommande IN (SELECT IDCommande FROM commandes WHERE IDProduit = '$id_produit')";
    $result_supprimer_expeditions = mysqli_query($connexion, $sql_supprimer_expeditions);

    if (!$result_supprimer_expeditions) {
        echo "Erreur lors de la suppression des expéditions associées au produit : " . mysqli_error($connexion);
        exit;
    }

    // Supprimer les commandes associées à ce produit
    $sql_supprimer_commandes = "DELETE FROM commandes WHERE IDProduit = '$id_produit'";
    $result_supprimer_commandes = mysqli_query($connexion, $sql_supprimer_commandes);

    if (!$result_supprimer_commandes) {
        echo "Erreur lors de la suppression des commandes associées au produit : " . mysqli_error($connexion);
        exit;
    }

    // Ensuite, supprimer le produit
    $sql_supprimer_produit = "DELETE FROM produits WHERE IDProduit = '$id_produit'";
    $result_supprimer_produit = mysqli_query($connexion, $sql_supprimer_produit);

    if ($result_supprimer_produit) {
        echo "Le produit a été supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression du produit : " . mysqli_error($connexion);
    }

    mysqli_close($connexion);
}
?>
