<?php
return [

	// dải ip của mytour office
	'office_ip' 	 => explode(',', env('OFFICE_IP', '117.4.251.193,117.4.252.36,222.252.17.6,14.161.22.221,118.69.63.8')),

	// developer emails
	'dev_emails' => env('DEV_EMAILS', 'quanghieu2104@gmail.com'),

	// email của supper user
	'super_user' => env('APP_SUPER_USER', 'quanghieu2104@gmail.com'),

	// sub domains
	'domain' => [
		'site'  => env('SITE_DOMAIN', 'mytour'),
		'admin' => env('ADMIN_DOMAIN', 'admin'),
		'api'	=> env('API_DOMAIN', 'api'),
	],

	// 
	'tld'		=> env('TLD', 'vn'),
	
];


