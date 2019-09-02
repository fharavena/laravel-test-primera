<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rutas de prueba
Route::get('/', function () {
    return ('<h1>Hola mundo con Laravel</h1>');
});

Route::get('/welcome', function () {
    return view('welcome');
});


Route::get('/pruebas/{nombre?}', function ($nombre = null) {
    $texto =  '<h2>Text desde una ruta</h2>';
    $texto .= 'Nombre: ' . $nombre;
    return view('pruebas', array(
        'texto' => $texto
    ));
});

Route::get('/animales', 'PruebasController@index');
Route::get('/test-orm', 'PruebasController@testOrm');

/*
Metodos HTTP

*GET: Conseguir datos o recuross
*POST: Guardar datos o recursos o hacer logica desde un formulario
*PUT: Actualizar datos o recursos
*DELETE: Eliminar datos o recursos
*/

//Rutas de api
Route::get('/usuario/pruebas', 'UserController@pruebas');
Route::get('/categoria/pruebas', 'CategoryController@pruebas');
Route::get('/post/pruebas', 'PostController@pruebas');


//Rutas del controlador de usuario
Route::post('/api/register', 'UserController@register');
Route::post('/api/login', 'UserController@login');
