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

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function index(AuthService $authService, Request $request)
    {

        $request = Request::createFromGlobals();

        if ( !$request->isXmlHttpRequest() ) {
            // If client has cookies (he is authenticated), redirect him to user page
            if ( $authService->validateUser($request) ) {
                return $this->redirectToRoute('index');
            } else {
                // If common request
                return $this->render('login/index.html.twig', [
                    'page_name' => ' - авторизация',
                    // 'message' => $authService->validateUser($request),
                    'message' => '',
                    'db_roles' => $authService->selectRoles(),
                ]);
            }
        } else {
            // If client want auth, use AuthService functions
            if ( $request->request->get('type') === 'auth' ) {
                $message = $authService->index($request);
                return $message;
            } else {
                // If AJAX query without terminated parameters
                die();
            }
        }

    }
}
