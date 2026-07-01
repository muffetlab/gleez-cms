<?php

return array(

    // The public accessible directory where the file will be copied
	'public_dir' => 'media',

    // Default upload media directory
	'upload_dir' => 'media/pictures',

    // Write the files to the public directory?
	'cache' => Kohana::$environment === Kohana::PRODUCTION,

    // Compress assets?
	'compress' => Kohana::$environment === Kohana::PRODUCTION,

    // Combine multiple css/js files into single file. Defaults to FALSE
	'combine' => FALSE,

    // Supported image formats
	'supported_image_formats' => array(
		'jpe',
		'jpg',
		'jpeg',
		'gif',
		'png',
	),

    // Maximum size of POST data that PHP will accept (e.g., '200K', '5MiB', '1M', '500B')
	'post_max_size' => '8M',

    // Image quality
	'quality' => 85,
);
