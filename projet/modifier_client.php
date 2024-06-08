<?php
 // Établir une connexion à la base de données
 $connexion = mysqli_connect("localhost", "root", "", "Selling_electronic_devices");

 // Vérifier si la connexion a réussi
 if (!$connexion) {
     die("Erreur de connexion à la base de données : " . mysqli_connect_error());
 }
 $id_client = $_POST['id_client'];

 $sql_verifier_client = "SELECT * FROM Clients WHERE IDClient = '$id_client'";
 $result_verifier_client = mysqli_query($connexion, $sql_verifier_client);

 if (!$result_verifier_client || mysqli_num_rows($result_verifier_client) == 0) {
     echo "Aucun client trouvé avec cet ID.";
     exit; // Arrêter l'exécution du script si aucun client n'est trouvé
 }

// Vérifier si le formulaire pour modifier un client a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
    // Récupérer les nouvelles valeurs des champs du client à modifier
    $id_client = $_POST['id_client'];
    $nouvelle_adresse_email = $_POST['nouvelle_adresse_email'];
    $nouvelle_adresse = $_POST['nouvelle_adresse'];
    $nouveau_nom = $_POST['nouveau_nom'];
    $nouveau_prenom = $_POST['nouveau_prenom'];
    $nouveau_mot_de_passe = $_POST['nouveau_mot_de_passe'];
    $nouvelles_autres_informations = $_POST['nouvelles_autres_informations'];

   
    // Préparer et exécuter la requête SQL pour modifier les données du client dans la table 'Clients'
    $sql_modifier_client = "UPDATE Clients SET AdresseEmail = '$nouvelle_adresse_email', Adresse = '$nouvelle_adresse', Nom = '$nouveau_nom', Prenom = '$nouveau_prenom', MotDePasse = '$nouveau_mot_de_passe', AutresInformationsClient = '$nouvelles_autres_informations' WHERE IDClient = '$id_client'";
    $result_modifier_client = mysqli_query($connexion, $sql_modifier_client);

    // Vérifier si la modification des données du client s'est bien déroulée
    if ($result_modifier_client) {
        echo "Les données du client ont été modifiées avec succès.";
    } else {
        echo "Erreur lors de la modification des données du client : " . mysqli_error($connexion);
    }

    // Fermer la connexion à la base de données
    mysqli_close($connexion);
}
?>

