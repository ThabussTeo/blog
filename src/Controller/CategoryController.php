<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    private ArticleRepository $articleRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(ArticleRepository $articleRepository, CategoryRepository $categoryRepository) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
    }


    #[Route('/categories', name: 'app_categories')]
    public function categories(): Response
    {

        return $this->render('categories/categories.html.twig', [
            "categories" => $this->categoryRepository->findBy([], ['title' => 'ASC'])
        ]);
    }



    #[Route('/categories/{slug}', name: 'app_category', priority: 1)]
    public function category($slug, PaginatorInterface $paginator, Request $request, CategoryRepository $categoryRepository): Response
    {
        $articles = $paginator->paginate(
            $this->articleRepository->findBy(["category" => $categoryRepository->findBy(["slug" => $slug])] , ['createdAt' => 'DESC']),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('article/articles.html.twig', [
            "articles" => $articles
        ]);
    }
}
