<?php

return [
    'form' => [
        'toolbar' => [
            'buttons' => [
                'save' => [
                    'label' => 'lang:admin::lang.button_save',
                    'class' => 'btn btn-primary',
                    'data-request' => 'onSave',
                    'data-progress-indicator' => 'admin::lang.text_saving',
                ],
            ],
        ],
        'fields' => [
            'info' => [
                'type' => 'partial',
                'path' => 'igniterlabs.giftup::settings.info',
            ],
            'is_live' => [
                'type' => 'radiotoggle',
                'default' => 'staging',
                'options' => [
                    'staging' => 'Staging',
                    'live' => 'Live',
                ],
            ],
            'api_key' => [
                'label' => 'lang:igniterlabs.giftup::default.label_api_key',
                'type' => 'textarea',
                'span' => 'left',
            ],

        ],
    ],
];
