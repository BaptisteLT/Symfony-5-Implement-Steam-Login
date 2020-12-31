<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SteamAuthController extends AbstractController
{
    /**
     * @Route("/steam/auth", name="steam_auth")
     */
    public function index(): Response
    {
        return $this->render('steam_auth/index.html.twig', [
            'controller_name' => 'SteamAuthController',
        ]);
    }

    /**
     * @Route("/logincheck", name="logincheck")
     */
    public function loginCheck()
    {
        return $this->render('steam_auth/index.html.twig', [
            'controller_name' => 'SteamAuthController',
        ]);
    }
}
