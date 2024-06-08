
<?php
$connexion = mysqli_connect("localhost", "root", "", "Selling_electronic_devices");

if (!$connexion) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données envoyées par le formulaire pour le client
    $nom = mysqli_real_escape_string($connexion, $_POST['nom']);
    $prenom = mysqli_real_escape_string($connexion, $_POST['prenom']);
    $adresse = mysqli_real_escape_string($connexion, $_POST['adresse']);
    $email = mysqli_real_escape_string($connexion, $_POST['email']);
    $phone = mysqli_real_escape_string($connexion, $_POST['phone']);
    $autreinfo = mysqli_real_escape_string($connexion, $_POST['autreinfo']);

    // Préparer et exécuter la requête SQL pour insérer les données du client dans la table 'Clients'
    $sql_client = "INSERT INTO Clients (Nom, Prenom, Adresse, AdresseEmail, MotDePasse, AutresInformationsClient) 
                   VALUES ('$nom', '$prenom', '$adresse', '$email', '$phone', '$autreinfo')";
    $result_client = mysqli_query($connexion, $sql_client);

    // Vérifier si l'insertion des données du client s'est bien déroulée
    if ($result_client) {
        // Récupérer l'ID du client nouvellement inséré
        $id_client = mysqli_insert_id($connexion);

        // Récupérer la quantité et le produit sélectionnés dans le formulaire
        $produits = explode(',', $_POST['panier-produits']);
        $quantites = explode(',', $_POST['panier-quantites']);
        
        // حلقة للانتقال عبر المنتجات والكميات وتنفيذ الاستعلام SQL لكل منها
        $success = true; // يحتوي على قيمة true إذا تم تسجيل الطلب بنجاح لجميع المنتجات، وإلا يصبح false
        for ($i = 0; $i < count($produits); $i++) {
            $produit = mysqli_real_escape_string($connexion, $produits[$i]);
            $quantite = mysqli_real_escape_string($connexion, $quantites[$i]);
        
            // استعلم قاعدة البيانات للحصول على المعلومات الخاصة بالمنتج وقم بإجراء العمليات اللازمة
            // اختر المنتج والكمية باستخدام $produit و $quantite
            // أدخل الكود هنا
            $sql_prix_produit = "SELECT IDProduit, Prix, StockDisponible FROM Produits WHERE NomProduit = '$produit' LIMIT 1";
            $result_prix_produit = mysqli_query($connexion, $sql_prix_produit);
    
            if ($result_prix_produit) {
                // Vérifier موجودية المنتج وتوفر الكمية المطلوبة
                if (mysqli_num_rows($result_prix_produit) == 1) {
                    $row = mysqli_fetch_assoc($result_prix_produit);
                    $id_produit = $row['IDProduit'];
                    $prix_produit = $row['Prix'];
                    $stock_disponible = $row['StockDisponible'];

                    // التحقق من توفر الكمية
                    if ($quantite <= $stock_disponible) {
                        // حساب المبلغ الإجمالي
                        $montant_total = $prix_produit * $quantite;
                        // تحديث الكمية المتبقية في الجدول
                        $nouveau_stock = $stock_disponible - $quantite;
                        $sql_update_stock = "UPDATE Produits SET StockDisponible = $nouveau_stock WHERE IDProduit = $id_produit";
                        $result_update_stock = mysqli_query($connexion, $sql_update_stock);
                        
                        // إذا تم تحديث الكمية بنجاح
                        if ($result_update_stock) {
                            // إدراج بيانات الطلب في جدول الطلبات
                            $sql_commande = "INSERT INTO Commandes (IDClient, IDProduit, DateCommande, StatutCommande, MontantTotal, Quantite) 
                                             VALUES ('$id_client', '$id_produit', NOW(), 'En cours', '$montant_total', '$quantite')";
                            $result_commande = mysqli_query($connexion, $sql_commande);

                            // إذا تم تسجيل الطلب بنجاح
                            if (!$result_commande) {
                                $success = false;
                                echo "Erreur lors de l'enregistrement de la commande : " . mysqli_error($connexion);
                            }
                        } else {
                            $success = false;
                            echo "Erreur lors de la mise à jour du stock pour le produit $produit : " . mysqli_error($connexion);
                        }
                    } else {
                        $success = false;
                        echo "عذرًا، الكمية المطلوبة من المنتج $produit غير متوفرة حاليًا.";
                    }
                } else {
                    $success = false;
                    echo "Erreur lors de la récupération des informations sur le produit $produit.";
                }
            } else {
                $success = false;
                echo "Erreur lors de l'exécution de la requête pour récupérer les informations sur le produit $produit : " . mysqli_error($connexion);
            }
        }

        // إذا تم تسجيل الطلب بنجاح لجميع المنتجات
        if ($success) {
            echo "La commande a été enregistrée avec succès.";
        }

    } else {
        echo "Erreur lors de l'enregistrement des informations du client : " . mysqli_error($connexion);
    }

    // Fermer la connexion à la base de données
    mysqli_close($connexion);
}
?>
