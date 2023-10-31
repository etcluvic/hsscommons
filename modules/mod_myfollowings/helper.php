<?php  
namespace Modules\Myfollowings;  
  
use Hubzero\Module\Module;  
// use App;  
  
class Helper extends Module  
{  
    public function display()  
    {  
        // Retrieve rows from the database  
        // $this->rows = $this->getItems();  
  
        // Render the view  
        require $this->getLayoutPath();  
    }  
  
    // public function getItems()  
    // {  
    //     $db = App::get('db');  
    //     $db->setQuery(" ... ");  
    //     return $db->loadObjectList();  
    // }  
}