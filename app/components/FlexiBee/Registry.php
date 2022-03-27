<?php

namespace FlexibeeClient;

use FlexibeeClient\Result\Factory\IResultFactory;
use FlexibeeClient\Result\Result;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Registry
{
    /** @var string */
    private $type;

    /** @var string */
    private $host;

    /** @var string */
    private $company;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var Client */
    private $client;

    /** @var string */
    private $outputFormat = 'json';

    /** @var string */
    private $filter;

    /** @var array */
    private $parameters = [];

    const TYPE_ADDRESS = 'adresar';
    const TYPE_JOB = 'zakazka';
    const TYPE_ORDER_IN = 'objednavka-prijata';

    /**
     * @param $type
     * @param $host
     * @param $company
     * @param $username
     * @param $password
     */
    public function __construct($type, $host, $company, $username, $password, IResultFactory $resultFactory)
    {
        $this->type = $type;
        $this->host = $host;
        $this->company = $company;
        $this->username = $username;
        $this->password = $password;
        $this->resultFactory = $resultFactory;
    }

    /**
     * @param bool|null $dryRun
     * @return Registry
     */
    public function setDryRun($dryRun = true)
    {
        return $this->setParameter('dry-run', $dryRun, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param bool|null $failOnWarning
     * @return Registry
     */
    public function setFailOnWarning($failOnWarning = true)
    {
        return $this->setParameter('fail-on-warning', $failOnWarning, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param string|null $reportName
     * @return Registry
     */
    public function setReportName($reportName)
    {
        return $this->setParameter('report-name', $reportName);
    }

    /**
     * @param string|null $reportLang
     * @return Registry
     */
    public function setReportLang($reportLang)
    {
        return $this->setParameter('report-lang', $reportLang);
    }

    /**
     * @param boolean|null $reportSign
     * @return Registry
     */
    public function setReportSign($reportSign = true)
    {
        return $this->setParameter('report-sign', $reportSign);
    }

    /**
     * @param string|null $detail
     * @return Registry
     */
    public function setDetail($detail)
    {
        return $this->setParameter('detail', $detail);
    }

    /**
     * @param string $mode
     * @return Registry
     */
    public function setMode($mode)
    {
        return $this->setParameter('mode', $mode);
    }

    /**
     * @param int|null $limit
     * @return Registry
     */
    public function setLimit($limit)
    {
        return $this->setParameter('limit', $limit, function ($value) {
            return (int) $value;
        });
    }

    /**
     * @param int|null $start
     * @return Registry
     */
    public function setStart($start)
    {
        return $this->setParameter('start', $start, function ($value) {
            return (int) $value;
        });
    }

    /**
     * @param string $order
     * @return Registry
     */
    public function setOrder($order)
    {
        return $this->setParameter('order', $order);
    }

    /**
     * @param string $sort
     * @return Registry
     */
    public function setSort($sort)
    {
        return $this->setParameter('sort', $sort);
    }

    /**
     * @param bool|null $addRowCount
     * @return Registry
     */
    public function setAddRowCount($addRowCount = true)
    {
        return $this->setParameter('add-row-count', $addRowCount, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param array|null $relations
     * @return Registry
     */
    public function setRelations($relations)
    {
        return $this->setParameter('relations', $relations);
    }

    /**
     * @param string|null $includes
     * @return Registry
     */
    public function setIncludes($includes)
    {
        return $this->setParameter('includes', $includes);
    }

    /**
     * @param string|null $useExtId
     * @return Registry
     */
    public function setUseExtId($useExtId)
    {
        return $this->setParameter('use-ext-id', $useExtId);
    }

    /**
     * @param bool|null $useInternal
     * @return Registry
     */
    public function setUseInternal($useInternal = true)
    {
        return $this->setParameter('use-internal', $useInternal, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param bool|null $labelsAs
     * @return Registry
     */
    public function setLabelsAs($labelsAs = true)
    {
        return $this->setParameter('stitky-as-ids', $labelsAs, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param bool|null $onlyExtIds
     * @return Registry
     */
    public function setOnlyExtIds($onlyExtIds = true)
    {
        return $this->setParameter('only-ext-ids', $onlyExtIds, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param bool|null $noExtIds
     * @return Registry
     */
    public function setNoExtIds($noExtIds = true)
    {
        return $this->setParameter('no-ext-ids', $noExtIds, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param bool|null $noIds
     * @return Registry
     */
    public function setNoIds($noIds = true)
    {
        return $this->setParameter('no-ids', $noIds, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param bool|null $codeAsId
     * @return Registry
     */
    public function setCodeAsId($codeAsId = true)
    {
        return $this->setParameter('code-as-id', $codeAsId, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param bool|null $noHttpErrors
     * @return Registry
     */
    public function setNoHttpErrors($noHttpErrors = true)
    {
        return $this->setParameter('no-http-errors', $noHttpErrors, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param bool|null $exportSettings
     * @return Registry
     */
    public function setExportSettings($exportSettings = true)
    {
        return $this->setParameter('export-settings', $exportSettings, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param bool|null $asGui
     * @return Registry
     */
    public function setAsGui($asGui = true)
    {
        return $this->setParameter('as-gui', $asGui, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param bool|null $codeInResponse
     * @return Registry
     */
    public function setCodeInResponse($codeInResponse = true)
    {
        return $this->setParameter('code-in-response', $codeInResponse, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param bool|null $addGlobalVersion
     * @return Registry
     */
    public function setAddGlobalVersion($addGlobalVersion = true)
    {
        return $this->setParameter('add-global-version', $addGlobalVersion, function ($value) {
            return (bool) $value;
        });
    }

    /**
     * @param string|null $encoding
     * @return Registry
     */
    public function setEncoding($encoding)
    {
        return $this->setParameter('encoding', $encoding);
    }

    /**
     * @param string|null $delimiter
     * @return Registry
     */
    public function setDelimiter($delimiter)
    {
        return $this->setParameter('delimiter', $delimiter);
    }

    /**
     * @param string|null $format
     * @return Registry
     */
    public function setFormat($format)
    {
        return $this->setParameter('format', $format);
    }

    /**
     * @param string|null $auth
     * @return Registry
     */
    public function setAuth($auth)
    {
        return $this->setParameter('auth', $auth);
    }

    /**
     * @param string|null $labelGroups
     * @return Registry
     */
    public function setLabelGroups($labelGroups)
    {
        return $this->setParameter('skupina-stitku', $labelGroups);
    }

    /**
     * @param string $outputFormat
     * @return Registry
     */
    public function setOutputFormat($outputFormat)
    {
        $this->outputFormat = $outputFormat;
        return $this;
    }

    /**
     * @param string $filter
     * @return Registry
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Create endpoint
     * @param array $data
     * @return Result
     */
    public function callCreate(array $data)
    {
        $url = $this->getBaseUrl() . '.' . $this->outputFormat . $this->getAppendUrl();

        $request = new Request('PUT', $url);
        $response = $this->getClient()->send($request, array_merge($this->getBaseRequestOptions(), ['json' => [
            'flexibee' => [
                $this->type => $data
            ]
        ]]));

        return $this->resultFactory->createResult($this->outputFormat, $response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * Update endpoint
     * @param int $id
     * @param array $data
     * @return Result
     */
    public function callUpdate($id, array $data)
    {
        $url = $this->getBaseUrl() . '/' . $id . '.' . $this->outputFormat . $this->getAppendUrl();
        $request = new Request('PUT', $url);
        $response = $this->getClient()->send($request, array_merge($this->getBaseRequestOptions(), ['json' => [
            'flexibee' => [
                $this->type => $data
            ]
        ]]));

        return $this->resultFactory->createResult($this->outputFormat, $response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * Detail endpoint
     * @param int $id
     * @return Result
     */
    public function callDetail($id)
    {
        $url = $this->getBaseUrl() . '/' . $id . '.' . $this->outputFormat . $this->getAppendUrl();
        $request = new Request('GET', $url);
        $response = $this->getClient()->send($request, $this->getBaseRequestOptions());
        return $this->resultFactory->createResult($this->outputFormat, $response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * Relations endpoint
     * @param null|int $id
     * @param null|string $type
     * @return Result
     */
    public function callRelations($id = null, $type = null)
    {
        if ($id && $type) {
            $url = $this->getBaseUrl() . '/' . $id . '/' . $type . '.' . $this->outputFormat . $this->getAppendUrl();
        } else {
            $url = $this->getBaseUrl() . '/relations.' . $this->outputFormat . $this->getAppendUrl();
        }

        $request = new Request('GET', $url);
        $response = $this->getClient()->send($request, $this->getBaseRequestOptions());
        return $this->resultFactory->createResult($this->outputFormat, $response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * Reports endpoint
     * @return Result
     */
    public function callReports()
    {
        $url = $this->getBaseUrl() . '/reports.' . $this->outputFormat . $this->getAppendUrl();
        $request = new Request('GET', $url);
        $response = $this->getClient()->send($request, $this->getBaseRequestOptions());
        return $this->resultFactory->createResult($this->outputFormat, $response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * Registry properties endpoint
     * @return Result
     */
    public function callProperties()
    {
        $url = $this->getBaseUrl() . '/properties.' . $this->outputFormat . $this->getAppendUrl();
        $request = new Request('GET', $url);
        $response = $this->getClient()->send($request, $this->getBaseRequestOptions());
        return $this->resultFactory->createResult($this->outputFormat, $response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * Listing endpoint
     * @return Result
     */
    public function callList()
    {
        $url = $this->getBaseUrl();
        if ($this->filter) {
            $url .= '/(' . $this->filter . ')';
        }
        $url .= '.' . $this->outputFormat . $this->getAppendUrl();

        $request = new Request('GET', $url);
        $response = $this->getClient()->send($request, $this->getBaseRequestOptions());
        return $this->resultFactory->createResult($this->outputFormat, $response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * Count endpoint
     * @return Result
     */
    public function callCount()
    {
        $url = $this->getBaseUrl();
        if ($this->filter) {
            $url .= '/(' . $this->filter . ')';
        }
        $url .= '/$sum.' . $this->outputFormat . $this->getAppendUrl();

        $request = new Request('GET', $url);
        $response = $this->getClient()->send($request, $this->getBaseRequestOptions());
        return $this->resultFactory->createResult($this->outputFormat, $response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * Build base registry url
     * @return string
     */
    private function getBaseUrl()
    {
        return $this->host . '/c/' . $this->company . '/' . $this->type;
    }

    /**
     * Build query string
     * @return null|string
     */
    private function getAppendUrl()
    {
        if (!count($this->parameters)) {
            return null;
        }

        return '?' . http_build_query($this->parameters);
    }

    /**
     * @return array
     */
    private function getBaseRequestOptions()
    {
        return [
            'auth' => [$this->username, $this->password],
            'verify' => false,
            'http_errors' => false
        ];
    }

    /**
     * @return Client
     */
    private function getClient()
    {
        if (!$this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param callable|null $handle
     * @return Registry
     */
    private function setParameter($key, $value, $handle = null)
    {
        if ($value === null) {
            unset($this->parameters[$key]);
            return $this;
        }

        $handledValue = is_callable($handle) ? $handle($value) : $value;
        $this->parameters[$key] = $handledValue;
        return $this;
    }
}
