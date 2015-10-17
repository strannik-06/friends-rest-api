<?php

namespace AppBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Document\User;

class LoadUserData extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $users = array(
            1 => array(
                'name' => 'John Doe',
                'email' => 'john_joe@email.com',
                'friends' => array(),
                'requests' => array(),
            ),
            2 => array(
                'name' => 'Sara Doe',
                'email' => 'sara_joe@email.com',
                'friends' => array(),
                'requests' => array(1),
            ),
            3 => array(
                'name' => 'James Doe',
                'email' => 'james_joe@email.com',
                'friends' => array(),
                'requests' => array(1, 2),
            ),
            4 => array(
                'name' => 'Jane Smith',
                'email' => 'jane_smith@email.com',
                'friends' => array(),
                'requests' => array(3),
            ),
            5 => array(
                'name' => 'Anonymous',
                'email' => 'anonymous@email.com',
                'friends' => array(),
                'requests' => array(4),
            ),
        );

        foreach ($users as $id => $userData) {
            $user = new User();
            $user->setName($userData['name']);
            $user->setEmail($userData['email']);
            foreach ($userData['friends'] as $friendId) {
                $user->addFriend($this->getReference('user_' . $friendId));
            }
            foreach ($userData['requests'] as $requestId) {
                $user->addRequest($this->getReference('user_' . $requestId));
            }
            $manager->persist($user);
            $this->addReference('user_' . $id, $user);
        }

        $manager->flush();
    }
}
