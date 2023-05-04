<?php  
namespace Components\File\Site\Controllers;  
  
use Hubzero\Component\SiteController;

use Filesystem;
use Request;
use User;
use Lang;
  
class File extends SiteController  
{  
    public function uploadTask()
    {
        // $view = $this->view;
        // $view->fileName = "Uploaded file successfully";
        // $this->displayTask();

        // Check if they're logged in
		if (User::isGuest())
		{
			return $this->displayTask();
		}

        // Incoming file
		$file = Request::getArray('upload', '', 'files');
        $this->view->fileName = $file['name'];
        $this->view->fileSize = $file['size'];

        if (!$file['name'] || $file['size'] == 0)
		{
			$this->setError(Lang::txt('COM_FILE_NO_FILE'));
			return $this->displayTask();
		}

        // Build the file path
		$path = 'users' . DS . 'uploads';

        if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_FILE_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return $this->displayTask();
			}
		}

        // Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);

        // Ensure file names fit.
		$ext = Filesystem::extension($file['name']);

        // Get media config
		$mediaConfig = Component::params('com_media');

        // Check that the file type is allowed
		$allowed = array_values(array_filter(explode(',', $mediaConfig->get('upload_extensions'))));

        if (!empty($allowed) && !in_array(strtolower($ext), $allowed))
		{
			$this->setError(Lang::txt('COM_FILE_ERROR_UPLOADING_INVALID_FILE', implode(', ', $allowed)));

			return $this->displayTask();
		}

        // Size limit is in MB, so we need to turn it into just B
		$sizeLimit = $mediaConfig->get('upload_maxsize', 10);
		$sizeLimit = $sizeLimit * 1024 * 1024;

        if ($file['size'] > $sizeLimit)
		{
			$this->setError(Lang::txt('COM_FILE_ERROR_UPLOADING_FILE_TOO_BIG', \Hubzero\Utility\Number::formatBytes($sizeLimit)));

			return $this->displayTask();
		}

        // Make sure the file name is unique
		$file['name'] = str_replace(' ', '_', $file['name']);
		if (strlen($file['name']) > 230)
		{
			$file['name']  = substr($file['name'], 0, 230);
			$file['name'] .= '.' . $ext;
		}

        // Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_FILE_ERROR_UPLOADING'));
		}

        // Virus scan
		if (!Filesystem::isSafe($path . DS . $file['name']))
		{
			Filesystem::delete($path . DS . $file['name']);

			$this->setError(Lang::txt('COM_FILE_ERROR_UPLOADING'));
		}
        
        $this->view->fileName = "Uploaded file successfully";
        // Push through to the media view
		$this->displayTask();
    }

    public function displayTask()   
    {  
        $view = $this->view;
        
        // Pass the view any data it may need  
        $view->greeting = 'Hello, World!';
        
          
        // Set any errors  
        $view->setErrors($this->getErrors());  
          
        // Output the HTML  
        $view->setLayout('display')
            ->display();
    }
}