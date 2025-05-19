<?php


// List of all Admin GET Routes protected by AUTH
return [
    'users_index' => ['/users'],
    'users_create' => ['/users/create'],
    'users_show' => ['/users/1'], // Now user 1 exists
    'users_edit' => ['/users/1/edit'],
    'needcodes' => ['/needcodes'],
    'needcodes_edit' => ['/needcodes/1/edit'],
    'needcodes_create' => ['/needcodes/create'],
    'servicecodes' => ['/servicecodes'],
    'servicecodes_edit' => ['/servicecodes/FBR/edit'],
    'servicecodes_create' => ['/servicecodes/create'],
    'organisations' => ['/organisations'],
    'organisations_create' => ['/organisations/create'],
    'organisations_show' => ['/organisations/1'],
    'organisations_edit' => ['/organisations/1/edit'],
    'representatives' => ['/representatives'],
    'representatives_create' => ['/representatives/create'],
    'representatives_show' => ['/representatives/1'],
    'representatives_edit' => ['/representatives/1/edit'],
    'metrics' => ['/metrics'],
];
