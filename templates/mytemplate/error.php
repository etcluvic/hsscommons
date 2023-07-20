<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/**
 * Modified by CANARIE Inc. for the HSSCommons project.
 *
 * Summary of changes: Minor customization.
 *
 */

defined('_HZEXEC_') or die();

Html::behavior('framework', true);
Html::behavior('modal');

// Include global scripts
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js?v=' . filemtime(__DIR__ . '/js/hub.js'));

$p = substr(PATH_APP, strlen(PATH_ROOT));

// Get browser info to set some classes
$menu = App::get('menu');
$browser = new \Hubzero\Browser\Detector();
$cls = array(
  'no-js',
  $browser->name(),
  $browser->name() . $browser->major(),
  $this->direction,
  ($menu->getActive() == $menu->getDefault() ? 'home' : '')
);

$code = (is_numeric($this->error->getCode()) && $this->error->getCode() > 100 ? $this->error->getCode() : 500);

Lang::load('tpl_' . $this->template) ||
Lang::load('tpl_' . $this->template, __DIR__);

// Prepend site name to document title
$this->setTitle(Config::get('sitename') . ' - ' . $this->getTitle());

// Redirect to home page if 404 error
// if ($this->error->getCode() == 404) {
// 	App::redirect('/', $message = 'Requested page not found');
// }
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo implode(' ', $cls); ?>">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css?v=<?php echo filemtime(__DIR__ . '/css/error.css'); ?>" />

    <!-- Include favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $this->baseurl . '/templates/' . $this->template . '/apple-touch-icon.png'?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $this->baseurl . '/templates/' . $this->template . '/favicon-32x32.png'?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $this->baseurl . '/templates/' . $this->template . '/favicon-16x16.png'?>">
    <link rel="manifest" href="<?php echo $this->baseurl . '/templates/' . $this->template . '/site.webmanifest'?>">

  </head>
  <body>
    <!-- Archie: hidden div tag to store side-wide public information -->
    <div id="public-info" data-session-timeout="<?php echo Config::get('session')->lifetime; ?>" style="display: none;"></div>

    <div id="outer-wrap">
      <?php echo \Hubzero\Module\Helper::renderModules("helppane"); ?>

      <div id="top">
        <div id="splash">
          <div class="inner-wrap">
            <header id="masthead">
              <?php echo \Hubzero\Module\Helper::renderModules("notices"); ?>
              <h1>
                <a href="<?php echo Request::root(); ?>" title="<?php echo Config::get('sitename'); ?>">
                	<!--  Modified by CANARIE Inc. Beginning  -->
                	<img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/img/logos/color_logo_transparent.png" alt="<?php echo Config::get('sitename'); ?>" width="151" height="45"/>
                	<!--  Modified by CANARIE Inc. End  -->
                </a>
              </h1>
              <nav id="account" class="account-navigation">
                <ul>
                  <li>
                    <a class="icon-search" href="<?php echo Route::url('index.php?option=com_search'); ?>" title="<?php echo Lang::txt('TPL_MYTEMPLATE_SEARCH'); ?>"><?php echo Lang::txt('TPL_MYTEMPLATE_SEARCH'); ?></a>
                    <jdoc:include type="modules" name="search" />
                  </li>
                <?php if (!User::isGuest()) { ?>
                  <li class="loggedin">
                    <a href="<?php echo Route::url(User::link()); ?>">
                      <img src="<?php echo User::picture(); ?>" alt="<?php echo User::get('name'); ?>" width="30" height="30" />
                      <span class="account-details">
                        <?php echo stripslashes(User::get('name')); ?> 
                        <span class="account-email"><?php echo User::get('email'); ?></span>
                      </span>
                    </a>
                    <ul>
                      <li id="account-dashboard">
                        <a href="<?php echo Route::url(User::link() . '&active=dashboard'); ?>"><span><?php echo Lang::txt('TPL_MYTEMPLATE_ACCOUNT_DASHBOARD'); ?></span></a>
                      </li>
                      <li id="account-profile">
                        <a href="<?php echo Route::url(User::link() . '&active=profile'); ?>"><span><?php echo Lang::txt('TPL_MYTEMPLATE_ACCOUNT_PROFILE'); ?></span></a>
                      </li>
                      <li id="account-logout">
                        <a href="<?php echo Route::url('index.php?option=com_users&view=logout'); ?>"><span><?php echo Lang::txt('TPL_MYTEMPLATE_LOGOUT'); ?></span></a>
                      </li>
                    </ul>
                  </li>
                <?php } else { ?>
                  <li>
                    <a class="icon-login" href="<?php echo Route::url('index.php?option=com_users&view=login'); ?>" title="<?php echo Lang::txt('TPL_MYTEMPLATE_LOGIN'); ?>"><?php echo Lang::txt('TPL_MYTEMPLATE_LOGIN'); ?></a>
                  </li>
                <?php } ?>
                </ul>
              </nav>
              <nav id="nav" class="main-navigation" aria-label="<?php echo Lang::txt('TPL_MYTEMPLATE_MAINMENU'); ?>">
                <?php echo \Hubzero\Module\Helper::renderModules("user3"); ?>
              </nav>
            </header>

            <main id="errorbox" class="<?php echo 'code' . $this->error->getCode(); ?>">
              <div class="inner">
                <div class="error-heading">
                  <h2 class="error-code">
                    <?php echo $code; ?>
                  </h2>
                  <div>
                    <p>We couldn't find the page you were looking for</p>
                    <div id="go-back-btn" class="btn">Go back</div>
                    <script>
                      document.getElementById("go-back-btn").onclick = function () {
                        history.back();
                      };
                    </script>
                  </div>
                </div>

                <p class="error"><?php 
                  if ($this->debug)
                  {
                    $message = $this->error->getMessage();
                  }
                  else
                  {
                    switch ($this->error->getCode())
                    {
                      case 404:
                        $message = Lang::txt('TPL_MYTEMPLATE_404_HEADER');
                        break;
                      case 403:
                        $message = Lang::txt('TPL_MYTEMPLATE_403_HEADER');
                        break;
                      case 500:
                      default:
                        $message = Lang::txt('TPL_MYTEMPLATE_500_HEADER');
                        break;
                    }
                  }
                  echo $message;
                ?></p>
              </div><!-- / .inner -->
            </main><!-- / #content -->

          </div><!-- / .inner-wrap -->
        </div><!-- / .inner-wrap -->
      </div><!-- / #top -->

      <?php if ($this->debug) { ?>
        <div class="backtrace-wrap">
          <?php echo $this->renderBacktrace(); ?>
        </div>
      <?php } ?>

      <footer id="footer">
      <?php echo \Hubzero\Module\Helper::renderModules("footer"); ?>
      </footer>
    </div><!-- / #wrap -->
  </body>
</html>