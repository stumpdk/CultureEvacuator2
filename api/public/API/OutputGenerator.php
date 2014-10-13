<?php

    class OutputGenerator{
        private $result;
        public $results;
        private $returnType;
        private $output;
        
        public function __construct($result, $returntype)
        {
            $this->result = $result;
            $this->returnType = $returntype;
            
            $this->createData();
        }
        
        private function createData()
        {
            switch($this->returnType){
                case 'JSON':
                case 'json':
                    $this->createJSON();
                    break;
                case 'GeoJSON':
                case 'geojson':
                    $this->createGeoJSON();
                    break;
            }
            
            $this->outputData();
        }
        
        private function createJSON(){

            $results = array();
            $i = 0;
            while($curResult = $this->result->fetch_assoc()){
                $results[] = $curResult;
                $i++;
            }
            
            $this->results = $i;
            $this->output = $results;
        }
        
        private function startJSON()
        {
            $this->output .= '[';
        }
        
        private function endJSON()
        {
            $this->output .= ']';
        }        
        
        private function createGeoJSON()
        {
            $this->startGeoJSON();
            
            $this->endGeoJSON();
        }        
        
        private function startGeoJSON()
        {
            
        }
        
        private function endGeoJSON()
        {
        
        }
        
        private function outputData(){
            if (isset($_REQUEST["callback"])) {
                header('Content-type: text/javascript');
                print str_replace('{', '', $_REQUEST["callback"]) .'('.json_encode($this->output,JSON_UNESCAPED_UNICODE).')';
            } else {
                header('Content-type: application/json');
                print json_encode($this->output,JSON_UNESCAPED_UNICODE);
            }        
        }        
    }

?>
