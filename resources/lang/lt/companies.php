<?php

return [
    'title' => 'Įmonės',
    'description' => 'Valdyti įmones ir jų informaciją',
    'add' => 'Pridėti įmonę',
    'create' => 'Sukurti įmonę',
    'create_button' => 'Pridėti įmonę',
    'edit' => 'Redaguoti',
    'edit_title' => 'Redaguoti įmonę',
    'update_button' => 'Atnaujinti įmonę',
    'delete' => 'Ištrinti',
    'delete_confirm' => 'Ištrynus, įmonė bus visam laikui pašalinta.',
    'view' => 'Peržiūra',
    
    'fields' => [
        'company_code' => 'Įmonės kodas',
        'company_name' => 'Įmonės pavadinimas',
        'activity' => 'Veikla',
        'type' => 'Tipas',
        'country' => 'Valstybė',
        'registration_date' => 'Registracijos data',
        'vat_code' => 'PVM kodas',
        'created' => 'Sukurta',
        'no_activity' => '-',
        'veiklos_pradzia' => 'Veiklos pradžia',
        'veiklos_pabaiga' => 'Veiklos pabaiga',
        'detailed_information' => 'Detali informacija',
        'view_details' => 'Peržiūrėti detales',
    ],
    
    'filters' => [
        'company_code' => 'Įmonės kodas',
        'company_name' => 'Įmonės pavadinimas',
        'search_by_code' => 'Ieškoti pagal įmonės kodą',
        'search_by_name' => 'Ieškoti pagal įmonės pavadinimą',
        'search_by_vat_code' => 'Ieškoti pagal PVM kodą',
    ],
    
    'form_fields' => [
        'company_code' => 'Įmonės kodas',
        'company_name' => 'Įmonės pavadinimas',
        'client_type' => 'Kliento tipas',
        'country' => 'Valstybė',
        'registration_date' => 'Registracijos data',
        'deregistration_date' => 'Išregistravimo data',
        'type_code' => 'Tipo kodas',
        'type_description' => 'Tipo aprašymas',
        'vat_code' => 'PVM kodas',
        'vat_code_prefix' => 'PVM kodo priešdėlis',
        'vat_registered' => 'PVM įregistruota',
        'vat_deregistered' => 'PVM išregistruota',
        'division_number' => 'Padalinio numeris',
        'division_name' => 'Padalinio pavadinimas',
        'division_municipality' => 'Padalinio savivaldybė',
        'division_code' => 'Padalinio kodas',
    ],
    
    'form_placeholders' => [
        'company_code' => 'Įveskite įmonės kodą',
        'company_name' => 'Įveskite įmonės pavadinimą',
        'client_type' => 'pvz., LJA',
        'country' => 'pvz., LTU',
        'vat_code_prefix' => 'pvz., LT',
    ],
    
    'form_help' => [
        'company_code' => 'Unikalus įmonės identifikacijos kodas',
    ],
    
    'alerts' => [
        'created' => 'Įmonė sėkmingai sukurta',
        'updated' => 'Įmonė sėkmingai atnaujinta',
        'deleted' => 'Įmonė sėkmingai ištrinta',
        'saved' => 'Įmonė sėkmingai išsaugota',
    ],
];