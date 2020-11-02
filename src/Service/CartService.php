<?php

namespace App\Service;

use App\Model\ArticleManager;
use App\Model\CommandManager;
use Stripe\Stripe;

class CartService
{
    public function add($article)
    {
        if (!empty($_SESSION['cart'][$article])) {
            $_SESSION['cart'][$article]++;
        } else {
            $_SESSION['cart'][$article] = 1;
        }
        $_SESSION['count'] = $this->countArticles();
        header('Location:/home/index');
    }

    function delete($article)
    {
        $cart = $_SESSION['cart'];
        if (!empty($cart[$article])) {
            unset($cart[$article]);
        }
        $_SESSION['cart'] = $cart;
        header('Location:/home/cart');
    }

    function cartInfos()
    {
        if (isset($_SESSION['cart'])) {
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

    function totalCart()
    {
        $total = 0;
        foreach ($this->cartInfos() as $item) {
            $total += $item['qty'] * $item['price'];
        }
        return $total;
    }

    public function countArticles()
    {
        $total = 0;
        foreach ($this->cartInfos() as $item) {
            $total += $item['qty'];
        }
        return $total;
    }

    public function payment($infos)
    {
        $stripe = \Stripe\Stripe::setApiKey(API_KEY);

        $commandManager = new CommandManager();
        $data = [
            'name' => $infos['name'],
            'address' => $infos['address'],
            'total' => $this->totalCart(),
            'date' => date("Y-m-d")
        ];
        $commandManager->insert($data);

        try {
            $data = [
                'source' => $_POST['stripeToken'],
                'description' => $_POST['name'],
                'email' => $_POST['email']
            ];
            $customer = \Stripe\Customer::create($data);

            $charge = \Stripe\Charge::create([
                'amount' => $this->totalCart(),
                'currency' => 'eur',
                'description' => 'Example charge',
                'customer' => $customer->id,
                'statement_descriptor' => 'Custom descriptor',
            ]);

            unset($_SESSION['cart']);
            unset($_SESSION['count']);
            $_SESSION['transaction'] = $charge->receipt_url;

            $sender = 'jenny.test4php@gmail.com';
            $recipient = 'jenny.viannay75@gmail.com';

            $subject = "Commande confirmée";
            $message = "Félicitation, vous recevrez votre commande dans un délai de 48h !";
            $headers = 'From:' . $sender;

            $isSend = mail($recipient, $subject, $message, $headers);
            if (!$isSend) {
                var_dump("Error: Message not accepted"); die;
            }

            header('Location:/home/success');
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $e->getError();
        }
    }
}
