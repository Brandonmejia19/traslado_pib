<?php

return [

    'label' => 'Exportar :label',

    'modal' => [

        'heading' => 'Exportar :label',

        'form' => [

            'columns' => [

                'label' => 'Columnas',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column habilitada',
                    ],

                    'label' => [
                        'label' => 'Etiqueta de :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Exportar',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Exportación completada',

            'actions' => [

                'download_csv' => [
                    'label' => 'Descargar .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Descargar .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'La exportación es demasiado grande',
            'body' => 'No puedes exportar más de 1 fila a la vez.|No puedes exportar más de :count filas a la vez.',
        ],

        'started' => [
            'title' => 'Exportación iniciada',
            'body' => 'Tu exportación ha comenzado y se procesará 1 fila en segundo plano.|Tu exportación ha comenzado y se procesarán :count filas en segundo plano.',
        ],

    ],

    'file_name' => 'exportacion-:export_id-:model',

];
