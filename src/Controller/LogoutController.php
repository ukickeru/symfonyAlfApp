<?php

namespace App\Controller;

// Default
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// HTTP Foundation
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

// Authentication
use App\Service\AuthService;

class LogoutController extends AbstractController
{
    /**
     * @Route("/logout", name="logout")
     */
    public function index(AuthService $authService, Request $request)
    {

        $request = Request::createFromGlobals();

        if ( !$request->isXmlHttpRequest() ) {
            // If client has cookies (he is authenticated), redirect him to user page
            return $this->redirectToRoute('login');
        } else {
            // If client want auth, use AuthService functions
            return $authService->userLogout($request);
            // return $this->redirectToRoute('login');
        }
    }
}
