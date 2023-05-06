<?php
/*
Custom plugin developed by Archie to add an e-commerce store to groups
*/
// No direct access  
defined( '_HZEXEC_' ) or die();

include \Component::path('com_groups') . DS . 'models' . DS . 'orm' . DS . 'product.php';

class plgGroupsCommerce extends \Hubzero\Plugin\Plugin
{
    /** 
     * Affects constructor behavior. 
     * If true, language files will be loaded automatically. 
     * 
     * @var  boolean 
     */  
    protected $_autoloadLanguage = false;

    /** 
     * Constructor 
     * 
     * @param  object   $subject The object to observe 
     * @param  array   $config  An array that holds the plugin configuration 
     * @return  void 
     */  
    public function __construct(&$subject, $config)  
    {  
        parent::__construct($subject, $config);  
      
        $this->model = new Product();
    } 

    /**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => 'Online store',
			'default_access'   => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f07a'
		);
		return $area;
	}

    public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
    {
        $this->group = $group;
        // The output array we're returning
		$arr = array(
			'html' => '',
			'metadata' => array()
		);

        $view = new \Hubzero\Plugin\View(array(
            'folder' => 'groups',
            'element' => 'commerce',
            'name' => 'products'
        ));

        // Set any errors  
        if ($this->getError())  
        {  
            $view->setError( $this->getError() );  
        }

        // Return the view
        // return $view->loadTemplate();
        $active = Request::getCmd('active', '');
        $plugintask = Request::getString('plugintask', '');
        echo $plugintask;
        
        switch($plugintask) {
            case 'addproduct':
                $arr['html'] = $this->addProduct();
                break;
            default:
                $arr['html'] = $this->displayProducts();
                break;
        }

        return $arr;
    }

    /**
     * Display existing products
     */
    public function displayProducts()
    {
        $products = $this->model->getAll();
        $view = new \Hubzero\Plugin\View(array(
            'folder' => 'groups',
            'element' => 'commerce',
            'name' => 'products',
        ));
        $view->set('products', $products);

        // Set any errors  
        if ($this->getError())  
        {  
            $view->setError( $this->getError() );  
        }

        return $view->loadTemplate();
    }

    /**
     * Add a new product
     */
    public function addProduct()
    {
        $method = Request::getString('method', 'get', 'post');
        $id = Request::getInt('id', '', 'post');
        $title = Request::getString('title', '', 'post');
        $price = Request::getInt('price', '', 'post');
        $description = Request::getString('description', '', 'post');

        if ($method === 'post') {
            echo "Saving a new product";
            $newProduct = $this->model->oneOrNew($id);
            $newProduct->set("title", $title);
            $newProduct->set("price", $price);
            $newProduct->set("description", $description);
            if (!$newProduct->save())
            {
                echo "Failed to save the new product";
            }
            else {
                App::redirect(DS . "groups" . DS . $this->group->get('cn') . DS . 'commerce');
            }
        }

        $view = new \Hubzero\Plugin\View(array(
            'folder' => 'groups',
            'element' => 'commerce',
            'name' => 'products',
            'layout' => 'manage'
        ));

        $view->set('products', $this->model->getAll());

        // Set any errors  
        if ($this->getError())  
        {  
            $view->setError( $this->getError() );  
        }

        return $view->loadTemplate();
    }
}