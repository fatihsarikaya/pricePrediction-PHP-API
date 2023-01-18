<?php

namespace App\Services\PricePrediction;

use App\Core\ServiceResponse;
use App\Interfaces\Eloquent\ITransformService;
use App\Interfaces\PricePrediction\IPricePredictionService;
use App\Models\Eloquent\Transform;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Facades\Log;

class PricePredictionService extends BasePricePredictionService implements IPricePredictionService
{
    /**
     * @var $transformService
     */
    private $transformService;

    /**
     * @param ITransformService $transformService
     */
    public function __construct(ITransformService $transformService)
    {
        parent::__construct();
        $this->transformService = $transformService;
    }

    /**
     * @param mixed $brand
     * @param mixed $model
     * @param mixed $kilometerFrom
     * @param mixed $kilometerTo
     * @param mixed $yearFrom
     * @param mixed $yearTo
     * @param mixed $fuelTypes
     * @param mixed $gearBoxes
     * @param mixed $powerFrom
     * @param mixed $powerTo
     * @param mixed $bodyType
     * @param mixed $doors
     *
     * @return ServiceResponse
     */
    public function getByParameters(
        $brand,
        $model,
        $kilometerFrom,
        $kilometerTo,
        $yearFrom,
        $yearTo,
        $fuelTypes,
        $gearBoxes,
        $powerFrom,
        $powerTo,
        $bodyType,
        $doors
    ): ServiceResponse
    {
        putenv('WEBDRIVER_CHROME_DRIVER=' . base_path('chromedriver.exe'));
        // putenv('WEBDRIVER_CHROME_DRIVER=/var/www/html/chromedriver');
        set_time_limit(3600);
        $endpoint = $this->mobileDeUrl;
        $priceList = [];

        //$car = collect();
        //$response = $client->post('',[
        //    'query' => [
        //    'yearFrom' => $car->registration_date - 2, (registration_date='2015-01-01')
        //    'yearTo' => $car->registration_date + 2, (sadece yil alinacak)
        //    'kilometerFrom' => $car->km - 5000,
        //    'kilometerTo' => $car->km + 5000,
        //    'powerFrom' => $car->kiloWatt - 40, (PS -> kW a cevirilip getirilecek)
        //    'powerTo' => $car->kiloWatt + 40,
        //    ]
        //]);

        $targetBrand = $this->transformService->getTargetValue('brand', $brand, 'mobilede')->getData();
        $targetModel = $this->transformService->getTargetValue('model', $model, 'mobilede')->getData();
        $targetFuelTypes = collect($fuelTypes)->map(function ($fuelType) {
            return $this->transformService->getTargetValue('fuelTypes', $fuelType, 'mobilede')->getData();
        })->all();
        $targetGearBoxes = collect($gearBoxes)->map(function ($gearBox) {
            return $this->transformService->getTargetValue('gearBoxes', $gearBox, 'mobilede')->getData();
        })->all();
        $targetBodyType = $this->transformService->getTargetValue('bodyType', $bodyType, 'mobilede')->getData();
        $targetDoors = $this->transformService->getTargetValue('doors', $doors, 'mobilede')->getData();
        
        function roundFrom($kilometerFrom): int
        {
            if ($kilometerFrom == 0) {
                return $kilometerFrom = 0;
            } elseif ($kilometerFrom >=0 && $kilometerFrom <=10000) {
                return $kilometerFrom = 0;
            } elseif ($kilometerFrom >=10001 && $kilometerFrom <=30000) {
                return $kilometerFrom = 10000;
            } elseif ($kilometerFrom >=30001 && $kilometerFrom <=50000) {
                return $kilometerFrom = 30000;
            } elseif ($kilometerFrom >=50001 && $kilometerFrom <=70000) {
                return $kilometerFrom = 50000;
            } elseif ($kilometerFrom >=70001 && $kilometerFrom <=100000) {
                return $kilometerFrom = 70000;
            } elseif ($kilometerFrom >=100001 && $kilometerFrom <=125000) {
                return $kilometerFrom = 100000;
            } elseif ($kilometerFrom >=125001 && $kilometerFrom <=150000) {
                return $kilometerFrom = 125000;
            } elseif ($kilometerFrom >=150001 && $kilometerFrom <=175000) {
                return $kilometerFrom = 150000;
            } elseif ($kilometerFrom >=175001 && $kilometerFrom <=200000) {
                return $kilometerFrom = 175000;
            } else { ($kilometerFrom >=200001 && $kilometerFrom <=250000);
                return $kilometerFrom = 200000;
            }
        }

        function roundTo($kilometerTo): int
        {
            if ($kilometerTo == 0) {
                return $kilometerTo = 0;
            } elseif ($kilometerTo >=0 && $kilometerTo <=10000) {
                return $kilometerTo = 10000;
            } elseif ($kilometerTo >=10001 && $kilometerTo <=30000) {
                return $kilometerTo = 30000;
            } elseif ($kilometerTo >=30001 && $kilometerTo <=50000) {
                return $kilometerTo = 50000;
            } elseif ($kilometerTo >=50001 && $kilometerTo <=70000) {
                return $kilometerTo = 70000;
            } elseif ($kilometerTo >=70001 && $kilometerTo <=100000) {
                return $kilometerTo = 100000;
            } elseif ($kilometerTo >=100001 && $kilometerTo <=125000) {
                return $kilometerTo = 125000;
            } elseif ($kilometerTo >=125001 && $kilometerTo <=150000) {
                return $kilometerTo = 150000;
            } elseif ($kilometerTo >=150001 && $kilometerTo <=175000) {
                return $kilometerTo = 175000;
            } elseif ($kilometerTo >=175001 && $kilometerTo <=200000) {
                return $kilometerTo = 200000;
            } else { ($kilometerTo >=200001 && $kilometerTo <=250000);
                return $kilometerTo = 250000;
            }
        }

        function roundPowerFrom($powerFrom): int
        {
            return $powerFrom -= 10;
        }

        function roundPowerTo($powerTo): int
        {
            return $powerTo += 10;
        }
        
        $parameters = [
            'ms' => $targetBrand . ';' . $targetModel,
            'ml' => $kilometerFrom . ':' . $kilometerTo,
            'fr' => $yearFrom . ':' . $yearTo,
            'ft' => implode(' ', $targetFuelTypes),
            'tr' => implode(' ', $targetGearBoxes),
            'powertype' => 'kw',
            'pw' => $powerFrom . ':' . $powerTo,
            'c' => $targetBodyType,
            'cn' => 'DE',
            //'sortOption.sortBy' => 'specifics.mileage',
            //'sortOption.sortOrder' => 'DESCENDING',
            'sortOption.sortBy' => 'searchNetGrossPrice',
            'sortOption.sortOrder' => 'ASCENDING',
            'isSearchRequest' => 'true',
            'dam' => '0',
            'page' => 1
        ];

//       $chromeDriver = ChromeDriver::start();
//       $chromeDriver->manage()->window()->minimize();
//       $mobileDeLastUrl = $endpoint . '?' . http_build_query($parameters) . ($targetDoors && $targetDoors != '' ? '&' . $targetDoors : '');
//       $chromeDriver->get($mobileDeLastUrl);
//       $sources = $chromeDriver->getPageSource();
       $mobileDeLastUrl = $endpoint . '?' . http_build_query($parameters) . ($targetDoors && $targetDoors != '' ? '&' . $targetDoors : '');
       $chromeOptions = new ChromeOptions();
       $chromeOptions->addArguments(['--headless', '--disable-gpu', '--window-size=1920,1080', '--no-sandbox', '--disable-dev-shm-usage']);
       //$chromeOptions->addArguments([                '--disable-gpu','--window-size=1920,1080', '--no-sandbox', '--disable-dev-shm-usage']);
       $capabilities = DesiredCapabilities::chrome();
       $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $chromeOptions);
       $capabilities->setCapability('userAgent','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.114 Safari/537.36 OPR/89.0.4447.64');
       $capabilities->setCapability('user-agent','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.114 Safari/537.36 OPR/89.0.4447.64');

