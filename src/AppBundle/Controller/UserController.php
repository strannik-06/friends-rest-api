<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Document\User;
use AppBundle\Service\User as UserService;

class UserController extends Controller
{
    /**
     * @param string $userEmail
     * @param string $friendEmail
     *
     * @return JsonResponse
     *
     * @Route("/send-request/{userEmail}/{friendEmail}", name="send-request")
     */
    public function sendRequestAction($userEmail, $friendEmail)
    {
        try {
            $user = $this->getUserService()->getUserByEmail($userEmail);
            $friend = $this->getUserService()->getUserByEmail($friendEmail);

            if ($friend->isRequest($user)) {
                return new JsonResponse('Request was already sent', 404);
            }
            if ($user->isFriend($friend)) {
                return new JsonResponse('Friend was already added', 404);
            }

            $this->getUserService()->addRequest($friend, $user);

            return new JsonResponse('Request is successfully sent');

        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 404);
        }
    }

    /**
     * @param string $userEmail
     * @param string $requestEmail
     *
     * @return JsonResponse
     *
     * @Route("/confirm-request/{userEmail}/{requestEmail}", name="confirm-request")
     */
    public function confirmRequestAction($userEmail, $requestEmail)
    {
        try {
            $user = $this->getUserService()->getUserByEmail($userEmail);
            $request = $this->getUserService()->getUserByEmail($requestEmail);

            if (!$user->isRequest($request)) {
                return new JsonResponse('No request found', 404);
            }

            $this->getUserService()->confirmRequest($user, $request);

            return new JsonResponse('Request is successfully confirmed');

        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 404);
        }
    }

    /**
     * @param string $userEmail
     * @param string $requestEmail
     *
     * @return JsonResponse
     *
     * @Route("/reject-request/{userEmail}/{requestEmail}", name="reject-request")
     */
    public function rejectRequestAction($userEmail, $requestEmail)
    {
        try {
            $user = $this->getUserService()->getUserByEmail($userEmail);
            $request = $this->getUserService()->getUserByEmail($requestEmail);

            if (!$user->isRequest($request)) {
                return new JsonResponse('No request found', 404);
            }

            $this->getUserService()->rejectRequest($user, $request);

            return new JsonResponse('Request is successfully rejected');

        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 404);
        }
    }

    /**
     * @param string $userEmail
     *
     * @return JsonResponse
     *
     * @Route("/requests/{userEmail}", name="requests")
     */
    public function showRequestsAction($userEmail)
    {
        try {
            $user = $this->getUserService()->getUserByEmail($userEmail);

            $output = array();
            /* @var User $request */
            foreach ($user->getRequests() as $request) {
                $output[] = $request->getEmail();
            }

            return new JsonResponse($output);

        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 404);
        }
    }

    /**
     * @param string $userEmail
     *
     * @return JsonResponse
     *
     * @Route("/friends/{userEmail}", name="friends")
     */
    public function showFriendsAction($userEmail)
    {
        try {
            $user = $this->getUserService()->getUserByEmail($userEmail);

            $output = array();
            /* @var User $friend */
            foreach ($user->getFriends() as $friend) {
                $output[] = $friend->getEmail();
            }

            return new JsonResponse($output);

        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 404);
        }
    }

    /**
     * @param string  $userEmail
     * @param integer $n
     *
     * @return JsonResponse
     *
     * @Route("/friends-friends/{userEmail}/{n}", name="friends-friends")
     */
    public function showFriendsFriendsAction($userEmail, $n)
    {
        try {
            $user = $this->getUserService()->getUserByEmail($userEmail);

            $output = array();
            /* @var User $friend */
            foreach ($user->getFriends() as $friend) {
                $output = array_merge($output, $this->getUserService()->getFriendsFriends($friend, $n));
            }

            return new JsonResponse(array_values(array_unique($output)));

        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 404);
        }
    }

    /**
     * @return UserService
     */
    private function getUserService()
    {
        return $this->get('app.user');
    }
}
