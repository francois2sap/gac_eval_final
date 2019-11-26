<?php

    include 'App.php';
    
    $config = json_decode(file_get_contents("config.json"), true);

    $app = new App($config);
    $app->run();



    var_dump($config);
    include_once("controller/ImportController.php");
    $controller = new UserController();
    $controller->invoke();

?>