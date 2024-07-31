<?php

namespace App\Services;

use App\Services\Contracts\SMSInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Facades\Log;

class TextGlobalSMSService implements SMSInterface
{
    /**
     * @param string $phoneNumber
     * @param string $body
     * @return GuzzleResponse
     * @throws GuzzleException
     * @throws Exception
     */
    public static function sendText(string $phoneNumber, string $body): GuzzleResponse
    {
        $form = config('app.name');
        $authToken = base64_encode(
            config('config.globalTextUserName') . ':' . config('config.globalTextPassword')
        );

        $apiUrl = config('config.globalTextApiUrl') . '/sms/1/text/single';

        $client = new Client();

        try {
            /** @var GuzzleResponse $result */
            $result = $client->post($apiUrl, [
                'json' => [
                    'from' => $form,
                    'to'   => $phoneNumber,
                    'text' => $body,
                ],
                'headers' => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . $authToken,
                ],
            ]);
        } catch (ClientException $e) {
            Log::critical("TextGlobal request finished with error: {$e->getResponse()->getBody()}");
            throw new Exception( "TextGlobal request finished with error: {$e->getResponse()->getBody()}");
        }

        return $result;
    }
}
