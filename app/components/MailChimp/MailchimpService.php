<?php

namespace Services;

use Nette\InvalidStateException;

/** Subscribes and deletes list member
 * 
 * @author Pavel Zbytovský 2015
 * @link https://github.com/zbycz/mailchimp-v3-php
 * @license MIT
 *
 * @testcase
$mail = 'zbycz@example.com';
dump($container->mailchimp->getStatus($mail) == FALSE);
dump($container->mailchimp->subscribe($mail, 'jm', 'př') == NULL);
dump($container->mailchimp->getStatus($mail) == 'subscribed');
dump($container->mailchimp->delete($mail) == NULL);
dump($container->mailchimp->getStatus($mail) == FALSE);
 */
class MailchimpService
{

	protected $apiurl;
	protected $apikey;
	protected $list;
	protected $logfile;
	protected $userid;

	public function __construct($params, $user)
	{
		$this->apiurl = rtrim($params['apiurl'], '/');
		$this->apikey = $params['apikey'];
		$this->list = $params['list'];
		$this->logfile = $params['logfile'];

		$this->userid = $user->id;
	}

	/** Subscribe new email (or updates existing subscriber status+name)
	 * @param string $email
	 * @param string $fname
	 * @param string $lname
	 */
	public function subscribe($email, $fname, $lname, $_rerun = false)
	{
		$status = $this->getStatus($email);

		if (!$status) //add new
		{
			$this->apiCall('POST', "/lists/$this->list/members", array(
				"email_address" => $email,
				"merge_fields" => array("FNAME" => $fname, "LNAME" => $lname),
				"status" => "subscribed",
			));
		}
		//status is a string, but not the right one
		elseif ($status != 'subscribed')
		{
			$this->apiCall('PATCH', "/lists/$this->list/members/" . md5($email), array(
				"email_address" => $email,
				"merge_fields" => array("FNAME" => $fname, "LNAME" => $lname),
				"status" => "subscribed",
			));
		}
		else //status wrong (ie. pending)  -> delete mail and re-add it
		{
			if ($_rerun)
			{
				throw new InvalidStateException("Error in Mailchimp#subscribe($email) - after delete status didn't emit 404");
   		}
		
			$this->delete($email);
			$this->subscribe($email, $fname, $lname, true);
		}
	}

	/** Unsubscribe email (if doesnt exist, nothing happens)
	 * @param $email
	 */
	public function unsubscribe($email)
	{
		//can return 404, but doesnt matter
		$this->apiCall('PATCH', "/lists/$this->list/members/" . md5($email), array(
			"status" => "unsubscribed",
		));
	}

	/** Deletes email user (CAREFUL!! better unsubscibe him)
	 * @param $email
	 */
//	public function delete($email)
//	{
//		//can return 404, but doesnt matter
//		$this->apiCall('DELETE', "/lists/$this->list/members/" . md5($email));
//	}

	/** Get status of non-exist or subscribed|unsubscribed|pending|cleaned
	 * @param $email
	 * @return false|string
	 */
	public function getStatus($email)
	{
		$ret = $this->apiCall('GET', "/lists/$this->list/members/" . md5($email));

		if ($ret->code == 404)
		{
			return false;
		}

		return $ret->response->status;
	}

	/** Make custom api call
	 * @param string $method POST or GET
	 * @param string $resource starting with /
	 * @param array $body sent as JSON
	 * @return object(code, response)
	 */
	public function apiCall($method, $resource, $body = NULL)
	{
		$json = $body ? json_encode($body) : '';

		//construct request
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->apiurl . $resource);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERPWD, "apikey:" . $this->apikey);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if (in_array($method, array('POST', 'PATCH', 'DELETE')))
		{
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		}

		if ($body)
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		}

		//exec
		$res = curl_exec($ch);
		$log = date('Y-m-d H:i:s') . " (uid=" . $this->userid . ") $method $resource" . str_replace("\n", "", $json);

		//curl error - connection, and such
		if ($res === false)
		{
			$err = curl_error($ch);
			file_put_contents($this->logfile, "$log >>> curl error: $err\n", FILE_APPEND);
			throw new InvalidStateException("Mailchimp request failed. Reason: $err");
		}

		//response OK - build returned object
		$return = (object)array(
			'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
			'response' => json_decode($res),
		);
		curl_close($ch);
		
		//log request&response
		$response = str_replace("\n", "", $res);
		file_put_contents($this->logfile, "$log >>> $return->code $response\n", FILE_APPEND);

		//mailchimp error
		if ($return->code == 401)
		{
			throw new InvalidStateException("Mailchimp request failed. Reason: 401 {$return->response->detail}");
		}

		return $return;
	}
}