       $chromeDriver = ChromeDriver::start($capabilities);
       $chromeDriver->get($mobileDeLastUrl);
       $sources = $chromeDriver->getPageSource();

        preg_match_all('~<span class=\"h3 u-block\">(.*?)&nbsp;€</span>~', $sources, $prices);

        foreach ($prices[1] as $price) {
            $priceList[] = (int)str_replace('.', '', $price);
        }

        $chromeDriver->close();
        $autoScoutLastUrl = '';
        if (count($priceList) < 10) {
            $targetBrand = $this->transformService->getTargetValue('brand', $brand, 'autoscout')->getData();
            $targetModel = $this->transformService->getTargetValue('model', $model, 'autoscout')->getData();
            $endpoint = $this->autoScoutUrl . '/' . $targetBrand . '/' . $targetModel;
            $targetFuelTypes = collect($fuelTypes)->map(function ($fuelType) {
                return $this->transformService->getTargetValue('fuelTypes', $fuelType, 'autoscout')->getData();
            })->all();
            $targetGearBoxes = collect($gearBoxes)->map(function ($gearBox) {
                return $this->transformService->getTargetValue('gearBoxes', $gearBox, 'autoscout')->getData();
            })->all();
            $targetBodyType = $this->transformService->getTargetValue('bodyType', $bodyType, 'autoscout')->getData();
            $targetDoors = $this->transformService->getTargetValue('doors', $doors, 'autoscout')->getData();

            $parameters = [
                'kmfrom' => roundFrom($kilometerFrom),
                'kmto' => roundTo($kilometerTo),
                'fregfrom' => $yearFrom,
                'fregto' => $yearTo,
                'fuel' => implode(' ', $targetFuelTypes),
                'gear' => implode(' ', $targetGearBoxes),
                'powertype' => 'kw',
                'powerfrom' => $powerFrom,
                'powerto' => $powerTo,
                'body' => $targetBodyType,
                //'sort' => 'mileage',
                //'desc' => '1',
                'sort' => 'price',
                'desc' => "0",
                'cy' => "D",
                'page' => 1,
            ];

            $autoScoutLastUrl = $endpoint . '?' . http_build_query($parameters) . ($targetDoors && $targetDoors != '' ? '&' . $targetDoors : '');
            $response = $this->client->get($autoScoutLastUrl, [
                'headers' => [
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36 OPR/91.0.4516.95',
                ],
            ]);

            $clean1 = str_replace(["\n", "\t", "\r", "  "], null, $response->getBody()->getContents());
            $clean2 = str_replace(["&quot;"], null, $clean1);
            $clean3 = preg_replace('~{(.*?)}~', null, $clean2);
            $cleanResult = preg_replace('~{(.*?)}~', null, $clean3);

            preg_match_all('~<article (.*?)</article>~', $cleanResult, $articles);

            if ($articles[1]) {
                foreach ($articles[1] as $article) {
                    preg_match('~<p class="Price_price__WZayw" .*?>(.*?)</p>~', $article, $priceObject);
                    $priceWithCurrency = str_replace(',-', null, $priceObject[1]);
                    $priceWithoutCurrency = explode(' ', $priceWithCurrency)[1];
                    $price = intval(str_replace('.', null, $priceWithoutCurrency));
                    $priceList[] = $price;
                }
            }
        }
        
        //$priceList = array_slice($priceList,1);

        $minimumPrice = "";
        $maximumPrice = "";
        $count        = "";
        //$averagePrice = "";

        if (count($priceList) == 0) {
            $averagePrice = "0";

        } else {
            $minimumPrice = $priceList[0];
            $maximumPrice = end($priceList);
            $count        = count($priceList);
            $averagePrice = array_sum($priceList) / count($priceList);
        }

        return new ServiceResponse(
            true,
            'Price prediction is successful.',
            200,
            [
                'mobileDeResultsUrl' => $mobileDeLastUrl,
                'autoscoutResultsUrl' => $autoScoutLastUrl,
                'avarage' => intval($averagePrice),
                'minimumPrice' => $minimumPrice,
                'maximumPrice' => $maximumPrice,
                'count' => $count,
                'priceList' => $priceList
                
            ]
        );
    }
}
