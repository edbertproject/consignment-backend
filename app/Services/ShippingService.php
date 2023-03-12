<?php

namespace App\Services;

use App\Entities\ApiExternalLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ShippingService
{
    public static function checkCourierCost($origin, $destination, $weight, $courier)
    {
        $baseUrl = config('raja-ongkir.base_url');

        $options = [
            'headers' => [
                'key' => config('raja-ongkir.key'),
            ],
            'json' =>[
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier
            ]
        ];

        try {
            $client = new Client(['verify' => false]);
            $response = $client->POST($baseUrl, $options);
            $rawResponse = $response->getBody()->__toString();
            $parsedResponse = json_decode($rawResponse);

        } catch(ClientException $e) {
            $responseCode = $e->getResponse()->getStatusCode();
            $responseMessage = $e->getResponse()->getBody()->getContents();

            $parsedResponse = [
                'code' => $responseCode,
                'message' => $responseMessage
            ];
        }

        ApiExternalLog::create([
            'vendor' => 'RAJA_ONGKIR',
            'url' => $baseUrl,
            'request_header' => json_encode($options['headers']),
            'request_body' => json_encode($options['json']),
            'response' => json_encode($parsedResponse)
        ]);

        return $parsedResponse;
    }

    public static function checkAllCourierCost($origin, $destination, $weight, $couriers): array
    {
        $courierCosts = [];

        foreach ($couriers as $courier) {
            $response = self::checkCourierCost($origin, $destination, $weight, $courier);

            $results = @$response->rajaongkir->results;
            if (!empty($results)) {
                foreach ($results as $result) {

                    foreach ($result->costs as $cost) {
                        $costAmount = collect($cost->cost)->first();
                        $estDay = trim(str_replace('HARI','',$costAmount->etd));

                        $courierCosts[] = [
                            'code' => strtoupper($result->code),
                            'name' => $result->name,
                            'service' => $cost->service,
                            'description' => $cost->description,
                            'cost' => $costAmount->value,
                            'estimation_day' => $estDay . ' Hari',
                        ];
                    }
                }
            }
        }

        return $courierCosts;
    }
}
