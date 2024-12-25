<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Produit;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $product = new Produit();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('app_product');
        }

        $products = $em->getRepository(Produit::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form,
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_product_delete')]
    public function delete(Request $request, EntityManagerInterface $em, Produit $product)
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('csrf'))) {
            $em->remove($product);
            $em->flush();
        }
        return $this->redirectToRoute('app_product');
    }
}
