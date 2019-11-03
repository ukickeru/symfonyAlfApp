<?php

namespace App\Controller;

// Default
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// HTTP Foundation
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Authentication
use App\Service\AuthService;

class IndexController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function index(AuthService $authService, Request $request)
    {

        // return $this->redirectToRoute('test_d_b');

        $request = Request::createFromGlobals();

        if ( !$request->isXmlHttpRequest() ) {
            // If common request
            return $this->render('index/index.html.twig', [
                'page_name' => ' - авторизация',
                // 'message' => $authService->checkRole('postgres'),
                'message' => '',
                'db_roles' => $authService->selectRoles(),
            ]);
        } else {
            // If client want auth, use AuthService functions
            if ( $request->request->get('type') === 'auth' ) {
                $message = $authService->index($request);
                return $message;
            }
        }

    }

}
