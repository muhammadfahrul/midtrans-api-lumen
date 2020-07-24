<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// $router->group(['middleware' => 'authjwt'], function () use ($router) {
    $router->group(['prefix' => 'api/v1'], function () use ($router) {
        $router->get('/customer', 'CustomerController@showAll');
        $router->get('/customer/{id}', 'CustomerController@showId');
        $router->post('/customer', 'CustomerController@add');
        $router->put('/customer/{id}', 'CustomerController@update');
        $router->delete('/customer/{id}', 'CustomerController@delete');

        $router->get('/order', 'OrderController@showAllJoin');
        // $router->get('/order-join', 'OrderController@showAllJoin');
        $router->get('/order/{id}', 'OrderController@showIdJoin');
        // $router->get('/order-join/{id}', 'OrderController@showIdJoin');
        $router->post('/order', 'OrderController@add');
        $router->put('/order/{id}', 'OrderController@update');
        $router->delete('/order/{id}', 'OrderController@delete');

        // $router->get('/order-item', 'OrderItemController@showAll');
        // $router->get('/order-item-join', 'OrderItemController@showAllJoin');
        // $router->get('/order-item/{id}', 'OrderItemController@showId');
        // $router->get('/order-item-join/{id}', 'OrderItemController@showIdJoin');
        // $router->post('/order-item', 'OrderItemController@add');
        // $router->put('/order-item/{id}', 'OrderItemController@update');
        // $router->delete('/order-item/{id}', 'OrderItemController@delete');
    
        $router->get('/product', 'ProductController@showAll');
        $router->get('/product/{id}', 'ProductController@showId');
        $router->post('/product', 'ProductController@add');
        $router->put('/product/{id}', 'ProductController@update');
        $router->delete('/product/{id}', 'ProductController@delete');
    
        $router->get('/payment', 'PaymentController@showAll');
        // $router->get('/payment-join', 'PaymentController@showAllJoin');
        $router->get('/payment/{id}', 'PaymentController@showId');
        // $router->get('/payment-join/{id}', 'PaymentController@showIdJoin');
        $router->post('/payment', 'PaymentController@add');
        $router->put('/payment/{id}', 'PaymentController@update');
        $router->delete('/payment/{id}', 'PaymentController@delete');
        $router->post('/payment/midtrans/push', 'PaymentController@midtransPush');
    });
// });
