<?php

namespace App\Controller;

use App\Model\ArticleManager;
use App\Model\CommandManager;

class AdminController extends AbstractController
{
    public function index()
    {
        $commandManager = new CommandManager();
        $articleManager = new ArticleManager();
        $articles = $articleManager->selectAll();
        $commands = $commandManager->selectAll();
        return $this->twig->render('Admin/index.html.twig', [
            'articles' => $articles,
            'commands' => $commands
        ]);
    }

    public function editArticle($id = null)
    {
        $articleManager = new ArticleManager();
        $errorForm = null;
        $article = null;
        if ($id != null){
            $article = $articleManager->selectOneById($id);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['name']) && !empty($_POST['price']) && !empty($_POST['img'])) {
                $data = [
                    'id' => $id ? $id : '',
                    'name' => $_POST['name'],
                    'price' => $_POST['price'],
                    'img' => $_POST['img']
                ];
                if (isset($_POST['id']) && !empty($_POST['id'])) {
                    $articleManager->update($data);
                    header('Location:/admin/index');
                } else {
                    $articleManager->insert($data);
                    header('Location:/admin/index');
                }
            } else {
                $errorForm = 'Tous les champs sont obligatoires.';
            }
        }
        return $this->twig->render('Admin/edit_article.html.twig', [
            'article' => $article ? $article : null,
            'errorForm' => $errorForm
        ]);
    }

    public function deleteArticle($id)
    {
        $articleManager = new ArticleManager();
        $articleManager->delete($id);
        header('Location:/admin/index');
    }

    public function deleteCommand($id)
    {
        $commandManager = new CommandManager();
        $commandManager->delete($id);
        header('Location:/admin/index');
    }
}
