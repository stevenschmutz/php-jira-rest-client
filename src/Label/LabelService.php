<?php

namespace JiraRestApi\Label;

use JiraRestApi\AgileApiTrait;
use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Issue\Issue;
use Psr\Log\LoggerInterface;

class LabelService extends \JiraRestApi\JiraClient
{
    use AgileApiTrait;

    private $uri = '/labels';

    public function __construct(ConfigurationInterface $configuration = null, LoggerInterface $logger = null, $path = './')
    {
        parent::__construct($configuration, $logger, $path);
        $this->setupAPIUri('api');
    }

    public function getAllProjects()
    {
        $xml = $this->exec($this->uri.'/suggest?query=!', null);
        return $xml;
    }
}
