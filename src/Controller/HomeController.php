<?php

namespace App\Controller;

use App\Model\ArticleManager;
use App\Controller\CartController;

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
        $cartController = new CartController();
        $articleManager = new ArticleManager();
        $articles = $articleManager->selectAll();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (!empty($_POST['search'])) {
                $articles = $articleManager->searchArticles($_POST['search']);
            }
            if (!empty($_POST['add_article'])) {
                $article = $_POST['add_article'];
                $cartController->addArticle($article);
            }
        }
        return $this->twig->render('Home/index.html.twig', [
            'articles' => $articles
        ]);
    }

    public function showArticle($id)
    {
        $cartController = new CartController();
        $articleManager = new ArticleManager();
        $article = $articleManager->selectOneById($id);
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (!empty($_POST['add_article'])) {
                $article = $_POST['add_article'];
                $cartController->addArticle($article);
            }
        }
        return $this->twig->render('Home/show_article.html.twig', ['article' => $article]);
    }

    public function cart()
    {
        $cartController = new CartController();
        $errorForm = null;
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['delete_id'])) {
                $article = $_POST['delete_id'];
                $cartController->deleteArticle($article);
            }
            if (isset($_POST['payment'])) {
                if (!empty($_POST['name']) && !empty($_POST['address'])) {
                    $cartController->payment($_POST);
                } else {
                    $errorForm = "Tous les champs sont obligatoires !";
                }
            }
        }
        return $this->twig->render('Home/cart.html.twig', [
            'cartInfos' => $cartController->getCartInfos() ? $cartController->getCartInfos() : null,
            'total' => $cartController->getCartInfos() ? $cartController->getTotalCart() : null,
            'errorForm' => $errorForm
        ]);
    }

    public function success()
    {
        return $this->twig->render('Home/success.html.twig');
    }
}
