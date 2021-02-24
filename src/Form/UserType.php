<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, ['label' => 'Nom','required' => true])
            ->add('prenom', null, ['label' => 'Prénom','required' => true])
            ->add('civilite',  null, ['label' => 'Civilité','required' => true])
            ->add('datenaissance',  BirthdayType::class, ['label' => 'Date de naissance','required' => true])
            ->add('telephone', null, ['label' => 'N° de téléphone','required' => false])
            ->add('ville', null, ['label' => 'Ville','required' => false])
            ->add('codepostal', null, ['label' => 'Code postal','required' => false])
            ->add('pays', CountryType::class, [
                'required' => false,
                'preferred_choices' => ['DE'],
                'label' => 'Pays',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'address.form.country.placeholder'
                ],
                'label_attr' => [
                    'class' => 'col-sm-2 col-form-label'
                ],
            ])
            ->add('numsecu', null, ['label' => 'N° de sécurité sociale','required' => false])
            ->add('image', FileType::class, [
                'label' => 'Photo de profil (JPEG, PNG, GIF)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez ajouter une image au format JPEG, PNG ou GIF !',
                    ])
                ],
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
