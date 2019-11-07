<?php

namespace App\Service;

// Default
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Logger service
use Psr\Log\LoggerInterface;

// HTTP Foundation
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Cookie;

// Redirecting
use Symfony\Component\HttpFoundation\RedirectResponse;

// Doctrine ORM
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

// PHP functions
use function json_encode;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function debugDumpParams;

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
            $role = $this->clearJSONstring($request->request->get('role'));
            return $this->sendPin($role);
        } else if ( $action === 'login' ) {
            $role = $this->clearJSONstring($request->request->get('role'));
            $pin = $this->clearJSONstring($request->request->get('pin'));
            return $this->checkPin($role,$pin);
        }

    }

    // Select agent roles from DB
    public function selectRoles() {

        // Main query
        $query = "
            SELECT childs.rolname
            FROM pg_catalog.pg_roles AS childs
            INNER JOIN pg_catalog.pg_auth_members ON childs.oid = pg_auth_members.member
                INNER JOIN pg_catalog.pg_roles AS parents ON parents.oid = pg_auth_members.roleid
            WHERE parents.rolname = 'agent_group'
            ORDER BY childs.rolname
        ";

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
            $query = "
                SELECT staff.create_new_temp_password(:agentRole)
            ";
            $entityManager = $this->getDoctrine()->getManager();
            $stmt = $entityManager->getConnection()->prepare($query);
            $stmt->bindValue('agentRole', $agentRole);
            $stmt->execute();

            $list = $stmt->fetchAll();

            return $this->message('', 'Пин-код был отправлен на электронную почту.', 'warning');

        } else {
            return $this->message('Ошибка!', 'Пожалуйста, выберите логин из списка.', 'modal');
        }

    }

    // Check user pin-code
    private function checkPin($agentRole,$pin) {

        if ( $this->checkRole($agentRole) ) {

            // Call create_new_temp_password() that create and send pincode
            $query = "
                SELECT staff.check_temp_password(:agentRole,:pin)
            ";
            $entityManager = $this->getDoctrine()->getManager();
            $stmt = $entityManager->getConnection()->prepare($query);
            $stmt->bindValue('agentRole', $agentRole);
            $stmt->bindValue('pin', $pin);
            $stmt->execute();

            $list = $stmt->fetch();

            // If user input true pin, authorizing him
            if ( $list['check_temp_password'] == null ) {
                return $this->message('Ошибка!','Вы ввели неверный пин. Попробуйте ещё раз.', 'modal');
            } else {
                return $this->getAuth($agentRole,$list['check_temp_password']);
            }

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
    public function getAuth($agentRole, $agentPassword) {

        // Common client session time from DB config
        $time = $this->getCommonConfig() * 3600;

        // Create new session storage with custom liftime parameter
        $sessionStorage = new NativeSessionStorage(array("gc_maxlifetime" => $time));
        $session = new Session($sessionStorage);

        // Revalidate session if it already started
        if ( $session->isStarted() ) {
            $session->invalidate();
        }

        $session->start();

        // Set session parameters for DB connection
        $session->set('username', $agentRole);
        $session->set('password', $agentPassword);

        $response = new JsonResponse();

        // New cookie with custo lifetime
        $cookie = new Cookie(
            'token',    // Cookie name
            $session->getId(),    // Cookie value
            time() + $time,  // Expires common time in hours
            '/', // Site path
            null, // Domain name
            true, // Transmisson only throw HTTPS
            true // HTTP access only
        );

        // Return response with cookie
        $response->headers->setCookie( $cookie );
        $response->setData(array('signin' => true));
        return $response;

    }

    // Get current config (for example, time for sessions & cookies)
    public function getCommonConfig() {

        // Get common session time from DB
        $query = "
            SELECT attribute_value
            FROM common.config
            WHERE attribute_name = 'agent_role_lifetime'
        ";
        $entityManager = $this->getDoctrine()->getManager();
        $stmt = $entityManager->getConnection()->prepare($query);
        $stmt->execute();

        $list = preg_replace("/[^-0-9]+/", '', $stmt->fetch());

        return $list['attribute_value'];

    }

    // Check user cookies
    public function validateUser(Request $request) {
        // If set 'token', then gives user access to system
        if ( $request->cookies->has('token') ) {
            return true;
        } else {
            return false;
        }
    }

    // Check user connection to DB
    public function validateUserDBConn($username, $password) {

        //  ДОБАВИТЬ ВАЛИДАЦИЮ ПОДКЛЮЧЕНИЯ К БД!!!!!

    }

    /*/////////////////////--------------------
                   User logout
    --------------------/////////////////////*/

    // Logout
    public function userLogout(Request $request) {
        $session = new Session();
        $session->invalidate();
        // $request->cookies->remove('token');
        // $request->cookies->remove('PHPSESSID');
        $response = new JsonResponse();
        $response->headers->clearCookie('token');
        $response->headers->clearCookie('PHPSESSID');
        return $response;

    }

    /*/////////////////////--------------------
                   User information
    --------------------/////////////////////*/

    // Session username (returns login)
    public function userInfo() {
        $session = new Session();
        $username = $session->get('username');
        return $username;
    }

    // Username & password from session (temporary debug function)
    public function sessionInfo() {
        $session = new Session();
        $username = $session->get('username');
        $password = $session->get('password');
        $SESSID = $session->getId();
        $debugInfo = "Session (ID: $SESSID): ";
        $debugInfo .= "Username: ".$username." / DB Password: ".$password;
        return $debugInfo;
    }

    /*/////////////////////--------------------
                    DB functions
    --------------------/////////////////////*/

    // Return new PDO
    public function getUserDBConn() {

        $session = new Session();
        $username = $session->get('username');
        $password = $session->get('password');

        try {
            $dbh = new PDO('pgsql:host=localhost;port=8585;dbname=alfa', $username, $password, array(
                PDO::ATTR_PERSISTENT => true
            ));

            $config = new \Doctrine\DBAL\Configuration();
            $connectionParams = array(
                'dbname' => 'alfa',
                'user' => $username,
                'password' => $password,
                'host' => 'localhost',
                'port' => '8585',
                'pdo' => $dbh,
            );

            $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
            $message = implode('Success!\n',var_dump($conn));
        } catch (\DBALException $e) {
            $message = sprintf('DBALException [%i]: %s', $e->getCode(), $e->getMessage());
            // $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        } catch (\PDOException $e) {
            $message = sprintf('PDOException [%i]: %s', $e->getCode(), $e->getMessage());
            // $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        } catch (\ORMException $e) {
            $message = sprintf('ORMException [%i]: %s', $e->getCode(), $e->getMessage());
            // $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        } catch (\Exception $e) {
            $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage());
            // $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        }

        return $this->message('Debug info from getUserDBConn', $message, 'modal');

    }

    /*/////////////////////--------------------
                  Service functions
    --------------------/////////////////////*/

    // Clear JSON string from special characters
    public function clearJSONstring($str) {
        return json_decode(json_encode($str), true);
    }


}
