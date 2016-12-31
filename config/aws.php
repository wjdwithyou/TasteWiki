<?php

use Aws\Laravel\AwsServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | AWS SDK Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration options set in this file will be passed directly to the
    | `Aws\Sdk` object, from which all client objects are created. The minimum
    | required options are declared here, but the full set of possible options
    | are documented at:
    | http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html
    |
    */
	'credentials' => [
		'key' => 'AKIAICLBTLD7B5MCIUHA',
		'secret' => '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',
	],
    'region' => 'ap-northeast-2',
    'version' => 'latest',
	'Ses' => [
		'region' => 'ap-northeast-2',
	],
    /*
    'ua_append' => [
        'L5MOD/' . AwsServiceProvider::VERSION,
    ],
    */
];