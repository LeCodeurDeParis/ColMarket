<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom', TextType::class, [
                'label_attr' => ['class' => 'text-white'],
                'attr' => ['class' => 'text-black'],
                'row_attr' => ['class' => 'flex gap-4 mb-6'],
            ])
            ->add('Description', TextType::class, [
                'label_attr' => ['class' => 'text-white'],
                'attr' => ['class' => 'text-black'],
                'row_attr' => ['class' => 'flex gap-4 mb-6 w-[80%]'],
            ])
            ->add('Prix', TextType::class, [
                'label_attr' => ['class' => 'text-white'],
                'attr' => ['class' => 'text-black'],
                'row_attr' => ['class' => 'flex gap-4 mb-6'],

            ])
            ->add('Stock', TextType::class, [
                'label_attr' => ['class' => 'text-white'],
                'attr' => ['class' => 'text-black'],
                'row_attr' => ['class' => 'flex gap-4 mb-6'],


            ])
            ->add('Photo', FileType::class, [
                'label' => 'Image (jpg, jpeg, png)',
                'label_attr' => ['class' => 'text-white'],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (jpg, jpeg, png)',
                    ])
                ],
            ], )
            ->add('save', SubmitType::class, [
                'label' => 'Sauvergarder le Produit', 
                'attr' => ['class' => 'bg-gradient-to-br from-[#475569] to-[#112C51] py-2 px-4 rounded-lg']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
