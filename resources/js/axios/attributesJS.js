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
function submitForm(form, url, responseMessageElement) {

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

/**
 * Generic function to send POST requests to Laravel Controllers then display the response
 * using the Flash Message control
 * @param url A string representing the route
 * @param payload A set of data to be sent
 */
function axiosPost(url, payload) {

    // Send the data to the Registration Controller with the form data and attributes
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

            //console.error('Error:', error);
            //debugger;

            // Save the error flash message
            saveFlashMessage(error.response.data.details, 'danger');

            // Reload the window to display the message
            window.location = window.location;

        });

}


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

    // Create an empty array of the attributes
    let parsedAttributes = [];

    // Add the JSON data to the parsedAttributes array with the text value inserted
    Array.from(attributeData).forEach((option, index) => {
        let optionData = JSON.parse(option.value);
        optionData = {...optionData, description: option.text}
        parsedAttributes.push(optionData);
    });

    // Append the attribute data array to the form data
    registrantData.append('arrayData', JSON.stringify(parsedAttributes));

    /*
    // Output the form data to the console
    for (const pair of registrantData.entries()) {
        console.log(pair[0], pair[1]);
    }
    */

    //debugger;

    return registrantData

}



/**
 * Function to compile the registrant data and submit it to be saved
 */

export function saveRegistrant() {

    // Call the function to get the details
    const registrantData = registrantDetails()

    // Send the data to the Registration Controller with the form data and attributes
    axiosPost('/registrations-store', registrantData);

    /*
    // Send the data to the Registration Controller with the form data and attributes
    axios.post('/registrations-store', registrantData)
        .then(response => {

            // Save the flash message
            saveFlashMessage(response.data.message, 'success');

            //console.log(response.data.redirect_url);

            // Redirect to the URL provided by Laravel
            window.location.href = response.data.redirect_url;

        })
        .catch(error => {

            //console.error('Error:', error);

            //debugger;

            // Save the error flash message
            saveFlashMessage(error.response.data.details, 'danger');

            // Reload the window to display the message
            window.location = window.location;

        });
    */
}


/**
 * Function to save the customer record after editing
 *
 */
export function saveCustomer() {

    // Call the function to get the details
    const registrantData = registrantDetails()

    axiosPost('/customer-update', registrantData);

/*
    // Send the data to the Registration Controller with the form data and attributes
    axios.post('/customer-update', registrantData)
        .then(response => {
            console.log(response.data);

            //const data = response.data

            //alert(data.join('\n'));
            alert(response.data.message);
        })
        .catch(error => {
            console.error(error.response.data);
        });
*/

}




/**
 * Function to call the update attribute processes
 * @param {HTMLFormElement} list - the list containing the data
 */
//export async function updateAttribute(list) {
export function updateAttribute(list) {

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

    let filteredArray = [];

    // Update our list values based on the selected code and action
    if (parsedData.action === "add") {

        //const list = document.getElementById("current-attributes-list");
        // Add code to the selected list and update the action to Remove
        newOption.value = '{"sort":'.concat(parsedData.sort, ',"code":"',parsedData.code, '","type":"', parsedData.type, '","action":"remove"}');
        newOption.text = selectedText;
        toList.appendChild(newOption);

//        console.log(parsedData.code);
/*
        // Get all the available actions from our Attribute Controller
        const codeArray = await fetchActions();

        // Filter the array to match the current code
        codeArray.forEach(item => {
            if (item.code === parsedData.code) {
                filteredArray.push(item);
            }
        });
*/
        //console.log(filteredArray);



    } else if (parsedData.action === "remove") {
        // Add code to the selected list and update the action to Add
        newOption.value = '{"sort":'.concat(parsedData.sort, ',"code":"',parsedData.code, '","type":"', parsedData.type, '","action":"add"}');
        newOption.text = selectedText;
        toList.appendChild(newOption);
/*
        // Get all the available actions from our Attribute Controller
        const codeArray = await fetchActions();

        // Filter the array to match the current code
        codeArray.forEach(item => {
            if (item.code === parsedData.code) {
                filteredArray.push(item);
            }
        });
*/
    }


    /**
     * Load the toList options into an array so we can update whether they
     * are Disabled or not and sort them into the correct order based on the
     * Index value
     */
    let optionsArray = Array.from(toList.options);

/*
    optionsArray.forEach(item => {
        console.log(item);
    });

    optionsArray =  optionsArray.map(item => {
        let obj = JSON.parse(item);

        console.log(obj);

    })
    let optionsArray = await updateOptions(filteredArray);

*/

    optionsArray.sort((a, b) => {
        const aData = JSON.parse(a.value);
        const bData = JSON.parse(b.value);

        return aData.sort - bData.sort;
    });

    toList.innerHTML = "";

    optionsArray.forEach(option => toList.appendChild(option));

    updateOptions();
    //console.log(optionsArray);

    const optionToRemove = fromList.options[selectedIndex]
    fromList.removeChild(optionToRemove)

    //console.log(parsedData.sort);
}



