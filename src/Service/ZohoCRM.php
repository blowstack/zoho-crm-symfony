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

    public function createLead($firstName, $lastName, $email = null, $description = null, $company = null, $phone = null, $attachment = null) {

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
            $leadId = $response->getData()[0]->getEntityId();

            if ($attachment) {
                $RecordLead = ZCRMRecord::getInstance('Leads', $leadId);
                $RecordLead->uploadAttachment($attachment);
            }

        }
        catch (Exception $exception) {
            // this is not proper way to handle exceptions on the production env
            return $exception;
        }

    }

    public function createDeal($dealName, $firstName, $lastName, $email, $account = null) {

        $RecordDeal = ZCRMRecord::getInstance("Deals",null);

        $accountId = null;
        $contactId = null;

        if ($account) {
            $accountId = $this->findAccountId($account) ?? $this->createAccount($account);
        }

        if ($firstName && $lastName && $email) {
            $contactId = $this->findContactId($email) ?? $this->createContact($firstName, $lastName, $email, $accountId) ;
        }


        $RecordDeal->setFieldValue('Deal_Name', $dealName);
        $RecordDeal->setFieldValue('Contact_Name', $contactId);
        $RecordDeal->setFieldValue('Account_Name', $accountId);

        $Deals = ZCRMModule::getInstance('Deals');

        $arrayOfDeals = [];
        array_push($arrayOfDeals, $RecordDeal);

        try {
            $response = $Deals->createRecords($arrayOfDeals);
            $dealId = $response->getData()[0]->getEntityId();

        }
        catch (Exception $exception) {
            // this is not proper way to handle exceptions on the production env
            return $exception;
        }
    }

    function getRecords($moduleName, $email) {

        $Module = ZCRMModule::getInstance($moduleName);
        return $Module->searchRecordsByEmail($email);
    }

    public function findAccountId($companyName) {
        $Accounts = ZCRMModule::getInstance('Accounts');
        $criteria = 'Account_Name:equals:' . $companyName;

        try {
            $SearchResult =  $Accounts->searchRecordsByCriteria($criteria);
            $data =  $SearchResult->getData();
            return $data[0]->getEntityId();

        }
        catch (\Exception $exception) {
            return null;
        }
    }

    public function createAccount($company_name) {

        if ($company_name != null) {

            $RecordAccount = ZCRMRecord::getInstance("Accounts", null);
            $RecordAccount->setFieldValue('Account_Name', $company_name);
            $array[0] = $RecordAccount;

            $Accounts = ZCRMModule::getInstance('Accounts');
            $Response = $Accounts->createRecords($array);

            return $Response->getData()[0]->getEntityId();
        }
        else {
            return null;
        }
    }

    public function findContactId($email) {

        $Contacts = ZCRMModule::getInstance('Contacts');

        try {
            $Contact = $Contacts->searchRecordsByEmail($email);
            return $Contact->getData()[0]->getEntityId();
        }
        catch (\Exception $exception) {
            return null;
        }
    }

    public function createContact($first_name, $last_name, $email, $account_id = null) {

        $Contacts = ZCRMModule::getInstance('Contacts');
        $RecordContact = ZCRMRecord::getInstance("Contacts",null);
        $RecordContact->setFieldValue('First_Name', $first_name);
        $RecordContact->setFieldValue('Last_Name', $last_name);
        $RecordContact->setFieldValue('Email',$email);
        if ($account_id) {
            $RecordContact->setFieldValue('Account_Name',$account_id);
        }

        $array[0] = $RecordContact;
        $Response = $Contacts->createRecords($array);

        return $Response->getData()[0]->getEntityId();
    }

}
