
import axios from 'axios';


export function submitData() {
    axios.post('/submit-data', {name: 'John Doe'})
        .then(response => {
            console.log(response.data);
            alert(response.data.message);
        })
        .catch(error => {
            console.error(error);
        });
}

export function submitNewData() {
    axios.post('/submit-data', {name: 'Fred Doe'})
        .then(response => {
            console.log(response.data);
            alert(response.data.message);
        })
        .catch(error => {
            console.error(error);
        });
}

/*
export function submitAll(data) {
    // Update this to get the FormData as a supplied payload
    const data = [
        ...new FormData(form1).entries(),
        ...new FormData(form2).entries()
    ]

    for(var pair in data.entries()) {
        console.log(pair[0], pair[1])
    }

    console.log(data);
}
*/
