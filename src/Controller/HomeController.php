<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Produit;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        // Crée une nouvelle instance de Produit
        $product = new Produit();
        
        // Crée un formulaire pour le produit
        $form = $this->createForm(ProductType::class, $product, [
            'attr' => ['class' => 'flex flex-col items-center mb-auto p-8 bg-slate-600 text-white rounded-lg']
        ]);

        // Gère la requête du formulaire
        $form->handleRequest($request);
        
        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('Photo')->getData();

            // Vérifie si un fichier image a été téléchargé
            if ($imageFile) {
                // Génère un nouveau nom de fichier unique
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    // Déplace le fichier téléchargé vers le répertoire de téléchargement
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gère une exception si le fichier ne peut pas être déplacé
                    throw $e;
                }

                // Définit le nom de la photo dans l'entité Produit
                $product->setPhoto($newFilename);
            }

            // Prépare l'entité Produit dans la base de données
            $em->persist($product);
            $em->flush();

            // Redirige vers la route 'app_home'
            return $this->redirectToRoute('app_home');
        }

        // Récupère l'utilisateur connecté
        $user = $this->getUser();
        
        // Récupère tous les produits de la base de données
        $products = $em->getRepository(Produit::class)->findAll();
        
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'user' => $user,
            'form' => $form,
        ]);
    }
}