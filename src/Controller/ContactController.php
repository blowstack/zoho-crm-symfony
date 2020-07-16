<?php

namespace App\Controller;

use App\Service\ZohoCRM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
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
    public function index(Request $request, ZohoCRM $zohoCRM, KernelInterface $kernelInterface)
    {
        $ContactForm = $this->createForm(ContactFormType::class);

        $ContactForm->handleRequest($request);

        if($ContactForm->isSubmitted() && $ContactForm->isValid()) {

            $data = $ContactForm->getData();
            $attachment = $data['attachment'];

            if($attachment) {
                $directory =  $kernelInterface->getProjectDir() . '/tmp/files';
                $fileName = pathinfo($data['attachment']->getClientOriginalName(), PATHINFO_FILENAME) .'.' . $attachment->guessExtension();
                $attachment->move($directory, $fileName );
                $attachment_path = $directory . '/' . $fileName;
            }

            $zohoCRM->createLead(
                $data['firstName'],
                $data['lastName'],
                $data['email'],
                $data['content'],
                $data['company'],
                $data['phone'],
                $attachment_path ?? $attachment
            );
        }

        return $this->render('contact/index.html.twig', [
            'Form' => $ContactForm->createView(),
        ]);
    }
}
