<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\oauth\ZohoOAuth;

class ZohoOauthController extends AbstractController
{
  /**
   * @Route("/zoho/oauth/{grant_token}", name="zoho_oauth")
   * @param $grant_token
   */
    public function generateTokens($grant_token)
    {

      $configuration = [
        "client_id"			=> 	'1000.A17M9ZGQRZEFDIFEM9TQ378I3NF9PH',
        "client_secret"		=> 	'b76bd9cb9e10d4982762c7c1812ded35cb54d5404f',
        "redirect_uri"		=>	'http://dummy_address',
        "currentUserEmail"	=>	'p.golon@blowstack.com',
        "token_persistence_path" =>"/home/blowstack/Projects/boilerplates/zoho_crm_laravel/config/Zoho"
      ];

      $result = 'failed';

      try {
        ZCRMRestClient::initialize($configuration);
        $oAuthClient = ZohoOAuth::getClientInstance();
        $grantToken = $grant_token;
        $oAuthTokens = $oAuthClient->generateAccessToken($grantToken);
        $result = 'success';
      }
      catch (\Exception $exception) {
        $result = $exception;
      }


        return $this->render('zoho_oauth/index.html.twig', [
            'result' => $result,
        ]);
    }
}
