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
            'icon' => 'page',
        ],
        [
            'label' => 'Templates',
            'route' => 'site-templates.index',
            'icon' => 'template',
        ],
        [
            'label' => 'Menu',
            'route' => '#',
            'icon' => 'menu',
        ],
    ],
],

[
    'label' => 'Content',
    'icon'  => 'content',
    'children' => [
        ['label' => 'Destinations', 'route' => 'destinations.index', 'icon' => 'destination'],
        ['label' => 'Offers', 'route' => 'offers.index', 'icon' => 'offer'],
    ],
],

[
    'label' => 'Media',
    'route' => 'media.index',
    'icon'  => 'media',
],

[
    'label' => 'Themes',
    'route' => 'themes.index',
    'icon'  => 'design',
    'active' => 'themes.*',
],

[
    'label' => 'Users & Roles',
    'icon'  => 'users',
    'children' => [
        ['label' => 'Users', 'route' => 'users.index', 'icon' => 'user'],
        ['label' => 'Roles', 'route' => 'roles.index', 'icon' => 'role'],
        ['label' => 'Permissions', 'route' => 'permissions.index', 'icon' => 'permission'],
    ],
],

];
