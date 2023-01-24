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

class AnnonceController extends AbstractController
{
    #[Route('/', name: 'app.dash')]
    #[Route('/page/{page}', name: 'app.dash.page')]
    public function index(
        Request $request,
        AnnonceService $annonceService,
        CategoryService $categoryService,
        int $page = 1,
    ): Response {
        //$page = (int)$request->get('page', 1);

        $limit = (int)$request->get('limit', 5);

        $filters = [];

        if ($request->get('query') !== null) {
            $filters['query'] = $request->get('query');
        }

        if ($request->get('categories') !== null && is_array($request->get('categories'))) {

            $filters['in_categories'] = $request->get('categories');
        }

        if ($request->get('price_sup') !== null) {
            $filters['price_sup'] = (int)$request->get('price_sup');
        }

        if ($request->get('price_inf') !== null) {
            $filters['price_inf'] = (int)$request->get('price_inf');
        }

        $order = [];
        $allowedOrder = ['price', 'postedDate', 'title'];
        if ($request->get('order') !== null && str_contains($request->get('order'), ',')) {
            $o_ = explode(',', $request->get('order'));
            if (in_array($o_[0], $allowedOrder, true)) {
                $order[$o_[0]] = strtoupper($o_[1]);
            }
        }

        try {
            $annonces = $annonceService->getAnnonces($filters, $order, $page, $limit);
        } catch (\Throwable $e) {
            if ($e->getCode() === 10) {
                // page does not exists
                throw $this->createNotFoundException('La page n\'existe pas !');
            }
            $annonces = [
                'results' => [],
                'count' => 0,
                'totalPages' => 1,
                'error' => $e->getMessage(),
            ];
        }


        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'annonceQuery' => $annonces,
            'queryParams' => http_build_query($_GET),
            'actualPage' => $page,
            'categories' => $categoryService->getAllCategories(),
        ]);
    }

    #[Route('/annonce/add', name: 'app.home')]
    public function addAnnonce(
        EntityManagerInterface $em,
        Request $request,
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('You need to be logged');
        }

        $annonce = new Annonce(
            $user, 'Ma nouvelle annonce',
            0, false
        );
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($annonce->isPremium() && !$user->isPremium()) {
                throw new BadRequestHttpException('you are not a premium user');
            }
            // example of not mapped data
            $notes = $form->get('notes')?->getData();
            $em->persist($annonce);
            $em->flush();
        }

        return $this->render('default/add.annonce.html.twig', [
            'formulaireAddAnnonce' => $form->createView(),
        ]);
    }

    #[Route('/annonce/{id}', name: 'ads.display.simple', requirements: ['id' => '^\d+'])]
    public function displaySimple(
        ExampleService $exampleService,
        EntityManagerInterface $em,
        int $id
    ): Response {
        $seller = $exampleService->getSeller();
        $annonce = $em->getRepository(Annonce::class)
            ->findOneBy(['id' => $id]);

        if (!$annonce instanceof Annonce) {
            throw new NotFoundHttpException('Annonce does not exist');
        }


        return $this->render('default/ad.display.html.twig', [
            'controller_name' => 'DefaultController',
            'seller' => $seller,
            'annonce' => $annonce,
        ]);
    }

    #[Route('/annonce/{cat}/{id}', name: 'ads.display', requirements: ['id' => '^\d+', 'cat' => '[a-z][a-z0-9_-]+'])]
    public function display(string $cat, int $id): Response
    {
        return $this->render('default/ad.display.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
