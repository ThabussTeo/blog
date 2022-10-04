<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{

    private ArticleRepository $articleRepository;
    private CommentRepository $commentRepository;

    public function __construct(ArticleRepository $articleRepository, CommentRepository $commentRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->commentRepository = $commentRepository;
    }

    #[Route('/articles', name: 'app_articles')]
    public function articles(PaginatorInterface $paginator, Request $request): Response
    {
        $articles = $paginator->paginate(
            $this->articleRepository->findBy([], ['createdAt' => 'DESC']),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('article/articles.html.twig', [
            "articles" => $articles
        ]);
    }

    #[Route('/articles/{slug}', name: 'app_article')]
    public function article($slug): Response
    {
        return $this->render('article/article.html.twig', [

            //TODO DRY

            "article" => $this->articleRepository->findOneBy(array("slug" => $slug)),
            "comments" => $this->commentRepository->findBy(["article" => $this->articleRepository->findOneBy(array("slug" => $slug))]),
            "commentsCount" => count($this->commentRepository->findBy(["article" => $this->articleRepository->findOneBy(array("slug" => $slug))]))
        ]);
    }


    #[Route('/articles/new', name: 'app_article_new', priority: 1)]
    public function newArticle(SluggerInterface $slug): Response
    {

        $article = new Article();
        $articleForm = $this->createForm(ArticleType::class, $article);
        return $this->renderForm("article/new.html.twig", [
            "articleForm" => $articleForm
        ]);


        /*
        $article->setTitle("My article 2")
                ->setContent("My article content v2")
                ->setSlug($slug->slug($article->getTitle())->lower())
                ->setCreatedAt(new DateTime());

        $this->articleRepository->add($article, true);
        */


    }



}
