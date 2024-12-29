<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\ContenuPanier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class PanierController extends AbstractController
{
    #[Route('/user/panier', name: 'app_panier')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir votre panier.');
            return $this->redirectToRoute('app_login');
        }

        $panier = $em->getRepository(Panier::class)->findOneBy([
            'user' => $user,
            'etat' => "0"
        ]);

        if (!$panier) {
            $panier = new Panier();
            $panier->setEtat(false);
            $panier->setUser($user);
            $em->persist($panier);
            $em->flush(); 
        }

        
        $contenus = $em->getRepository(ContenuPanier::class)->findBy(['panier' => $panier->getId()]);

        $total = 0;
        foreach ($contenus as $contenu) {
            $total += $contenu->getProduit()->getPrix() * $contenu->getQuantite();
        }

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'contenus' => $contenus,
            'total' => $total,
        ]);
    }
    #[Route('/user/panier/add/{id}', name: 'app_panier_add')]
    public function add(EntityManagerInterface $em, Produit $product): Response
    {
        $user = $this->getUser();
        $panier = $em->getRepository(Panier::class)->findOneBy(['user' => $user, 'etat' => false]);

        if (!$panier) {
            $panier = new Panier();
            $panier->setEtat(false);
            $panier->setUser($user);
            $em->persist($panier);
            $em->flush(); 
        }

        if (!$product) {
            $this->addFlash('error', 'Produit non trouvé.');
            return $this->redirectToRoute('app_home');
        }

        $contenuPanier = $em->getRepository(ContenuPanier::class)->findOneBy([
            'panier' => $panier,
            'produit' => $product
        ]);

        if ($contenuPanier) {
            $contenuPanier->setQuantite($contenuPanier->getQuantite() + 1);
        } else {
            $contenuPanier = new ContenuPanier();
            $contenuPanier->setPanier($panier);
            $contenuPanier->setProduit($product);
            $contenuPanier->setQuantite(1);
            $contenuPanier->setDate(new \DateTime());
            $em->persist($contenuPanier);
        }

        $em->flush();

        $this->addFlash('success', 'Produit ajouté au panier.');
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/user/remove/{id}', name: 'app_panier_remove')]
    public function remove(EntityManagerInterface $em, Produit $product): Response
    {
        $user = $this->getUser();
        $panier = $em->getRepository(Panier::class)->findOneBy(['user' => $user, 'etat' => false]);

        $contenuPanier = $em->getRepository(ContenuPanier::class)->findOneBy([
            'panier' => $panier,
            'produit' => $product
        ]);

        if (!$contenuPanier) {
            $this->addFlash('error', 'Produit introuvable dans le panier.');
            return $this->redirectToRoute('app_panier');
        }

        $em->remove($contenuPanier);
        $em->flush();

        $this->addFlash('success', 'Produit retiré du panier.');
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/user/buy', name: 'app_panier_buy')]
    public function buy(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $panier = $em->getRepository(Panier::class)->findOneBy([
            'user' => $user,
            'etat' => false
        ]);

        $contenus = $em->getRepository(ContenuPanier::class)->findBy(['panier' => $panier]);

        if (empty($contenus)) {
            $this->addFlash('error', 'Le panier est vide.');
            return $this->redirectToRoute('app_panier');
        }

        foreach ($contenus as $contenu) {
            $product = $contenu->getProduit();
            $quantite = $contenu->getQuantite();
            $nouveauStock = $product->getStock() - $quantite;
    
            if ($nouveauStock < 0) {
                $this->addFlash('error', 'Stock insuffisant pour le produit ' . $product->getNom());
                return $this->redirectToRoute('app_panier');
            }
    
            $product->setStock($nouveauStock);

            if ($nouveauStock == 0) {
                $contenusADelete = $em->getRepository(ContenuPanier::class)->findBy(['produit' => $product]);
                foreach ($contenusADelete as $contenuADelete) {
                    $em->remove($contenuADelete);
                }
                $em->remove($product);
                $this->addFlash('info', 'Produit ' . $product->getNom() . ' supprimé car le stock est à 0.');
            }
        }

        $panier->setEtat(true);
        $panier->setDateAchat(new \DateTime());
        $em->flush();

        $nouveauPanier = new Panier();
        $nouveauPanier->setUser($user);
        $nouveauPanier->setEtat(false);
        $em->persist($nouveauPanier);
        $em->flush();

        $this->addFlash('success', 'Achat validé.');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/user/deletePanier/{id}', name: 'app_panier_delete')]
    public function deletePanier(EntityManagerInterface $em,): Response
    {
        $user = $this->getUser();
        $panier = $em->getRepository(Panier::class)->findOneBy(['user' => $user, 'etat' => false]);

        $contenus = $em->getRepository(ContenuPanier::class)->findBy(['panier' => $panier]);
        foreach ($contenus as $contenu) {
            $em->remove($contenu);
        }

        $em->remove($panier);
        $em->flush();

        $this->addFlash('success', 'Panier Supprimé.');
        return $this->redirectToRoute('app_home');
    }

}
