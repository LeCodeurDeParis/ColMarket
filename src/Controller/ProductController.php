<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('csrf'))) {
            $em->remove($product);
            $em->flush();
        }
        return $this->redirectToRoute('app_product');
    }
    
    #[Route('/admin/product/update/{id}', name: 'app_product_update')]
    public function updateProduct(Request $request, EntityManagerInterface $em, Produit $product): Response
    {
        $form = $this->createForm(ProductType::class, $product, [
            'attr' => ['class' => 'flex flex-col items-center mb-auto p-8 bg-slate-600 text-white rounded-lg']
        ]);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }
    
        return $this->render('product/update.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

}
