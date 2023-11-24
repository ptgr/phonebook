<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

		$router->withPath('api/v1')
			->addRoute('contacts', 'Contact:index')
			->addRoute('contacts/create', 'Contact:create')
			->addRoute('contacts/update/<id>', 'Contact:update')
			->addRoute('contacts/<id>', 'Contact:show');

		return $router;
	}
}
