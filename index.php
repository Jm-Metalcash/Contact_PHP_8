<?php
include 'lang/lang.fr_be.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="css/display-form.css" <?= rand() ?>">
    <link rel="stylesheet" href="css/form-bordereau.css" <?= rand() ?>">
    <link rel="stylesheet" href="css/form-contact.css" <?= rand() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div id="content">
        <div class="formbold-main-wrapper">
            <div class="formbold-form-wrapper">

                <!-- Appointment Info Section -->
                <div class="appointment-info">
                    <h3 class="appointment-title"><?= APPOINTMENT_TITLE ?></h3>
                    <p class="appointment-description">
                        <i class="fas fa-phone-alt phone-icon"></i>
                        <a href="tel:+32474011293" class="phone-link">+32 78 150 160</a>
                    </p>
                    <p class="appointment-hours">
                        <?= APPOINTMENT_HOURS ?>
                    </p>
                </div>


                <div class="choice-contact-form">
                    <p><?= CHOICE_CONTACT_FORM ?></p>
                    <div class="choice-contact-buttons">
                        <button class="choice-contact-button" id="contactButton" onclick="displayForm('form-contact-box', this)"><i class="fa-regular fa-envelope"></i> <?= CONTACT_BUTTON ?></button>
                        <button class="choice-contact-button" id="bordereauButton" onclick="displayForm('form-bordereau-box', this)"><i class="fa-regular fa-file-lines"></i> <?= BORDEREAU_BUTTON ?></button>
                    </div>
                </div>

                <!-- FORM CONTACT US -->
                <div id="form-contact-box" class="form-box">
                    <div class="formbold-form-title">
                        <h2 class=""><?= FORM_CONTACT_TITLE ?></h2>

                    </div>
                    <p class="form-contact-description">
                        <?= FORM_CONTACT_DESCRIPTION ?>
                    </p>
                    <form method="POST" action="process_contact.php" id="contactForm" autocomplete="off">
                        <div class="formbold-input-flex">
                            <div>
                                <label for="firstname" class="formbold-form-label">
                                    <?= NEW_FIELD_FIRSTNAME ?>
                                </label>
                                <input type="text" name="firstname" id="firstname" class="formbold-form-input input-value"
                                    placeholder="<?= NEW_PLACEHOLDER_FIRSTNAME ?>" />
                            </div>
                            <div>
                                <label for="lastname" class="formbold-form-label"> <?= NEW_FIELD_LASTNAME ?> </label>
                                <input type="text" name="lastname" id="lastname" class="formbold-form-input input-value"
                                    placeholder="<?= NEW_PLACEHOLDER_LASTNAME ?>" />
                            </div>
                        </div>

                        <div class="formbold-input-flex">
                            <div>
                                <label for="email" class="formbold-form-label"> <?= NEW_FIELD_EMAIL ?> </label>
                                <input type="email" name="email" id="email" class="formbold-form-input input-value"
                                    placeholder="dupont@exemple.com" required />
                                <div id="emailError" class="error-message"><?= NEW_ERROR_MESSAGE_EMAIL ?></div>
                            </div>
                            <div>
                                <label for="phone" class="formbold-form-label"> <?= NEW_FIELD_PHONE ?> </label>
                                <input type="text" name="phone" id="phone" class="formbold-form-input input-value"
                                    placeholder="<?= NEW_PLACEHOLDER_PHONE ?>" required />
                                <div id="phoneError" class="error-message"><?= NEW_ERROR_MESSAGE_PHONE ?></div>
                            </div>
                        </div>

                        <div class="formbold-input-flex">
                            <div>
                                <label for="subject" class="formbold-form-label"> <?= NEW_FIELD_SUBJECT ?> </label>
                                <select name="subject" id="subject" class="formbold-form-input input-value">
                                    <option value="<?= NEW_MAIL_SUBJECT1 ?>" selected><?= NEW_MAIL_SUBJECT1 ?></option>
                                    <option value="<?= NEW_MAIL_SUBJECT2 ?>"><?= NEW_MAIL_SUBJECT2 ?></option>
                                    <option value="<?= NEW_MAIL_SUBJECT3 ?>"><?= NEW_MAIL_SUBJECT3 ?></option>
                                    <option value="<?= NEW_MAIL_SUBJECT4 ?>"><?= NEW_MAIL_SUBJECT4 ?></option>
                                </select>
                            </div>
                        </div>

                        <!-- CAPTCHA -->
                        <!-- <div class="g-recaptcha" data-sitekey="6Lf7n5MiAAAAAP7_TlZ0Uw2-5onMv5NbnUUSxkkX"></div> -->

                        <div class="formbold-input-flex">
                            <div style="width: 100%;">
                                <label for="question" class="formbold-form-label"><?= NEW_FIELD_MESSAGE_SUBJECT ?></label>
                                <textarea name="question" id="question" class="formbold-form-input input-value" rows="8" placeholder="<?= NEW_PLACEHOLDER_MESSAGE_SUBJECT ?>" required></textarea>
                            </div>
                        </div>


                        <div class="btn-submit-form">
                            <button type="submit" class="formbold-btn"><?= NEW_BTN_SUBMIT_FORM_CONTACT ?></button>
                            <div id="formError" class="form-error-message"><?= NEW_MESSAGE_ERROR_SUBMIT_CONTACTFORM ?></div>
                        </div>
                    </form>
                </div>
                <!-- END FORM CONTACT US -->

                <!-- FORM BORDEREAU GENERATE -->
                <div id="form-bordereau-box" class="form-box">
                    <form action="process_bordereau.php" method="POST" id="bordereauForm" autocomplete="off">
                        <input type="hidden" name="uniqueId" value="<?php echo random_int(1000000000000, 9999999999999); ?>">
                        <div class="formbold-form-title">
                            <h2 class=""><?= NEW_BORDEREAU_TITLE ?></h2>
                            <p>
                                <?= NEW_BORDEREAU_DESCRIPTION ?>
                            </p>
                            <div class="formbold-address">
                                <p><?= NEW_BORDEREAU_ADDRESS ?></p>
                            </div>
                            <p class="note-package"><?= NEW_NOTE_BORDEREAU ?></p>
                        </div>

                        <h3 class="header-section"><?= TITLE_HEADER_INFORMATIONNS ?></h3>
                        <div class="formbold-input-flex">
                            <div>
                                <label for="firstname" class="formbold-form-label">
                                    <?= NEW_FIELD_FIRSTNAME ?>
                                </label>
                                <input type="text" name="firstname" id="firstname" class="formbold-form-input input-value"
                                    placeholder="<?= NEW_PLACEHOLDER_FIRSTNAME ?>" required />
                            </div>
                            <div>
                                <label for="lastname" class="formbold-form-label"> <?= NEW_FIELD_LASTNAME ?> </label>
                                <input type="text" name="lastname" id="lastname" class="formbold-form-input input-value"
                                    placeholder="<?= NEW_PLACEHOLDER_LASTNAME ?>" required />
                            </div>
                        </div>

                        <div class="formbold-input-flex">
                            <div>
                                <label for="email" class="formbold-form-label"> <?= NEW_FIELD_EMAIL ?> </label>
                                <input type="email" name="email" id="email" class="formbold-form-input input-value"
                                    placeholder="dupont@exemple.com" required />
                                <div id="emailError" class="error-message"><?= NEW_ERROR_MESSAGE_EMAIL ?></div>
                            </div>
                            <div>
                                <label for="phone" class="formbold-form-label"> <?= NEW_FIELD_PHONE ?> </label>
                                <input type="text" name="phone" id="phone" class="formbold-form-input input-value"
                                    placeholder="<?= NEW_PLACEHOLDER_PHONE ?>" required />
                                <div id="phoneError" class="error-message"><?= NEW_ERROR_MESSAGE_PHONE ?></div>
                            </div>
                        </div>

                        <h3 class="header-section"><?= TITLE_HEADER_ADDRESS ?></h3>
                        <div class="formbold-input-flex">
                            <div style="width: 100% !important;">
                                <label for="address" class="formbold-form-label"><?= NEW_FIELD_ADDRESS ?></label>
                                <input type="text" name="address" id="address" class="formbold-form-input input-value"
                                    placeholder="<?= NEW_PLACEHOLDER_ADDRESS ?>" required />
                            </div>
                            <div>
                                <label for="postalCode" class="formbold-form-label"><?= NEW_FIELD_POSTALCODE ?></label>
                                <input type="text" name="postalCode" id="postalCode" class="formbold-form-input input-value"
                                    placeholder="<?= NEW_PLACEHOLDER_POSTALCODE ?>" required />
                            </div>
                        </div>


                        <div class="formbold-input-flex" style="margin-top: 15px;">
                            <div>
                                <label for="locality" class="formbold-form-label"><?= NEW_FIELD_LOCALITY ?></label>
                                <input type="text" name="locality" id="locality" class="formbold-form-input input-value"
                                    placeholder="<?= NEW_PLACEHOLDER_LOCALITY ?>" required />
                            </div>
                            <div>
                                <label for="country" class="formbold-form-label"><?= NEW_FIELD_COUNTRY ?></label>
                                <input type="text" name="country" id="country" class="formbold-form-input input-value"
                                    placeholder="<?= NEW_PLACEHOLDER_COUNTRY ?>" required />
                            </div>
                        </div>


                        <h3 class="header-section" style="margin-top: 60px !important;"><?= TITLE_HEADER_BANK ?></h3>
                        <div class="formbold-input-flex" style="margin-bottom: 0px !important;">
                            <div class="formbold-mb-3">
                                <label for="accountHolder" class="formbold-form-label">
                                    <?= NEW_FIELD_ACCOUNT_HOLDER ?>
                                </label>
                                <input type="text" name="accountHolder" id="accountHolder"
                                    class="formbold-form-input readonly-input" disabled />
                            </div>


                            <div class="formbold-mb-3">
                                <label for="iban" class="formbold-form-label">
                                    <?= NEW_FIELD_IBAN ?>
                                </label>
                                <input type="text" name="iban" id="iban" class="formbold-form-input input-value" placeholder="exemple: BE6800000000011" required />
                                <div id="ibanError" class="error-message"><?= NEW_ERROR_MESSAGE_IBAN ?></div>
                            </div>
                        </div>



                        <div class="formbold-input-flex" style="margin-top: 20px;">
                            <div>
                                <label for="bankName" class="formbold-form-label"> <?= NEW_FIELD_BANKNAME ?> </label>
                                <input type="text" name="bankName" id="bankName" class="formbold-form-input input-value"
                                    placeholder="<?= NEW_PLACEHOLDER_BANKNAME ?>" required />
                            </div>
                            <div>
                                <label for="swift" class="formbold-form-label"> <?= NEW_FIELD_SWIFT ?> </label>
                                <input type="text" name="swift" id="swift" class="formbold-form-input input-value"
                                    placeholder="exemple: ECMSBEBBXXX" required />
                            </div>
                        </div>

                        <h3 class="header-section" style="margin-top: 60px !important;"><?= TITLE_HEADER_ID_CARD ?></h3>
                        <div class="formbold-input-flex" style="margin-bottom: 0px !important;">
                            <div>
                                <label for="idCard" class="formbold-form-label"> <?= NEW_FIELD_ID_CARD ?> </label>
                                <input type="text" name="idCard" id="idCard" class="formbold-form-input input-value"
                                    placeholder="exemple: 000-5900000-02" required />
                            </div>
                            <div class="formbold-mb-3" style="margin-bottom: 0px !important;">
                                <label for="expiryDate" class="formbold-form-label">
                                    <?= NEW_FIELD_EXPIRY_CARD_ID ?>
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" id="expiryDate" name="expiryDate" class="formbold-form-input masked-input" maxlength="10" placeholder="jj.mm.aaaa" required>
                                    <span class="mask">__/__/____</span>
                                </div>
                                <div id="expiryError" class="error-message"><?= NEW_ERROR_MESSAGE_EXPIRY_ID_CARD ?></div>
                                <div id="expiryDateError" class="error-message"><?= NEW_ERROR_MESSAGE_EXPIRYDATE_ID_CARD ?></div>
                            </div>
                        </div>


                        <h3 class="header-section" style="margin-top: 25px !important;"><?= TITLE_HEADER_PACKAGES ?></h3>
                        <div>
                            <label for="packageNumber" class="formbold-form-label"><?= NEW_FIELD_PACKAGE_NUMBER ?></label>
                            <input type="number" step="1" min="1" name="packageNumber" id="packageNumber" class="formbold-form-input" placeholder="0" style="width: 12%; margin-bottom: 20px;" required />
                        </div>
                        <div id="packagesContainer"></div>


                        <div class="formbold-checkbox-wrapper">
                            <label for="supportCheckbox" class="formbold-checkbox-label">
                                <div class="formbold-relative">
                                    <input type="checkbox" id="supportCheckbox" class="formbold-input-checkbox" required />
                                    <div class="formbold-checkbox-inner">
                                        <span class="formbold-opacity-0">
                                            <svg width="11" height="8" viewBox="0 0 11 8" fill="none"
                                                class="formbold-stroke-current">
                                                <path
                                                    d="M10.0915 0.951972L10.0867 0.946075L10.0813 0.940568C9.90076 0.753564 9.61034 0.753146 9.42927 0.939309L4.16201 6.22962L1.58507 3.63469C1.40401 3.44841 1.11351 3.44879 0.932892 3.63584C0.755703 3.81933 0.755703 4.10875 0.932892 4.29224L0.932878 4.29225L0.934851 4.29424L3.58046 6.95832C3.73676 7.11955 3.94983 7.2 4.1473 7.2C4.36196 7.2 4.55963 7.11773 4.71406 6.9584L10.0468 1.60234C10.2436 1.4199 10.2421 1.1339 10.0915 0.951972ZM4.2327 6.30081L4.2317 6.2998C4.23206 6.30015 4.23237 6.30049 4.23269 6.30082L4.2327 6.30081Z"
                                                    stroke-width="0.4"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <?= NEW_FIELD_LASTTEXT_CONDITION ?>
                                <a href="<?= NEW_LINK_CONDITION ?>" target="_blank"> <?= NEW_FIELD_FIRSTTEXT_CONDITION ?></a>
                            </label>
                        </div>

                        <div class="btn-submit-form">
                            <button type="submit" class="formbold-btn"><?= BTN_SUBMIT_BORDEREAU ?></button>
                            <div id="formError" class="form-error-message"><?= BTN_SUBMIT_ERROR_BORDEREAU ?></div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        const NEW_TEXT_INDEX_PACKAGE = "<?= NEW_TITLE_INDEX_PACKAGE ?>";
        const NEW_TEXT_TYPE_METAL = "<?= NEW_FIELD_TYPE_METAL ?>";
        const NEW_PLACEHLDR_TYPE_METAL = "<?= NEW_PLACEHOLDER_TYPE_METAL ?>";
        const NEW_TEXT_WEIGHT = "<?= NEW_FIELD_WEIGHT ?>";
        const NEW_PLACEHLDR_WEIGHT = "<?= NEW_FIELD_WEIGHT_PLACEHOLDER ?>";
        const NEW_TEXT_DESCRIPTION_PACKAGE = "<?= NEW_FIELD_DESCRIPTION_PACKAGE ?>";
        const NEW_PLACEHLDR_DESCRIPTION_PACKAGE = "<?= NEW_PLACEHOLDER_DESCRIPTION_PACKAGE ?>"
        const NEW_BUTTON_ADD_METAL = "<?= NEW_BTN_ADD_METAL ?>"
        const NEW_ERROR_TOTAL_WEIGHT = "<?= NEW_ERROR_TOTAL_WEIGHT ?>"
        const NEW_ERROR_TOTAL_MATERIAL = "<?= NEW_ERROR_TOTAL_MATERIAL ?>"
    </script>

    <!-- IMPORT SCRIPTS JS -->
    <script src="js/main.js"></script>
    <script src="js/display-form.js"></script>
    <script src="js/inputAnimation.js"></script>
    <script src="js/validateInput.js"></script>
    <script src="js/addPackage.js"></script>
    <script src="js/addressFindingAPI.js"></script>
    <script src="js/IBANAPI.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSabS4IR4na718B5zm0NB0sPdgg3Da-7E&libraries=places&callback=initAutocomplete"
        defer>
    </script>
    <script src="js/recaptchaValidation.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>


</body>

</html>