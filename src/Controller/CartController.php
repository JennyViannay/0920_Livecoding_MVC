<?php

namespace App\Controller;

use App\Model\ArticleManager;
use App\Model\CommandManager;

class CartController extends AbstractController
{
    public function addArticle($article)
    {
        if (!empty($_SESSION['cart'][$article])) {
            $_SESSION['cart'][$article]++;
        } else {
            $_SESSION['cart'][$article] = 1;
        }
        $_SESSION['count'] = $this->countArticles();
        header('Location:/home/index');
    }

    function deleteArticle($article)
    {
        $cart = $_SESSION['cart'];
        if (!empty($cart[$article])) {
            unset($cart[$article]);
        }
        $_SESSION['cart'] = $cart;
        header('Location:/home/cart');
    }

    function getCartInfos()
    {
        if(isset($_SESSION['cart'])){
            $cart = $_SESSION['cart'];
            $cartInfos = [];
            $articleManager = new ArticleManager();
            foreach ($cart as $article => $qty) {
                $infosArticle = $articleManager->selectOneById(intval($article));
                $infosArticle['qty'] = $qty;
                $cartInfos[] = $infosArticle;
            }
            return $cartInfos;
        } else {
            return false;
        }
    }

    function getTotalCart()
    {
        $total = 0;
        foreach ($this->getCartInfos() as $item) {
            $total += $item['qty'] * $item['price'];
        }
        return $total;
    }

    public function countArticles()
    {
        $total = 0;
        foreach ($this->getCartInfos() as $item) {
            $total += $item['qty'];
        }
        return $total;
    }

    public function payment($infos)
    {
        $commandManager = new CommandManager();
        $data = [
            'name' => $infos['name'],
            'address' => $infos['address'],
            'total' => $this->getTotalCart(),
            'date' => date("Y-m-d")
        ];
        $commandManager->insert($data);
        session_destroy();
        return header('Location:/home/success');
    }
}
