<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('user_profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}
