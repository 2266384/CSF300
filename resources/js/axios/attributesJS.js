import axios from 'axios';

/**
 * Constants for use in multiple functions
 * @type {HTMLElement}
 */
const addButton = document.getElementById('add-attribute');
const addForm = document.getElementById('available-attributes');
const availableList = document.getElementById('available-attribute-list');

const removeButton = document.getElementById('remove-attribute');
const removeForm = document.getElementById('current-attributes');
const currentList = document.getElementById('current-attributes-list');

const saveRegistrantButton = document.getElementById('save-registrant');
const saveCustomerButton = document.getElementById('save-customer');





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

            console.log(error.response.data.errors);

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
    let tlcDate = document.getElementById('tlc').value;
    let phrDate = document.getElementById('phr').value;
    let yahDate = document.getElementById('yah').value;

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

export function saveRegistrant() {

    // Call the function to get the details
    const registrantData = registrantDetails();

//    console.log(registrantData.entries);
//    debugger;

    // Send the data to the Registration Controller with the form data and attributes
    axiosPost('/registrations-store', registrantData);

}


/**
 * Function to save the customer record after editing
 *
 */
export function saveCustomer() {

    // Call the function to get the details
    const registrantData = registrantDetails()

    axiosPost('/customer-update', registrantData);

}




/**
 * Function to call the update attribute processes
 * @param {HTMLFormElement} list - the list containing the data
 */

export async function updateAttribute(list) {

debugger;

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






/**
 * Function that automatically applies the javascript to the external buttons
 * based on the provided elements
 */
function initializeFormHandlers() {
    const submitButton = document.getElementById('externalSubmitBtn');
    const responseMessage = document.getElementById('responseMessage');
    const form = document.getElementById('userForm');

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
            saveCustomer('/customer-update');
        })
    }

}


// Initialize form handlers when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeFormHandlers);

