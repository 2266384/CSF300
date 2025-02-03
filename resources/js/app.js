import './bootstrap';

import axios from 'axios';
import ApexCharts from 'apexcharts'

// Set Axios default headers for CSRF token
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['Accept'] = 'application/json';


// Optionally set the base URL if required
axios.defaults.baseURL = 'https://laravel.test.psr.orb.local';

axios.defaults.timeout = 5000; // Set timeout for requests

import { submitData, submitNewData } from './axios/submitData.js';
import { updateAttribute } from "./axios/custom.js";

window.submitData = submitData;
window.submitNewData = submitNewData;
window.updateAttribute = updateAttribute;

