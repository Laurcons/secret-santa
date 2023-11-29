<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();

        if ($user) {
            return $this->redirectToRoute("app_santa");
        }
        return $this->redirectToRoute("app_login_begin");
    }
}
