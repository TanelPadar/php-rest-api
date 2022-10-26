<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->options('/{routes:.+}', function ($request, $response, $args) {
  return $response;
});

$app->add(function ($req, $res, $next) {
  $response = $next($req, $res);
  return $response
    ->withHeader('Access-Control-Allow-Origin', '*')
    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});


$app->get('/users', function(Request $request, Response $response){
  $sql = "SELECT * FROM kasutajad";

  try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo json_encode($customers);
  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }
});


$app->get('/users/{id}', function(Request $request, Response $response){
  $id = $request->getAttribute('id');

  $sql = "SELECT * FROM kasutajad WHERE id = $id";

  try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->query($sql);
    $customer = $stmt->fetch(PDO::FETCH_OBJ);
    $db = null;
    echo json_encode($customer);
  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }
});


$app->post('/users', function(Request $request, Response $response){
  $name = $request->getParam('name');
  $username = $request->getParam('username');
  $email = $request->getParam('email');


  $sql = "INSERT INTO kasutajad (name,username,email) VALUES
    (:name,:username,:email)";

  try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':username',  $username);
    $stmt->bindParam(':email',      $email);


    $stmt->execute();

    echo '{"notice": {"text": "user Added"}';

  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }
});


$app->put('/users/{id}', function(Request $request, Response $response){
  $id = $request->getAttribute('id');
  $name = $request->getParam('name');
  $username = $request->getParam('username');
  $email = $request->getParam('email');


  $sql = "UPDATE customers SET
				name 	= :name,
				username 	= :username,
                email		= :email,
               
			WHERE id = $id";

  try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':username',  $username);
    $stmt->bindParam(':email',      $email);

    $stmt->execute();

    echo '{"notice": {"text": "user Updated"}';

  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }
});

// Delete Customer
$app->delete('/users/{id}', function(Request $request, Response $response){
  $id = $request->getAttribute('id');

  $sql = "DELETE FROM customers WHERE id = $id";

  try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $db = null;
    echo '{"notice": {"text": "user Deleted"}';
  } catch(PDOException $e){
    echo '{"error": {"text": '.$e->getMessage().'}';
  }
});