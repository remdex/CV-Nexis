<?php

return [
    'title' => 'Companies',
    'description' => 'Manage companies and their information',
    'add' => 'Add Company',
    'create' => 'Create Company',
    'create_button' => 'Add Company',
    'edit' => 'Edit Company',
    'edit_title' => 'Edit Company',
    'update_button' => 'Update Company',
    'delete' => 'Delete',
    'delete_confirm' => 'After deleting, the company will be gone forever.',
    'view' => 'View',
    
    'fields' => [
        'company_code' => 'Company Code',
        'company_name' => 'Company Name',
        'activity' => 'Activity',
        'type' => 'Type',
        'country' => 'Country',
        'registration_date' => 'Registration Date',
        'vat_code' => 'VAT Code',
        'created' => 'Created',
        'no_activity' => '-',
        'detailed_information' => 'Detailed Information',
        'view_details' => 'View Details',
    ],
    
    'filters' => [
        'company_code' => 'Company Code',
        'company_name' => 'Company Name',
        'search_by_code' => 'Search by company code',
        'search_by_name' => 'Search by company name',
        'search_by_vat_code' => 'Search by VAT code',
    ],
    
    'form_fields' => [
        'company_code' => 'Company Code',
        'company_name' => 'Company Name',
        'client_type' => 'Client Type',
        'country' => 'Country',
        'registration_date' => 'Registration Date',
        'deregistration_date' => 'Deregistration Date',
        'type_code' => 'Type Code',
        'type_description' => 'Type Description',
        'vat_code' => 'VAT Code',
        'vat_code_prefix' => 'VAT Code Prefix',
        'vat_registered' => 'VAT Registered',
        'vat_deregistered' => 'VAT Deregistered',
        'division_number' => 'Division Number',
        'division_name' => 'Division Name',
        'division_municipality' => 'Division Municipality',
        'division_code' => 'Division Code',
        'veiklos_pradzia' => 'Activity start',
        'veiklos_pabaiga' => 'Activity end',
    ],
    
    'form_placeholders' => [
        'company_code' => 'Enter company code',
        'company_name' => 'Enter company name',
        'client_type' => 'e.g., LJA',
        'country' => 'e.g., LTU',
        'vat_code_prefix' => 'e.g., LT',
    ],
    
    'form_help' => [
        'company_code' => 'Unique company identification code',
    ],
    
    'alerts' => [
        'created' => 'Company created successfully',
        'updated' => 'Company updated successfully',
        'deleted' => 'Company deleted successfully',
        'saved' => 'Company saved successfully',
    ],
];