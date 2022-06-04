<?php
namespace App\Services;
use GuzzleHttp\Client;      
use Guzzle\Http\Exception\ClientErrorResponseException;
use Carbon\Carbon;


class PaymentService {

    public $client;
    public $url = 'http://18.220.121.223:8980/gateway/services/v1/collect/push';

    public function __construct(Client $client) 
    {
        $this->client = $client;
    }

    public function ussdPush($phone, $amount, $transactionNo) 
    {
        
        $username = "13";
        $password = "Nachangia@2022";
        $timestamp = "20220506104027";
        
        $apipassword = base64_encode(hash("sha256", $username.$password.$timestamp, true));

        try {
            $response = $this->client->request('POST', $this->url, [
                "verify" => false,
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                "json" => [
                    "body" => (object) array(
                        "request" => (object) array(
                            "command" => "UssdPush",
                            "transactionNumber" => $transactionNo,
                            "msisdn" => $phone,
                            "amount" => $amount
                        )
                    ),
                    "header" => (object) array(
                        "username"  => $username,
                        "password"  => $apipassword,
                        "timestamp" => $timestamp
                    )
                ]
            ]);
            $statuscode = $response->getStatusCode();
            if ($statuscode == 200) {
                $responseData = json_decode($response->getBody()->getContents()); 
                return response()->json($responseData);               
            }
        } catch (ClientErrorResponseException $exception) {
            $responseBody = $exception->getResponse()->getBody(true);
            return response()->json($responseBody);
        }
    }
}