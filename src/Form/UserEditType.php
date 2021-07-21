<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class)
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
            ->add('password', null, [
                'label' => 'Mot de passe',
                'label_translation_parameters' => [
                    '%company%' => 'ACME Inc.',
                ],
            ]) 
            ->add('roles', ChoiceType::class,[
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => false,
                'multiple' => true,
                'label' => 'Rôles',

            ])
          
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
