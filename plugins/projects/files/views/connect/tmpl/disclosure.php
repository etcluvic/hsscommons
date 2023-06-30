<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$current_url = $_SERVER['REQUEST_URI'];
Log::info($current_url);
?>

<div id="plg-header">
    <h3 class="disclosure-title">Google Drive disclosure</h3>
</div>
<p class="disclosure-content">HSSCommon’s use and transfer to any other app of information received from Google APIs will adhere to <a href="https://developers.google.com/terms/api-services-user-data-policy#additional_requirements_for_specific_api_scopes" target="_blank">Google API Services User Data Policy</a>, including the Limited Use requirements. To continue, please click the button below.</p>
<a class="btn btn-primary" href="<?php echo $current_url . '&' . 'disclosure_confirmed=1'; ?>">I agree to give HSSCommons access to my Google Drive</a>