<?php

namespace App\Controller\admin;

use App\Service\ZohoCRM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ZohoController extends AbstractController
{
    /**
     * @Route("/admin/zoho/lead/{email}", name="admin_zoho_lead")
     * @param ZohoCRM $zohoCRM
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ZohoCRM $zohoCRM, $email)
    {

        $lead = $zohoCRM->getRecords('Leads', $email);

        return $this->render('admin/lead.html.twig', [
            'lead' => $lead,
        ]);
    }
}
