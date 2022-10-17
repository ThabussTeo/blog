<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use Symfony\Component\Form\FormError;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{

    private ArticleRepository $articleRepository;
    private CommentRepository $commentRepository;
    private UserRepository $userRepository;

    public function __construct(ArticleRepository $articleRepository, CommentRepository $commentRepository, UserRepository $userRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/articles', name: 'app_articles')]
    public function articles(PaginatorInterface $paginator, Request $request): Response
    {
        $articles = $paginator->paginate(
            $this->articleRepository->findBy(["isPublished" => true], ['createdAt' => 'DESC']),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('article/articles.html.twig', [
            "articles" => $articles
        ]);
    }

    #[Route('/articles/{slug}', name: 'app_article')]
    public function article($slug, Request $request): Response
    {

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $username = $commentForm->get("username")->getData();
            $user = $this->userRepository->findOneBy(["username" => $username]);
            $comment->setCreatedAt(new \DateTimeImmutable())
                    ->setArticle($this->articleRepository->findOneBy(["slug" => $slug]));

            if (!empty($username) && !empty($user)) {
                $comment->setUser($user);
                $this->commentRepository->add($comment, true);
            } else if (empty($username)) {
                $this->commentRepository->add($comment, true);
            }
            else {
                $commentForm->get('username')->addError(new FormError('The username does not exist.'));

            }
        }
        return $this->renderForm('article/article.html.twig', [
            "article" => $this->articleRepository->findOneBy(["slug" => $slug]),
            "comments" => $this->commentRepository->findBy(["article" => $this->articleRepository->findOneBy(["slug" => $slug])]),
            "commentForm" => $commentForm
        ]);
    }


    #[Route('/articles/new', name: 'app_article_new',  methods: ["GET", "POST"], priority: 1)]
    public function newArticle(SluggerInterface $slug, Request $request): Response
    {

        $article = new Article();
        $articleForm = $this->createForm(ArticleType::class, $article);

        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            $article->setSlug($slug->slug($article->getTitle())->lower())
                    ->setCreatedAt(new \DateTime());

            $this->articleRepository->add($article, true);
            return $this->redirectToRoute("app_articles");

        }

        return $this->renderForm("article/newArticle.html.twig", [
            "articleForm" => $articleForm
        ]);
    }

    #[Route('/articles/edit/{slug}', name: 'app_article_edit',  methods: ["GET", "POST"], priority: 1)]
    public function editArticle(SluggerInterface $slugger, Request $request, $slug): Response
    {

        $article = $this->articleRepository->findOneBy(["slug" => $slug]);
        $articleForm = $this->createForm(ArticleType::class, $article);

        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            $article->setSlug($slugger->slug($article->getTitle())->lower());

            $this->articleRepository->add($article, true);
            return $this->redirectToRoute("app_articles");

        }

        return $this->renderForm("article/editArticle.html.twig", [
            "articleForm" => $articleForm,
            "article" => $article
        ]);
    }






}
