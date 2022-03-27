<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Utils;

use BulkGate;

class Compress
{
	use BulkGate\Strict;

	/**
	 * @param $data
	 * @param int $encoding_mode
	 * @return string
	 */
	public static function compress($data, $encoding_mode = 9)
	{
		return (string) base64_encode(gzencode(serialize($data), (int) $encoding_mode));
	}


	/**
	 * @param string $data
	 * @return mixed
	 */
	public static function decompress($data)
	{
		return unserialize(gzdecode(base64_decode($data, true)));
	}
}
