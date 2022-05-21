<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use Nette\Utils\Random;


/**
 * Users management.
 */
final class UserManager implements Nette\Security\IAuthenticator
{
	use Nette\SmartObject;

	const
		TABLE_NAME = 'users',
		PERSONS = 'persons',
		COLUMN_ID = 'id',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_LOGIN = 'login',
		COLUMN_ROLE = 'role';


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($login, $password) = $credentials;

		$row = $this->getUsers()
			->where(self::COLUMN_LOGIN, $login)
			->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('Neplatný uživatel.', self::IDENTITY_NOT_FOUND);

		} elseif (md5($password) != $row[self::COLUMN_PASSWORD_HASH]) {
			throw new Nette\Security\AuthenticationException('Neplatné heslo.', self::INVALID_CREDENTIAL);
		}


		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}

    public function getUsers(){
        return $this->database->table(self::TABLE_NAME);
    }


	public function add($values)
	{
		try {
			$this->getUsers()->insert($values);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new \App\Model\DuplicateNameException;
		}
	}

	public function update($values, $id)
	{
		try {
			$this->getUsers()
			->where("id", $id)
			->update($values);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new \App\Model\DuplicateNameException;
		}
	}

	public function delete($id)
	{
		$this->getUsers()
		->where("id", $id)
		->delete();
	}

    public function findByEmail($email)
    {
        return $this->getUsers()
            ->where("email", $email)
            ->fetch();
    }

	public function findByRole($role)
	{
		return $this->getUsers()
			->where("role >= ?", $role);
	}

	public function findByBranch($branch)
	{
		return $this->getUsers()
			->where("branch = ?", $branch);
	}

	public function find($id)
	{
		return $this->getUsers()
			->where("id", $id)
			->fetch();
	}

	public function activate($hash)
	{
		$account = $this->getUsers()->where("hash", $hash);
		if($account->fetch()){
			$account->update(array("active"=>1, "hash"=>""));
			return true;
		}
		else{
			return false;
		}
	}

	public function resetPassword($hash)
	{
		$account = $this->getUsers()->where("hash", $hash);
		$acc = $account->fetch();
		if($acc){
			$password = Random::generate(10);
			$account->update(array("password"=>md5($password), "hash"=>""));
			return array("email"=>$acc->email, "pass"=>$password);
		}
		else{
			return false;
		}
	}

    public function cleanRegistrations($days){
        $limit = new \nette\utils\DateTime();
        $limit->modify("-$days days");
        $this->getUsers()
        ->where("registered < ?", $limit)
        ->where("active", 0)
        ->delete();
    }
    
    public function getPersons(){
        return $this->database->table(self::PERSONS);
    }

    public function getActivePersons(){
        return $this->getPersons()
			->where("active = ?", true);
    }

    public function getActivePersonsByRole($role){
        return $this->getPersons()
			->where("active = ?", true)
			->where("role = ?", $role);
    }


	public function addPerson($values)
	{
		try {
			return $this->getPersons()->insert($values);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new \App\Model\DuplicateNameException;
		}
	}

	public function updatePerson($values, $id)
	{
		try {
			$this->getPersons()
			->where("id", $id)
			->update($values);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new \App\Model\DuplicateNameException;
		}
	}

	public function deletePerson($id)
	{
		$this->getPersons()
		->where("id", $id)
		->delete();
	}
	
	public function findPerson($id)
	{
		return $this->getPersons()
			->where("id", $id)
			->fetch();
	}
	
    
}



class DuplicateNameException extends \Exception
{
}

