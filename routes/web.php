<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
//    $response = \Illuminate\Support\Facades\Http::get('https://wiveda.de/api/v1/cars');
//    $brands = collect(json_decode($response->body()));
//
//    foreach ($brands as $brand => $models) {
//        $newCarBrand = new \App\Models\Eloquent\CarBrand;
//        $newCarBrand->name = $brand;
//        $newCarBrand->save();
//
//        foreach ($models as $model) {
//            $newCarBrandModel = new \App\Models\Eloquent\CarBrandModel;
//            $newCarBrandModel->car_brand_id = $newCarBrand->id;
//            $newCarBrandModel->name = $model;
//            $newCarBrandModel->save();
//        }
//    }
//
//    return 'transfer completed';


    $mobileDeBrands = MobileDeBrand::all();
    $autoscoutBrands = AutoscoutBrand::all();
    $myBrands = \App\Models\Eloquent\CarBrand::all();

    foreach ($myBrands as $myBrand) {
        $mobiledebrand = $mobileDeBrands->where('brand', $myBrand->name)->first();
        $autoscoutbrand = $autoscoutBrands->where('brand', str_replace(' ', '-', $myBrand->name))->first();
        if ($mobiledebrand) {
            $check = \App\Models\Eloquent\Transform::where('relation_type', 'brand')
                ->where('relation_id', $myBrand->id)
                ->where('target_system', 'mobilede')
                ->where('target_value', $mobiledebrand->brand_id)->first();
            if (!$check) {
                $newTransform = new \App\Models\Eloquent\Transform;
                $newTransform->relation_type = 'brand';
                $newTransform->relation_id = $myBrand->id;
                $newTransform->target_system = 'mobilede';
                $newTransform->target_value = $mobiledebrand->brand_id;
                $newTransform->save();
            }
        }

        if ($autoscoutbrand) {
            $check = \App\Models\Eloquent\Transform::where('relation_type', 'brand')
                ->where('relation_id', $myBrand->id)
                ->where('target_system', 'autoscout')
                ->where('target_value', $autoscoutbrand->brand)->first();
            if (!$check) {
                $newTransform = new \App\Models\Eloquent\Transform;
                $newTransform->relation_type = 'brand';
                $newTransform->relation_id = $myBrand->id;
                $newTransform->target_system = 'autoscout';
                $newTransform->target_value = $autoscoutbrand->brand;
                $newTransform->save();
            }
        }
    }

});
