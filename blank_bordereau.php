<?php
include 'lang/lang.de_de.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

require 'vendor/autoload.php';

session_start(); // Démarrer la session

// Définir un ID statique pour le code-barres
$uniqueId = '0000000000000';

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

// Générer le code-barres basé sur l'ID statique
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
    <title><?= NEW_TITLE_PAGE_BORDEREAU ?> - Metalcash</title>
    <link rel="stylesheet" href="css/bordereau.css?<?= rand() ?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div id="content">

        <div class="download-pdf downloadPdfButton downloadPdfBlank" data-package-id="1">
            <div id="downloadPdf">
                <a href="#" class="btn-slide2" onclick="downloadPdf('1')">
                    <span class="circle2"><i class="fa fa-download"></i></span>
                    <span class="title-hover2"><?= BTN2_DOWNLOAD_ALL_PDF_BLANK ?></span>
                    <span class="title2"><?= BTN_DOWNLOAD_ALL_PDF_BLANK ?></span>
                </a>
            </div>
        </div>

        <div class="pdf-container">
            <div class="container bordereau" id="bordereau-000">
                <header>
                    <div class="header-barcode">
                        <div class="header-title">
                            <h1><?= htmlspecialchars(TITLE_HEADER_BORDEREAU) ?></h1>
                        </div>
                        <div class="barcode-section">
                            <img src="<?= htmlspecialchars($relativeBarcodePathGeneral); ?>" alt="Metalcash - Bordereau ID" />
                            
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
                    <section class="beneficiary-info-blank">
                        <div class="column">
                            <h3><?= htmlspecialchars(BENEFICIARY_INFO) ?></h3>
                            <div class="info-grid info-grid-blank">
                                <p><?= htmlspecialchars(NEW_FIELD_FIRSTNAME) ?>: <span class="filling-area-blank"></span></p>
                                <p><?= htmlspecialchars(NEW_FIELD_LASTNAME) ?>: <span class="filling-area-blank"></span></p>
                                <p><?= htmlspecialchars(BENEFICIARY_INFO_STREET) ?><br /> <?= htmlspecialchars(BENEFICIARY_INFO_LOCALITY) ?><br /> <?= htmlspecialchars(NEW_FIELD_COUNTRY) ?>: <span class="filling-area-blank filling-area-address"></span></p>
                                <p><?= htmlspecialchars(BENEFICIARY_INFO_PHONE) ?>: <span class="filling-area-blank"></span></p>
                                <p><?= htmlspecialchars(NEW_FIELD_EMAIL) ?>: <span class="filling-area-blank"></span></p>
                            </div>
                        </div>
                        <div class="column">
                            <h3><?= htmlspecialchars(TITLE_HEADER_BANK) ?></h3>
                            <div class="info-grid info-grid-blank">
                                <p><?= htmlspecialchars(NEW_FIELD_IBAN) ?>: <span class="filling-area-blank" style="width: 315px !important;"></span></p>
                                <p><?= htmlspecialchars(BENEFICIARY_INFO_BANKNAME) ?>: <span class="filling-area-blank" style="width: 315px !important;"></span></p>
                                <p><?= htmlspecialchars(NEW_FIELD_SWIFT) ?>: <span class="filling-area-blank" style="width: 315px !important;"></span></p>
                            </div>
                            <div class="info-grid info-grid-blank" style="margin-top: 42px;">
                                <p class="spaced-blank"><?= htmlspecialchars(NEW_FIELD_ID_CARD_BORDEREAU) ?>: <span class="filling-area-blank" style="width: 257px !important;"></span></p>
                                <p class="spaced-blank"><?= htmlspecialchars(NEW_FIELD_ID_EXPIRY_BORDEREAU) ?>: <span class="filling-area-blank" style="width: 257px !important;"></span></p>
                                <span class="expiry-date-separate" style="color: #a5a5a5;">/</span>
                                <span class="expiry-date-separate2" style="color: #a5a5a5;">/</span>
                            </div>
                        </div>
                    </section>

                    <section class="">
                        <table>
                            <thead>
                                <tr>
                                    <th style="padding-left: 40px !important;"><?= NEW_FIELD_TYPE_METAL ?></th>
                                    <th style="padding-left: 100px !important;"><?= NEW_FIELD_DESCRIPTION_PACKAGE ?></th>
                                    <th style="text-align: center;"><?= NEW_FIELD_WEIGHT ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="height: 30px;">
                                    <td style="border-right: 1px solid #ddd;"></td>
                                    <td style="border-right: 1px solid #ddd;"></td>
                                    <td></td>
                                </tr>
                                <tr style="height: 30px;">
                                    <td style="border-right: 1px solid #ddd;"></td>
                                    <td style="border-right: 1px solid #ddd;"></td>
                                    <td></td>
                                </tr>
                                <tr style="height: 30px;">
                                    <td style="border-right: 1px solid #ddd;"></td>
                                    <td style="border-right: 1px solid #ddd;"></td>
                                    <td></td>
                                </tr>
                                <!-- <tr style="height: 30px;">
                                    <td style="border-right: 1px solid #ddd;"></td>
                                    <td style="border-right: 1px solid #ddd;"></td>
                                    <td></td>
                                </tr> -->
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
                        <img src="<?= htmlspecialchars($relativeBarcodePathGeneral); ?>" alt="Metalcash-package-id" />
                        <span class="cut-uniqueId"></span>
                        <span class="cut-name cut-name-blank"><?= BORDEREAU_BLANK_NAME_FIRSTNAME ?> : </span>
                    </div>
                </div>

            </div>
        </div>

        <div class="footer-btn-back">
            <a href="/" class="formbold-btn"><?= BORDEREAU_BTN_BACK ?></a>
        </div>
    </div>

    <script>
        function downloadPdf(packageId) {
            const element = document.getElementById('bordereau-000');
            const downloadButton = document.querySelector('.download-pdf[data-package-id="1"]');

            downloadButton.style.visibility = 'hidden';

            const options = {
                margin: [5, 4, 0, 8],
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
                }
            };

            html2pdf().from(element).set(options).save().then(() => {
                downloadButton.style.visibility = 'visible';
            });
        }
    </script>
</body>

</html>