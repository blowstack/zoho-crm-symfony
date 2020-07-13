<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactFormType;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index()
    {
        $ContactForm = $this->createForm(ContactFormType::class);


        return $this->render('contact/index.html.twig', [
            'Form' => $ContactForm->createView(),
        ]);
    }
}
