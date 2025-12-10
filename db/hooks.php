<?php

$callbacks = [];

$config = get_config('local_catalog');

if($config->showinnavigation) {
    $callbacks[] = [
        'hook' => core\hook\navigation\primary_extend::class,
        'callback' => [local_catalog\local\hook_callbacks::class, 'catalog_primary_extend'],
        'priority' => 100,
    ];
}