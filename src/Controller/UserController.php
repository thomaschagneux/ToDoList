<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    #[Route('/users', name: 'user_list')]
    public function userList(): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_LIST);
        $users = $this->userRepository->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/create', name: 'user_create')]
    public function userCreate(Request $request): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_CREATE);
        $user = new User();
        $userCreateForm = $this->createForm(UserType::class, $user);
        $userCreateForm->handleRequest($request);

        if ($userCreateForm->isSubmitted() && $userCreateForm->isValid()) {
            /** @var string $password */
            $password = $user->getPassword();
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'user_create_form' => $userCreateForm->createView(),
        ]);
    }

    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function userEdit(User $user, Request $request): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_EDIT, $user);

        $userEditForm = $this->createForm(UserType::class, $user);
        $userEditForm->handleRequest($request);

        if ($userEditForm->isSubmitted() && $userEditForm->isValid()) {
            /** @var string $password */
            $password = $user->getPassword();

            $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
            $this->entityManager->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'user_edit_form' => $userEditForm->createView(),
        ]);
    }
}
