<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// HTTP Foundation
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

// Authentication
use App\Service\AuthService;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(AuthService $authService, Request $request)
    {

        $request = Request::createFromGlobals();

        if ( !$request->isXmlHttpRequest() ) {
            // If client has cookies (he is authenticated), redirect him to user page
            if ( $authService->validateUser($request) ) {
                return $this->render('user/index.html.twig', [
                    'controller_name' => 'UserController',
                    'debug_info' => $authService->sessionInfo(),
                    'request_path' => $request->getPathInfo(),
                    'username' => $authService->userInfo(),
                    'request_parameters' => $request->query->all(),
                    'request_all' => dump($request),
                    'custom_debug_field' => $authService->usefullConfig(),
                ]);
            } else {
                return $this->redirectToRoute('login');
            }
        } else {
            // If testing client DB connection
            if ( $request->request->get('type') == 'testDBConn' ) {
                return $authService->getUserDBConn();
            } else {
                die();
            }
        }
    }
}
