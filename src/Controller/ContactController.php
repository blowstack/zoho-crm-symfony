<?php

namespace App\Controller;

use App\Service\ZohoCRM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactFormType;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param ZohoCRM $zohoCRM
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, ZohoCRM $zohoCRM)
    {
        $ContactForm = $this->createForm(ContactFormType::class);

        $ContactForm->handleRequest($request);

        if($ContactForm->isSubmitted() && $ContactForm->isValid()) {

            $data = $ContactForm->getData();
            $zohoCRM->createLead(
                $data['firstName'],
                $data['lastName'],
                $data['email'],
                $data['content'],
                $data['company'],
                $data['phone']
            );
        }

        return $this->render('contact/index.html.twig', [
            'Form' => $ContactForm->createView(),
        ]);
    }
}
