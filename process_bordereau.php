<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/phpmailer/phpmailer/src/Exception.php';
require './vendor/phpmailer/phpmailer/src/PHPMailer.php';
require './vendor/phpmailer/phpmailer/src/SMTP.php';

include 'config.php';
$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firstname'], $_POST['lastname'], $_POST['email'])) {

    $uniqueId = filter_input(INPUT_POST, 'uniqueId', FILTER_SANITIZE_STRING);
    $firstname = htmlspecialchars(trim($_POST['firstname']), ENT_QUOTES, 'UTF-8');
    $lastname = htmlspecialchars(trim($_POST['lastname']), ENT_QUOTES, 'UTF-8');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']), ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars(trim($_POST['address']), ENT_QUOTES, 'UTF-8');
    $postalCode = htmlspecialchars(trim($_POST['postalCode']), ENT_QUOTES, 'UTF-8');
    $locality = htmlspecialchars(trim($_POST['locality']), ENT_QUOTES, 'UTF-8');
    $country = htmlspecialchars(trim($_POST['country']), ENT_QUOTES, 'UTF-8');
    $iban = htmlspecialchars(trim($_POST['iban']), ENT_QUOTES, 'UTF-8');
    $bankName = htmlspecialchars(trim($_POST['bankName']), ENT_QUOTES, 'UTF-8');
    $swift = htmlspecialchars(trim($_POST['swift']), ENT_QUOTES, 'UTF-8');
    $idCard = htmlspecialchars(trim($_POST['idCard']), ENT_QUOTES, 'UTF-8');
    $expiryDate = htmlspecialchars(trim($_POST['expiryDate']), ENT_QUOTES, 'UTF-8');

    // Insertion des données dans `bordereau_generate`
    $insertBordereau = $db->prepare("
        INSERT INTO bordereau_generate (id, status, firstname, lastname, email, phone, address, postalCode, locality, country, accountHolder, iban, bankName, swift, idCard, expiryDate)
        VALUES (:id, :status, :firstname, :lastname, :email, :phone, :address, :postalCode, :locality, :country, :accountHolder, :iban, :bankName, :swift, :idCard, :expiryDate)
    ");

    $insertBordereau->execute([
        ':id' => $uniqueId,
        ':status' => 1,
        ':firstname' => $firstname,
        ':lastname' => $lastname,
        ':email' => $email,
        ':phone' => $phone,
        ':address' => $address,
        ':postalCode' => $postalCode,
        ':locality' => $locality,
        ':country' => $country,
        ':accountHolder' => $firstname . ' ' . $lastname,
        ':iban' => $iban,
        ':bankName' => $bankName,
        ':swift' => $swift,
        ':idCard' => $idCard,
        ':expiryDate' => $expiryDate
    ]);

    if (isset($_POST['materialType']) && is_array($_POST['materialType'])) {
        $insertPackage = $db->prepare("
            INSERT INTO package_informations (id, bordereau_id, package_number)
            VALUES (:id, :bordereau_id, :package_number)
        ");

        $insertContent = $db->prepare("
            INSERT INTO package_content (package_id, material_type, description, weight)
            VALUES (:package_id, :material_type, :description, :weight)
        ");

        foreach ($_POST['materialType'] as $packageId => $materials) {
            $currentUniqueId = $uniqueId . '-' . sprintf('%02d', $packageId);

            $insertPackage->execute([
                ':id' => $currentUniqueId,
                ':bordereau_id' => $uniqueId,
                ':package_number' => $packageId
            ]);

            foreach ($materials as $index => $materialType) {
                $description = htmlspecialchars($_POST['description'][$packageId][$index] ?? null, ENT_QUOTES, 'UTF-8');
                $weight = htmlspecialchars($_POST['weight'][$packageId][$index] ?? null, ENT_QUOTES, 'UTF-8');

                $insertContent->execute([
                    ':package_id' => $currentUniqueId,
                    ':material_type' => htmlspecialchars($materialType, ENT_QUOTES, 'UTF-8'),
                    ':description' => $description,
                    ':weight' => $weight
                ]);
            }
        }
    }

    $packagesQuery = $db->prepare("
        SELECT p.id AS package_id, p.package_number, c.material_type, c.description, c.weight 
        FROM package_informations p 
        LEFT JOIN package_content c ON p.id = c.package_id 
        WHERE p.bordereau_id = :bordereau_id 
        ORDER BY p.package_number, c.material_type
    ");
    $packagesQuery->execute([':bordereau_id' => $uniqueId]);
    $packagesData = $packagesQuery->fetchAll(PDO::FETCH_ASSOC);

    $packagesTables = "";
    $currentPackageId = null;
    foreach ($packagesData as $data) {
        if ($currentPackageId !== $data['package_id']) {
            if ($currentPackageId !== null) {
                $packagesTables .= "</table>";
            }
            $currentPackageId = $data['package_id'];

            $packagesTables .= "
            <div style='background-color: #f9f9f9; padding: 10px; margin-bottom: 10px; border-radius: 8px;'>
                <h3 style='color: #333; font-size: 18px; margin-top: 10px;'>Colis {$data['package_number']} ({$data['package_id']})</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <th style='border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;'>Type de Matériau</th>
                        <th style='border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;'>Description</th>
                        <th style='border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;'>Poids (kg)</th>
                    </tr>";
        }

        $packagesTables .= "
            <tr>
                <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($data['material_type'], ENT_QUOTES, 'UTF-8') ."</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8') ."</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($data['weight'], ENT_QUOTES, 'UTF-8') ."</td>
            </tr>";
    }
    $packagesTables .= "</table></div>";

    $subject = "[Metalcash - Notification] Nouveau bordereau généré ({$uniqueId})";
    $message = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>$subject</title>
    </head>
    <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
        <div style='max-width: 900px; margin: auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1);'>
            <div style='text-align: center; padding-bottom: 20px; border-bottom: 1px solid #eaeaea;'>
                <h1 style='font-size: 16px; color: #005893;'>Metalcash</h1>
                <h2 style='font-size: 24px; color: #333;'>Détails du Bordereau</h2>
            </div>
            <div style='padding: 20px;'>
                <p><strong>ID Bordereau :</strong> {$uniqueId}</p>
                <p><strong>Prénom :</strong> {$firstname}</p>
                <p><strong>Nom :</strong> {$lastname}</p>
                <p><strong>E-mail :</strong> <a href='mailto:{$email}' style='color: #1d72b8;'>{$email}</a></p>
                <p><strong>Téléphone :</strong> {$phone}</p>
                <p><strong>Adresse :</strong> {$address}</p>
                <p><strong>Localité :</strong> {$locality}, {$postalCode}, {$country}</p>
                <p><strong>IBAN :</strong> {$iban}</p>
                <p><strong>Banque :</strong> {$bankName} ({$swift})</p>
                <p><strong>N° Carte d'identité :</strong> {$idCard}</p>
                <p><strong>Date d'expiration :</strong> {$expiryDate}</p>
                <h3>Récapitulatif des colis :</h3>
                $packagesTables
            </div>
        </div>
    </body>
    </html>";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'itmetalcash@gmail.com';
        $mail->Password = 'zqlsdmutmhkguhvh';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setLanguage('fr', './vendor/phpmailer/phpmailer/language');
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->setFrom('itmetalcash@gmail.com', 'Metalcash');
        $mail->addAddress('jm@metalcash.be');

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        echo "Notification envoyée avec succès.";
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'e-mail : {$mail->ErrorInfo}";
    }

    $_SESSION['uniqueId'] = $uniqueId;
    header("Location: bordereau_achat.php");
    exit();
} else {
    echo "Erreur : Données de formulaire manquantes.";
}
