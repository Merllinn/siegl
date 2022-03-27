<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Message\Bridges\MessageTracy;

use BulkGate;
use Tracy;

/**
 * User panel for Debugger Bar.
 */
class MessagePanel implements Tracy\IBarPanel
{
	use BulkGate\Strict;

	/** @var BulkGate\Message\IConnection */
	private $connection;

	/** @var int */
    private $count;


    /**
     * MessagePanel constructor.
     * @param BulkGate\Message\IConnection $connection
     */
	public function __construct(BulkGate\Message\IConnection $connection)
	{
		$this->connection = $connection;
	}


	/**
	 * Renders tab.
	 * @return string
	 */
	public function getTab()
	{
		if (headers_sent() && !session_id()) {
			return '';
		}

		ob_start(function () {});

		$info = $this->connection->getInfo();

		$this->count = 0;

		if (is_array($info) && count($info)) {
			foreach ($info as $i) {
				if ($i->action === 'transaction-sms') {
					$this->count++;

				} elseif ($i->action === 'bulk-sms') {
					$this->count += count($i->request['message']);
				}
			}
		}

		$count = $this->count;

		require __DIR__ . '/templates/MessagePanel.tab.phtml';

		return ob_get_clean();
	}


	/**
	 * Renders panel.
	 * @return string
	 */
	public function getPanel()
	{
		ob_start(function () {});

		$info = $this->connection->getInfo(true);

		$count = $this->count;

		require __DIR__ . '/templates/MessagePanel.panel.phtml';

		return ob_get_clean();
	}


    /**
     * @param int $status
     * @return bool
     */
	public function status($status)
    {
        return in_array($status, ['sent', 'accepted', 'scheduled']);
    }
}
