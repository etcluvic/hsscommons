<?php

use Hubzero\Database\Relational;

class Product extends Relational  
{  
   /**
	 * The table to which the class pertains
	 *
	 * @var  string
	 **/
	protected $table = '#__xgroups_products';

    /**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'title';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	public function getAll()
	{
		return $this::all()->rows();
	}
}  