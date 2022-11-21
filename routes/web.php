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


//    $mobileDeBrands = \App\Models\Eloquent\MobileDeBrand::all();
//    $autoscoutBrands = \App\Models\Eloquent\AutoscoutBrand::all();
//    $myBrands = \App\Models\Eloquent\CarBrand::all(); // $myBrand -> (db=price_prediction table=car_brands)
//
//    foreach ($myBrands as $myBrand) {
//        $lowerBrands = ucfirst(strtolower($myBrand->name));
//        //$mobile_brands = $mobileDeBrands->where('brand', $myBrand->name)->first();
//        $mobile_brands = $mobileDeBrands->where('brand', $lowerBrands)->first();
//        //$autoscout_brands = $autoscoutBrands->where('brand', str_replace(' ', '-', $myBrand->name))->first();
//        $autoscout_brands = $autoscoutBrands->where('brand', str_replace(' ', '-', $lowerBrands))->first();
//        if ($mobile_brands) {
//            $check = \App\Models\Eloquent\Transform::where('relation_type', 'brand')
//                ->where('relation_id', $myBrand->id)
//                ->where('target_system', 'mobilede')
//                ->where('target_value', $mobile_brands->brand_id)->first();
//            if (!$check) {
//                $newTransform = new \App\Models\Eloquent\Transform;
//                $newTransform->relation_type = 'brand';
//                $newTransform->relation_id = $myBrand->id;
//                $newTransform->target_system = 'mobilede';
//                $newTransform->target_value = $mobile_brands->brand_id;
//                $newTransform->save();
//            }
//        }
//
//        if ($autoscout_brands) {
//            $check = \App\Models\Eloquent\Transform::where('relation_type', 'brand')
//                ->where('relation_id', $myBrand->id)
//                ->where('target_system', 'autoscout')
//                ->where('target_value', $autoscout_brands->brand)->first();
//            if (!$check) {
//                $newTransform = new \App\Models\Eloquent\Transform;
//                $newTransform->relation_type = 'brand';
//                $newTransform->relation_id = $myBrand->id;
//                $newTransform->target_system = 'autoscout';
//                $newTransform->target_value = $autoscout_brands->brand;
//                $newTransform->save();
//            }
//        }
//    }
//    return 'transfer completed';
    return 'Home';

});
