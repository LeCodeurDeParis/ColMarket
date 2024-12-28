<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Produit;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $product = new Produit();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();
        $products = $em->getRepository(Produit::class)->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'user' => $user,
            'form' => $form,
        ]);
    }
}
