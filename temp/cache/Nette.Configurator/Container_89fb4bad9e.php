<?php
// source: D:\projects\htdocs\TP\siegl\app/config/config.neon 
// source: D:\projects\htdocs\TP\siegl\app/config/config.local.neon 

class Container_89fb4bad9e extends Nette\DI\Container
{
	protected $meta = [
		'types' => [
			'Nette\Application\Application' => [1 => ['application.application']],
			'Nette\Application\IPresenterFactory' => [1 => ['application.presenterFactory']],
			'Nette\Application\LinkGenerator' => [1 => ['application.linkGenerator']],
			'Nette\Caching\Storages\IJournal' => [1 => ['cache.journal']],
			'Nette\Caching\IStorage' => [1 => ['cache.storage']],
			'Nette\Database\Connection' => [1 => ['database.app.connection']],
			'Nette\Database\IStructure' => [1 => ['database.app.structure']],
			'Nette\Database\Structure' => [1 => ['database.app.structure']],
			'Nette\Database\IConventions' => [1 => ['database.app.conventions']],
			'Nette\Database\Conventions\DiscoveredConventions' => [1 => ['database.app.conventions']],
			'Nette\Database\Context' => [1 => ['database.app.context']],
			'Nette\Http\RequestFactory' => [1 => ['http.requestFactory']],
			'Nette\Http\IRequest' => [1 => ['http.request']],
			'Nette\Http\Request' => [1 => ['http.request']],
			'Nette\Http\IResponse' => [1 => ['http.response']],
			'Nette\Http\Response' => [1 => ['http.response']],
			'Nette\Http\Context' => [1 => ['http.context']],
			'Nette\Bridges\ApplicationLatte\ILatteFactory' => [1 => ['latte.latteFactory']],
			'Nette\Application\UI\ITemplateFactory' => [1 => ['latte.templateFactory']],
			'Nette\Mail\IMailer' => [1 => ['mail.mailer']],
			'Nette\Application\IRouter' => [1 => ['routing.router']],
			'Nette\Security\IUserStorage' => [1 => ['security.userStorage']],
			'Nette\Security\User' => [1 => ['security.user']],
			'Nette\Http\Session' => [1 => ['session.session']],
			'Tracy\ILogger' => [1 => ['tracy.logger']],
			'Tracy\BlueScreen' => [1 => ['tracy.blueScreen']],
			'Tracy\Bar' => [1 => ['tracy.bar']],
			'BulkGate\Message\IConnection' => [1 => ['bulkgate.connection']],
			'BulkGate\Message\Connection' => [1 => ['bulkgate.connection']],
			'BulkGate\Sms\ISender' => [1 => ['bulkgate.sender']],
			'BulkGate\Sms\Sender' => [1 => ['bulkgate.sender']],
			'Markette\Gopay\Api\GopaySoap' => [1 => ['gopay.driver']],
			'Markette\Gopay\Api\GopayHelper' => [1 => ['gopay.helper']],
			'Markette\Gopay\Config' => [1 => ['gopay.config']],
			'Markette\Gopay\Gopay' => [1 => ['gopay.gopay']],
			'Markette\Gopay\Service\AbstractPaymentService' => [
				1 => [
					'gopay.service.payment',
					'gopay.service.recurrentPayment',
					'gopay.service.preAuthorizedPayment',
				],
			],
			'Markette\Gopay\Service\AbstractService' => [
				1 => [
					'gopay.service.payment',
					'gopay.service.recurrentPayment',
					'gopay.service.preAuthorizedPayment',
				],
			],
			'Markette\Gopay\Service\PaymentService' => [1 => ['gopay.service.payment']],
			'Markette\Gopay\Service\RecurrentPaymentService' => [1 => ['gopay.service.recurrentPayment']],
			'Markette\Gopay\Service\PreAuthorizedPaymentService' => [1 => ['gopay.service.preAuthorizedPayment']],
			'Markette\Gopay\Form\Binder' => [1 => ['gopay.form.binder']],
			'App\Model\CategoryManager' => [1 => ['34_App_Model_CategoryManager']],
			'App\Model\CommonManager' => [1 => ['35_App_Model_CommonManager']],
			'App\Model\LanguageManager' => [1 => ['36_App_Model_LanguageManager']],
			'App\Model\OrderManager' => [1 => ['37_App_Model_OrderManager']],
			'App\Model\OrderStatusManager' => [1 => ['38_App_Model_OrderStatusManager']],
			'App\Model\PageManager' => [1 => ['39_App_Model_PageManager']],
			'App\Model\ProductManager' => [1 => ['40_App_Model_ProductManager']],
			'App\Model\ProjectManager' => [1 => ['41_App_Model_ProjectManager']],
			'App\Model\TranslateManager' => [1 => ['42_App_Model_TranslateManager']],
			'Nette\Security\IAuthenticator' => [1 => ['43_App_Model_UserManager']],
			'App\Model\UserManager' => [1 => ['43_App_Model_UserManager']],
			'App\Model\VoucherManager' => [1 => ['44_App_Model_VoucherManager']],
			'Services\MailchimpService' => [1 => ['mailchimp']],
			'App\AdminModule\Presenters\BasePresenter' => [
				1 => [
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
				],
			],
			'App\FrontModule\Presenters\BasePresenter' => [
				1 => [
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'Nette\Application\UI\Presenter' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'Nette\Application\UI\Control' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'Nette\Application\UI\Component' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'Nette\ComponentModel\Container' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'Nette\ComponentModel\Component' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'Nette\Application\IPresenter' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
					'application.20',
					'application.21',
				],
			],
			'ArrayAccess' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'Nette\Application\UI\IStatePersistent' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'Nette\Application\UI\ISignalReceiver' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'Nette\ComponentModel\IComponent' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'Nette\ComponentModel\IContainer' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'Nette\Application\UI\IRenderable' => [
				[
					'application.1',
					'application.2',
					'application.3',
					'application.4',
					'application.5',
					'application.6',
					'application.7',
					'application.8',
					'application.9',
					'application.10',
					'application.11',
					'application.12',
					'application.13',
					'application.14',
					'application.15',
					'application.16',
					'application.17',
					'application.18',
					'application.19',
				],
			],
			'App\AdminModule\Presenters\AdminsPresenter' => [1 => ['application.1']],
			'App\AdminModule\Presenters\CategoriesPresenter' => [1 => ['application.2']],
			'App\AdminModule\Presenters\HomepagePresenter' => [1 => ['application.3']],
			'App\AdminModule\Presenters\LanguagesPresenter' => [1 => ['application.4']],
			'App\AdminModule\Presenters\OrdersForms' => [1 => ['application.5']],
			'App\AdminModule\Presenters\OrdersPresenter' => [1 => ['application.5']],
			'App\AdminModule\Presenters\OrderStatusesPresenter' => [1 => ['application.6']],
			'App\AdminModule\Presenters\PageGalleryPresenter' => [1 => ['application.7']],
			'App\AdminModule\Presenters\PagesPresenter' => [1 => ['application.8']],
			'App\AdminModule\Presenters\ProductGalleryPresenter' => [1 => ['application.9']],
			'App\AdminModule\Presenters\ProductsPresenter' => [1 => ['application.10']],
			'App\AdminModule\Presenters\ProjectsForms' => [1 => ['application.11']],
			'App\AdminModule\Presenters\ProjectsPresenter' => [1 => ['application.11']],
			'App\AdminModule\Presenters\SettingsPresenter' => [1 => ['application.12']],
			'App\AdminModule\Presenters\SizesPresenter' => [1 => ['application.13']],
			'App\AdminModule\Presenters\SliderPresenter' => [1 => ['application.14']],
			'App\AdminModule\Presenters\VouchersPresenter' => [1 => ['application.15']],
			'App\FrontModule\Presenters\CronPresenter' => [1 => ['application.16']],
			'App\FrontModule\Presenters\HomepageForms' => [1 => ['application.17']],
			'App\FrontModule\Presenters\HomepagePresenter' => [1 => ['application.17']],
			'App\FrontModule\Presenters\SignForms' => [1 => ['application.18']],
			'App\FrontModule\Presenters\SignPresenter' => [1 => ['application.18']],
			'App\FrontModule\Presenters\XmlPresenter' => [1 => ['application.19']],
			'NetteModule\ErrorPresenter' => [1 => ['application.20']],
			'NetteModule\MicroPresenter' => [1 => ['application.21']],
			'Nette\DI\Container' => [1 => ['container']],
		],
		'services' => [
			'34_App_Model_CategoryManager' => 'App\Model\CategoryManager',
			'35_App_Model_CommonManager' => 'App\Model\CommonManager',
			'36_App_Model_LanguageManager' => 'App\Model\LanguageManager',
			'37_App_Model_OrderManager' => 'App\Model\OrderManager',
			'38_App_Model_OrderStatusManager' => 'App\Model\OrderStatusManager',
			'39_App_Model_PageManager' => 'App\Model\PageManager',
			'40_App_Model_ProductManager' => 'App\Model\ProductManager',
			'41_App_Model_ProjectManager' => 'App\Model\ProjectManager',
			'42_App_Model_TranslateManager' => 'App\Model\TranslateManager',
			'43_App_Model_UserManager' => 'App\Model\UserManager',
			'44_App_Model_VoucherManager' => 'App\Model\VoucherManager',
			'application.1' => 'App\AdminModule\Presenters\AdminsPresenter',
			'application.10' => 'App\AdminModule\Presenters\ProductsPresenter',
			'application.11' => 'App\AdminModule\Presenters\ProjectsPresenter',
			'application.12' => 'App\AdminModule\Presenters\SettingsPresenter',
			'application.13' => 'App\AdminModule\Presenters\SizesPresenter',
			'application.14' => 'App\AdminModule\Presenters\SliderPresenter',
			'application.15' => 'App\AdminModule\Presenters\VouchersPresenter',
			'application.16' => 'App\FrontModule\Presenters\CronPresenter',
			'application.17' => 'App\FrontModule\Presenters\HomepagePresenter',
			'application.18' => 'App\FrontModule\Presenters\SignPresenter',
			'application.19' => 'App\FrontModule\Presenters\XmlPresenter',
			'application.2' => 'App\AdminModule\Presenters\CategoriesPresenter',
			'application.20' => 'NetteModule\ErrorPresenter',
			'application.21' => 'NetteModule\MicroPresenter',
			'application.3' => 'App\AdminModule\Presenters\HomepagePresenter',
			'application.4' => 'App\AdminModule\Presenters\LanguagesPresenter',
			'application.5' => 'App\AdminModule\Presenters\OrdersPresenter',
			'application.6' => 'App\AdminModule\Presenters\OrderStatusesPresenter',
			'application.7' => 'App\AdminModule\Presenters\PageGalleryPresenter',
			'application.8' => 'App\AdminModule\Presenters\PagesPresenter',
			'application.9' => 'App\AdminModule\Presenters\ProductGalleryPresenter',
			'application.application' => 'Nette\Application\Application',
			'application.linkGenerator' => 'Nette\Application\LinkGenerator',
			'application.presenterFactory' => 'Nette\Application\IPresenterFactory',
			'bulkgate.connection' => 'BulkGate\Message\Connection',
			'bulkgate.sender' => 'BulkGate\Sms\Sender',
			'cache.journal' => 'Nette\Caching\Storages\IJournal',
			'cache.storage' => 'Nette\Caching\IStorage',
			'container' => 'Nette\DI\Container',
			'database.app.connection' => 'Nette\Database\Connection',
			'database.app.context' => 'Nette\Database\Context',
			'database.app.conventions' => 'Nette\Database\Conventions\DiscoveredConventions',
			'database.app.structure' => 'Nette\Database\Structure',
			'gopay.config' => 'Markette\Gopay\Config',
			'gopay.driver' => 'Markette\Gopay\Api\GopaySoap',
			'gopay.form.binder' => 'Markette\Gopay\Form\Binder',
			'gopay.gopay' => 'Markette\Gopay\Gopay',
			'gopay.helper' => 'Markette\Gopay\Api\GopayHelper',
			'gopay.service.payment' => 'Markette\Gopay\Service\PaymentService',
			'gopay.service.preAuthorizedPayment' => 'Markette\Gopay\Service\PreAuthorizedPaymentService',
			'gopay.service.recurrentPayment' => 'Markette\Gopay\Service\RecurrentPaymentService',
			'http.context' => 'Nette\Http\Context',
			'http.request' => 'Nette\Http\Request',
			'http.requestFactory' => 'Nette\Http\RequestFactory',
			'http.response' => 'Nette\Http\Response',
			'latte.latteFactory' => 'Latte\Engine',
			'latte.templateFactory' => 'Nette\Application\UI\ITemplateFactory',
			'mail.mailer' => 'Nette\Mail\IMailer',
			'mailchimp' => 'Services\MailchimpService',
			'routing.router' => 'Nette\Application\IRouter',
			'security.user' => 'Nette\Security\User',
			'security.userStorage' => 'Nette\Security\IUserStorage',
			'session.session' => 'Nette\Http\Session',
			'tracy.bar' => 'Tracy\Bar',
			'tracy.blueScreen' => 'Tracy\BlueScreen',
			'tracy.logger' => 'Tracy\ILogger',
		],
		'tags' => [
			'inject' => [
				'application.1' => true,
				'application.10' => true,
				'application.11' => true,
				'application.12' => true,
				'application.13' => true,
				'application.14' => true,
				'application.15' => true,
				'application.16' => true,
				'application.17' => true,
				'application.18' => true,
				'application.19' => true,
				'application.2' => true,
				'application.20' => true,
				'application.21' => true,
				'application.3' => true,
				'application.4' => true,
				'application.5' => true,
				'application.6' => true,
				'application.7' => true,
				'application.8' => true,
				'application.9' => true,
			],
			'nette.presenter' => [
				'application.1' => 'App\AdminModule\Presenters\AdminsPresenter',
				'application.10' => 'App\AdminModule\Presenters\ProductsPresenter',
				'application.11' => 'App\AdminModule\Presenters\ProjectsPresenter',
				'application.12' => 'App\AdminModule\Presenters\SettingsPresenter',
				'application.13' => 'App\AdminModule\Presenters\SizesPresenter',
				'application.14' => 'App\AdminModule\Presenters\SliderPresenter',
				'application.15' => 'App\AdminModule\Presenters\VouchersPresenter',
				'application.16' => 'App\FrontModule\Presenters\CronPresenter',
				'application.17' => 'App\FrontModule\Presenters\HomepagePresenter',
				'application.18' => 'App\FrontModule\Presenters\SignPresenter',
				'application.19' => 'App\FrontModule\Presenters\XmlPresenter',
				'application.2' => 'App\AdminModule\Presenters\CategoriesPresenter',
				'application.20' => 'NetteModule\ErrorPresenter',
				'application.21' => 'NetteModule\MicroPresenter',
				'application.3' => 'App\AdminModule\Presenters\HomepagePresenter',
				'application.4' => 'App\AdminModule\Presenters\LanguagesPresenter',
				'application.5' => 'App\AdminModule\Presenters\OrdersPresenter',
				'application.6' => 'App\AdminModule\Presenters\OrderStatusesPresenter',
				'application.7' => 'App\AdminModule\Presenters\PageGalleryPresenter',
				'application.8' => 'App\AdminModule\Presenters\PagesPresenter',
				'application.9' => 'App\AdminModule\Presenters\ProductGalleryPresenter',
			],
		],
		'aliases' => [
			'application' => 'application.application',
			'cacheStorage' => 'cache.storage',
			'database.app' => 'database.app.connection',
			'httpRequest' => 'http.request',
			'httpResponse' => 'http.response',
			'nette.cacheJournal' => 'cache.journal',
			'nette.database.app' => 'database.app',
			'nette.database.app.context' => 'database.app.context',
			'nette.httpContext' => 'http.context',
			'nette.httpRequestFactory' => 'http.requestFactory',
			'nette.latteFactory' => 'latte.latteFactory',
			'nette.mailer' => 'mail.mailer',
			'nette.presenterFactory' => 'application.presenterFactory',
			'nette.templateFactory' => 'latte.templateFactory',
			'nette.userStorage' => 'security.userStorage',
			'router' => 'routing.router',
			'session' => 'session.session',
			'user' => 'security.user',
		],
	];


