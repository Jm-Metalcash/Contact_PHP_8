<?php
include 'lang/lang.fr_be.php';

session_start(); // Démarrer la session

// Importer la bibliothèque pour le code-barres
require 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

include 'config.php';

// Connexion à la base de données
try {
    $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
} catch (PDOException $e) {
    die("Erreur : Impossible de se connecter à la base de données.");
}

// Vérifier si l'ID unique est fourni en session ou via la requête
if (isset($_SESSION['uniqueId'])) {
    $uniqueId = $_SESSION['uniqueId'];
} else {
    die("Erreur : Aucun ID unique disponible.");
}

// Récupérer les informations du bordereau depuis la base de données
try {
    $query = $db->prepare("SELECT * FROM bordereau_generate WHERE id = :id");
    $query->execute([':id' => $uniqueId]);
    $formData = $query->fetch(PDO::FETCH_ASSOC);

    if (!$formData) {
        throw new Exception("Erreur : Aucun bordereau trouvé pour cet ID.");
    }
} catch (Exception $e) {
    error_log($e->getMessage(), 0);
    die("Une erreur est survenue, veuillez réessayer plus tard.");
}

// Vérifier les données importantes pour éviter les erreurs d'affichage
if (empty($formData['iban']) || empty($formData['phone'])) {
    die("Erreur : Certaines informations de contact sont manquantes.");
}

// Récupérer les informations des colis et leur contenu
try {
    $packagesQuery = $db->prepare("SELECT * FROM package_informations WHERE bordereau_id = :bordereau_id");
    $packagesQuery->execute([':bordereau_id' => $uniqueId]);
    $packages = $packagesQuery->fetchAll(PDO::FETCH_ASSOC);

    $packageContentQuery = $db->prepare("SELECT * FROM package_content WHERE package_id = :package_id");
} catch (Exception $e) {
    error_log("Erreur lors de la récupération des informations de colis : " . $e->getMessage(), 0);
    die("Une erreur est survenue lors de la récupération des informations des colis.");
}

// Dossier des codes-barres
$barcodeDir = __DIR__ . '/images/barcodes/';

// Créer une instance du générateur de code-barres
$generator = new BarcodeGeneratorPNG();

