<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Crée une nouvelle instance de User
        $user = new User();
        
        // Crée un formulaire pour l'inscription de l'utilisateur
        $form = $this->createForm(RegistrationFormType::class, $user);
        
        // Gère la requête du formulaire
        $form->handleRequest($request);

        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode le mot de passe en clair
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Prépare l'entité User dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Ajoute un message flash de succès
            $this->addFlash('success', 'Inscription réussie. Vous pouvez maintenant vous connecter.');

            // Redirige vers la route de connexion
            return $this->redirectToRoute('app_login');
        } else {
            // Ajoute un message flash d'erreur si le formulaire n'est pas valide
            $this->addFlash('error', 'Il y a des erreurs dans le formulaire. Veuillez les corriger.');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}