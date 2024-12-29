<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Panier;
use App\Entity\ContenuPanier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\User;
use App\Form\UserProfileType;

class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        // Récupère les paniers achetés de l'utilisateur
        $paniers = $em->getRepository(Panier::class)->findBy([
            'user' => $user,
            'etat' => "1"
        ]);

        // Récupère les contenus des paniers
        $contenus = [];
        foreach ($paniers as $panier) {
            $contenus[$panier->getId()] = $em->getRepository(ContenuPanier::class)->findBy([
                'panier' => $panier
            ]);
        }

        return $this->render('user_profile/index.html.twig', [
            'user' => $user,
            'paniers' => $paniers,
            'contenus' => $contenus,
        ]);
    }

    #[Route('/user/profile/update/{id}', name: 'app_user_profile_update')]
    public function update(Request $request, EntityManagerInterface $em): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        // Crée un formulaire pour mettre à jour le profil de l'utilisateur
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Prépare les modifications de l'utilisateur dans la base de données
            $em->persist($user);
            $em->flush();

            // Ajoute un message flash de succès
            $this->addFlash('success', 'Profil mis à jour avec succès.');

            // Redirige vers la route 'app_user_profile'
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user_profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/user/profile/showCartByID/{id}', name: 'app_user_profile_cart_by_id')]
    public function showCartByID(EntityManagerInterface $em, int $id): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        // Récupère le panier avec l'ID donné, l'utilisateur et l'état "1" (acheté)
        $panier = $em->getRepository(Panier::class)->findOneBy([
            'id' => $id,
            'user' => $user,
            'etat' => "1"
        ]);

        // Si le panier n'existe pas, ajoute un message flash d'erreur
        if (!$panier) {
            $this->addFlash('error', 'Panier non trouvé avec l\'id.' . $id);
        }

        // Récupère les contenus du panier
        $contenuPanier = $em->getRepository(ContenuPanier::class)->findBy(['panier' => $panier]);

        return $this->render('user_profile/contenuCommande.html.twig', [
            'user' => $user,
            'panier' => $panier,
            'contenuPanier' => $contenuPanier,
        ]);
    }
}