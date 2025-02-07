<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1')]
class TestController extends AbstractController
{
    private function initializeUsers(SessionInterface $session): void
    {
        if (!$session->has('users')) {
            $session->set('users', [
                ['id' => '1', 'email' => 'ipz231_shvye@student.ztu.edu.ua', 'name' => 'Vladyslav'],
                ['id' => '2', 'email' => 'ipz231_shvye2@student.ztu.edu.ua', 'name' => 'Vladyslav2'],
                ['id' => '3', 'email' => 'ipz231_shvye3@student.ztu.edu.ua', 'name' => 'Vladyslav3'],
                ['id' => '4', 'email' => 'ipz231_shvye4@student.ztu.edu.ua', 'name' => 'Vladyslav4'],
                ['id' => '5', 'email' => 'ipz231_shvye5@student.ztu.edu.ua', 'name' => 'Vladyslav5'],
                ['id' => '6', 'email' => 'ipz231_shvye6@student.ztu.edu.ua', 'name' => 'Vladyslav6'],
                ['id' => '7', 'email' => 'ipz231_shvye7@student.ztu.edu.ua', 'name' => 'Vladyslav7'],
            ]);
        }
    }

    #[Route('/users', name: 'app_collection_users', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getCollection(SessionInterface $session): JsonResponse
    {
        $this->initializeUsers($session);
        return new JsonResponse(['data' => $session->get('users')], Response::HTTP_OK);
    }

    #[Route('/users/{id}', name: 'app_item_users', methods: ['GET'])]
    public function getItem(string $id, SessionInterface $session): JsonResponse
    {
        $this->initializeUsers($session);
        $userData = $this->findUserById($id, $session);
        return new JsonResponse(['data' => $userData], Response::HTTP_OK);
    }

    #[Route('/users', name: 'app_create_users', methods: ['POST'])]
    public function createItem(Request $request, SessionInterface $session): JsonResponse
    {
        $this->initializeUsers($session);
        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['email'], $requestData['name'])) {
            throw new UnprocessableEntityHttpException("name and email are required");
        }

        $users = $session->get('users');
        $newId = count($users) + 1;
        $newUser = [
            'id'    => (string) $newId,
            'name'  => $requestData['name'],
            'email' => $requestData['email']
        ];

        $users[] = $newUser;
        $session->set('users', $users);

        return new JsonResponse(['data' => $newUser], Response::HTTP_CREATED);
    }

    #[Route('/users/{id}', name: 'app_delete_users', methods: ['DELETE'])]
    public function deleteItem(string $id, SessionInterface $session): JsonResponse
    {
        $this->initializeUsers($session);
        $users = $session->get('users');

        foreach ($users as $index => $user) {
            if ($user['id'] === $id) {
                array_splice($users, $index, 1);
                $session->set('users', $users);
                return new JsonResponse([], Response::HTTP_NO_CONTENT);
            }
        }

        throw new NotFoundHttpException("User with id $id not found");
    }

    #[Route('/users/{id}', name: 'app_update_users', methods: ['PATCH'])]
    public function updateItem(string $id, Request $request, SessionInterface $session): JsonResponse
    {
        $this->initializeUsers($session);
        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['name'])) {
            throw new UnprocessableEntityHttpException("name is required");
        }
        if (!isset($requestData['email'])) {
            throw new UnprocessableEntityHttpException("email is required");
        }

        $users = $session->get('users');
        foreach ($users as &$user) {
            if ($user['id'] === $id) {
                $user['name'] = $requestData['name'];
                $user['email'] = $requestData['email'];
                $session->set('users', $users);
                return new JsonResponse(['data' => $user], Response::HTTP_OK);
            }
        }

        throw new NotFoundHttpException("User with id $id not found");
    }

    private function findUserById(string $id, SessionInterface $session): array
    {
        $this->initializeUsers($session);
        $users = $session->get('users');

        foreach ($users as $user) {
            if ($user['id'] === $id) {
                return $user;
            }
        }
        throw new NotFoundHttpException("User with id $id not found");
    }
}