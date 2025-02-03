import axios from 'axios';

/**
 * Constants for use in multiple functions
 * @type {HTMLElement}
 */

// Lists for needs and services
const availableList = document.getElementById('available-attribute-list');
const currentList = document.getElementById('current-attributes-list');


const addButton = document.getElementById('add-attribute');
const addForm = document.getElementById('available-attributes');
const removeButton = document.getElementById('remove-attribute');
const removeForm = document.getElementById('current-attributes');

// These should be able to be added into an array or their individual functions
const saveRegistrantButton = document.getElementById('save-registrant');
const saveCustomerButton = document.getElementById('save-customer');
const updateUserButton = document.getElementById('update-user');
const saveNeedButton = document.getElementById('save-need');
const createNeedButton = document.getElementById('create-need');
const saveServiceButton = document.getElementById('save-service');
const createServiceButton = document.getElementById('create-service');
const saveUserButton = document.getElementById('save-user');
const createOrganisationButton = document.getElementById('create-organisation');
const updateOrganisationButton = document.getElementById('update-organisation');
const createRepresentativeButton = document.getElementById('create-representative');
const updateRepresentativeButton = document.getElementById('update-representative');




/**
 * Function to add the attribute to the selected list
 * @param {HTMLFormElement} form - the form element to be submitted
 * @param {string} url - the endpoint to send the form data to
 * @param {HTMLElement} responseMessageElement - the element to display success/error messages
 */
/*function submitForm(form, url, responseMessageElement) {

    const formData=new FormData(form);

    axios.post(url, formData)
        .then(response => {
            responseMessageElement.innerText = response.data.message;
        })
        .catch(error => {
            responseMessageElement.innerText = 'An error occurred.';
            console.error(error);
        });
}
*/


/**
 * Generic function to send POST requests to Laravel Controllers then display the response
 * using the Flash Message control
 * @param url A string representing the route
 * @param payload A set of data to be sent
 */
function axiosPost(url, payload) {

    //console.log(url, payload);
    //debugger;

    // Clear previous errors
    document.querySelectorAll('.error').forEach(function (el) {
        el.textContent = '';
    });
    document.querySelectorAll('.is-invalid').forEach(function (el) {
        el.classList.remove('is-invalid');
    });


    // Send the data to the URL with the payload
    axios.post(url, payload)

        .then(response => {

            // Save the flash message
            saveFlashMessage(response.data.message, 'success');

            //console.log(response.data.redirect_url);
            //debugger;

            // Redirect to the URL provided by Laravel
            window.location.href = response.data.redirect_url;

        })
        .catch(error => {

/*
            // Catches the error message and displays it to the screen
            // The message resets the error field highlights at the moment so needs to be reviewed

            // Save the flash message
            saveFlashMessage(error.response.data.message, 'error');

            // Redirect to the URL provided by Laravel
            window.location.href = window.location;


 */
/*
            console.log(error.response.data.errors);
            console.log(error.response.data.message);
            debugger;
*/

            if (error.response && error.response.status === 422) {
                // Get the validation errors
                const errors = error.response.data.errors;


                // Update the error fields
                Object.keys(errors).forEach(function (key) {
                    const errorSpan = document.getElementById(`${key}-error`);
                    if (errorSpan) {
                        errorSpan.textContent = errors[key][0]; // Show the first error message
                    }

                    const inputField = document.querySelector(`[name="${key}"]`);
                    //console.log(inputField);
                    //debugger;
                    if (inputField) {
                        inputField.classList.add('is-invalid');
                    }

                });

            } else {

                //console.error('Error:', error);
                //debugger;

                // Save the error flash message
                saveFlashMessage(error.response?.data?.message
                    || 'An unexpected error occurred.', 'danger');

                // Reload the window to display the message
                window.location = window.location;

            }
        });

}

/*
function displayValidationErrors(errors) {

    // Clear any previous errors
    document.querySelectorAll('.error').forEach(el => el.textContent = '');

    // Display new errors
    for (const [field, messages] of Object.entries(errors)) {
        const errorElement = document.querySelector(`#${field}-error`);
        if (errorElement) {
            errorElement.textContent = messages.join(', ');
        }
    }
}
 */




/**
 * Function to display flash messages - stores as a local session so it's maintained during
 * redirect
 */
function showFlashMessage(message, type) {

    const flashContainer = document.getElementById('flash-container');

    if (!flashContainer) {
        console.error('Flash container not found');
        return;
    }

    const flashMessage = document.createElement('div');
    flashMessage.className = `alert alert-${type} d-inline-block`;
    flashMessage.textContent = message;

    flashContainer.innerHTML = '';
    flashContainer.appendChild(flashMessage);

    setTimeout(()=> {
        flashMessage.style.opacity = 0;    // Fade out
        setTimeout(() => {
            if (flashContainer.contains(flashMessage)) {
                flashMessage.remove();
            }
        }, 100);
    },1500)
}

