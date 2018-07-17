<?php

return
	[
		'crud_prefix' => 'cruds',
		'media_prefix' => 'media',
		'media_default_settings' => [ ],
		'components' => [
			[
				'name' => 'dashboard-components',
				'path' => '/vendor/vshapovalov/crud/assets/js/dashboard-components.js'
			]
		],
        "theme" => [
            "name" => "light",
            "colors" => [
                "primary" => "#CE93D8",
                "secondary" =>"#E1BEE7",
                "accent" =>"#795548",
                "error" => "#f44336",
                "warning" => "#ffeb3b",
                "info" => "#2196f3",
                "success" => "#4caf50"
            ]
        ],
		"tinymce" => [
			"content_css" => "",
			"style_formats" => [ ]
		]
	];