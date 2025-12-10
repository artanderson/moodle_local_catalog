<?php

$callbacks = [];

$showinnavigation;

try {
    $showinnavigation = get_config('local_catalog', 'showinnavigation');
}
catch (dml_exception $e) {
    $showinnavigation = false;
}


if($showinnavigation) {
    $callbacks[] = [
        'hook' => core\hook\navigation\primary_extend::class,
        'callback' => [local_catalog\local\hook_callbacks::class, 'catalog_primary_extend'],
        'priority' => 100,
    ];
}