<?php
    class clsSalesFileProcessor{
        private $baseDir='C:/Users/Joseph/Documents/Quiznos/VB Code/ReadPos/';
        private $filePath='';
        private $fileData='test';

        // array of end of receipt Tags
        private $endOfReceipt=array(
        "\x1Bi\x1Bc4\x1",                //End of most of the receipts, Note: has to be double qouted due to the special characters
        'STORE NAME: 00991       \r\n', // End of each segment of the "X1 TRANSACTION LOG REPORT"
        'STORE NAME: Quiznos     \r\n'   //older receipts !
        );

        public function __construct(){

        }

        /**
        * gets the breakdown of the receipts in an array
        * @return array() the break down of all the receipts
        */
        public function getReceiptsArray($receiptDate){
          if (!$receiptDate){die('Receipt Date Is Missing!');}
          if (!preg_match('/(0[1-9]|[1-2][0-9]|3[0-1])(0[1-9]|1[0-2])20[0-9][0-9]/', $receiptDate)){die('Receipt Date Format Error (Should be \'ddmmyyyy\'!');}
            $pattern='/(\x1B(!|r))|(\x1D\/\x00)/';
            $cleanData= preg_replace($pattern, '', $this->fileData);
            
            //adding a cut tag at the end of each part in the "X1 TRANSACTION LOG REPORT" 
            $pattern='/(STORE NAME: 00991       \r\n)|(STORE NAME: Quiznos     \r\n)/';
            $cleanData= preg_replace($pattern, "$0\r\n\x1Bi\x1Bc4\x1", $cleanData);
            
            //cutting the data to array
            $receipts=preg_split('/(\x1Bi\x1Bc4\x1)/', $cleanData); 
            
            /////////////////
            //filtering certain receipts:
            $pattern='/         Quizno\'s Subs Store #991       .* ORDER #/s';
            $receipts=preg_grep($pattern,$receipts) ;
            
            $pattern='/CANCEL|COPY/';
            $receipts=preg_grep($pattern,$receipts,PREG_GREP_INVERT) ; 
           // $receipts=preg_grep($pattern,$receipts) ;             
            
            
//            //$pattern='/TOTAL              \$[ ]{5,8}[0-9]{1,3}\.[0-9]{1,2}/'; 
//            $pattern='/                        \$[ ]{5,8}[-]{0,1}[0-9]{1,3}\.[0-9]{1,2}/';
//            foreach($receipts as $key=>$value){
//             if (preg_match($pattern,$value,$match[0])){
//              //$receipts[$key]=preg_replace('/TOTAL              \$[ ]{5,8}/','',$match[0][0]);
//              $receipts[$key]=preg_replace('/                        \$[ ]{5,8}/','',$match[0][0]);      
//             } ;          
//            }
//            
//            echo 'Total:',array_sum($receipts);
        return $receipts;
        }

        /**
        * to set the $filePath also read and set the file the $fileData
        * @param string $filePath
        */ 
        public function __set($property,$value){
            switch ($property){
                case 'filePath':  
                    if (!file_exists($value)){
                        //We should handel this error somehow
                        //redirecting to an error page for instance
                        return false;  
                    }
                    $this->$property=$value;
                    $this->fileData=file_get_contents($value);
                    break;
                case 'fileData':
                    break; 
            }    
        }

        public function __get($property){ 
            if (property_exists($this,$property)){
                return $this->$property;
            }   
        } 

        /**
        * process the data in $fileData and returns a collection of receipts
        * @returns clsKeyCountCollection
        */
        private function createReceiptCollection(){

        }
 
    }

    $pr=new clsSalesFileProcessor();
    $pr->filePath='C:\Users\Joseph\Documents\Quiznos\VB Code\ReadPos\Nov 23 2010.txt';
    //echo $pr->fileData ;
    //echo strpos($pr->fileData, "STORE NAME: 00991       \r\n")7
    $receipts=$pr->getReceiptsArray();
    foreach ($receipts as $receipt){
    echo '<pre>'.$receipt.'</pre><hr>';}
?>
