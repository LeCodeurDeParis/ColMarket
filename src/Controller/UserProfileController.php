<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Panier;
use App\Entity\ContenuPanier;
use Doctrine\ORM\EntityManagerInterface;

class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $paniers = $em->getRepository(Panier::class)->findBy([
            'user' => $user,
            'etat' => "1"
        ]);

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
    #[Route('/user/profile/showCartByID/{id}', name: 'app_user_profile_cart_by_id')]
    public function showCartByID(EntityManagerInterface $em, int $id): Response
    {
        $user = $this->getUser();

        $panier = $em->getRepository(Panier::class)->findOneBy([
            'id' => $id,
            'user' => $user,
            'etat' => "1"
        ]);

        if (!$panier) {
            throw $this->createNotFoundException('No cart found for id ' . $id);
        }

        $contenuPanier = $em->getRepository(ContenuPanier::class)->findBy(['panier' => $panier]);

        return $this->render('user_profile/contenuCommande.html.twig', [
            'user' => $user,
            'panier' => $panier,
            'contenuPanier' => $contenuPanier,
        ]);
    }

}
