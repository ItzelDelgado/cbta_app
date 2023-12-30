<?php 
use Illuminate\Support\Facades\Route; //Importamos para generar nuestras rutas.
//Debemos avisarle a laravel que hemos creado un nuevo archivo de rutas en providers
Route::get('/admin', function(){
    return "Hola desde el administrador";
});

