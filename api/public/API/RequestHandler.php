<?php
    /*
     * Main class for handling API requests.
     * See createQueryByRequest for details.
     */

    class RequestHandler{
        private $type;
        private $limit;
        private $outputFormat;
        private $queryBuilder;
        private $preQueries;
        private $postQueries;
        private $outputGenerator;
        private $usagePolicy;
        private $executionTime;

        /**
         * Constructor. Setting the type, output format, limit and query.
         * Starting the receive and output classes.
         */
        public function __construct(){

            error_reporting(APIConfig::$errorReportLevel);

            $timeStart = microtime(true);
            $this->type = $this->getParameter('type', 'string', true);

            //Setting the output format. JSON by default
            $outputFormat = $this->getParameter('format', 'string');
            if($outputFormat == null){
                $this->outputFormat = 'json';
            }
            else{
                $this->outputFormat = $outputFormat;
            }

            //Setting the limit
            $this->limit = 0;
            $this->limit = $this->getParameter('limit', 'int');

            $this->createQueryByRequest();

            if(!APIConfig::$debug)
                $this->getAndOutputData();

            $this->executionTime = microtime(true) - $timeStart;

            if(APIConfig::$enableStatistics)
                $this->addStatistics();

            if(APIConfig::$debug)
                $this->outputDebuggingInfo();
        }

        /**
         * Getting parameters in a safe way.
         * Unsetting $_REQUEST[$name]
         * Returning the value
         */
        private function getParameter($name, $type, $isRequired = false)
        {
            $parameter = null;
            switch($type){
                case 'int':
                    $parameter = (int)$_REQUEST[$name];
                    break;

                case 'float':
                    $parameter = (float)$_REQUEST[$name];
                    break;

                case 'date':
                case 'datetime':
                    $parameter = (float)str_replace('-', '', $_REQUEST[$name]);
                    break;

                case 'string':
                default:
                    $parameter = Database::getInstance()->makeStringSqlSafe($_REQUEST[$name]);
                    break;
            }

            if(isset($_REQUEST[$name]))
            {
                unset($_REQUEST[$name]);
                return $parameter;
            }
            else{
                if($isRequired) die('Necessary value not given');
                return null;
            }
        }

        /**
         * Chosing the query and setting the fields, joins and conditions based on the type of data required
         * This is where the queries are created!
         */
        private function createQueryByRequest()
        {
            $joins = null;
            $conditions = array();
            $orderBy = null;
            $selectType = 'AND';

            //Configuring the usage policies
            //By default the settings are set by the APIConfig class
            $this->usagePolicy = new UsagePolicy(strtolower($this->type));

            switch(strtolower($this->type))
            {
                case 'comments':

                    $conditions = array();

                    $conditions[] = new FieldCondition('post_id', 'post_id', $this->getParameter('post_id', 'int'), '=', true);
                    $conditions[] = new FieldCondition('fb_id', 'facebook_id');
                    $conditions[] = new FieldCondition('created_time', 'created_time');
                    $conditions[] = new FieldCondition('user_id', 'user_id');
                    $conditions[] = new FieldCondition('user_name', 'user_name');

                    //$joins = 'av_stam_eksemplar LEFT JOIN av_stam on av_stam_eksemplar.av_stam_id = av_stam.id LEFT JOIN metadata_version LEFT JOIN av_stam.a_id = metadata_version.id';
                    $joins = '`ce_comments`';
                    break;

                case 'posts':

                    $conditions = array();

                    $conditions[] = new FieldCondition('id', 'id', $this->getParameter('id', 'int'), '=', true);
                    $conditions[] = new FieldCondition('picture', 'picture');
                    $conditions[] = new FieldCondition('link', 'link');
                    $conditions[] = new FieldCondition('created_time', 'created_time');
                    $conditions[] = new FieldCondition('message', 'message');

                    //$joins = 'av_stam_eksemplar LEFT JOIN av_stam on av_stam_eksemplar.av_stam_id = av_stam.id LEFT JOIN metadata_version LEFT JOIN av_stam.a_id = metadata_version.id';
                    $joins = '`ce_posts`';
                    break;

                case 'comment_keywords':

                    $conditions = array();

                    $conditions[] = new FieldCondition('comments.id', 'comment_id', $this->getParameter('id', 'int'), '=', true);
                    $conditions[] = new FieldCondition('keyword', 'keyword', $this->getParameter('keyword', 'string'), 'LIKE', true);

                    //$joins = 'av_stam_eksemplar LEFT JOIN av_stam on av_stam_eksemplar.av_stam_id = av_stam.id LEFT JOIN metadata_version LEFT JOIN av_stam.a_id = metadata_version.id';
                    $joins = '`keywords left join keywords_comments on keywords.id = keywords_comments.keyword_id LEFT JOIN comments on keywords_comments.comment_id = comments.id`';
                    break;

                default:
                    die('Service not supported');
                    break;
            }

            //If the request is not allowed according to the given usage policy, the request is stopped
            if(!$this->usagePolicy->requestAllowed()){
                //echo json_encode(array('error' => 'No more requests allowed by this type and IP'));
                die('Request not allowed');
            }

            if($selectType == null)
                $selectType = 'AND';

            //Constructing the query
            $this->queryBuilder = new QueryBuilder($conditions, $joins, $this->limit, $groupBy, $orderBy, $selectType);
        }

        /**
         * Running the queries and returning the data in the choosen format
         */
        private function getAndOutputData()
        {
            Database::getInstance()->runQueryQueue($this->preQueries);
            $this->outputGenerator = new OutputGenerator(Database::getInstance()->runQueryGetResult($this->queryBuilder->sqlQuery), $this->outputFormat);
            Database::getInstance()->runQueryQueue($this->postQueries);
        }

        /**
         * Saving statistics
         */
        private function addStatistics()
        {
            $Statistics = new Statistics();
            $Statistics->addRequestEntry($this->type, $this->queryBuilder->toJSON(), $this->executionTime, $this->outputGenerator->results);
        }

        /**
         * Output debug information
         */
        private function outputDebuggingInfo(){
            var_dump($this);
            //var_dump($this->queryBuilder->sqlQuery);
            echo 'Query explained: <br>';
            var_dump( Database::getInstance()->runQueryGetAssocList('EXPLAIN ' . $this->queryBuilder->sqlQuery) );
            die();
        }
    }

?>
