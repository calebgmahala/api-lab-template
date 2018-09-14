<?php
namespace caleb\sports;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require './vendor/autoload.php';

class App
{

   private $app;
   public function __construct($db) {

     $config['db']['host']   = 'localhost';
     $config['db']['user']   = 'root';
     $config['db']['pass']   = 'root';
     $config['db']['dbname'] = 'sportsdb';

     $app = new \Slim\App(['settings' => $config]);

     $container = $app->getContainer();
     $container['db'] = $db;

     $container['logger'] = function($c) {
         $logger = new \Monolog\Logger('my_logger');
         $file_handler = new \Monolog\Handler\StreamHandler('./logs/app.log');
         $logger->pushHandler($file_handler);
         return $logger;
     };
     // get all sports
     $app->get('/sports', function (Request $request, Response $response) {
         $this->logger->addInfo("GET /sports");
         $sports = $this->db->query('SELECT * from sports')->fetchAll();
         $jsonResponse = $response->withJson($sports);
         return $jsonResponse;
     });
     //get sport by id
     $app->get('/sports/{id}', function (Request $request, Response $response, array $args) {
         $id = $args['id'];
         $this->logger->addInfo("GET /sports/".$id);
         $person = $this->db->query('SELECT * from sports where id='.$id)->fetch();

         if($person){
           $response =  $response->withJson($person);
         } else {
           $errorData = array('status' => 404, 'message' => 'not found');
           $response = $response->withJson($errorData, 404);
         }
         return $response;

     });
     //edit query
     $app->put('/sports/{id}', function (Request $request, Response $response, array $args) {
         $id = $args['id'];
         $this->logger->addInfo("PUT /sports/".$id);

         // check that peron exists
         $person = $this->db->query('SELECT * from sports where id='.$id)->fetch();
         if(!$person){
           $errorData = array('status' => 404, 'message' => 'not found');
           $response = $response->withJson($errorData, 404);
           return $response;
         }

         // build query string
         $updateString = "UPDATE sports SET ";
         $fields = $request->getParsedBody();
         $keysArray = array_keys($fields);
         $last_key = end($keysArray);
         foreach($fields as $field => $value) {
           $updateString = $updateString . "$field = '$value'";
           if ($field != $last_key) {
             // conditionally add a comma to avoid sql syntax problems
             $updateString = $updateString . ", ";
           }
         }
         $updateString = $updateString . " WHERE id = $id;";

         // execute query
         try {
           $this->db->exec($updateString);
         } catch (\PDOException $e) {
           $errorData = array('status' => 400, 'message' => 'Invalid data provided to update');
           return $response->withJson($errorData, 400);
         }
         // return updated record
         $person = $this->db->query('SELECT * from sports where id='.$id)->fetch();
         $jsonResponse = $response->withJson($person);

         return $jsonResponse;
     });
     //delete sport
     $app->delete('/sports/{id}', function (Request $request, Response $response, array $args) {
       $id = $args['id'];
       $this->logger->addInfo("DELETE /sports/".$id);
       $deleteSuccessful = $this->db->exec('DELETE FROM sports where id='.$id);
       if($deleteSuccessful){
         $response = $response->withStatus(200);
       } else {
         $errorData = array('status' => 404, 'message' => 'not found');
         $response = $response->withJson($errorData, 404);
       }
       return $response;
     });
     //create sport 
     $app->post('/sports/new', function (Request $request, Response $response) {
       $this->logger->addInfo("POST /sports/new");

       // insert query
       $query = "INSERT INTO sports VALUES (" . $request->getParsedBody() . ')';
       $createSuccessful = $this->db->exec($query);
       if($createSuccessful){
         $response = $response->withStatus(200);
       } else {
         $errorData = array('status' => 404, 'message' => 'not found');
         $response = $response->withJson($errorData, 404);
       }
       return $response;
     });

     $this->app = $app;
   }

   /**
    * Get an instance of the application.
    *
    * @return \Slim\App
    */
   public function get()
   {
       return $this->app;
   }
 }
