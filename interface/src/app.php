<?php
namespace caleb\sportsInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

require './vendor/autoload.php';

class App
{
   private $app;
   private const SCRIPT_INCLUDE = '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
   <script
     src="https://code.jquery.com/jquery-3.3.1.min.js"
     integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
     crossorigin="anonymous"></script>
   </head>
   <script src=".public/script.js"></script>';


   public function __construct() {

     $app = new \Slim\App();

     $container = $app->getContainer();

     $container['logger'] = function($c) {
         $logger = new \Monolog\Logger('my_logger');
         $file_handler = new \Monolog\Handler\StreamHandler('./logs/app.log');
         $logger->pushHandler($file_handler);
         return $logger;
     };
     $container['renderer'] = new PhpRenderer("./templates");

     function makeApiRequest($path){
       $ch = curl_init();

       //Set the URL that you want to GET by using the CURLOPT_URL option.
       curl_setopt($ch, CURLOPT_URL, "http://localhost/api/$path");
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

       $response = curl_exec($ch);
       return json_decode($response, true);
     }

     $app->get('/', function (Request $request, Response $response, array $args) {
       $responseRecords = makeApiRequest('sports');

       $templateVariables = [
           "title" => "Sports",
           "responseRecords" => $responseRecords
       ];
       return $this->renderer->render($response, "/sports.html", $templateVariables);
     });

     $app->get('/sports/add', function(Request $request, Response $response) {
       $templateVariables = [
         "type" => "new",
         "title" => "Create Sport"
       ];
       return $this->renderer->render($response, "/sportsForm.html", $templateVariables);
     });

     $app->get('/sports/{id}', function (Request $request, Response $response, array $args) {
         $id = $args['id'];
         $responseRecords = makeApiRequest('sports/'.$id);
         if($responseRecords["teamsport"] == false){
          $teamsport = "False";
        } else {
          $teamsport = "True";
        }
        if($responseRecords["esport"] == false){
          $esport = "False";
        } else {
          $esport = "True";
        }
         $body = "<h1>Name: ".$responseRecords['name']."</h1>";
         $body = $body . "<h2>Teamsport: ".$teamsport."</h2>";
         $body = $body . "<h2>Best Player: ".$responseRecords['best_player']."</h3>";
         $body = $body . "<h2>eSport: ".$esport."</h2>";
         $response->getBody()->write($body);
         return $response;
     });
     $app->get('/sports/{id}/edit', function (Request $request, Response $response, array $args) {
         $id = $args['id'];
         $responseRecord = makeApiRequest('sports/'.$id);
         $templateVariables = [
           "type" => "edit",
           "title" => "Edit User",
           "id" => $id,
           "sport" => $responseRecord
         ];
         return $this->renderer->render($response, "/sportsEditForm.html", $templateVariables);

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