	public function __construct(array $params = [])
	{
		$this->parameters = $params;
		$this->parameters += [
			'appDir' => 'D:\projects\htdocs\TP\siegl\app',
			'wwwDir' => 'D:\projects\htdocs\TP\siegl',
			'debugMode' => true,
			'productionMode' => false,
			'consoleMode' => false,
			'tempDir' => 'D:\projects\htdocs\TP\siegl\app/../temp',
			'mailchimp' => [
				'apiurl' => 'https://us8.api.mailchimp.com/3.0/',
				'logfile' => 'D:\projects\htdocs\TP\siegl\app/../temp/mailchimp.log',
			],
		];
	}


	public function createService__34_App_Model_CategoryManager(): App\Model\CategoryManager
	{
		$service = new App\Model\CategoryManager($this->getService('database.app.context'));
		return $service;
	}


	public function createService__35_App_Model_CommonManager(): App\Model\CommonManager
	{
		$service = new App\Model\CommonManager($this->getService('database.app.context'));
		return $service;
	}


	public function createService__36_App_Model_LanguageManager(): App\Model\LanguageManager
	{
		$service = new App\Model\LanguageManager($this->getService('database.app.context'));
		return $service;
	}


	public function createService__37_App_Model_OrderManager(): App\Model\OrderManager
	{
		$service = new App\Model\OrderManager($this->getService('database.app.context'));
		return $service;
	}


