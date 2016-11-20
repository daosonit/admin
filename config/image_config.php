<?php

return array(
    'maxSize'     => 512000,//500kb

    /**
     * path picture partner
     */
    'pathPartner' => '/resources/pictures/partner/',
    'sizePartner' => array(SIZE_SMALL  => ['w' => 84, 'h' => 63],
                           SIZE_MEDIUM => ['w' => 268, 'h' => 201],
                           SIZE_LARGE  => ['w' => 384, 'h' => 288]),
    'namePartner' => 'parter',

    /**
     * Users
     */
    'sizeUser'    => ['user' => ['w' => 400, 'h' => 400]],
    'pathUser'    => '/resources/pictures/users/',
);