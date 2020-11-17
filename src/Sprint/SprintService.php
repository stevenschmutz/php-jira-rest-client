<?php
/**
 * Created by PhpStorm.
 * User: meshulam
 * Date: 11/08/2017
 * Time: 17:28.
 */

namespace JiraRestApi\Sprint;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Issue\Issue;
use JiraRestApi\JiraClient;
use JiraRestApi\JiraException;
use Psr\Log\LoggerInterface;

class SprintService extends JiraClient
{
    //https://jira01.devtools.intel.com/rest/agile/1.0/board?projectKeyOrId=34012
    private $uri = '/sprint';

    public function __construct(ConfigurationInterface $configuration = null, LoggerInterface $logger = null, $path = './')
    {
        parent::__construct($configuration, $logger, $path);
        $this->setAPIUri('/rest/agile/1.0');
    }

    /**
     * @param object $json JSON object structure from json_decode
     *
     * @throws \JsonMapper_Exception
     *
     * @return Sprint
     */
    public function getSprintFromJSON($json)
    {
        $sprint = $this->json_mapper->map(
            $json,
            new Sprint()
        );

        return $sprint;
    }

    /**
     *  get all Sprint list.
     *
     * @param string $sprintId
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Sprint
     */
    public function getSprint(string $sprintId)
    {

    try {

        $ret = $this->exec($this->uri.'/'.$sprintId, null);
        $this->log->info("Result=\n".$ret);
        
        if(is_null($ret)) {
            return false;
        }

        $sprint = $this->json_mapper->map(
            json_decode($ret), new Sprint()
        );
    
        return $sprint;
    } catch (JiraRestApi\JiraException $e) {
       $this->log->error("Error Occured! " . $e->getMessage());
    }
    }

    /**
     * @param string|int $sprintId
     * @param array      $paramArray
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Issue[] array of Issue
     */
    public function getSprintIssues($sprintId, $paramArray = [])
    {
    
        try {
            $json = $this->exec($this->uri.'/'.$sprintId.'/issue'.$this->toHttpQueryParameter($paramArray), null);
            $issues = $this->json_mapper->mapArray(
            json_decode($json)->issues,
                new \ArrayObject(),
                Issue::class
            );

            return $issues;
        
        } catch (JiraException $e) {
			$this->log->error("Error Occured! " . $e->getMessage());
			return FALSE;
	}
        
    }


    public function returnSprintsForBoard($boardID)
    {
        //GET /rest/agile/1.0/board/{boardId}/sprint
        $json = $this->exec($this->uri.'/board/'.$boardID.'/sprint', null);
        $sprints = json_decode($json);
        return $sprints;
    }

    public function addIssueToSprint($sprintId, $issueArray) 
    {

        $postData = json_encode([
            'issues' => $issueArray,
        ], JSON_UNESCAPED_UNICODE);

        $this->log->info("Issue to add=\n".$postData);
        $ret = $this->exec($this->uri."/$sprintId/issue/", $postData, 'POST');
        
        return json_decode($ret);    
    } 

    public function createSprint($sprintName, $boardID) 
    {

        $postData = json_encode([
            'name' => $sprintName,
            'originBoardId'=> (int)$boardID
        ], JSON_NUMERIC_CHECK);
        $ret = $this->exec($this->uri, $postData, 'POST');

        return $sprint = $this->json_mapper->map(
            json_decode($ret), new Sprint()
        );
    
    } 
}
