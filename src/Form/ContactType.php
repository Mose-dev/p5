<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'attr' => [
                    'class' => 'Form-control'
                ]
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet de votre message',
                'attr' => [
                    'class' => 'Form-control'
                ]
            ])
            ->add('message', CKEditorType::class, [
                'label' => 'Votre message'
            ])
            ->add('Envoyer', SubmitType::class,[
                'attr' => [
                    'class' => 'btn-success'
                ] 
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
