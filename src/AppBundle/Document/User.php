<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MongoDB\Document
 */
class User
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\String
     */
    private $name;

    /**
     * @MongoDB\String @MongoDB\Index(unique=true)
     */
    private $email;

    /**
     * @MongoDB\ReferenceMany(targetDocument="User")
     */
    private $friends;

    /**
     * @MongoDB\ReferenceMany(targetDocument="User")
     */
    private $requests;

    public function __construct()
    {
        $this->friends = new ArrayCollection();
        $this->requests = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add friend
     *
     * @param User $friend
     */
    public function addFriend(User $friend)
    {
        $this->friends[] = $friend;
    }

    /**
     * Remove friend
     *
     * @param User $friend
     */
    public function removeFriend(User $friend)
    {
        $this->friends->removeElement($friend);
    }

    /**
     * Get friends
     *
     * @return ArrayCollection $friends
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * Add request
     *
     * @param User $request
     */
    public function addRequest(User $request)
    {
        $this->requests[] = $request;
    }

    /**
     * Remove request
     *
     * @param User $request
     */
    public function removeRequest(User $request)
    {
        $this->requests->removeElement($request);
    }

    /**
     * Get requests
     *
     * @return ArrayCollection $requests
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param User $request
     *
     * @return bool
     */
    public function isRequest($request)
    {
        /* @var User $userRequest */
        foreach ($this->getRequests() as $userRequest) {
            if ($userRequest->getId() == $request->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $friend
     *
     * @return bool
     */
    public function isFriend($friend)
    {
        /* @var User $userFriend */
        foreach ($this->getFriends() as $userFriend) {
            if ($userFriend->getId() == $friend->getId()) {
                return true;
            }
        }

        return false;
    }
}