	public function createService__38_App_Model_OrderStatusManager(): App\Model\OrderStatusManager
	{
		$service = new App\Model\OrderStatusManager($this->getService('database.app.context'));
		return $service;
	}


	public function createService__39_App_Model_PageManager(): App\Model\PageManager
	{
		$service = new App\Model\PageManager($this->getService('database.app.context'));
		return $service;
	}


	public function createService__40_App_Model_ProductManager(): App\Model\ProductManager
	{
		$service = new App\Model\ProductManager($this->getService('database.app.context'));
		return $service;
	}


	public function createService__41_App_Model_ProjectManager(): App\Model\ProjectManager
	{
		$service = new App\Model\ProjectManager($this->getService('database.app.context'));
		return $service;
	}


	public function createService__42_App_Model_TranslateManager(): App\Model\TranslateManager
	{
		$service = new App\Model\TranslateManager($this->getService('database.app.context'));
		return $service;
	}


	public function createService__43_App_Model_UserManager(): App\Model\UserManager
	{
		$service = new App\Model\UserManager($this->getService('database.app.context'));
		return $service;
	}


	public function createService__44_App_Model_VoucherManager(): App\Model\VoucherManager
	{
		$service = new App\Model\VoucherManager($this->getService('database.app.context'));
		return $service;
	}


