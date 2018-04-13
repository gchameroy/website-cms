<?php

namespace AppBundle\Manager;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function get(int $id, bool $withCheck = true): User
    {
        /** @var $user User */
        $user = $this->userRepository->find($id);
        if ($withCheck === true) {
            $this->checkUser($user);
        }

        return $user;
    }

    public function getList(): array
    {
        return $this->userRepository->findAll();
    }

    public function getNew(): User
    {
        return new User();
    }

    public function save(User $user): User
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function remove(User $user): void
    {
        if (!$user) {
            return;
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    private function checkUser(?User $user): void
    {
        if (!$user) {
            throw new NotFoundHttpException('User Not Found.');
        }
    }
}
