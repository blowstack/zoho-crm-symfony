<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('course', ChoiceType::class, [
                'choices' => [
                    'Symfony' => 'Symfony',
                    'Laravel' => 'Laravel',
                    'Phalcon' => 'Phalcon',
                ]
            ])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('company', TextType::class)
            ->add('email', EmailType::class ,['required' => false])
            ->add('save', SubmitType::class)
        ;
    }

}
