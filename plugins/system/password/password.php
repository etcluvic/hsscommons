<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin checking for spam offences after routing
 */
class plgSystemPassword extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after parsing route
	 *
	 * @return  void
	 */
	public function onAfterRoute()
	{
		if (App::isSite() && !User::isGuest())
		{
			$exceptions = [
				'com_login.logout',
				'com_users.logout',
				'com_users.userlogout',
				'com_support.tickets.save.index',
				'com_members.changepassword',
				'com_members.media.download.profiles',
				'com_members.save.profiles',
				'com_members.profiles.save',
				'com_members.profiles.save.profiles'
			];

			$current  = Request::getWord('option', '');
			$current .= ($controller = Request::getWord('controller', false)) ? '.' . $controller : '';
			$current .= ($task       = Request::getWord('task', false)) ? '.' . $task : '';
			$current .= ($view       = Request::getWord('view', false)) ? '.' . $view : '';

			$badpassword     = Session::get('badpassword', false);
			$expiredpassword = Session::get('expiredpassword', false);

			// If guest, proceed as normal and they'll land on the login page
			if (!in_array($current, $exceptions) && ($badpassword || $expiredpassword))
			{
				Request::setVar('option', 'com_members');
				Request::setVar('task', 'changepassword');
				Request::setVar('id', 0);

				$this->loadLanguage();

				if ($badpassword)
				{
					Notify::warning(Lang::txt('PLG_SYSTEM_PASSWORD_REQUIREMENTS_NOT_MET'));
				}

				if ($expiredpassword)
				{
					if ($current !== 'com_members.changepassword.profiles') {
						Notify::warning(Lang::txt('PLG_SYSTEM_PASSWORD_EXPIRED'));
					} else {
						Notify::success('Password changed successfully');
					}
				}

				$this->event->stop();
			}
		}
	}
}
