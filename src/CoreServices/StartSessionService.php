<?php

namespace Jawan\CoreServices;

use Jawan\Core\Services\CoreServices\CoreServicesAbstract;

/**
 * Start sessions service class
 */
class StartSessionService extends CoreServicesAbstract
{
	/**
	 * {@inheritDoc}
	 */
	public static function run()
	{
		$app = \Jawan\Core\App::getInstance();
		$app->session->start();
		$app->session->regenerateID();
	}

}