// Check for flash message in local storage
document.addEventListener('DOMContentLoaded', () => {
    const flashMessage = localStorage.getItem('flashMessage');
    const flashType = localStorage.getItem('flashType');

    if (flashMessage && flashType) {
        showFlashMessage(flashMessage, flashType);

        // Clear the flash message after showing
        localStorage.removeItem('flashMessage');
        localStorage.removeItem('flashType');
    }
});

// Save the flash message before page reload
function saveFlashMessage(message, type) {
    localStorage.setItem('flashMessage', message);
    localStorage.setItem('flashType', type);
}


/**
 * Function to get the formData from the registrant form which then suffixes
 * the data with the selected attributes
 * @returns {FormData}
 */
function registrantDetails() {

    const registrantForm = document.getElementById('registrant-data');

    // Get the Registrant Form Data and the list of selected attributes
    const registrantData = new FormData(registrantForm);
    const attributeData = currentList.options;
    let tlcDate = document.getElementById('tlc-picker').value;
    let phrDate = document.getElementById('phr-picker').value;
    let yahDate = document.getElementById('yah-picker').value;

    // Create an empty array of the attributes
    let parsedAttributes = [];

    // Add the JSON data to the parsedAttributes array with the text value inserted
    Array.from(attributeData).forEach((option, index) => {
        let optionData = JSON.parse(option.value);

        optionData = {
            ...optionData,
            description: option.text,
        }
        parsedAttributes.push(optionData);
    });


    // Append the attribute data array to the form data
    registrantData.append('arrayData', JSON.stringify(parsedAttributes));
    registrantData.append('tlc_date', tlcDate);
    registrantData.append('phr_date', phrDate);
    registrantData.append('yah_date', yahDate);

/*
    console.log(registrantData.entries);

    // Output the form data to the console
    for (const pair of registrantData.entries()) {
        console.log(pair[0], pair[1]);
    }

    debugger;
*/

    return registrantData

}



/**
 * Function to compile the registrant data and submit it to be saved
 */

export function saveRegistrant(url) {

    // Call the function to get the details
    const registrantData = registrantDetails();

//    console.log(registrantData.entries);
//    debugger;

    // Send the data to the Registration Controller with the form data and attributes
    axiosPost(url, registrantData);

}


/**
 * Function to save the Organisation record after editing
 *
 */
export function saveOrganisation(url) {

    // The list of currently selected properties
    const currentPropertiesList = document.getElementById('current-properties');
    const organisationForm = document.getElementById('organisation-data');

    // Get the Registrant Form Data and the list of selected attributes
    const organisationData = new FormData(organisationForm);
    const propertiesData = currentPropertiesList.options;

    // Create an empty array of the properties
    let parsedProperties = [];

    // Add the JSON data to the parsedProperties array with the text value inserted
    Array.from(propertiesData).forEach((property, index) => {
        let propertyData = property.value;

        parsedProperties.push(propertyData);
    });

    // Append the attribute data array to the form data
    organisationData.append('propertyData', JSON.stringify(parsedProperties));

    axiosPost(url, organisationData);

}


export function createWithBoolean(url, type) {

    let form;

    switch (type) {
        case 'need':
            form = document.getElementById('create-need-code-data');
        break;
        case 'service':
            form = document.getElementById('create-service-code-data');
        break;
        case 'organisation':
            form = document.getElementById('create-organisation-data');
        break;
        case 'representative':
            form = document.getElementById('create-representative-data');
            break;
    }

    // Get the Registrant Form Data and the list of selected attributes
    const data = new FormData(form);

    // Ensure `active` is always added, even if unchecked
    const activeCheckbox = form.querySelector('input[type="checkbox"][name="active"]');
    data.set('active', activeCheckbox && activeCheckbox.checked ? '1' : '0');

    // If we have a checkbox called Token then ensure it is always added
    const tokenCheckbox = form.querySelector('input[type="checkbox"][name="token"]');
    if( tokenCheckbox ) {
        data.set('token', tokenCheckbox && tokenCheckbox.checked ? '1' : '0');
    }



/*
    console.log(data.entries);

    // Output the form data to the console
    for (const pair of data.entries()) {
        console.log(pair[0], pair[1]);
    }

    debugger;
*/

    axiosPost(url, data);
}




/**
 * Function to call the update attribute processes
 * @param {HTMLFormElement} list - the list containing the data
 */

