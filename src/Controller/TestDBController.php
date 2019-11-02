<?php

namespace App\Controller;

// Default
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// Doctrine ORM
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

// HTTP Foundation
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

// Entities
use App\Entity\TestEntity;

// Exceptions
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use PDOException;
use Exception;

class TestDBController extends AbstractController
{

    /**
     * @Route("/test_db", name="test_d_b")
     */
    public function index(Request $request) {

        $request = Request::createFromGlobals();

        if ( $request->isXmlHttpRequest() ) {

            if ( ( null !== $request->request->get('role')) & ( null !== $request->request->get('action') ) & ( null !== $request->request->get('entityId') ) ) {

                // return new JsonResponse(array('data' => $request->request->get('queryStr')));
                // return new JsonResponse(array('data' => 'Query is right'));
                // return new JsonResponse(array('data' => $request->request->all()));

                $role = $request->request->get('role');
                $action = $request->request->get('action');
                $entityId = $request->request->get('entityId');
                $response = ''; // Return to user, used for handle exceptions

                if ( ( $role !== '' ) & ( $role !== 'undefined' ) ) {

                    $query = "
                        SET ROLE '".$role."';
                    ";

                    $entityManager = $this->getDoctrine()->getManager();
                    $stmt = $entityManager->getConnection()->prepare($query);
                    $stmt->execute();

                    if ( ( $entityId !== '' ) & ( $entityId !== 'undefined' ) ) {

                        if ( $action == 'delete' ) {

                            $response = $this->deleteTestEntity($role, $entityId);

                            return new JsonResponse(array('data' => "$response"));

                        } elseif ( $action == 'add' ) {

                            $response = $this->setTestEntities($role);

                            return new JsonResponse(array('data' => "$response"));

                        } elseif ( $action == 'update' ) {

                            // setTestEntities();

                        } else {

                            return new JsonResponse(array('data' => 'Action isn\'t defined'));

                        }

                    } else {

                        return new JsonResponse(array('data' => 'Entity Id isn\'t defined'));

                    }

                } else {

                    return new JsonResponse(array('data' => 'Role isn\'t defined'));

                }


            } else {

                return new JsonResponse(array('data' => 'Query isn\'t right'));

            }

        } else {

            // Set some entities
            // $this->setTestEntities();

            return $this->render('test_db/index.html.twig', [
                'page_name' => ' - database functions test',
                'db_roles' => $this->selectRoles(),
                'entities' => $this->displayTestEntities(),
            ]);

        }

    }

    public function selectRoles() {

        $query = "
            SELECT rolname
            FROM pg_roles
            WHERE rolname NOT LIKE 'pg%'
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

    public function setTestEntities($role) {

        $entityManager = $this->getDoctrine()->getManager();

        $testEntity = new TestEntity();
        $testEntity->setDescription('Description for one more Test object');

        try {
            $entityManager->persist($testEntity);
            $entityManager->flush();
            $message = 'For user "'.$role.'" was add one more entity';
        } catch (\DBALException $e) {
            // $message = sprintf('DBALException [%i]: %s', $e->getCode(), $e->getMessage());
            $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        } catch (\PDOException $e) {
            // $message = sprintf('PDOException [%i]: %s', $e->getCode(), $e->getMessage());
            $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        } catch (\ORMException $e) {
            // $message = sprintf('ORMException [%i]: %s', $e->getCode(), $e->getMessage());
            $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        } catch (\Exception $e) {
            // $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage());
            $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        }

        return $message;

    }

    public function displayTestEntities() {

        $repository = $this->getDoctrine()->getRepository(TestEntity::class);

        $testEntities = $repository->findAll();

        return $testEntities;

    }

    public function deleteTestEntity($role, $id) {

        $entityManager = $this->getDoctrine()->getManager();

        try {
            $testEntity = $entityManager->getRepository(TestEntity::class)->find($id);
            $entityManager->remove($testEntity);
            $entityManager->flush();
            $message = 'For user "'.$role.'" was delete entity #'.$id;
        } catch (\DBALException $e) {
            // $message = sprintf('DBALException [%i]: %s', $e->getCode(), $e->getMessage());
            $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        } catch (\PDOException $e) {
            // $message = sprintf('PDOException [%i]: %s', $e->getCode(), $e->getMessage());
            $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        } catch (\ORMException $e) {
            // $message = sprintf('ORMException [%i]: %s', $e->getCode(), $e->getMessage());
            $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        } catch (\Exception $e) {
            // $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage());
            $message = sprintf('Возникла внутренняя ошибка. Пожалуйста, обратитесь к системному администратору.');
        }

        return $message;

    }

}
