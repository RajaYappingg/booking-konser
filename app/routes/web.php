<?php

declare(strict_types=1);

$router->get('', 'HomeController@index');
$router->get('concerts', 'ConcertController@index');
$router->get('concerts/{id}', 'ConcertController@show');
$router->post('bookings', 'BookingController@store');
$router->post('bookings/{id}/cancel', 'BookingController@cancel');
$router->get('bookings', 'BookingController@index');

$router->get('login', 'AuthController@showLogin');
$router->post('login', 'AuthController@login');
$router->get('register', 'AuthController@showRegister');
$router->post('register', 'AuthController@register');
$router->post('logout', 'AuthController@logout');
