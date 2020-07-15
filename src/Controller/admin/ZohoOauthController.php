<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ZohoOauthFormType;
use App\Service\ZohoCRM;

class ZohoOauthController extends AbstractController
{

    /**
     * @Route("/admin/zoho/oauth", name="admin_zoho_oauth")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {

        $Form = $this->createForm(ZohoOauthFormType::class);

        $Form->handleRequest($request);

        if($Form->isSubmitted() && $Form->isValid()) {

            $grantToken = $Form->getData()['grant_token'];

            return$this->forward('App\Controller\admin\ZohoOauthController::generateTokens', [
                'grant_token'  => $grantToken,
            ]);
        }

        return $this->render('admin/zoho_oauth/index.html.twig', [
            'Form' => $Form->createView()
        ]);
    }

    /**
     * @Route("/admin/zoho/oauth/{grant_token}", name="admin_zoho_oauth_generate")
     * @param ZohoCRM $zohoCRM
     * @param $grant_token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generateTokens(ZohoCRM $zohoCRM, $grant_token)
    {

//      $configuration = [
//        "client_id"			=> 	'1000.FT392KQV3WP8YMZ4A8L3PPJO40DRKH',
//        "client_secret"		=> 	'676611b6b29038ab5d6cb82c2ae5bb2a7843cfb728',
//        "redirect_uri"		=>	'http://dummy_address',
//        "currentUserEmail"	=>	'dev@blowstack.com',
//        "token_persistence_path" =>"/home/blowstack/Projects/boilerplates/zoho_crm_symfony/config/Zoho"
//      ];
//
//      $result = 'failed';
//
//      try {
//        ZCRMRestClient::initialize($configuration);
//        $oAuthClient = ZohoOAuth::getClientInstance();
//        $grantToken = $grant_token;
//        $oAuthTokens = $oAuthClient->generateAccessToken($grantToken);
//        $result = 'success';
//      }
//      catch (\Exception $exception) {
//        $result = $exception;
//      }

        $result = $zohoCRM->generateAccessToken($grant_token);

        return $this->render('admin/zoho_oauth/index.html.twig', [
            'result' => $result,
        ]);
    }
}
