<?php

return [

    /*
    | Sagay City has 25 barangays. VERIFY every spelling against SWD / PSA records,
    | because the registration form only accepts values from this list.
    */
    'barangays' => [
        'Andres Bonifacio', 'Bato', 'Baviera', 'Bulanon', 'Campo Himoga-an',
        'Campo Santiago', 'Colonia Divina', 'Fabrica', 'General Luna',
        'Himoga-an Baybay', 'Lopez Jaena', 'Make', 'Malubon', 'Molocaboc',
        'Old Sagay', 'Paraiso', 'Plaridel', 'Poblacion I', 'Poblacion II',
        'Puey', 'Rafaela Barrera', 'Rizal', 'Taba-ao', 'Tadlong', 'Vito',
    ],

    /*
    | Map sitio / area names that OpenStreetMap returns (left, any case)
    | to the official barangay (right, must match the list above exactly).
    | Add entries as you notice wrong or missing matches on the form.
    |
    | Example:
    |   'some sitio name' => 'Old Sagay',
    */
    'barangay_aliases' => [
        //
    ],

];
