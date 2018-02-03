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

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->group(['prefix' => 'video', 'middleware' => 'auth'], function () use ($router) {
        $router->get('/', 'VideoController@all');
        $router->get('/{vid:\d+}', 'VideoController@get');
        $router->post('/', 'VideoController@create');
        $router->delete('/{vid:\d+}', 'VideoController@delete');
        $router->put('/{vid:\d+}', 'VideoController@update');
    });

    $router->group(['prefix' => 'video-template', 'middleware' => 'auth'], function () use ($router) {
        $router->get('/', 'VideoTemplateController@all');
        $router->get('/{vid:\d+}', 'VideoTemplateController@get');
        $router->post('/', 'VideoTemplateController@create');
        $router->delete('/{vid:\d+}', 'VideoTemplateController@delete');
        $router->put('/{vid:\d+}', 'VideoTemplateController@update');
    });


    $router->group(['prefix' => 'settings', 'middleware' => 'auth'], function () use ($router) {
        $router->get('/', 'SettingsController@all');
        $router->get('/{vid:\d+}', 'SettingsController@get');
        $router->post('/', 'SettingsController@create');
        $router->delete('/{vid:\d+}', 'SettingsController@delete');
        $router->put('/{vid:\d+}', 'SettingsController@update');
    });

    $router->group(['prefix' => 'public-setting', 'middleware' => 'auth'], function () use ($router) {
        $router->get('/', 'WechatSettingsController@all');
        $router->get('/{vid:\d+}', 'WechatSettingsController@get');
        $router->post('/', 'WechatSettingsController@create');
        $router->delete('/{vid:\d+}', 'WechatSettingsController@delete');
        $router->put('/{vid:\d+}', 'WechatSettingsController@update');
    });

    $router->group(['prefix' => 'summary', 'middleware' => 'auth'], function () use ($router) {
        $router->get('/', 'SummaryController@all');
        $router->get('/{id:\d+}', 'SummaryController@get');
        $router->post('/', 'SummaryController@create');
        $router->delete('/{id:\d+}', 'SummaryController@delete');
        $router->put('/{id:\d+}', 'SummaryController@update');
    });

    $router->group(['prefix' => 'page-settings', 'middleware' => 'auth'], function () use ($router) {
        $router->get('/', 'PageSettingsController@all');
        $router->get('/{id:\d+}', 'PageSettingsController@get');
        $router->post('/', 'PageSettingsController@create');
        $router->delete('/{id:\d+}', 'PageSettingsController@delete');
        $router->put('/{id:\d+}', 'PageSettingsController@update');
        $router->post('/{id:\d+}', 'PageSettingsController@publish');
    });

    $router->group(['prefix' => 'user', 'middleware' => 'auth'], function () use ($router) {

        $router->get('/detail', 'LoginController@detail');
    });

    $router->post('user/login', 'LoginController@login');

    $router->get('qr-code', 'SummaryController@qrCode');


});

$router->get('/rss/view-{hash}-{id:\d+}.htm', 'API\PageViewController@view');
$router->get('/rss/test', 'API\PageViewController@test');
$router->get('/rss/wait.htm', 'API\PageViewController@firstChannel');



