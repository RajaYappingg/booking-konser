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

$router->get('admin', 'AdminController@index');
$router->get('admin/concerts', 'AdminController@concerts');
$router->get('admin/concerts/create', 'AdminController@createConcertForm');
$router->post('admin/concerts', 'AdminController@storeConcert');
$router->get('admin/concerts/{id}/edit', 'AdminController@editConcertForm');
$router->post('admin/concerts/{id}/update', 'AdminController@updateConcert');
$router->post('admin/concerts/{id}/delete', 'AdminController@deleteConcert');
$router->get('admin/concerts/{id}/bookings', 'AdminController@concertBookings');

$router->get('admin/vouchers', 'AdminController@vouchers');
$router->get('admin/vouchers/create', 'AdminController@createVoucherForm');
$router->post('admin/vouchers', 'AdminController@storeVoucher');
$router->get('admin/vouchers/{id}/edit', 'AdminController@editVoucherForm');
$router->post('admin/vouchers/{id}/update', 'AdminController@updateVoucher');
$router->post('admin/vouchers/{id}/delete', 'AdminController@deleteVoucher');

$router->get('admin/bookings', 'AdminController@bookings');
$router->get('admin/users', 'AdminController@users');
