<?php

use App\Services\NumberSettingService;

return [
    'column_name' => 'number',

    'settings' => [
        [
            'entity' => \App\Entities\Invoice::class,
            'reset_type' => NumberSettingService::RESET_MONTHLY,
            'parts' => [
                ['sequence' => 1, 'type' => NumberSettingService::PART_TEXT, 'format' => 'INV-'],
                ['sequence' => 2, 'type' => NumberSettingService::PART_YEAR, 'format' => 'Y'],
                ['sequence' => 3, 'type' => NumberSettingService::PART_MONTH, 'format' => 'm'],
                ['sequence' => 4, 'type' => NumberSettingService::PART_TEXT, 'format' => '-'],
                ['sequence' => 5, 'type' => NumberSettingService::PART_COUNTER, 'format' => 5],
            ]
        ],
        [
            'entity' => \App\Entities\Order::class,
            'reset_type' => NumberSettingService::RESET_MONTHLY,
            'parts' => [
                ['sequence' => 1, 'type' => NumberSettingService::PART_TEXT, 'format' => 'ODR-'],
                ['sequence' => 2, 'type' => NumberSettingService::PART_YEAR, 'format' => 'Y'],
                ['sequence' => 3, 'type' => NumberSettingService::PART_MONTH, 'format' => 'm'],
                ['sequence' => 4, 'type' => NumberSettingService::PART_TEXT, 'format' => '-'],
                ['sequence' => 5, 'type' => NumberSettingService::PART_COUNTER, 'format' => 6],
            ]
        ]
    ]
];
