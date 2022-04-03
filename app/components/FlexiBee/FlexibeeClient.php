<?php

namespace FlexibeeClient;

use FlexibeeClient\Registry;
use FlexibeeClient\Result\Factory\IResultFactory;
use FlexibeeClient\Result\Factory\ResultFactory;

class FlexibeeClient
{
    /** @var string */
    private $host;

    /** @var string */
    private $company;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var IResultFactory */
    private $resultFactory;

    public function __construct($host, $company, $username, $password, IResultFactory $resultFactory = null)
    {
        $this->host = $host;
        $this->company = $company;
        $this->username = $username;
        $this->password = $password;
        $this->resultFactory = $resultFactory ? $resultFactory : new ResultFactory();
    }

    /**
     * @param string $type
     * @return Registry
     */
    public function registry($type)
    {
        return new Registry($type, $this->host, $this->company, $this->username, $this->password, $this->resultFactory);
    }
}
