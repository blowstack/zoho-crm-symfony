<?php

namespace App\Controller;

use App\Form\OrderFormType;
use App\Service\ZohoCRM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     * @param Request $request
     * @param ZohoCRM $zohoCRM
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, ZohoCRM $zohoCRM)
    {

        $OrderForm = $this->createForm(OrderFormType::class);

        $OrderForm->handleRequest($request);

        if ($OrderForm->isSubmitted() && $OrderForm->isValid())
        {
            $data = $OrderForm->getData();
            $dealName = $data['course'];
            $firstName = $data['firstName'];
            $lastName = $data['lastName'];
            $email = $data['email'];
            $account = $data['company'];



            $zohoCRM->createDeal(
                $dealName,
                $firstName,
                $lastName,
                $email,
                $account

            );

        }

        return $this->render('order/index.html.twig', [
            'Form' => $OrderForm->createView()
        ]);
    }
}
