<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Message\Bridges\MessageDI;

use Nette, BulkGate;


/**
 * Message extension for Nette DI.
 */
class MessageExtension extends Nette\DI\CompilerExtension
{
	public $defaults = [
		'application_id' => null,
		'application_token' => null,
        'api_version' => '1.0',
        'api_type' => 'php-sdk'
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		$builder->addDefinition($this->prefix('connection'))
            ->setAutowired("BulkGate\\Message\\IConnection")
            ->setFactory("BulkGate\\Message\\Connection", [
                'application_id' => $config['application_id'],
                'application_token' => $config['application_token'],
                'api' => 'https://portal.bulkgate.com/api/'.$config['api_version'].'/'.$config['api_type'],
                'application_product' => 'nette'
            ]);

		if(class_exists('BulkGate\Sms\Sender'))
        {
            $builder->addDefinition($this->prefix('sender'))
                ->setAutowired('BulkGate\Sms\ISender')
                ->setFactory('BulkGate\Sms\Sender');
        }
    }


	public function afterCompile(Nette\PhpGenerator\ClassType $class)
	{
		$init = $class->getMethod('initialize');

		$line = "\$this->getService('tracy.bar')->addPanel(new BulkGate\\Message\\Bridges\\MessageTracy\\MessagePanel(\$this->getService('" . $this->prefix('connection') . "')));";

		$init->addBody($line);
	}
}
