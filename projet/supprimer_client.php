<?php

// التحقق من نوع الطلب ومن وجود الزر 'supprimer' في النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supprimer'])) {
    // الحصول على معرف العميل من النموذج
    $id_client = $_POST['id_client'];

    // إنشاء اتصال بقاعدة البيانات
    $connexion = mysqli_connect("localhost", "root", "", "Selling_electronic_devices");

    // التحقق من نجاح الاتصال بقاعدة البيانات
    if (!$connexion) {
        die("Erreur de connexion à la base de données : " . mysqli_connect_error());
    }

    // التحقق مما إذا كان العميل موجود في قاعدة البيانات
    $sql_verifier_client = "SELECT * FROM Clients WHERE IDClient = '$id_client'";
    $result_verifier_client = mysqli_query($connexion, $sql_verifier_client);

    if (!$result_verifier_client || mysqli_num_rows($result_verifier_client) == 0) {
        echo "Aucun client trouvé avec cet ID.";
        exit; // إيقاف تنفيذ البرنامج إذا لم يتم العثور على العميل
    }

    // حذف السجلات المرتبطة بهذا العميل في جدول historiquecommandes أولاً
    $sql_supprimer_historique_commandes = "DELETE FROM historiquecommandes WHERE IDCommande IN (SELECT IDCommande FROM commandes WHERE IDClient = '$id_client')";
    $result_supprimer_historique_commandes = mysqli_query($connexion, $sql_supprimer_historique_commandes);

    // التحقق من نجاح حذف السجلات في جدول historiquecommandes
    if (!$result_supprimer_historique_commandes) {
        echo "Erreur lors de la suppression des enregistrements dans la table historiquecommandes : " . mysqli_error($connexion);
        exit;
    }

    // حذف السجلات المرتبطة بهذا العميل في جدول expeditions
    $sql_supprimer_expeditions = "DELETE FROM expeditions WHERE IDCommande IN (SELECT IDCommande FROM commandes WHERE IDClient = '$id_client')";
    $result_supprimer_expeditions = mysqli_query($connexion, $sql_supprimer_expeditions);

    // التحقق من نجاح حذف السجلات في جدول expeditions
    if (!$result_supprimer_expeditions) {
        echo "Erreur lors de la suppression des enregistrements dans la table expeditions : " . mysqli_error($connexion);
        exit;
    }

    // حذف الطلبات المرتبطة بهذا العميل في جدول commandes
    $sql_supprimer_commandes = "DELETE FROM commandes WHERE IDClient = '$id_client'";
    $result_supprimer_commandes = mysqli_query($connexion, $sql_supprimer_commandes);

    // التحقق من نجاح حذف الطلبات
    if (!$result_supprimer_commandes) {
        echo "Erreur lors de la suppression des commandes du client : " . mysqli_error($connexion);
        exit;
    }

    // حذف العميل نفسه في جدول clients
    $sql_supprimer_client = "DELETE FROM clients WHERE IDClient = '$id_client'";
    $result_supprimer_client = mysqli_query($connexion, $sql_supprimer_client);

    // التحقق من نجاح حذف العميل
    if ($result_supprimer_client) {
        echo "Le client a été supprimé avec succès.";
    } else {
        if (mysqli_affected_rows($connexion) == 0) {
            echo "Aucun client trouvé avec cet identifiant.";
        } else {
            echo "Erreur lors de la suppression du client : " . mysqli_error($connexion);
        }
    }

    // إغلاق الاتصال بقاعدة البيانات
    mysqli_close($connexion);
}
?>
