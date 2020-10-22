<?php

namespace App\Controller;

use App\Model\UserManager;

class SecurityController extends AbstractController
{
    public function login()
    {
        $userManager = new UserManager();
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $user = $userManager->search($_POST['email']);
                if ($user) {
                    if ($user->password === md5($_POST['password'])) {
                        $_SESSION['user_id'] = $user->id;
                        $_SESSION['username'] = $user->username;
                        header('Location:/admin/index');
                    } else {
                        $error = 'Password incorrect !';
                    }
                } else {
                    $error = 'User not found';
                }
            } else {
                $error = 'Tous les champs sont obligatoires !';
            }
        }
        return $this->twig->render('Admin/login.html.twig', [
            'error' => $error
        ]);
    }

    public function register()
    {
        $userManager = new UserManager();
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['email']) &&
                !empty($_POST['username']) &&
                !empty($_POST['password']) &&
                !empty($_POST['password2'])) {
                $user = $userManager->search($_POST['email']);
                if ($user) {
                    $error = 'Email already exist';
                }
                if ($_POST['password'] != $_POST['password2']) {
                    $error = 'Password do not match';
                }
                if ($error === null) {
                    $payload = [
                        'email' => $_POST['email'],
                        'username' => $_POST['username'],
                        'password' => md5($_POST['password'])
                    ];
                    $idUser = $userManager->insert($payload);
                    if ($idUser) {
                        $_SESSION['user_id'] = $idUser;
                        $_SESSION['username'] = $payload['username'];
                        header('Location:/admin/index');
                    }
                }
            }
        }
        return $this->twig->render('Admin/register.html.twig', [
            'error' => $error
        ]);
    }

    public function logout()
    {
        session_destroy();
        header('Location:/home/index');
    }
}
