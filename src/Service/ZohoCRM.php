<?php


namespace App\Service;


use Symfony\Component\Config\Definition\Exception\Exception;
use zcrmsdk\crm\crud\ZCRMModule;
use zcrmsdk\crm\crud\ZCRMRecord;
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
            "token_persistence_path" => $config_values['token_persistence_path'],
            "access_type" => "offline",
            "persistence_handler_class" => "ZohoOAuthPersistenceHandler",
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

    public function createLead($firstName, $lastName, $email, $description = null, $company = null, $phone = null) {

        $RecordLead = ZCRMRecord::getInstance("Leads",null);
        $RecordLead->setFieldValue('First_Name', $firstName);
        $RecordLead->setFieldValue('Last_Name', $lastName);
        $RecordLead->setFieldValue('Email', $email);
        $RecordLead->setFieldValue('Company', $company);
        $RecordLead->setFieldValue('Phone', $phone);
        $RecordLead->setFieldValue('Description', $description);
        $RecordLead->setFieldValue('Lead_Source', 'contact form');

        $Leads = ZCRMModule::getInstance('Leads');

        $arrayOfLeads = [];
        array_push($arrayOfLeads, $RecordLead);

        try {
            $response = $Leads->createRecords($arrayOfLeads);
            $result = $response->getData()[0]->getEntityId();
        }
        catch (Exception $exception) {
            $result = $exception;
        }

        return $result;
    }

    function getRecords($moduleName, $email) {

        $Module = ZCRMModule::getInstance($moduleName);
        return $Module->searchRecordsByEmail($email);
    }

}
