<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        // Vérifie si l'utilisateur est connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir votre panier.');
            return $this->redirectToRoute('app_login');
        }

        // Récupère le panier de l'utilisateur avec l'état "0" (non acheté)
        $panier = $em->getRepository(Panier::class)->findOneBy([
            'user' => $user,
            'etat' => "0"
        ]);

        // Si le panier n'existe pas, crée un nouveau panier
        if (!$panier) {
            $panier = new Panier();
            $panier->setEtat(false);
            $panier->setUser($user);
            $em->persist($panier);
            $em->flush(); 
        }

        // Récupère les contenus du panier
        $contenus = $em->getRepository(ContenuPanier::class)->findBy(['panier' => $panier->getId()]);

        // Calcule le total du panier
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
        // Récupère l'utilisateur connecté
        $user = $this->getUser();
        
        // Récupère le panier de l'utilisateur avec l'état "0" (non acheté)
        $panier = $em->getRepository(Panier::class)->findOneBy(['user' => $user, 'etat' => false]);

        // Si le panier n'existe pas, crée un nouveau panier
        if (!$panier) {
            $panier = new Panier();
            $panier->setEtat(false);
            $panier->setUser($user);
            $em->persist($panier);
            $em->flush(); 
        }

        // Vérifie si le produit existe
        if (!$product) {
            $this->addFlash('error', 'Produit non trouvé.');
            return $this->redirectToRoute('app_home');
        }

        // Récupère le contenu du panier pour le produit
        $contenuPanier = $em->getRepository(ContenuPanier::class)->findOneBy([
            'panier' => $panier,
            'produit' => $product
        ]);

        // Si le contenu existe, augmente la quantité, sinon crée un nouveau contenu
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

        // Sauvegarde les modifications
        $em->flush();

        // Ajoute un message flash de succès
        $this->addFlash('success', 'Produit ajouté au panier.');
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/user/remove/{id}', name: 'app_panier_remove')]
    public function remove(EntityManagerInterface $em, Produit $product): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();
        
        // Récupère le panier de l'utilisateur avec l'état "0" (non acheté)
        $panier = $em->getRepository(Panier::class)->findOneBy(['user' => $user, 'etat' => false]);

        // Récupère le contenu du panier pour le produit
        $contenuPanier = $em->getRepository(ContenuPanier::class)->findOneBy([
            'panier' => $panier,
            'produit' => $product
        ]);

        // Vérifie si le contenu existe
        if (!$contenuPanier) {
            $this->addFlash('error', 'Produit introuvable dans le panier.');
            return $this->redirectToRoute('app_panier');
        }

        // Supprime le contenu du panier
        $em->remove($contenuPanier);
        $em->flush();

        // Ajoute un message flash de succès
        $this->addFlash('success', 'Produit retiré du panier.');
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/user/buy', name: 'app_panier_buy')]
    public function buy(EntityManagerInterface $em): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        // Récupère le panier de l'utilisateur avec l'état "0" (non acheté)
        $panier = $em->getRepository(Panier::class)->findOneBy([
            'user' => $user,
            'etat' => false
        ]);

        // Récupère les contenus du panier
        $contenus = $em->getRepository(ContenuPanier::class)->findBy(['panier' => $panier]);

        // Vérifie si le panier est vide
        if (empty($contenus)) {
            $this->addFlash('error', 'Le panier est vide.');
            return $this->redirectToRoute('app_panier');
        }

        // Vérifie le stock et met à jour les quantités
        foreach ($contenus as $contenu) {
            $product = $contenu->getProduit();
            $quantite = $contenu->getQuantite();
            $nouveauStock = $product->getStock() - $quantite;
    
            if ($nouveauStock < 0) {
                $this->addFlash('error', 'Stock insuffisant pour le produit ' . $product->getNom());
                return $this->redirectToRoute('app_panier');
            }
    
            $product->setStock($nouveauStock);

            // Supprime le produit si le stock est à 0
            if ($nouveauStock == 0) {
                $contenusADelete = $em->getRepository(ContenuPanier::class)->findBy(['produit' => $product]);
                foreach ($contenusADelete as $contenuADelete) {
                    $em->remove($contenuADelete);
                }
                $em->remove($product);
                $this->addFlash('info', 'Produit ' . $product->getNom() . ' supprimé car le stock est à 0.');
            }
        }

        // Met à jour l'état du panier et la date d'achat
        $panier->setEtat(true);
        $panier->setDateAchat(new \DateTime());
        $em->flush();

        // Crée un nouveau panier pour l'utilisateur
        $nouveauPanier = new Panier();
        $nouveauPanier->setUser($user);
        $nouveauPanier->setEtat(false);
        $em->persist($nouveauPanier);
        $em->flush();

        // Ajoute un message flash de succès
        $this->addFlash('success', 'Achat validé.');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/user/deletePanier/{id}', name: 'app_panier_delete')]
    public function deletePanier(EntityManagerInterface $em): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();
        
        // Récupère le panier de l'utilisateur avec l'état "0" (non acheté)
        $panier = $em->getRepository(Panier::class)->findOneBy(['user' => $user, 'etat' => false]);

        // Récupère les contenus du panier
        $contenus = $em->getRepository(ContenuPanier::class)->findBy(['panier' => $panier]);
        
        // Supprime tous les contenus du panier
        foreach ($contenus as $contenu) {
            $em->remove($contenu);
        }

        // Supprime le panier
        $em->remove($panier);
        $em->flush();

        // Ajoute un message flash de succès
        $this->addFlash('success', 'Panier Supprimé.');
        return $this->redirectToRoute('app_home');
    }
}