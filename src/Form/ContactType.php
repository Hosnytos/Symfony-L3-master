<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, ['label'=> "Nom", 'attr' => ['placeholder' => 'Saisissez votre nom']])
            ->add('email', EmailType::class, ['label'=> "Email", 'attr' => ['placeholder' => 'Ex : email@email.com']])
            ->add('objet', null, ['label'=> "Objet", 'attr' => ['placeholder' => 'Veuillez indiquer l\'objet de votre messsage']])
            ->add('message', null, ['label'=> "Message", 'attr' => ['placeholder' => 'Veuillez saisir votre message']])
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
