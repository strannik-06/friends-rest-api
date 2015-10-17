<?php

namespace AppBundle\Service;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry as MongoDB;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Document\User as UserDocument;

/**
 * User Service
 */
class User
{
    /**
     * @var MongoDB
     */
    protected $mongoDB;

    /**
     * @var DocumentManager
     */
    protected $manager;

    /**
     * @var array
     */
    private $friendsFriends = array();

    /**
     * @param MongoDB         $mongoDB
     * @param DocumentManager $manager
     */
    public function __construct(MongoDB $mongoDB, DocumentManager $manager)
    {
        $this->mongoDB = $mongoDB;
        $this->manager = $manager;
    }

    /**
     * @param $email
     *
     * @return UserDocument|null
     * @throws NotFoundHttpException
     */
    public function getUserByEmail($email)
    {
        $user = $this->mongoDB->getRepository('AppBundle:User')->findOneByEmail($email);
        if (!$user) {
            throw new NotFoundHttpException('No user found for email '.$email);
        }

        return $user;
    }

    /**
     * @param UserDocument $user
     * @param UserDocument $request
     */
    public function addRequest($user, $request)
    {
        $user->addRequest($request);

        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * @param UserDocument $user
     * @param UserDocument $request
     */
    public function confirmRequest($user, $request)
    {
        $user->removeRequest($request);
        $user->addFriend($request);
        $request->addFriend($user);

        $this->manager->persist($user);
        $this->manager->persist($request);
        $this->manager->flush();
    }

    /**
     * @param UserDocument $user
     * @param UserDocument $request
     */
    public function rejectRequest($user, $request)
    {
        $user->removeRequest($request);

        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * @param UserDocument $friend
     * @param integer      $n
     *
     * @return array
     */
    public function getFriendsFriends($friend, $n)
    {
        if ($n > 0) {
            $friendsFriends = $friend->getFriends();
            foreach ($friendsFriends as $friendsFriend) {
                $this->friendsFriends[] = $friendsFriend->getEmail();
                $this->getFriendsFriends($friendsFriend, $n-1);
            }
        }

        return array_unique($this->friendsFriends);
    }
}
