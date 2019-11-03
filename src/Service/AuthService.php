<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Logger service
use Psr\Log\LoggerInterface;

// HTTP Foundation
use Symfony\Component\HttpFoundation\JsonRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;

// Redirecting
use Symfony\Component\HttpFoundation\RedirectResponse;

// Doctrine ORM
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

// DB environment
use PDO;
use Doctrine\ORM;
use Doctrine\DBAL;

class AuthService extends AbstractController
{

    /*/////////////////////--------------------
      User identification and authentication
    --------------------/////////////////////*/

    // Index function
    public function index($request) {

        $action = $request->request->get('action');
        if ( $action === 'sendPin' ) {
            $role = $request->request->get('role');
            return $this->sendPin($role);
        } else if ( $action === 'login' ) {
            $role = $request->request->get('role');
            $pin = $request->request->get('pin');
            return $this->checkPin($role,$pin);
        }

    }

    // Select agent roles from DB
    public function selectRoles() {

        // Sumple query
        $query = "
            SELECT rolname
            FROM pg_roles
            WHERE rolname NOT LIKE 'pg%'
        ";

        // Main query
        // $query = "
        //     SELECT *
        //     FROM pg_catalog.pg_roles AS childs
        //     INNER JOIN pg_catalog.pg_auth_members ON childs.oid = pg_auth_members.member
        //         INNER JOIN pg_catalog.pg_roles AS parents ON parents.oid = pg_auth_members.roleid
        //     WHERE parents.rolname = 'agent_group'
        //     ORDER BY childs.rolname
        // ";

        $entityManager = $this->getDoctrine()->getManager();
        $stmt = $entityManager->getConnection()->prepare($query);
        $stmt->execute();

        $list = $stmt->fetchAll();

        $rolesList = array();

        foreach ($list as $key => $value) {
            foreach ($value as $role => $roleName) {
                $rolesList[] = $roleName;
            };
        };

        return $rolesList;

    }

    // Send message to user
    public function message($messageTitle, $messageBody, $messageType) {

        $message = [
            'title' => $messageTitle,
            'body' => $messageBody,
            'type' => $messageType
        ];

        return new JsonResponse(array('title' => $message['title'], 'body' => $message['body'], 'type' => $message['type']));

    }

    // Send email with pin-code
    private function sendPin($agentRole) {

        if ( $this->checkRole($agentRole) ) {

            // Call create_new_temp_password() that create and send pincode
            // $query = "
            //     SELECT staff.create_new_temp_password(:agentRole)
            // ";
            // $entityManager = $this->getDoctrine()->getManager();
            // $stmt = $entityManager->getConnection()->prepare($query);
            // $stmt->bindValue('agentRole', $agentRole);
            // $stmt->execute();
            //
            // $list = $stmt->fetchAll();

            return $this->message('', 'Пин-код был отправлен на электронную почту.', 'warning');

        } else {
            return $this->message('Ошибка!', 'Пожалуйста, выберите логин из списка.', 'modal');
        }

    }

    // Check user pin-code
    private function checkPin($agentRole,$pin) {

        if ( $this->checkRole($agentRole) ) {

            // Call create_new_temp_password() that create and send pincode
            // $query = "
            //     SELECT staff.check_temp_password(:agentRole,:pin)
            // ";
            // $entityManager = $this->getDoctrine()->getManager();
            // $stmt = $entityManager->getConnection()->prepare($query);
            // $stmt->bindValue('agentRole', $agentRole);
            // $stmt->bindValue('pin', $pin);
            // $stmt->execute();
            //
            // $list = $stmt->fetchAll();

            // If user input true pin, redirect to '/user'
            // if ( $list !== null ) {
            //     return this->getAuth($agentRole,$list);
            //     //return $this->redirectToRoute('user');
            // } else {
            //     return $this->message('Ошибка!','Вы ввели неверный пин. Попробуйте ещё раз.', 'modal');
            // }

            return $this->message('Warning!','You are trying login!', 'modal');

        } else {
            return $this->message('Ошибка!', 'Пожалуйста, выберите логин из списка.', 'modal');
        }

    }

    // Check agent role, send by user
    public function checkRole($agentRole) {
        $roles = $this->selectRoles();
        foreach ($roles as $key => $value) {
            if ( $value === $agentRole ) {
                return true;
            }
        }
        return false;
    }

    /*/////////////////////--------------------
                User authorization
    --------------------/////////////////////*/

    // Gives user access to system
    public function getAuth($agentRole,$agentPassword) {

        $time = $this->getCommonConfig();
        $random = random_bytes(10);

        $session = new Session();
        $session->start();
        $session->set('user', $agentRole);
        $session->set('pass', $agentPassword);

        $response = $this->forward('App\Controller\UserController::index', [
            'controller_name' => 'UserController',
            'debug_info' => 'Session ID:'.$session->getId()
        ]);
        $response->headers->setCookie(new Cookie('token', $session->getId()));
        return $response;

    }

    // Get current config (for example, time for sessions & cookies)
    public function getCommonConfig() {

        // $query = "
        //     SELECT attribute_value
        //     FROM common.config
        //     WHERE attribute_name = 'agent_role_lifetime'
        // ";
        // $entityManager = $this->getDoctrine()->getManager();
        // $stmt = $entityManager->getConnection()->prepare($query);
        // $stmt->execute();
        //
        // $list = preg_replace("/[^-0-9]+/", '', $stmt->fetchAll());
        //
        // return $list;

    }

}
