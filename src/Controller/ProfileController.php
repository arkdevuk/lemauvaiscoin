<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\User;
use App\services\AnnonceService;
use App\services\CategoryService;
use App\services\ExampleService;
use App\Type\AnnonceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/my-profile', name: 'profile.my')]
    public function cgu(): Response
    {


        return $this->render('profile/profile.my.twig', []);
    }
}
