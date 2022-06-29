<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin'], function () {
    // Permissions
//    Route::apiResource('permissions', 'PermissionsApiController');
//
//    // Roles
//    Route::apiResource('roles', 'RolesApiController');
//
//    // Users
//    Route::apiResource('users', 'UsersApiController');
//

    // Map
    Route::apiResource('maps', 'MapApiController');

    // Layer
    Route::apiResource('layers', 'LayerApiController');
});


Route::post("upload","SystemController@upload")->name("api.upload");

Route::get("home","SystemController@home");
Route::get("profile","SystemController@profile");
Route::get("dokumen","SystemController@dokumen");
Route::get("kompilasi_peta","SystemController@kompilasi_peta");
Route::get("galeri","SystemController@galeri");

Route::post("kritik","SystemController@store_kritik");