export async function updateAttribute(list) {

//debugger;

    let fromList;
    let toList;

    if(list === availableList) {
        fromList = availableList;
        toList = currentList;
    } else {
        fromList = currentList;
        toList = availableList;
    }

    // Get the index selected from the list and extract the Value and Text
    const selectedIndex = fromList.selectedIndex;
    const selectedOption = fromList.options[selectedIndex];
    const selectedValue = selectedOption.value;
    const selectedText = selectedOption.text;

    // Parse the Value as a JSON to select the elements
    const parsedData = JSON.parse(selectedValue);

    // Build a new option to add to the list we're passing to
    const newOption = document.createElement("option");


    // Update our list values based on the selected code and action
    if (parsedData.action === "add") {

        // Add code to the selected list and update the action to Remove
        newOption.value = '{"sort":'.concat(parsedData.sort, ',"code":"',parsedData.code, '","type":"', parsedData.type, '","state":"', parsedData.state, '","action":"remove"}');
        newOption.text = selectedText;
        toList.appendChild(newOption);

        // Remove the code
        const optionToRemove = fromList.options[selectedIndex]
        fromList.removeChild(optionToRemove)

    } else if (parsedData.action === "remove") {
        // Add code to the available list and update the action to Add
        newOption.value = '{"sort":'.concat(parsedData.sort, ',"code":"',parsedData.code, '","type":"', parsedData.type, '","state":"', parsedData.state, '","action":"add"}');
        newOption.text = selectedText;
        toList.appendChild(newOption);


        // Remove the code
        const optionToRemove = fromList.options[selectedIndex]
        fromList.removeChild(optionToRemove)

    }

    /**
     * Load the toList options into an array so we can update whether they
     * are Disabled or not and sort them into the correct order based on the
     * Index value
     */
    let optionsArray = await updateOptions();

    optionsArray.sort((a, b) => {
        return a.sort - b.sort;
    });

    availableList.innerHTML = "";

    optionsArray.forEach(o => {
        const option = document.createElement("option");
        option.value = JSON.stringify(o);
        option.text = o.text;
        availableList.appendChild(option);
    });

}



function refreshAPIToken() {

    const element = document.getElementById('representativeapitoken');

    fetch('/refresh-token', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
        .then(response => response.json())
        .then(data => {
            element.value = data.token;
            copyToClipboard(element);

        })
        .catch(error => console.error('Error generating token: ', error));
}

function copyToClipboard(element) {
    const text = element.value;
    navigator.clipboard.writeText(text)
        .then(() => {
            // Show a copied message
            const message = document.createElement("div");
            message.textContent = "Copied to clipboard!";
            message.style.position = "fixed";
            message.style.bottom = "20px";
            message.style.right = "20px";
            message.style.padding = "10px 15px";
            message.style.backgroundColor = "#4caf50";
            message.style.color = "white";
            message.style.borderRadius = "5px";
            message.style.boxShadow = "0px 4px 6px rgba(0, 0, 0, 0.1)";
            message.style.zIndex = "1000";
            message.style.fontSize = "14px";

            document.body.appendChild(message);

            // Remove the message after 2 seconds
            setTimeout(() => {
                message.remove();
            }, 2000);
        })
        .catch(err => {
            console.error("Failed to copy text: ", err);
        });
}





/**
 * Function that automatically applies the javascript to the external buttons
 * based on the provided elements
 */
function initializeFormHandlers() {

    // Attach submit button handler
/*    if (submitButton && form && responseMessage) {
        submitButton.addEventListener('click', () => {
            submitForm(form, '/submit-data', responseMessage);
        });
    }
*/
/*
    if (addButton && addForm) {
        addButton.addEventListener('click', ()=>{
            updateAttribute(availableList);
        })
    }

    if (removeButton && removeForm) {
        removeButton.addEventListener('click', ()=>{
            updateAttribute(currentList) ;
        })
    }
*/

    if (saveRegistrantButton) {
        saveRegistrantButton.addEventListener('click', ()=>{
            saveRegistrant('/registrations-store');
        })
    }

    if(saveCustomerButton) {
        saveCustomerButton.addEventListener('click', ()=>{
            saveRegistrant('/customer-update');
        })
    }

    if (updateUserButton) {
        updateUserButton.addEventListener('click', function () {
            document.getElementById('user-data').submit();
        })
    }

    if (saveNeedButton) {
        saveNeedButton.addEventListener('click', function() {
            document.getElementById('need-code-data').submit();
        })
    }

    if (createNeedButton) {
        createNeedButton.addEventListener('click', function () {
            createWithBoolean('/create-new-need', 'need');
        })
    }

    if (saveServiceButton) {
        saveServiceButton.addEventListener('click', function() {
            document.getElementById('service-code-data').submit();
        })
    }

    if (createServiceButton) {
        createServiceButton.addEventListener('click', function () {
            createWithBoolean('/create-new-service', 'service');
        })
    }

    if (saveUserButton) {
        saveUserButton.addEventListener('click', function() {
            document.getElementById('create-user-data').submit();
        })
    }

    if (createOrganisationButton) {
        createOrganisationButton.addEventListener('click', function () {
            createWithBoolean('/create-new-organisation', 'organisation');
        })
    }

    if (updateOrganisationButton) {
        updateOrganisationButton.addEventListener('click', function () {
            //document.getElementById('organisation-data').submit();
            saveOrganisation('/organisation-update');
        })
    }

    if (createRepresentativeButton) {
        createRepresentativeButton.addEventListener('click', function () {
            createWithBoolean('/create-new-representative', 'representative');
        })
    }

    if (updateRepresentativeButton) {
        updateRepresentativeButton.addEventListener('click', function () {
            document.getElementById('representative-data').submit();
        })
    }

    if(document.getElementById('create-api-token')) {
        document.getElementById('create-api-token').addEventListener('click', function () {
            refreshAPIToken();
        })
    }




}


// Initialize form handlers when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeFormHandlers);

