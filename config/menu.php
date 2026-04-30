<?php
return [

[
    'label' => 'Dashboard',
    'route' => 'dashboard',
    'icon'  => 'dashboard',
],

[
    'label' => 'Site Builder',
    'icon'  => 'builder',
    'active' => 'pages.*',
    'children' => [
        [
            'label' => 'Pages',
            'route' => 'pages.index',
        ],
        [
            'label' => 'Navigation',
            'route' => '#',
        ],
    ],
],

[
    'label' => 'Content',
    'icon'  => 'content',
    'children' => [
        ['label' => 'Destinations', 'route' => '#'],
        ['label' => 'Offers', 'route' => '#'],
        ['label' => 'Forms', 'route' => '#'],
    ],
],

[
    'label' => 'Media',
    'route' => '#',
    'icon'  => 'media',
],

[
    'label' => 'Design',
    'icon'  => 'design',
    'children' => [
        ['label' => 'Themes', 'route' => '#'],
        ['label' => 'Styles', 'route' => '#'],
    ],
],

[
    'label' => 'Users & Roles',
    'icon'  => 'users',
    'children' => [
        ['label' => 'Users', 'route' => '#'],
        ['label' => 'Roles', 'route' => '#'],
        ['label' => 'Permissions', 'route' => '#'],
    ],
],

[
    'label' => 'Marketing',
    'icon'  => 'marketing',
    'children' => [
        ['label' => 'Contacts', 'route' => '#'],
        ['label' => 'Deals', 'route' => '#'],
    ],
],

[
    'label' => 'Settings',
    'route' => '#',
    'icon'  => 'settings',
],

];