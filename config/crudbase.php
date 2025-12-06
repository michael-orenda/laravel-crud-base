<?php

return [

    // Where controllers live
    'controller_namespace' => 'App\\Http\\Controllers\\',

    // Where to scan for controllers
    'controller_path' => app_path('Http/Controllers'),

    // Automatically register CRUD routes for BaseController?
    'auto_crud_routes' => true,

    // Automatically register relationship routes for RelationshipBaseController?
    'auto_relationship_routes' => true,

    // Optional prefix: e.g. 'api'
    'route_prefix' => null, // or 'api'

];