// Fonction pour vider le dossier des codes-barres
function clearBarcodeDirectory($directory)
{
    if (is_dir($directory)) {
        $files = glob($directory . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

// Nettoyer le dossier des codes-barres
clearBarcodeDirectory($barcodeDir);

// Générer le code-barres général basé sur $uniqueId
$barcodeImageGeneral = $generator->getBarcode($uniqueId, $generator::TYPE_CODE_128);
$barcodePathGeneral = $barcodeDir . 'barcode-general-' . $uniqueId . '.png';

if (file_put_contents($barcodePathGeneral, $barcodeImageGeneral) === false) {
    die("Erreur : Impossible de créer l'image du code-barres général.");
}

$relativeBarcodePathGeneral = './images/barcodes/barcode-general-' . $uniqueId . '.png';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(NEW_TITLE_PAGE_BORDEREAU) ?> - Metalcash</title>
    <link rel="stylesheet" href="css/bordereau.css?<?= rand() ?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div id="content">

        <div class="download-all">
            <button onclick="downloadAllPdfs()" class="btn-slide2 btn-slide1">
                <span class="circle2"><i class="fa fa-download"></i></span>
                <span class="title-hover2"><?= BTN_TEXT2_DOWNLOAD_ALL_PDF ?></span>
                <span class="title2"><?= BTN_DOWNLOAD_ALL_PDF ?></span>
            </button>
        </div>

        <div id="all-bordereaux">
            <?php foreach ($packages as $package): ?>
                <?php
                $formattedPackageId = sprintf('%02d', $package['package_number']);
                $currentUniqueId = $uniqueId . '-' . $formattedPackageId;
                $barcodeImage = $generator->getBarcode($currentUniqueId, $generator::TYPE_CODE_128);
                $barcodePath = $barcodeDir . 'barcode-' . $currentUniqueId . '.png';

                if (file_put_contents($barcodePath, $barcodeImage) === false) {
                    die("Erreur : Impossible de créer l'image du code-barres pour le colis {$package['package_number']}.");
                }

                $relativeBarcodePath = './images/barcodes/barcode-' . $currentUniqueId . '.png';

                // Charger le contenu du colis
                $packageContentQuery->execute([':package_id' => $package['id']]);
                $materials = $packageContentQuery->fetchAll(PDO::FETCH_ASSOC);
                ?>


                <div class="download-pdf downloadPdfButton" data-package-id="<?php echo htmlspecialchars($package['package_number']); ?>">
                    <div id="downloadPdf">
                        <a href="#" class="btn-slide2" onclick="downloadPdf('<?php echo htmlspecialchars($package['package_number']); ?>')">
                            <span class="circle2"><i class="fa fa-download"></i></span>
                            <span class="title-hover2"><?= htmlspecialchars(BTN_TEXT2_DOWNLOAD_PDF) ?> <?php echo htmlspecialchars($package['package_number']); ?></span>
                            <span class="title2"><?= htmlspecialchars(BTN_DOWNLOAD_PDF) ?> <?php echo htmlspecialchars($package['package_number']); ?></span>
                        </a>
                    </div>
                </div>

                <div class="pdf-container">
                    <div class="container bordereau" id="bordereau-<?= htmlspecialchars($package['package_number']); ?>">
                        <header>
                            <div class="header-barcode">
                                <div class="header-title">
                                    <h1><?= htmlspecialchars(TITLE_HEADER_BORDEREAU) ?> <span class="package-ref"><?= htmlspecialchars($package['package_number']) ?>/<?= count($packages); ?></span></h1>
                                </div>
                                <div class="barcode-section">
                                    <p class="date"><?= htmlspecialchars(DATE_GENERATE_BARRE_CODE) ?> <?= date('d/m/Y'); ?></p>
                                    <img src="<?= htmlspecialchars($relativeBarcodePath); ?>" alt="Metalcash - Bordereau ID" />
                                    <p class="id_barcode"><?= htmlspecialchars($currentUniqueId) ?></p>
                                </div>
                            </div>
                            <div class="header-info">
                                <p class="header-info-p"><?= htmlspecialchars(NEW_BORDEREAU_DESCRIPTION) ?></p>
                                <div class="header-address">
                                    <h2>METALCASH / <?= htmlspecialchars(HEADER_BORDEREAU_INFORMATIONS) ?></h2>
                                    <p><?= htmlspecialchars(NEW_BORDEREAU_ADDRESS) ?></p>
                                </div>
                            </div>
                            <p class="note"><?= htmlspecialchars(NEW_NOTE_BORDEREAU) ?></p>
                        </header>

                        <main>
                            <section class="beneficiary-info">
                                <div class="column">
                                    <h3><?= htmlspecialchars(BENEFICIARY_INFO) ?></h3>
                                    <div class="info-grid">
                                        <p><?= htmlspecialchars(NEW_FIELD_FIRSTNAME) ?>: <span><?= htmlspecialchars($formData['firstname']); ?></span></p>
                                        <p><?= htmlspecialchars(NEW_FIELD_LASTNAME) ?>: <span><?= htmlspecialchars($formData['lastname']); ?></span></p>
                                        <p><?= htmlspecialchars(BENEFICIARY_INFO_STREET) ?>: <span><?= htmlspecialchars($formData['address']); ?></span></p>
                                        <p><?= htmlspecialchars(BENEFICIARY_INFO_LOCALITY) ?>: <span><?= htmlspecialchars($formData['postalCode']) . ', ' . htmlspecialchars($formData['locality']); ?></span></p>
                                        <p><?= htmlspecialchars(NEW_FIELD_COUNTRY) ?>: <span><?= htmlspecialchars($formData['country']); ?></span></p>
                                        <p><?= htmlspecialchars(BENEFICIARY_INFO_PHONE) ?>: <span><?= htmlspecialchars($formData['phone']); ?></span></p>
                                        <p><?= htmlspecialchars(NEW_FIELD_EMAIL) ?>: <span><?= htmlspecialchars($formData['email']); ?></span></p>
                                    </div>
                                </div>
                                <div class="column">
                                    <h3><?= htmlspecialchars(TITLE_HEADER_BANK) ?></h3>
                                    <div class="info-grid">
                                        <p><?= htmlspecialchars(NEW_FIELD_ACCOUNT_HOLDER) ?>: <span><?= htmlspecialchars($formData['firstname'] . ' ' . $formData['lastname']); ?></span></p>
                                        <p><?= htmlspecialchars(NEW_FIELD_IBAN) ?>: <span><?= htmlspecialchars($formData['iban']); ?></span></p>
                                        <p><?= htmlspecialchars(NEW_FIELD_BANKNAME) ?>: <span><?= htmlspecialchars($formData['bankName']); ?></span></p>
                                        <p><?= htmlspecialchars(NEW_FIELD_SWIFT) ?>: <span><?= htmlspecialchars($formData['swift']); ?></span></p>
                                        <div class="card-info">
                                            <p><?= htmlspecialchars(NEW_FIELD_ID_CARD_BORDEREAU) ?>: <span><?= htmlspecialchars($formData['idCard']); ?></span></p>
                                            <p><?= htmlspecialchars(NEW_FIELD_ID_EXPIRY_BORDEREAU) ?>: <span class="expiry-date-ci"><?= htmlspecialchars($formData['expiryDate']); ?></span></p>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="table-section">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= htmlspecialchars(NEW_FIELD_TYPE_METAL) ?></th>
                                            <th><?= htmlspecialchars(NEW_FIELD_DESCRIPTION_PACKAGE) ?></th>
                                            <th><?= htmlspecialchars(NEW_FIELD_WEIGHT) ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($materials as $index => $material): ?>
                                            <tr>
                                                <td><?= $index + 1; ?></td>
                                                <td><?= htmlspecialchars($material['material_type']); ?></td>
                                                <td><?= htmlspecialchars($material['description'] ?? 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($material['weight'] ?? 'N/A'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </section>
                        </main>

                        <footer>
                            <div class="footer-content">
                                <p class="accept-conditions"><?= htmlspecialchars(BORDEREAU_CONDITION_ACCEPT) ?></p>
                                <p class="certificate"><strong><?= htmlspecialchars(BORDEREAU_CERTIFCATE_TITLE) ?> :</strong><br><?= htmlspecialchars(BORDEREAU_CERTIFCATE_DESCRIPTION) ?></p>
                                <p class="conditions"><strong><?= htmlspecialchars(BORDEREAU_CONDITIONS_TITLE) ?> :</strong><br><a href="<?= htmlspecialchars(BORDEREAU_CONDITIONS_LINK1) ?>"><?= htmlspecialchars(BORDEREAU_CONDITIONS_LINK1) ?></a> — <a href="<?= htmlspecialchars(BORDEREAU_CONDITIONS_LINK2) ?>"><?= htmlspecialchars(BORDEREAU_CONDITIONS_LINK2) ?></a></p>
                            </div>
                            <div class="signature-section">
                                <p><?= htmlspecialchars(BORDEREAU_DATE) ?></p>
                                <p class="signature-name"><?= htmlspecialchars(BORDEREAU_SIGNATURE_NAME) ?></p>
                                <p><?= htmlspecialchars(BORDEREAU_SIGNATURE) ?></p>
                            </div>

                        </footer>

                        <div class="content-cut-out">
                            <i class="fa-solid fa-scissors scissors-icon"></i>
                            <span class="line-doted"></span>
                            <p><?= htmlspecialchars(BORDEREAU_CUTE_TEXT) ?></p>
                            <span class="line-doted2"></span>
                            <i class="fa-solid fa-scissors scissors-icon2"></i>
                            <div class="informations-cut">
                                <!-- Code-barres spécifique au colis -->
                                <img src="<?= htmlspecialchars($relativeBarcodePath); ?>" alt="Metalcash-package-id" />
                                <span class="cut-uniqueId"><?= htmlspecialchars($currentUniqueId) ?></span>
                                <span class="cut-packageId"><?= htmlspecialchars(NEW_TITLE_INDEX_PACKAGE) ?> <?= htmlspecialchars($package['package_number']) ?>/<?= count($packages); ?></span>
                                <span class="cut-name"><?= htmlspecialchars($formData['firstname'] . ' ' . $formData['lastname'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="footer-btn-back">
            <a href="/" class="formbold-btn"><?= htmlspecialchars(BORDEREAU_BTN_BACK) ?></a>
        </div>
    </div>

    <script>
        function downloadPdf(packageId) {
            const element = document.getElementById('bordereau-' + packageId);
            const downloadButton = document.querySelector('.download-pdf[data-package-id="' + packageId + '"]');

            downloadButton.style.visibility = 'hidden';

            const options = {
                margin: [5, 10, 0, 13],
                filename: 'bordereau_achat_metalcash_colis_' + packageId + '.pdf',
                html2canvas: {
                    scrollX: 0,
                    scrollY: 0,
                    scale: 2,
                    useCORS: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            html2pdf().from(element).set(options).save().then(() => {
                downloadButton.style.visibility = 'visible';
            });
        }

        function downloadAllPdfs() {
            const element = document.getElementById('all-bordereaux');
            const options = {
                margin: [5, 0, 0, 0],
                filename: 'bordereaux_achat_metalcash.pdf',
                html2canvas: {
                    scrollX: 0,
                    scrollY: 0,
                    scale: 2,
                    useCORS: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                },

            };

            // Ajouter la classe "pdf-mode" pour appliquer les styles spécifiques au PDF
            document.body.classList.add('pdf-mode');

            // Générer le PDF
            html2pdf().set(options).from(element).save().then(() => {
                // Retirer la classe "pdf-mode" après la génération du PDF
                document.body.classList.remove('pdf-mode');
            });
        }
    </script>

</body>

</html>