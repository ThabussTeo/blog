<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    private ArticleRepository $repository;

    public function __construct(ArticleRepository $repository) {
        $this->repository = $repository;
    }

    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('home/home.html.twig', [
            "articles" => $this->repository->findBy([], ['createdAt' => 'DESC'], limit : 10)
        ]);
    }


}
