<?php

namespace App\Controller;

use App\Model\ArticleManager;

class ApiController extends AbstractController
{
    public function articles()
    {
        $articleManager = new ArticleManager();
        $articles = $articleManager->selectAll();
        return json_encode($articles);
    }
}
