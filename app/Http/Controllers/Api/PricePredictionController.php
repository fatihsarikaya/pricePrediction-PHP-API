<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller;
use App\Core\HttpResponse;
use App\Http\Requests\Api\PricePredictionController\CheckRequest;
use App\Interfaces\PricePrediction\IPricePredictionService;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class PricePredictionController extends Controller
{
    use HttpResponse;

    /**
     * @var $pricePredictionService
     */
    private $pricePredictionService;

    public function __construct(IPricePredictionService $pricePredictionService)
    {
        $this->pricePredictionService = $pricePredictionService;
    }

    /**
     * @param CheckRequest $request
     */
    public function check(CheckRequest $request)
    {
        $response = $this->pricePredictionService->getByParameters(
            $request->brand,
            $request->model,
            $request->kilometerFrom,
            $request->kilometerTo,
            $request->yearFrom,
            $request->yearTo,
            $request->fuelTypes ?? [],
            $request->gearBoxes ?? [],
            $request->powerFrom,
            $request->powerTo,
            $request->bodyType,
            $request->doors
        );
        Log::channel('laravel.log')->info(json_encode($request->all()));
        Log::channel('laravel.log')->info(json_encode($response->getData()));

        //$logger = new Logger('my_logger');
        //$logger->pushHandler(new StreamHandler(__DIR__.'../../../../../resources/log/vin2.log',5,Logger::DEBUG,true,false ));
        //$logger->pushHandler(new FirePHPHandler());
        //$logger->info(json_encode($request->all()));
        //$logger->info(json_encode($response->getData()));

        return $this->httpResponse(
            $response->getMessage(),
            $response->getStatusCode(),
            $response->getData(),
            $response->isSuccess()
        );
    }
}
