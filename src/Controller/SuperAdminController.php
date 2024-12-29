<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Panier;
use App\Entity\ContenuPanier;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use DateTime;

#[IsGranted('ROLE_SUPER_ADMIN')]
class SuperAdminController extends AbstractController
{
    #[Route('/super/admin', name: 'app_super_admin')]
    public function index(EntityManagerInterface $em): Response
    {

        $paniers = $em->getRepository(Panier::class)->findBy(['etat' => false]);

        $allPanierNotPurchased = [];
        foreach ($paniers as $panier) {
            $allPanierNotPurchased[] = [
                'panier_id' => $panier->getId(),
                'user_email' => $panier->getUser()->getEmail(),
            ];
        }

        $dayStart = new DateTime('today');
        $dayEnd = new DateTime('tomorrow');

        $users = $em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.createdAt BETWEEN :dayStart AND :dayEnd')
            ->setParameter('dayStart', $dayStart)
            ->setParameter('dayEnd', $dayEnd)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
        
        return $this->render('super_admin/index.html.twig', [
            'paniers' => $allPanierNotPurchased,
            'users' => $users
        ]);
    }

    #[Route('/super/admin/showCartByID/{id}', name: 'app_super_admin_cart_by_id')]
    public function showCartByID(EntityManagerInterface $em, int $id): Response
    {
        $user = $this->getUser();

        $panier = $em->getRepository(Panier::class)->findOneBy([
            'id' => $id,
            'user' => $user,
            'etat' => "0"
        ]);

        if (!$panier) {
            throw $this->createNotFoundException('No cart found for id ' . $id);
        }

        $contenuPanier = $em->getRepository(ContenuPanier::class)->findBy(['panier' => $panier]);

        return $this->render('super_admin/contenuUserPanier.html.twig', [
            'user' => $user,
            'panier' => $panier,
            'contenuPanier' => $contenuPanier,
        ]);
    }
}