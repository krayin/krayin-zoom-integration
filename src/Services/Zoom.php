<?php

namespace Webkul\ZoomMeeting\Services;

use Illuminate\Support\Str;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Webkul\ZoomMeeting\Models\Account;
use Webkul\ZoomMeeting\Repositories\AccountRepository;

class Zoom
{
    /**
     * GuzzleHttp Client object
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * AccountRepository object
     *
     * @var \Webkul\ZoomMeeting\Services\AccountRepository
     */
    protected $accountRepository;

    /**
     * Zoom Meeting service constructor.
     *
     * @param \Webkul\ZoomMeeting\Repositories\AccountRepository  $accountRepository
     * @return void
     */
    function __construct(AccountRepository $accountRepository)
    {
        $this->client = new Client(['base_uri' => 'https://api.zoom.us']);

        $this->accountRepository = $accountRepository;
    }

    /**
     * @return string
     */
    public function createAuthUrl()
    {
        return 'https://zoom.us/oauth/authorize?response_type=code&client_id=' . config('services.zoom.client_id') . '&redirect_uri=' . config('services.zoom.redirect_uri');
    }

    /**
     * @param  string  $code
     * @return array
     */
    public function getAccessToken($code)
    {
        $client = new Client(['base_uri' => 'https://zoom.us']);

        $response = $client->request('POST', '/oauth/token', [
            'headers'     => [
                'Authorization' => 'Basic '. base64_encode(config('services.zoom.client_id') . ':' . config('services.zoom.client_secret')),
            ],

            'form_params' => [
                'grant_type'   => 'authorization_code',
                'code'         => $code,
                'redirect_uri' => config('services.zoom.redirect_uri'),
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param  array  $token
     * @return array
     */
    public function getUserInfo($token)
    {
        $response = $this->client->request('GET', '/v2/users/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token['access_token'],
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param  mixed  $account
     * @param  array  $data
     * @return array
     */
    public function createMeeting($account, $data)
    {
        try {
            $start = isset($data['schedule_from']) && $data['schedule_from']
                ? Carbon::createFromFormat('Y-m-d H:i:s', $data['schedule_from'])
                : Carbon::now();

            $response = $this->client->request('POST', '/v2/users/me/meetings', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $account->token['access_token'],
                ],

                'json'    => [
                    'topic'      => $data['title'] ?? '',
                    'type'       => 2,
                    'start_time' => $start->toAtomString(),
                    'timezone'   => $start->timezone->getName(),
                    'password'   => Str::random(7),
                ],
            ]);
    
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            if (401 == $e->getCode()) {
                $account = $this->refreshToken($account);

                $this->createMeeting($account, $data);
            } else {
                return $e->getMessage();
            }
        }
    }

    /**
     * @param  mixed  $account
     * @return array
     */
    public function refreshToken($account)
    {
        $client = new Client(['base_uri' => 'https://zoom.us']);

        $response = $client->request('POST', '/oauth/token', [
            'headers'     => [
                'Authorization' => 'Basic ' . base64_encode(config('services.zoom.client_id') . ':' . config('services.zoom.client_secret'))
            ],

            'form_params' => [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $account->token['refresh_token'],
            ],
        ]);

        $account = $this->accountRepository->update([
            'token' => json_decode($response->getBody()->getContents(), true),
        ], $account->id);

        return $account;
    }
}