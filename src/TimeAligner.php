<?php

namespace SoftCreatR\SteamGuard;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

use function json_decode;
use function time;

/**
 * This library is heavily inspired by
 *
 * @link https://github.com/MarlonColhado/SteamGenerateMobileAuthPHP
 * @link https://github.com/geel9/SteamAuth/blob/master/SteamAuth
 * @link https://gist.github.com/mooop12/1af7f0ffc8f28ea76f27abcba1e6da01
 * @link https://github.com/SteamTimeIdler/stidler/wiki/Getting-your-%27shared_secret%27-code-for-use-with-Auto-Restarter-on-Mobile-Authentication
 */
final class TimeAligner
{
    /**
     * The implemented HTTP client instance.
     */
    protected Client $client;

    /**
     * The local (server) time.
     */
    public int $localTime;

    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?? new Client();
    }

    /**
     * The time difference in seconds between the local time and the API time.
     */
    public int $timeDiff = 0;

    public function getSteamTime(): int
    {
        $this->localTime = time();
        $this->timeDiff = $this->localTime - $this->getTimeDiff();

        return $this->localTime + $this->timeDiff;
    }

    public function getTimeDiff(): int
    {
        try {
            $response = $this->getClient()->request(
                'POST',
                'https://api.steampowered.com/ITwoFactorService/QueryTime/v0001',
                ['steamid' => 0]
            );
            $responseBody = (string)$response->getBody();

            if ($response->getStatusCode() !== 200) {
                return 0;
            }

            $data = json_decode($responseBody, false, 512, JSON_THROW_ON_ERROR);

            if (!empty($data->response->server_time)) {
                return (int) $data->response->server_time;
            }
        } catch (Exception | GuzzleException $e) {
            // ignore
        }

        return 0;
    }

    public function getClient() : ClientInterface
    {
        return $this->client;
    }
}
