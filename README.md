Friends REST API - small REST application written using Symfony2 and MongoDB

<h3>Requirements:</h3>
PHP 5.6
MongoDB 3.0 (you need running server mongodb://localhost:27017)

<h3>Installation:</h3>
<code>composer install</code>

<h3>Usage:</h3>
<code>php app/console server:start</code> - run built-in symfony web server
<code>php app/console doctrine:mongodb:fixtures:load</code> - load initial fixtures

http://127.0.0.1:8000/send-request/{userEmail}/{friendEmail}
http://127.0.0.1:8000/confirm-request/{userEmail}/{requestEmail}
http://127.0.0.1:8000/reject-request/{userEmail}/{requestEmail}
http://127.0.0.1:8000/requests/{userEmail}
http://127.0.0.1:8000/friends/{userEmail}
http://127.0.0.1:8000/friends-friends/{userEmail}/{n}
(you can use john_joe@email.com, sara_joe@email.com, james_joe@email.com, jane_smith@email.com, anonymous@email.com)

<h3>To run tests:</h3>
<code>php app/console doctrine:mongodb:schema:drop</code> - clean database
<code>php app/console doctrine:mongodb:fixtures:load</code> - load fixtures
<code>phpunit -c app/phpunit.xml.dist</code>

<h3>Note:</h3>
To simplify writing functional tests its decided to use email instead of id in all requests.