// Subroutine to fetch data
async function fetchActions() {
    try {
        // Fetch data using Axios
        const response = await axios.get('/actions');

        //console.log(response.data.first);
        // Return the fetched data as an array
        return response.data; // Return filtered array
    } catch (error) {
        console.error('Error fetching data:', error);
        return []; // Return an empty array if there is an error
    }
}


async function updateOptions() {

    /**
     * Load the toList options into an array so we can update whether they
     * are Disabled or not and sort them into the correct order based on the
     * Index value
     */

    // Get all the available actions from our Attribute Controller
    const actionArray = await fetchActions();


    // Create an Array of all available attributes in the list
    const availableList = document.getElementById("available-attribute-list");
    let availableArray = Array.from(availableList.options);

    const selectedList = document.getElementById("current-attributes-list");
    let selectedArray = Array.from(selectedList.options);

    // Get the actions for the selected attributes
    let selectedActions = [];

    selectedArray.forEach(selected => {

        const s = JSON.parse(selected.value);

        // Filter the actionArray to only the Selected attributes
        actionArray.forEach(action => {

            if (action.sourcecode === s.code) {

                selectedActions.push(action);
            }

        });

    });

    //console.log(selectedActions);

    // Set the default an empty array to hold the data
    let updatedOptions = [];

    // Iterate through the available options and update the state
    availableArray.forEach(option => {
        const a = JSON.parse(option.value);
        console.log(a);

        // Set the default state
        let state;

        if(a.type === 'need') {
            state = 'enabled';
        } else {
            state = 'disabled';
        }

        selectedActions.forEach( action => {

            // Matching code, both Needs, disable action
            if(action.targetcode === a.code
                && a.type === action.type
                && action.action === 'disabled') {

            // Matching code, type service, action need, enable action
            } else if (action.targetcode === a.code
                && a.type !== action.type
                && action.action === 'enabled')
            console.log(action.targetcode);
        })

    })

    // Iterate through the current options
    availableArray.forEach(option => {
        const opt = JSON.parse(option.value)
        //console.log(opt.code);


        actionArray.filter(action => {

            //console.log('Target Code: ' + action.targetcode);
            //console.log('Option Code: ' + opt.code);

            if( action.targetcode === opt.code) {
//                console.log(action.targetcode + ' : ' + opt.code);
            }
        })

    })





    actionArray.forEach(action => {
        //const b = JSON.parse(action);
        //console.log(action);
    })

/*
    actionArray.map(item => {
        console.log(item);
    })

    optionsArray.forEach(option => {
        const a = JSON.parse(option.value);
        console.log(a);
    })
    // Default our state to enabled
    let state = 'enabled'

    // Iterate through the current options
    availableArray.forEach(option => {
        const opt = JSON.parse(option.value)
        //console.log(opt.code);


        actionArray.filter(action => {

            //console.log('Target Code: ' + action.targetcode);
            //console.log('Option Code: ' + opt.code);

            if( action.targetcode === opt.code) {
//                console.log(action.targetcode + ' : ' + opt.code);
            }
        })

    })

*/

/*
    let mergedArray = actionArray.map(action => {

        console.log(action);

        let match = optionsArray.map(option => {
            if (option.code === action.targetcode) {
                return {
                    ...action,
                    ...option,
                };
            }
            return null;
        }).filter(a => a !== null);

        return match.length > 0 ? match[0] : action;
    });
*/

/*
    let mergedArray = optionsArray.map(item1 => {
        let match = actionArray.map(action => {
            if (action.targetcode === item1.code) {
                return {
                    ...item1.sort,
                    ...item1.code,
                    ...item1.type,
                    //state: if(...item1.state = 'enabled' && action.action = 'Disable') { 'disabled'} else { 'enabled' }
                    ...item1.action,
                    };
            }
            return null;
        }).filter(a => a !== null);

        return match.length > 0 ? match[0] : item1;
    });
*/


/*
    mergedArray.forEach(option => {
        console.log(option.value);
    })
*/

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
    if (submitButton && form && responseMessage) {
        submitButton.addEventListener('click', () => {
            submitForm(form, '/submit-data', responseMessage);
        });
    }

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

