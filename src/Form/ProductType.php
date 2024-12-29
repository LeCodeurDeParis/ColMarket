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
                'label' => 'Mproduits.nom',
                'label_attr' => ['class' => 'block text-sm font-medium text-white'],
                'attr' => ['class' => 'mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'],
                'row_attr' => ['class' => 'mb-6'],
            ])
            ->add('Description', TextType::class, [
                'label' => 'Description',
                'label_attr' => ['class' => 'block text-sm font-medium text-white'],
                'attr' => ['class' => 'mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'],
                'row_attr' => ['class' => 'mb-6'],
            ])
            ->add('Prix', TextType::class, [
                'label' => 'Mproduits.prix',
                'label_attr' => ['class' => 'block text-sm font-medium text-white'],
                'attr' => ['class' => 'mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'],
                'row_attr' => ['class' => 'mb-6'],
            ])
            ->add('Stock', TextType::class, [
                'label' => 'Stock',
                'label_attr' => ['class' => 'block text-sm font-medium text-white'],
                'attr' => ['class' => 'mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'],
                'row_attr' => ['class' => 'mb-6'],
            ])
            ->add('Photo', FileType::class, [
                'label' => 'Image (jpg, jpeg, png)',
                'label_attr' => ['class' => 'block text-sm font-medium text-white'],
                'attr' => ['class' => 'mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'],
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
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Mproduits.save',
                'attr' => ['class' => 'mt-4 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-br from-[#475569] to-[#112C51] hover:from-[#112C51] hover:to-[#475569] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
