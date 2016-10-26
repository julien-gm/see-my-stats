<?php

namespace StatsBundle\Service;

use Doctrine\ORM\EntityManager;
use StatsBundle\Entity\User;

class UserManager
{

    /** @var EntityManager $em */
    private $em;


    /**
     * UserManager constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $userName
     * @param $email
     * @param $password
     * @throws \Exception
     */
    public function createAdmin($userName, $email, $password)
    {
        if (strlen($password) < 3) {
            throw new \Exception('Password should be at least 3 characters long.');
        }
        $user = new User();
        $user->setUsername($userName);
        $user->setPlainPassword($password);
        $user->setEmail($email);
        $user->setEnabled(true);
        $user->setRoles(['ROLE_ADMIN']);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param string $username
     * @return User
     */
    public function getUserByUsername($username)
    {
        return $this->em->getRepository('StatsBundle:User')->findOneBy(['username' => $username]);
    }

    /**
     * @param User $user
     * @param array $roles
     */
    public function updateUserRoles(User $user, array $roles)
    {
        $user->setRoles($roles);
        $this->em->persist($user);
        $this->em->flush();
    }

}
