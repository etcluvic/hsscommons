<?php  
// Define the namespace  
// Components\{ComponentName}\{ClientName};  
namespace Components\File\Site;  
  
// Get the requested controller  
$controllerName = Request::getCmd('controller', Request::getCmd('view', 'file'));  
  
// Ensure the controller exists  
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))  
{  
    App::abort(404, Lang::txt('Controller not found'));  
}  
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');  
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));  
  
// Instantiate controller  
$controller = new $controllerName();  
// Execute whatever task(s)  
$controller->execute();  