<?php


namespace App\Service;


use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\oauth\ZohoOAuth;

class ZohoCRM
{

    private $configuration;

    public function __construct(array $config_values) {

        $this->configuration = [
            "client_id" => $config_values['client_id'],
            "client_secret" => $config_values['client_secret'],
            "redirect_uri" => $config_values['redirect_uri'],
            "currentUserEmail" => $config_values['currentUserEmail'],
            "accounts_url" => "https://accounts.zoho.com",
            "token_persistence_path" => realpath($config_values['token_persistence_path']),
            "access_type" => "offline",
            "persistence_handler_class" => "ZohoOAuthPersistenceHandler"
        ];

        ZCRMRestClient::initialize($this->configuration);

    }


    public function generateAccessToken(string $grant_token): string {

        $configuration = $this->configuration;

        try {
            ZCRMRestClient::initialize($configuration);
            $oAuthClient = ZohoOAuth::getClientInstance();
            $grantToken = $grant_token;
            $oAuthTokens = $oAuthClient->generateAccessToken($grantToken);
            return 'success';
        } catch (\Exception $exception) {
            return $exception;
        }
    }

}