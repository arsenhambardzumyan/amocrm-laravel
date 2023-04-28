<?php

namespace App\Services;

define('TOKEN_FILE', DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'token_info.json');


use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;

class AmoCRMService
{
    protected $client;

    public function __construct()
    {
        $config = [
            'clientId' => 'de2f600f-6a46-47a8-9879-3696bcbe0193',
            'clientSecret' => 't1sYggXMyuFNU0nOGMJvSTfd6zaW1KPNnCnGv4L2Ty2KIcN73StvPuTZjP5UGvTa',
            'redirectUri' => 'https://youtube.com',
            'baseDomain' => 'aidmatevosyan.amocrm.ru',
//            'token' => [
//                'access_token' => env('AMOCRM_ACCESS_TOKEN'),
//                'refresh_token' => env('AMOCRM_REFRESH_TOKEN'),
//                'expires_in' => env('AMOCRM_TOKEN_EXPIRES_IN'),
//                'created_at' => env('AMOCRM_TOKEN_CREATED_AT'),
//            ],
        ];



        $this->client = new AmoCRMApiClient('de2f600f-6a46-47a8-9879-3696bcbe0193', 't1sYggXMyuFNU0nOGMJvSTfd6zaW1KPNnCnGv4L2Ty2KIcN73StvPuTZjP5UGvTa', 'https://crm.dev.itfabers.com/get-contacts');
       // dd($this->client);
    }

    public function auth() {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth2state'] = $state;
        if (isset($_GET['button'])) {
            echo $this->client->getOAuthClient()->getOAuthButton(
                [
                    'title' => 'Установить интеграцию',
                    'compact' => true,
                    'class_name' => 'className',
                    'color' => 'default',
                    'error_callback' => 'handleOauthError',
                    'state' => $state,
                ]
            );
            die;
        } else {
            $authorizationUrl = $this->client->getOAuthClient()->getAuthorizeUrl([
                'state' => $state,
                'mode' => 'post_message',
            ]);
            header('Location: ' . $authorizationUrl);
            die;
        }
    }

    public function getContacts()
    {
        if (isset($_GET['referer'])) {
            $this->client->setAccountBaseDomain($_GET['referer']);
        }
        try {
            $accessToken = $this->client->getOAuthClient()->getAccessTokenByCode($_GET['code']);

            if (!$accessToken->hasExpired()) {
                $this->saveToken([
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $this->client->getAccountBaseDomain(),
                ]);
            }
        } catch (Exception $e) {
            die((string)$e);
        }
        // dd($accessToken);
        $ownerDetails = $this->client->getOAuthClient()->getResourceOwner($accessToken);
        $this->client->setAccessToken($accessToken)
        ->setAccountBaseDomain($this->client->getAccountBaseDomain())
        ->onAccessTokenRefresh(
            function (AccessTokenInterface $accessToken, string $baseDomain) {
                $this->saveToken(
                    [
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $baseDomain,
                    ]
                );
            }
        );
        $contactsService = $this->client->contacts();
        // dd($contactsService);
        $contactsCollection = $contactsService->get();

        return $contactsCollection->toArray();
    }

    private function saveToken($accessToken) {
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];

            file_put_contents(TOKEN_FILE, json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }
}
