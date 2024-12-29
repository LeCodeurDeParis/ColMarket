<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Produit;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    #[Route('product/show/{id}', name: 'app_product_show')]
    public function showProductWithID(Request $request, EntityManagerInterface $em, Produit $product): Response
    {
        return $this->render('product/index.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/admin/product/delete/{id}', name: 'app_product_delete')]
    public function delete(Request $request, EntityManagerInterface $em, Produit $product)
    {
        // Vérifie la validité du token CSRF avant de supprimer le produit
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('csrf'))) {
            // Supprime le produit de la base de données
            $em->remove($product);
            $em->flush();
        }
        // Redirige vers la route 'app_product'
        return $this->redirectToRoute('app_product');
    }
    
    #[Route('/admin/product/update/{id}', name: 'app_product_update')]
    public function updateProduct(Request $request, EntityManagerInterface $em, Produit $product): Response
    {
        // Crée un formulaire pour mettre à jour le produit
        $form = $this->createForm(ProductType::class, $product, [
            'attr' => ['class' => 'flex flex-col items-center mb-auto p-8 bg-slate-600 text-white rounded-lg']
        ]);
    
        // Gère la requête du formulaire
        $form->handleRequest($request);
    
        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Prépare les modifications du produit dans la base de données
            $em->persist($product);
            $em->flush();
            // Redirige vers la route 'app_home'
            return $this->redirectToRoute('app_home');
        }
    
        return $this->render('product/update.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }
}