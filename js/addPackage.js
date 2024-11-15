const packagesContainer = document.getElementById('packagesContainer');
let currentPackageCount = 0; // Compte actuel des colis affichés

const bordereauFormCheck = document.getElementById("bordereauForm");


// Désactive le scroll dans l'input
document.getElementById('packageNumber').addEventListener('wheel', function(event) {
    event.preventDefault();
});

document.getElementById('packageNumber').addEventListener('change', function() {
    const packageNumber = parseInt(this.value) || currentPackageCount;

    // Ajouter ou retirer les sections de colis en fonction du nombre spécifié
    if (packageNumber > currentPackageCount) {
        for (let i = currentPackageCount + 1; i <= packageNumber; i++) {
            addPackageSection(i);
        }
    } else if (packageNumber < currentPackageCount) {
        for (let i = currentPackageCount; i > packageNumber; i--) {
            const packageDiv = document.getElementById(`package-${i}`);
            if (packageDiv) packageDiv.remove();
        }
    }
    currentPackageCount = packageNumber;
});

// Fonction pour vérifier le poids total des matériaux d'un colis spécifique
function checkTotalWeight(packageId) {
    const materialsSection = document.getElementById(`materials-section-${packageId}`);
    
    if (!materialsSection) return;

    const weightInputs = materialsSection.querySelectorAll('.weight-selected');
    let totalWeight = 0;

    // Calculer la somme des poids pour le colis spécifique
    weightInputs.forEach(input => {
        const weight = parseFloat(input.value) || 0;
        totalWeight += weight;
    });

    // Gérer l'affichage du message d'erreur en fonction du poids total
    const weightTotalError = document.getElementById(`weightTotalError-${packageId}`);
    if (weightTotalError) {
        if (totalWeight < 10) {
            weightTotalError.classList.add('show');
        } else {
            weightTotalError.classList.remove('show');
        }
    }
}

// Fonction pour ajouter un nouveau colis
function addPackageSection(index) {
    const packageDiv = document.createElement('div');
    packageDiv.classList.add('package-number');
    packageDiv.id = `package-${index}`;
    packageDiv.innerHTML = `
        <h4>${NEW_TEXT_INDEX_PACKAGE} ${index}</h4>
        <div class="materials-section" id="materials-section-${index}">
            <div class="formbold-input-flex material-entry">
                <div>
                    <label class="formbold-form-label">${NEW_TEXT_TYPE_METAL}</label>
                    <input type="text" name="materialType[${index}][]" class="formbold-form-input" placeholder="${NEW_PLACEHLDR_TYPE_METAL}" required />
                </div>
                <div>
                    <label class="formbold-form-label">${NEW_TEXT_WEIGHT}</label>
                    <input type="number" step="1" min="0" name="weight[${index}][]" class="formbold-form-input weight-selected" placeholder="${NEW_PLACEHLDR_WEIGHT}" required />
                </div>
                <div>
                    <label class="formbold-form-label">${NEW_TEXT_DESCRIPTION_PACKAGE}</label>
                    <input type="text" name="description[${index}][]" class="formbold-form-input" placeholder="${NEW_PLACEHLDR_DESCRIPTION_PACKAGE}" />
                </div>
            </div>
        </div>
        <button type="button" class="btn-add add-material-button" data-package-id="${index}">${NEW_BUTTON_ADD_METAL}</button>
        <p class="error-message" id="weightTotalError-${index}" style="margin-top: 0 !important;">${NEW_ERROR_TOTAL_WEIGHT}</p>
        <p class="error-message" id="materialLimitError-${index}">${NEW_ERROR_TOTAL_MATERIAL}</p>
    `;
    packagesContainer.appendChild(packageDiv);

    // Attacher l'événement input à chaque champ de poids existant
    packageDiv.querySelectorAll('.weight-selected').forEach(input => {
        input.addEventListener('input', () => checkTotalWeight(index));
    });

    // Bouton pour ajouter un nouveau matériau
    packageDiv.querySelector('.add-material-button').addEventListener('click', function() {
        addMaterial(index);
    });
}

// Fonction pour ajouter un nouveau matériau dans le colis spécifié
function addMaterial(packageId) {
    const materialsSection = document.getElementById(`materials-section-${packageId}`);
    const materialEntries = materialsSection.getElementsByClassName('material-entry');

    // Limite de matériaux à 5 par colis
    if (materialEntries.length >= 5) {
        const materialLimitError = document.getElementById(`materialLimitError-${packageId}`);
        if (materialLimitError) {
            materialLimitError.classList.add('show');
        }
        return;
    }

    // Supprimer le message d'erreur s'il existe et que la limite n'est pas atteinte
    const materialLimitError = document.getElementById(`materialLimitError-${packageId}`);
    if (materialLimitError) {
        materialLimitError.classList.remove('show');
    }

    const newMaterialEntry = document.createElement('div');
    newMaterialEntry.className = 'formbold-input-flex material-entry';

    newMaterialEntry.appendChild(createInputField('Type de matériaux', `materialType[${packageId}][]`, 'text', 'exemple: étain'));
    newMaterialEntry.appendChild(createInputField('Poids en kg', `weight[${packageId}][]`, 'number', 'Poids en kg', '1'));
    newMaterialEntry.appendChild(createInputField('Descriptif (facultatif)', `description[${packageId}][]`, 'text', 'exemple: couverts'));

    materialsSection.appendChild(newMaterialEntry);

    // Attacher l'événement input au nouveau champ de poids
    const weightInput = newMaterialEntry.querySelector('.weight-selected');
    if (weightInput) {
        weightInput.addEventListener('input', () => checkTotalWeight(packageId));
    }

    // Appel à la fonction checkTotalWeight pour recalculer immédiatement avec le nouvel input
    checkTotalWeight(packageId);
}

// Fonction pour créer un champ d'entrée dans le formulaire
function createInputField(labelText, name, type = 'text', placeholder = '', step = null) {
    const fieldWrapper = document.createElement('div');
    fieldWrapper.className = 'input-field-wrapper';

    const label = document.createElement('label');
    label.className = 'formbold-form-label';
    label.textContent = labelText;

    const input = document.createElement('input');
    input.type = type;
    input.name = name;
    input.className = type === 'number' ? 'formbold-form-input weight-selected' : 'formbold-form-input';
    input.placeholder = placeholder;

    if (step) {
        input.step = step;
    }

    fieldWrapper.appendChild(label);
    fieldWrapper.appendChild(input);

    return fieldWrapper;
}

