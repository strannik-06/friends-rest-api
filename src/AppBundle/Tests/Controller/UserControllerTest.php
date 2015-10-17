<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

/**
 * Data from fixtures: Sara has request from John, James has requests from John and Sara,
 * Jane has request from James, Anonymous has request from Jane.
 *
 * Test for AppBundle\Controller\UserController
 *
 * @group functional
 */
class UserControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * Sara confirmed request from John.
     *
     * @param string  $user
     * @param string  $request
     * @param integer $statusCode
     * @param string  $output
     *
     * @dataProvider confirmRequestDataProvider
     */
    public function testConfirmRequest($user, $request, $statusCode, $output)
    {
        $this->client->request('GET', '/confirm-request/'.$user.'/'.$request);
        $this->assertResponse($statusCode, $output);
    }

    /**
     * @return array
     */
    public function confirmRequestDataProvider()
    {
        return array(
            'no user found' => array(
                'user' => 'not_found@email.com',
                'request' => 'john_joe@email.com',
                'statusCode' => 404,
                'output' => 'No user found for email not_found@email.com',
            ),
            'no friend found' => array(
                'user' => 'sara_joe@email.com',
                'request' => 'not_existing@email.com',
                'statusCode' => 404,
                'output' => 'No user found for email not_existing@email.com',
            ),
            'success case' => array(
                'user' => 'sara_joe@email.com',
                'request' => 'john_joe@email.com',
                'statusCode' => 200,
                'output' => 'Request is successfully confirmed',
            ),
            'no request found' => array(
                'user' => 'sara_joe@email.com',
                'request' => 'john_joe@email.com',
                'statusCode' => 404,
                'output' => 'No request found',
            ),
        );
    }

    /**
     * John sent request to Jane.
     *
     * @param string  $user
     * @param string  $friend
     * @param integer $statusCode
     * @param string  $output
     *
     * @dataProvider sendRequestDataProvider
     */
    public function testSendRequest($user, $friend, $statusCode, $output)
    {
        $this->client->request('GET', '/send-request/'.$user.'/'.$friend);
        $this->assertResponse($statusCode, $output);
    }

    /**
     * @return array
     */
    public function sendRequestDataProvider()
    {
        return array(
            'no user found' => array(
                'user' => 'not_found@email.com',
                'friend' => 'john_joe@email.com',
                'statusCode' => 404,
                'output' => 'No user found for email not_found@email.com',
            ),
            'no friend found' => array(
                'user' => 'john_joe@email.com',
                'friend' => 'not_existing@email.com',
                'statusCode' => 404,
                'output' => 'No user found for email not_existing@email.com',
            ),
            'success case' => array(
                'user' => 'john_joe@email.com',
                'friend' => 'jane_smith@email.com',
                'statusCode' => 200,
                'output' => 'Request is successfully sent',
            ),
            'request already sent' => array(
                'user' => 'john_joe@email.com',
                'friend' => 'jane_smith@email.com',
                'statusCode' => 404,
                'output' => 'Request was already sent',
            ),
            'friend already added' => array(
                'user' => 'john_joe@email.com',
                'friend' => 'sara_joe@email.com',
                'statusCode' => 404,
                'output' => 'Friend was already added',
            ),
        );
    }

    /**
     * Jane rejected request from John.
     *
     * @param string  $user
     * @param string  $request
     * @param integer $statusCode
     * @param string  $output
     *
     * @dataProvider rejectRequestDataProvider
     */
    public function testRejectRequest($user, $request, $statusCode, $output)
    {
        $this->client->request('GET', '/reject-request/'.$user.'/'.$request);
        $this->assertResponse($statusCode, $output);
    }

    /**
     * @return array
     */
    public function rejectRequestDataProvider()
    {
        return array(
            'no user found' => array(
                'user' => 'not_found@email.com',
                'request' => 'john_joe@email.com',
                'statusCode' => 404,
                'output' => 'No user found for email not_found@email.com',
            ),
            'no friend found' => array(
                'user' => 'sara_joe@email.com',
                'request' => 'not_existing@email.com',
                'statusCode' => 404,
                'output' => 'No user found for email not_existing@email.com',
            ),
            'success case' => array(
                'user' => 'jane_smith@email.com',
                'request' => 'john_joe@email.com',
                'statusCode' => 200,
                'output' => 'Request is successfully rejected',
            ),
            'no request found' => array(
                'user' => 'jane_smith@email.com',
                'request' => 'john_joe@email.com',
                'statusCode' => 404,
                'output' => 'No request found',
            ),
        );
    }

    /**
     * @param string  $user
     * @param integer $statusCode
     * @param mixed   $output
     *
     * @dataProvider showRequestsDataProvider
     */
    public function testShowRequests($user, $statusCode, $output)
    {
        $this->client->request('GET', '/requests/'.$user);
        $this->assertResponse($statusCode, $output);
    }

    /**
     * @return array
     */
    public function showRequestsDataProvider()
    {
        return array(
            'no user found' => array(
                'user' => 'not_found@email.com',
                'statusCode' => 404,
                'output' => 'No user found for email not_found@email.com',
            ),
            'success case 1' => array(
                'user' => 'jane_smith@email.com',
                'statusCode' => 200,
                'output' => array('james_joe@email.com'),
            ),
            'success case 2' => array(
                'user' => 'james_joe@email.com',
                'statusCode' => 200,
                'output' => array('john_joe@email.com', 'sara_joe@email.com'),
            ),
        );
    }

    /**
     * Confirm all requests from fixtures.
     */
    public function testPrepareFriends()
    {
        $this->client->request('GET', '/confirm-request/james_joe@email.com/john_joe@email.com');
        $this->client->request('GET', '/confirm-request/james_joe@email.com/sara_joe@email.com');
        $this->client->request('GET', '/confirm-request/jane_smith@email.com/james_joe@email.com');
        $this->client->request('GET', '/confirm-request/anonymous@email.com/jane_smith@email.com');
    }

    /**
     * @param string  $user
     * @param integer $statusCode
     * @param mixed   $output
     *
     * @dataProvider showFriendsDataProvider
     */
    public function testShowFriends($user, $statusCode, $output)
    {
        $this->client->request('GET', '/friends/'.$user);
        $this->assertResponse($statusCode, $output);
    }

    /**
     * @return array
     */
    public function showFriendsDataProvider()
    {
        return array(
            'no user found' => array(
                'user' => 'not_found@email.com',
                'statusCode' => 404,
                'output' => 'No user found for email not_found@email.com',
            ),
            'success case 1' => array(
                'user' => 'jane_smith@email.com',
                'statusCode' => 200,
                'output' => array('james_joe@email.com', 'anonymous@email.com'),
            ),
            'success case 2' => array(
                'user' => 'anonymous@email.com',
                'statusCode' => 200,
                'output' => array('jane_smith@email.com'),
            ),
        );
    }

    /**
     * @param string  $user
     * @param integer $n
     * @param integer $statusCode
     * @param mixed   $output
     *
     * @dataProvider showFriendsFriendsDataProvider
     */
    public function testShowFriendsFriends($user, $n, $statusCode, $output)
    {
        $this->client->request('GET', '/friends-friends/'.$user.'/'.$n);
        $this->assertResponse($statusCode, $output);
    }

    /**
     * @return array
     */
    public function showFriendsFriendsDataProvider()
    {
        return array(
            'no user found' => array(
                'user' => 'not_found@email.com',
                'n' => 1,
                'statusCode' => 404,
                'output' => 'No user found for email not_found@email.com',
            ),
            'success case n = 0' => array(
                'user' => 'jane_smith@email.com',
                'n' => 0,
                'statusCode' => 200,
                'output' => array(),
            ),
            'success case n = 1' => array(
                'user' => 'sara_joe@email.com',
                'n' => 1,
                'statusCode' => 200,
                'output' => array('sara_joe@email.com', 'james_joe@email.com', 'john_joe@email.com',
                    'jane_smith@email.com'),
            ),
            'success case n = 2' => array(
                'user' => 'sara_joe@email.com',
                'n' => 2,
                'statusCode' => 200,
                'output' => array('sara_joe@email.com', 'john_joe@email.com', 'james_joe@email.com',
                    'jane_smith@email.com', 'anonymous@email.com'),
            ),
        );
    }

    /**
     * @param integer $statusCode
     * @param mixed   $output
     */
    private function assertResponse($statusCode, $output)
    {
        $response = $this->client->getResponse();
        $this->assertJson($response, $statusCode);
        $this->assertEquals($output, $this->getJsonContent($response));
    }
    
    /**
     * @param Response $response
     * @param integer  $statusCode
     */
    private function assertJson(Response $response, $statusCode = 200)
    {
        $this->assertEquals('application/json', $response->headers->get('Content-type'));
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * @param Response $response
     *
     * @return array json
     */
    private function getJsonContent(Response $response)
    {
        return json_decode($response->getContent(), true);
    }
}
