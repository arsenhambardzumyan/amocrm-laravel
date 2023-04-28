<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;

class AmoCRM
{
    protected $client;

    public function __construct()
    {
        dd(9285);
        $this->client = new AmoCRMApiClient('de2f600f-6a46-47a8-9879-3696bcbe0193', 't1sYggXMyuFNU0nOGMJvSTfd6zaW1KPNnCnGv4L2Ty2KIcN73StvPuTZjP5UGvTa', route('home'));
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
        try {
            $accessToken = $this->client->getOAuthClient()->getAccessTokenByCode($_GET['code']);

            if (!$accessToken->hasExpired()) {
                saveToken([
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $apiClient->getAccountBaseDomain(),
                ]);
            }
        } catch (Exception $e) {
            die((string)$e);
        }

        $ownerDetails = $this->client->getOAuthClient()->getResourceOwner($accessToken);
        $this->client->setAccessToken($accessToken)
        ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
        ->onAccessTokenRefresh(
            function (AccessTokenInterface $accessToken, string $baseDomain) {
                saveToken(
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
        $contactsCollection = $contactsService->get();

        return $contactsCollection->toArray();
    }
}
