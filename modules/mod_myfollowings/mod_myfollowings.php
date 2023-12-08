<?php  
/**
 * Custom module developed by Archie
 * Display an user's followings
 */

// Define the namespace  
namespace Modules\Myfollowings;  
  
// Include the helper file  
require_once __DIR__ . DS . 'helper.php';  
  
// Instantiate the module helper and call its display() method  
with(new Helper($params, $module))->display();