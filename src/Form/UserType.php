<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class)
            //->add('roles')
            ->add('name', null, [
                'label' => 'Nom',
                'label_translation_parameters' => [
                    '%company%' => 'ACME Inc.',
                ],
            ])
            ->add('firstname', null, [
                'label' => 'Prenom',
                'label_translation_parameters' => [
                    '%company%' => 'ACME Inc.',
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'label_translation_parameters' => [
                    '%company%' => 'ACME Inc.',
                ],
            ]); 
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
