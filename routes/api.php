<?php

return [
    // authentication routes
    ['method' => 'POST', 'path' => '/api/auth/register', 'action' => 'AuthController@register', 'auth' => false],
    ['method' => 'POST', 'path' => '/api/auth/login', 'action' => 'AuthController@login', 'auth' => false],
    ['method' => 'POST', 'path' => '/api/auth/logout', 'action' => 'AuthController@logout', 'auth' => true],
    ['method' => 'GET', 'path' => '/api/auth/me', 'action' => 'AuthController@me', 'auth' => true],

    // user management routes
    ['method' => 'GET', 'path' => '/api/users', 'action' => 'UserController@index', 'auth' => true],
    ['method' => 'GET', 'path' => '/api/users/{id}', 'action' => 'UserController@show', 'auth' => true],
    ['method' => 'POST', 'path' => '/api/users', 'action' => 'UserController@store', 'auth' => true],
    ['method' => 'PUT', 'path' => '/api/users/{id}', 'action' => 'UserController@update', 'auth' => true],
    ['method' => 'DELETE', 'path' => '/api/users/{id}', 'action' => 'UserController@destroy', 'auth' => true],

    // volet management routes
    ['method' => 'GET', 'path' => '/api/volets', 'action' => 'VoletController@index', 'auth' => false],
    ['method' => 'GET', 'path' => '/api/health', 'action' => 'HealthController@index', 'auth' => false],
    ['method' => 'POST', 'path' => '/api/volets', 'action' => 'VoletController@store', 'auth' => true],
    ['method' => 'GET', 'path' => '/api/volets/{id}', 'action' => 'VoletController@show', 'auth' => false],
    ['method' => 'PUT', 'path' => '/api/volets/{id}', 'action' => 'VoletController@update', 'auth' => true],
    ['method' => 'DELETE', 'path' => '/api/volets/{id}', 'action' => 'VoletController@destroy', 'auth' => true],
    ['method' => 'GET', 'path' => '/api/volets/{id}/activities', 'action' => 'VoletController@activities', 'auth' => false],
    ['method' => 'GET', 'path' => '/api/volets/{id}/partners', 'action' => 'VoletController@partners', 'auth' => false],
    ['method' => 'GET', 'path' => '/api/volets/{id}/posts', 'action' => 'VoletController@posts', 'auth' => false],
    ['method' => 'GET', 'path' => '/api/volets/{id}/campaigns', 'action' => 'VoletController@campaigns', 'auth' => false],

    // activity routes
    ['method' => 'GET', 'path' => '/api/activities', 'action' => 'ActivityController@index', 'auth' => false],
    ['method' => 'POST', 'path' => '/api/activities', 'action' => 'ActivityController@store', 'auth' => true],
    ['method' => 'GET', 'path' => '/api/activities/{id}', 'action' => 'ActivityController@show', 'auth' => false],
    ['method' => 'PUT', 'path' => '/api/activities/{id}', 'action' => 'ActivityController@update', 'auth' => true],
    ['method' => 'DELETE', 'path' => '/api/activities/{id}', 'action' => 'ActivityController@destroy', 'auth' => true],

    // partner routes
    ['method' => 'GET', 'path' => '/api/partners', 'action' => 'PartnerController@index', 'auth' => false],
    ['method' => 'POST', 'path' => '/api/partners', 'action' => 'PartnerController@store', 'auth' => true],
    ['method' => 'GET', 'path' => '/api/partners/{id}', 'action' => 'PartnerController@show', 'auth' => false],
    ['method' => 'PUT', 'path' => '/api/partners/{id}', 'action' => 'PartnerController@update', 'auth' => true],
    ['method' => 'DELETE', 'path' => '/api/partners/{id}', 'action' => 'PartnerController@destroy', 'auth' => true],

    // testimonials routes
    ['method' => 'GET', 'path' => '/api/testimonials', 'action' => 'TestimonialController@index', 'auth' => false],
    ['method' => 'POST', 'path' => '/api/testimonials', 'action' => 'TestimonialController@store', 'auth' => true],
    ['method' => 'GET', 'path' => '/api/testimonials/{id}', 'action' => 'TestimonialController@show', 'auth' => false],
    ['method' => 'PUT', 'path' => '/api/testimonials/{id}', 'action' => 'TestimonialController@update', 'auth' => true],
    ['method' => 'DELETE', 'path' => '/api/testimonials/{id}', 'action' => 'TestimonialController@destroy', 'auth' => true],

    // posts routes
    ['method' => 'GET', 'path' => '/api/posts', 'action' => 'PostController@index', 'auth' => false],
    ['method' => 'POST', 'path' => '/api/posts', 'action' => 'PostController@store', 'auth' => true],
    ['method' => 'GET', 'path' => '/api/posts/{id}', 'action' => 'PostController@show', 'auth' => false],
    ['method' => 'PUT', 'path' => '/api/posts/{id}', 'action' => 'PostController@update', 'auth' => true],
    ['method' => 'DELETE', 'path' => '/api/posts/{id}', 'action' => 'PostController@destroy', 'auth' => true],

    // campaigns routes
    ['method' => 'GET', 'path' => '/api/campaigns', 'action' => 'CampaignController@index', 'auth' => false],
    ['method' => 'POST', 'path' => '/api/campaigns', 'action' => 'CampaignController@store', 'auth' => true],
    ['method' => 'GET', 'path' => '/api/campaigns/{id}', 'action' => 'CampaignController@show', 'auth' => false],
    ['method' => 'PUT', 'path' => '/api/campaigns/{id}', 'action' => 'CampaignController@update', 'auth' => true],
    ['method' => 'DELETE', 'path' => '/api/campaigns/{id}', 'action' => 'CampaignController@destroy', 'auth' => true],

    // students routes
    ['method' => 'GET', 'path' => '/api/students', 'action' => 'StudentController@index', 'auth' => false],
    ['method' => 'POST', 'path' => '/api/students', 'action' => 'StudentController@store', 'auth' => true],
    ['method' => 'GET', 'path' => '/api/students/{id}', 'action' => 'StudentController@show', 'auth' => false],
    ['method' => 'PUT', 'path' => '/api/students/{id}', 'action' => 'StudentController@update', 'auth' => true],
    ['method' => 'DELETE', 'path' => '/api/students/{id}', 'action' => 'StudentController@destroy', 'auth' => true],

    // inscriptions routes
    ['method' => 'GET', 'path' => '/api/inscriptions', 'action' => 'InscriptionController@index', 'auth' => true],
    ['method' => 'POST', 'path' => '/api/inscriptions', 'action' => 'InscriptionController@store', 'auth' => true],
    ['method' => 'GET', 'path' => '/api/inscriptions/{id}', 'action' => 'InscriptionController@show', 'auth' => true],
    ['method' => 'PUT', 'path' => '/api/inscriptions/{id}', 'action' => 'InscriptionController@update', 'auth' => true],
    ['method' => 'DELETE', 'path' => '/api/inscriptions/{id}', 'action' => 'InscriptionController@destroy', 'auth' => true],
];
