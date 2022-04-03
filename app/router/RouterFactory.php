<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\CliRouter;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;


	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{

		$router = new RouteList();
        //$router[] = new Route('index.php', 'Front:Homepage:default');
        $router[] = $admin = new RouteList('Admin');
        $admin[] = new Route('admin/<presenter>/<action>', array('presenter' => 'Homepage','action' => 'default','lang' => 'cz'));;

        $router[] = $front = new RouteList('Front');
        $front[] = new Route('prihlasit', 'Homepage:login');
        $front[] = new Route('import-feed', array('presenter' => 'Xml','action' => 'importFeed'));
        $front[] = new Route('cron', array('presenter' => 'Cron','action' => 'default'));
        $front[] = new Route('platba[/<id>]', array('presenter' => 'Homepage','action' => 'payment'));
        $front[] = new Route('/[<id>]', array('presenter' => 'Homepage','action' => 'page','id' => '','lang' => 'cz'));
        $front[] = new Route('<presenter>/<action>', 'Homepage:page');

		return $router;

	}
}
