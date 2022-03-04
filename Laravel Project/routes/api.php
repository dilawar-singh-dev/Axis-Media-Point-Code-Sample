<?php

use Illuminate\Http\Request;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\NewsSubCategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AxisTvController;
use App\Http\Controllers\UserController;

// CATEGORIES
Route::group(['prefix' => 'category'], function () {
    // show data
    Route::get('/', [NewsCategoryController::class, 'index']);
    // show data with sub category
    Route::get('sub-category', [NewsCategoryController::class, 'subCategory']);
    Route::get('{id}/sub-category', [NewsCategoryController::class, 'Categorybyid']);
    
    // store data
    Route::post('/add', [NewsCategoryController::class, 'store']);
    // update data
    Route::post('/edit', [NewsCategoryController::class, 'update']);
    // delete data
    Route::post('/delete', [NewsCategoryController::class, 'destroy']);
});

// SUB CATEGORIES
Route::group(['prefix' => 'sub-category'], function () {
    // show data
    Route::get('/', [NewsSubCategoryController::class, 'index']);
    // store data
    Route::post('/add',  [NewsSubCategoryController::class, 'store']);
    Route::get('/{id}',  [NewsSubCategoryController::class, 'SubCategorybyid']);
    
    // update data
    Route::post('/edit',  [NewsSubCategoryController::class, 'update']);
    // delete data
    Route::post('/delete', [NewsSubCategoryController::class, 'destroy']);
});


// NEWS
Route::group(['prefix' => 'news'], function () {
    // show data
    Route::get('/', [NewsController::class, 'index']);
    // show data
    Route::get('/{id}', [NewsController::class, 'Getindex']);
    // show data
    Route::get('/slug/{slug}', [NewsController::class, 'GetNewsSlug']);
    // show data by category
    Route::get('/category', [NewsController::class, 'category']);
    // show data by sub category
    Route::get('/sub-category', [NewsController::class, 'subcategory']);
    // store data
    Route::post('/add', [NewsController::class, 'store'])->middleware('auth');

    // news/archive
    Route::get('/archives', [NewsController::class, 'getArchive']);


    // E NEWS PAPER 
    // STORE DATE SESSION
    Route::post('/add/e-paper/date', [NewsController::class, 'storeEPaperDate'])->middleware('auth');

    Route::post('/add/e-paper', [NewsController::class, 'storeEPaper'])->middleware('auth');
    Route::post('/add/e-paper/success', [NewsController::class, 'storeEPaperSuccess']);

    Route::get('/view/e-news-paper/', [NewsController::class, 'getENewsPaper']);

    Route::get('/view/e-news-dates', [NewsController::class, 'getENewsPaperDates']);

    Route::get('/delete/e-news-dates', [NewsController::class, 'deleteENewsPaperDates']);

   

    // update data
    Route::post('/edit', [NewsController::class, 'update']);
    // delete data
    Route::post('/delete', [NewsController::class, 'destroy']);

    // WEB SCRAPING 
    Route::get('/web/scrap', [NewsController::class, 'newsScraping']);


});

// E NEWS PAPER 
    // STORE DATE SESSION
    Route::post('/news/add/e-paper/date/session', [NewsController::class, 'storeEPaperDateSession']);


// AXIS NEWS
Route::group(['prefix' => 'axis-tv'], function () {
    // show data
    Route::get('/', [AxisTvController::class, 'index']);
    // store data
    Route::post('/add', [AxisTvController::class, 'store'])->middleware('auth');
});

Route::get('/news/archive/{date}', [NewsController::class, 'getArchives']);


Route::post('login', [NewsController::class, 'login'] );
Route::post('register', [NewsController::class, 'register']);
Route::group(['middleware' => 'auth:api'], function(){
  Route::get('user', [NewsController::class, 'user']);
});