<?php
/**
 * Authorizer class.
 * Use to check whether or not the user is allowed the perform
 * a given request based on the IP and the number of requests already performed
 * 
 * @author Bo
 */

class UsagePolicy {
    
    private $requestType;
    private $internalRequestsOnly;
    private $maxNumberOfRequests;
    private $timeRange; //Time range used to calculate the start time. Number of seconds.
    
    /**
     * Constructor. Creates the policy. maxNumberOfRequests and internalRequestsOnly have default values set in the APIConfig
     * 
     * @param type $requestType
     * @param type $maxNumberOfRequests
     * @param type $internalRequestsOnly
     */
    public function __construct($requestType, $maxNumberOfRequests = -1, $internalRequestsOnly = null) {
        $this->requestType = $requestType;
        
        if($maxNumberOfRequests == -1){
            $this->maxNumberOfRequests = APIConfig::$maxNumberOfRequests;
        }
        else{
            $this->maxNumberOfRequests = $maxNumberOfRequests;
        }
        
        if(is_null($internalRequestsOnly)){
            $this->internalRequestsOnly = APIConfig::$internalRequestsOnly;
        }
        else{
            $this->internalRequestsOnly = $internalRequestsOnly;
        }
        
        $this->timeRange = APIConfig::$requestCounterTimeRange;
    }
    
    /**
     * Checks whether or not the user is allowed to perform the request
     * Based on the number of requests and the IP address of the user
     * 
     * @return boolean
     */
    public function requestAllowed()
    {
        if($this->internalRequestsOnly){
            if($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR'])
                return false;
        }

        //The number of requests is checked, but only if statistics is enabled and the max number is not unlimited (-1)
        if(APIConfig::$enableStatistics && $this->maxNumberOfRequests !== -1){
            return $this->getNumberOfRequests() < $this->maxNumberOfRequests;
        }
        
        return true;
    }
    
    /**
     * Loads the number of requests, and checks wheter or not the maximum number is exceeded
     * 
     * @return boolean
     */
    private function getNumberOfRequests()
    {
        $startTime = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s")-$this->timeRange, date("n"), date("j"), date("Y")));  
        
        $query = "SELECT COUNT(id) as requestCount FROM api_statistics WHERE ";
        
        $query = $query . "type = \"" . $this->requestType .  "\" AND ";
        
        $query = $query . "timestamp > '$startTime' ";        
        
        $query = $query . 'AND ip LIKE \'' . $_SERVER['REMOTE_ADDR'] . '\'';
        
        $queryResult = Database::getInstance()->runQueryGetAssoc($query);
        
        return $queryResult['requestCount'] < $this->maxNumberOfRequests;
    }
}

?>
