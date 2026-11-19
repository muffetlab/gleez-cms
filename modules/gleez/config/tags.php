<?php

return [

    // Whether to normalize tags at all (recommended, as raw tags are preserved anyway.)
    'normalize_tags' => false,

    // Use Kohana's URL::title() helper for normalization
    'use_gleez_normalization' => false,

    // If 'use_gleez_normalization' is set to false, you can define your own normalization here.
    'custom_normalization' => '-a-zA-Z0-9',

    // Will append this string to any integer tags. This is supposed to prevent PHP
    // casting "string" integer tags as ints. Won't do anything to floats or non-numeric strings.
    'append_to_integer' => '',

    // The maximum length of a tag.
    'max_tag_length' => 32,

];