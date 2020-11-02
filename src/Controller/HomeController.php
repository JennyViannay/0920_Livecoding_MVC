<?php

namespace App\Controller;

use App\Model\ArticleManager;
use App\Service\CartService;

class HomeController extends AbstractController
{

    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $cartService = new CartService();
        $articleManager = new ArticleManager();
        $articles = $articleManager->selectAll();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (!empty($_POST['search'])) {
                $articles = $articleManager->search($_POST['search']);
            }
            if (!empty($_POST['add_article'])) {
                $article = $_POST['add_article'];
                $cartService->add($article);
            }
        }
        return $this->twig->render('Home/index.html.twig', [
            'articles' => $articles
        ]);
    }

    public function showArticle($id)
    {
        $cartService = new CartService();
        $articleManager = new ArticleManager();
        $article = $articleManager->selectOneById($id);
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (!empty($_POST['add_article'])) {
                $article = $_POST['add_article'];
                $cartService->add($article);
            }
        }
        return $this->twig->render('Home/show_article.html.twig', ['article' => $article]);
    }

    public function cart()
    {
        $cartService = new CartService();
        $errorForm = null;
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['delete_id'])) {
                $article = $_POST['delete_id'];
                $cartService->delete($article);
            }
            if (isset($_POST['payment'])) {
                if (!empty($_POST['name']) && !empty($_POST['address'])) {
                    $cartService->payment($_POST);
                } else {
                    $errorForm = "Tous les champs sont obligatoires !";
                }
            }
        }
        return $this->twig->render('Home/cart.html.twig', [
            'cartInfos' => $cartService->cartInfos() ? $cartService->cartInfos() : null,
            'total' => $cartService->cartInfos() ? $cartService->totalCart() : null,
            'errorForm' => $errorForm
        ]);
    }

    public function success()
    {
        return $this->twig->render('Home/success.html.twig');
    }
}
