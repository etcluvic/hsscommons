<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Profile;

use Hubzero\Database\Relational;
use Lang;

/**
 * User profile field option model
 */
class Option extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'user_profile';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'ordering';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'label'    => 'notempty',
		'field_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'ordering'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'value',
		'checked'
	);

	/**
	 * Generates automatic value field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticValue($data)
	{
		if (!isset($data['value']) || !$data['value'])
		{
			$data['value'] = $data['label'];
		}

		return $data['value'];
	}

	/**
     * Override the get method to translate the label
     *
     * @param   string  $key      The property to get
     * @param   mixed   $default  The default value if the property does not exist
     * @return  mixed   The property value
     */
    public function get($key, $default = null)
    {
        $value = parent::get($key, $default);

        if ($key == 'label')
        {
            $normalizedValue = preg_replace('/[^A-Za-z0-9]/', '_', $value);
        	$translationKey = 'COM_MEMBERS_DYNAMIC_' . strtoupper($normalizedValue);
            return Lang::txt($translationKey);
        }

        return $value;
    }

	/**
	 * Generates automatic checked field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticChecked($data)
	{
		if (!isset($data['checked']))
		{
			$data['checked'] = 0;
		}

		return (int)$data['checked'];
	}

	/**
	 * Get parent field
	 *
	 * @return  object
	 */
	public function field()
	{
		return $this->belongsToOne('Field', 'field_id');
	}

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOrdering($data)
	{
		if (!isset($data['ordering']))
		{
			$last = self::all()
				->select('ordering')
				->whereEquals('user_id', $this->get('user_id'))
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}
}
