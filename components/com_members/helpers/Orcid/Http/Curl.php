<?php
/**
 * @package   orcid-php
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Components\Members\Helpers\Orcid\Http;
use Orcid;

/**
 * Curl http transport class
 **/
class Curl extends Orcid\Http\Curl
{
    /**
     * The connection resource
     *
     * @var  object
     **/
    private $resource = null;

    /**
     * Constructs a new instance
     *
     * @return  void
     **/
    public function __construct()
    {
        $this->initialize();
    }
}
