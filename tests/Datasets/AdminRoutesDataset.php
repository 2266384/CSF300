<?php


// List of all Admin GET Routes protected by AUTH
return [
    'users index' => ['/users'],
    'users create' => ['/users/create'],
    'users show' => ['/users/1'], // Now user 1 exists
    'users edit' => ['/users/1/edit'],
    'needcodes' => ['/needcodes'],
    'needcodes edit' => ['/needcodes/1/edit'],
    'needcodes create' => ['/needcodes/create'],
    'servicecodes' => ['/servicecodes'],
    'servicecodes edit' => ['/servicecodes/FBR/edit'],
    'servicecodes create' => ['/servicecodes/create'],
    'organisations' => ['/organisations'],
    'organisations create' => ['/organisations/create'],
    'organisations show' => ['/organisations/1'],
    'organisations edit' => ['/organisations/1/edit'],
    'representatives' => ['/representatives'],
    'representatives create' => ['/representatives/create'],
    'representatives show' => ['/representatives/1'],
    'representatives edit' => ['/representatives/1/edit'],
    'metrics' => ['/metrics'],
];