	public function createServiceApplication__1(): App\AdminModule\Presenters\AdminsPresenter
	{
		$service = new App\AdminModule\Presenters\AdminsPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__10(): App\AdminModule\Presenters\ProductsPresenter
	{
		$service = new App\AdminModule\Presenters\ProductsPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__11(): App\AdminModule\Presenters\ProjectsPresenter
	{
		$service = new App\AdminModule\Presenters\ProjectsPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__12(): App\AdminModule\Presenters\SettingsPresenter
	{
		$service = new App\AdminModule\Presenters\SettingsPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__13(): App\AdminModule\Presenters\SizesPresenter
	{
		$service = new App\AdminModule\Presenters\SizesPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__14(): App\AdminModule\Presenters\SliderPresenter
	{
		$service = new App\AdminModule\Presenters\SliderPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__15(): App\AdminModule\Presenters\VouchersPresenter
	{
		$service = new App\AdminModule\Presenters\VouchersPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__16(): App\FrontModule\Presenters\CronPresenter
	{
		$service = new App\FrontModule\Presenters\CronPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__17(): App\FrontModule\Presenters\HomepagePresenter
	{
		$service = new App\FrontModule\Presenters\HomepagePresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->paymentService = $this->getService('gopay.service.payment');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__18(): App\FrontModule\Presenters\SignPresenter
	{
		$service = new App\FrontModule\Presenters\SignPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__19(): App\FrontModule\Presenters\XmlPresenter
	{
		$service = new App\FrontModule\Presenters\XmlPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__2(): App\AdminModule\Presenters\CategoriesPresenter
	{
		$service = new App\AdminModule\Presenters\CategoriesPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__20(): NetteModule\ErrorPresenter
	{
		$service = new NetteModule\ErrorPresenter($this->getService('tracy.logger'));
		return $service;
	}


	public function createServiceApplication__21(): NetteModule\MicroPresenter
	{
		$service = new NetteModule\MicroPresenter($this, $this->getService('http.request'), $this->getService('routing.router'));
		return $service;
	}


	public function createServiceApplication__3(): App\AdminModule\Presenters\HomepagePresenter
	{
		$service = new App\AdminModule\Presenters\HomepagePresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__4(): App\AdminModule\Presenters\LanguagesPresenter
	{
		$service = new App\AdminModule\Presenters\LanguagesPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__5(): App\AdminModule\Presenters\OrdersPresenter
	{
		$service = new App\AdminModule\Presenters\OrdersPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__6(): App\AdminModule\Presenters\OrderStatusesPresenter
	{
		$service = new App\AdminModule\Presenters\OrderStatusesPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__7(): App\AdminModule\Presenters\PageGalleryPresenter
	{
		$service = new App\AdminModule\Presenters\PageGalleryPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__8(): App\AdminModule\Presenters\PagesPresenter
	{
		$service = new App\AdminModule\Presenters\PagesPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__9(): App\AdminModule\Presenters\ProductGalleryPresenter
	{
		$service = new App\AdminModule\Presenters\ProductGalleryPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->voucherManager = $this->getService('44_App_Model_VoucherManager');
		$service->userManager = $this->getService('43_App_Model_UserManager');
		$service->translateManager = $this->getService('42_App_Model_TranslateManager');
		$service->sender = $this->getService('bulkgate.sender');
		$service->projectManager = $this->getService('41_App_Model_ProjectManager');
		$service->productManager = $this->getService('40_App_Model_ProductManager');
		$service->pageManager = $this->getService('39_App_Model_PageManager');
		$service->orderStatusManager = $this->getService('38_App_Model_OrderStatusManager');
		$service->orderManager = $this->getService('37_App_Model_OrderManager');
		$service->languageManager = $this->getService('36_App_Model_LanguageManager');
		$service->commonManager = $this->getService('35_App_Model_CommonManager');
		$service->categoryManager = $this->getService('34_App_Model_CategoryManager');
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__application(): Nette\Application\Application
	{
		$service = new Nette\Application\Application(
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response')
		);
		$service->catchExceptions = false;
		$service->errorPresenter = 'Nette:Error';
		Nette\Bridges\ApplicationTracy\RoutingPanel::initializePanel($service);
		$this->getService('tracy.bar')->addPanel(new Nette\Bridges\ApplicationTracy\RoutingPanel(
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('application.presenterFactory')
		));
		return $service;
	}


	public function createServiceApplication__linkGenerator(): Nette\Application\LinkGenerator
	{
		$service = new Nette\Application\LinkGenerator(
			$this->getService('routing.router'),
			$this->getService('http.request')->getUrl(),
			$this->getService('application.presenterFactory')
		);
		return $service;
	}


	public function createServiceApplication__presenterFactory(): Nette\Application\IPresenterFactory
	{
		$service = new Nette\Application\PresenterFactory(new Nette\Bridges\ApplicationDI\PresenterFactoryCallback(
			$this,
			5,
			'D:\projects\htdocs\TP\siegl\app/../temp/cache/Nette%5CBridges%5CApplicationDI%5CApplicationExtension'
		));
		$service->setMapping(['*' => 'App\*Module\Presenters\*Presenter']);
		return $service;
	}


	public function createServiceBulkgate__connection(): BulkGate\Message\Connection
	{
		$service = new BulkGate\Message\Connection(
			3229,
			'eoCUylaaKF3bjU82CxBPaDo5WROSucB38ZSGFGmSa9JdxZm7LP',
			'https://portal.bulkgate.com/api/1.0/php-sdk',
			'nette'
		);
		return $service;
	}


	public function createServiceBulkgate__sender(): BulkGate\Sms\Sender
	{
		$service = new BulkGate\Sms\Sender($this->getService('bulkgate.connection'));
		return $service;
	}


	public function createServiceCache__journal(): Nette\Caching\Storages\IJournal
	{
		$service = new Nette\Caching\Storages\SQLiteJournal('D:\projects\htdocs\TP\siegl\app/../temp/cache/journal.s3db');
		return $service;
	}


	public function createServiceCache__storage(): Nette\Caching\IStorage
	{
		$service = new Nette\Caching\Storages\FileStorage('D:\projects\htdocs\TP\siegl\app/../temp/cache', $this->getService('cache.journal'));
		return $service;
	}


	public function createServiceContainer(): Nette\DI\Container
	{
		return $this;
	}


	public function createServiceDatabase__app__connection(): Nette\Database\Connection
	{
		$service = new Nette\Database\Connection('mysql:host=127.0.0.1;dbname=tp-siegl', 'root', null, ['lazy' => true]);
		$this->getService('tracy.blueScreen')->addPanel('Nette\Bridges\DatabaseTracy\ConnectionPanel::renderException');
		Nette\Database\Helpers::createDebugPanel($service, true, 'app');
		return $service;
	}


	public function createServiceDatabase__app__context(): Nette\Database\Context
	{
		$service = new Nette\Database\Context(
			$this->getService('database.app.connection'),
			$this->getService('database.app.structure'),
			$this->getService('database.app.conventions'),
			$this->getService('cache.storage')
		);
		return $service;
	}


	public function createServiceDatabase__app__conventions(): Nette\Database\Conventions\DiscoveredConventions
	{
		$service = new Nette\Database\Conventions\DiscoveredConventions($this->getService('database.app.structure'));
		return $service;
	}


	public function createServiceDatabase__app__structure(): Nette\Database\Structure
	{
		$service = new Nette\Database\Structure($this->getService('database.app.connection'), $this->getService('cache.storage'));
		return $service;
	}


	public function createServiceGopay__config(): Markette\Gopay\Config
	{
		$service = new Markette\Gopay\Config(8031626471, '8kEdrwFarx8DFUEcTa2kXpNh', false);
		return $service;
	}


	public function createServiceGopay__driver(): Markette\Gopay\Api\GopaySoap
	{
		$service = new Markette\Gopay\Api\GopaySoap;
		return $service;
	}


	public function createServiceGopay__form__binder(): Markette\Gopay\Form\Binder
	{
		$service = new Markette\Gopay\Form\Binder;
		return $service;
	}


	public function createServiceGopay__gopay(): Markette\Gopay\Gopay
	{
		$service = new Markette\Gopay\Gopay(
			$this->getService('gopay.config'),
			$this->getService('gopay.driver'),
			$this->getService('gopay.helper')
		);
		return $service;
	}


	public function createServiceGopay__helper(): Markette\Gopay\Api\GopayHelper
	{
		$service = new Markette\Gopay\Api\GopayHelper;
		return $service;
	}


	public function createServiceGopay__service__payment(): Markette\Gopay\Service\PaymentService
	{
		$service = new Markette\Gopay\Service\PaymentService($this->getService('gopay.gopay'));
		$service->allowChangeChannel(true);
		$service->addChannel('eu_gp_kb', 'Zaplatit');
		return $service;
	}


	public function createServiceGopay__service__preAuthorizedPayment(): Markette\Gopay\Service\PreAuthorizedPaymentService
	{
		$service = new Markette\Gopay\Service\PreAuthorizedPaymentService($this->getService('gopay.gopay'));
		$service->allowChangeChannel(true);
		$service->addChannel('eu_gp_kb', 'Zaplatit');
		return $service;
	}


	public function createServiceGopay__service__recurrentPayment(): Markette\Gopay\Service\RecurrentPaymentService
	{
		$service = new Markette\Gopay\Service\RecurrentPaymentService($this->getService('gopay.gopay'));
		$service->allowChangeChannel(true);
		$service->addChannel('eu_gp_kb', 'Zaplatit');
		return $service;
	}


	public function createServiceHttp__context(): Nette\Http\Context
	{
		$service = new Nette\Http\Context($this->getService('http.request'), $this->getService('http.response'));
		trigger_error('Service http.context is deprecated.', 16384);
		return $service;
	}


	public function createServiceHttp__request(): Nette\Http\Request
	{
		$service = $this->getService('http.requestFactory')->createHttpRequest();
		return $service;
	}


	public function createServiceHttp__requestFactory(): Nette\Http\RequestFactory
	{
		$service = new Nette\Http\RequestFactory;
		$service->setProxy([]);
		return $service;
	}


	public function createServiceHttp__response(): Nette\Http\Response
	{
		$service = new Nette\Http\Response;
		return $service;
	}


	public function createServiceLatte__latteFactory(): Nette\Bridges\ApplicationLatte\ILatteFactory
	{
		return new class ($this) implements Nette\Bridges\ApplicationLatte\ILatteFactory {
			private $container;


			public function __construct(Container_89fb4bad9e $container)
			{
				$this->container = $container;
			}


			public function create(): Latte\Engine
			{
				$service = new Latte\Engine;
				$service->setTempDirectory('D:\projects\htdocs\TP\siegl\app/../temp/cache/latte');
				$service->setAutoRefresh(true);
				$service->setContentType('html');
				Nette\Utils\Html::$xhtml = false;
				$service->onCompile[] = function ($engine) { App\Utils\Macros::install($engine->getCompiler()); };
				$service->setTempDirectory(null);
				return $service;
			}
		};
	}


	public function createServiceLatte__templateFactory(): Nette\Application\UI\ITemplateFactory
	{
		$service = new Nette\Bridges\ApplicationLatte\TemplateFactory(
			$this->getService('latte.latteFactory'),
			$this->getService('http.request'),
			$this->getService('security.user'),
			$this->getService('cache.storage'),
			null
		);
		return $service;
	}


	public function createServiceMail__mailer(): Nette\Mail\IMailer
	{
		$service = new Nette\Mail\SendmailMailer;
		return $service;
	}


	public function createServiceMailchimp(): Services\MailchimpService
	{
		$service = new Services\MailchimpService(
			[
				'apiurl' => 'https://us8.api.mailchimp.com/3.0/',
				'logfile' => 'D:\projects\htdocs\TP\siegl\app/../temp/mailchimp.log',
			],
			$this->getService('security.user')
		);
		return $service;
	}


	public function createServiceRouting__router(): Nette\Application\IRouter
	{
		$service = App\RouterFactory::createRouter();
		return $service;
	}


	public function createServiceSecurity__user(): Nette\Security\User
	{
		$service = new Nette\Security\User($this->getService('security.userStorage'), $this->getService('43_App_Model_UserManager'));
		$this->getService('tracy.bar')->addPanel(new Nette\Bridges\SecurityTracy\UserPanel($service));
		return $service;
	}


	public function createServiceSecurity__userStorage(): Nette\Security\IUserStorage
	{
		$service = new Nette\Http\UserStorage($this->getService('session.session'));
		return $service;
	}


	public function createServiceSession__session(): Nette\Http\Session
	{
		$service = new Nette\Http\Session($this->getService('http.request'), $this->getService('http.response'));
		$service->setExpiration('14 days');
		$service->setOptions(['savePath' => 'D:\projects\htdocs\TP\siegl\app/../temp/sessions', 'name' => 'polatisk']);
		return $service;
	}


	public function createServiceTracy__bar(): Tracy\Bar
	{
		$service = Tracy\Debugger::getBar();
		return $service;
	}


	public function createServiceTracy__blueScreen(): Tracy\BlueScreen
	{
		$service = Tracy\Debugger::getBlueScreen();
		return $service;
	}


	public function createServiceTracy__logger(): Tracy\ILogger
	{
		$service = Tracy\Debugger::getLogger();
		return $service;
	}


	public function initialize()
	{
		$this->getService('tracy.bar')->addPanel(new Nette\Bridges\DITracy\ContainerPanel($this));
		$this->getService('http.response')->setHeader('X-Powered-By', 'Nette Framework');
		$this->getService('http.response')->setHeader('Content-Type', 'text/html; charset=utf-8');
		$this->getService('http.response')->setHeader('X-Frame-Options', 'SAMEORIGIN');
		$this->getService('session.session')->start();
		Tracy\Debugger::$editorMapping = [];
		Tracy\Debugger::getLogger($this->getService('tracy.logger'))->mailer = [new Tracy\Bridges\Nette\MailSender($this->getService('mail.mailer'), null), 'send'];
		if ($tmp = $this->getByType("Nette\Http\Session", false)) { $tmp->start(); Tracy\Debugger::dispatch(); };
		$this->getService('tracy.bar')->addPanel(new BulkGate\Message\Bridges\MessageTracy\MessagePanel($this->getService('bulkgate.connection')));
		Markette\Gopay\DI\Helpers::registerAddPaymentButtonsUsingDependencyContainer($this, 'gopay.service');
	}
}
