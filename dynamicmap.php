<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dynamicmap extends CI_Controller {
    public function index()
    {
		
    }
    public function queryAbstract(){
    	
    	$id = $this->input->get('PaperID');
    	
    	$this->db_abstract = $this->load->database('abstract', TRUE);
    	$sql="SELECT Abstract FROM PaperAbstractMapped where PaperID=?";
    	$tmp=$this->db_abstract->query($sql,array($id))->result_array();
        $result['Abstract']=$tmp[0]['Abstract'];
    	//echo
    	//echo $tmp;
    	 echo json_encode($result);
    	/*if ($tmp!=null){
    		$result['Abstract']=$tmp[0]['Abstract'];

    	}
    	else $result['Abstract']=null;*/
        //$result['Abstract']=$tmp[0]['Abstract'];
    	//echo json_encode($result);

    }
    public function querypaper(){
    	$id = $this->input->get('PaperID');
    	$this->db_mag = $this->load->database('mag', TRUE); 
    	$sql="SELECT OriginalPaperTitle,PaperPublishYear FROM Papers WHERE PaperID=?";
    	$tmp=$this->db_mag->query($sql,array($id))->result_array();
    	$result['Title']=$tmp[0]['OriginalPaperTitle'];
    	$result['Year']=$tmp[0]['PaperPublishYear'];
        $result['node']='P'+$id;
    	/*$sql="SELECT AuthorName FROM PaperAuthorAffiliations INNER JOIN Authors on PaperAuthorAffiliations.PaperID=Authors.PaperID WHERE Authors.PaperID=? ORDER BY AuthorSequenceNumber";
    	$tmp=$this->db_mag->query($sql,array($id))->result_array();

    	$result['AuthorNum'] = sizeof($tmp);

    	for (var i=0;i<$result['AuthorNum'];i++){
    		$result['Author'][i]=$tmp[i]['AuthorName'];
    	}*/

        echo json_encode($result);



    }
     public function queryauthor(){
    	$id = $this->input->get('AuthorID');
    	$this->db_mag = $this->load->database('mag', TRUE); 
    	$sql="SELECT AuthorName FROM Authors WHERE AuthorID=?";
    	$tmp=$this->db_mag->query($sql,array($id))->result_array();
    	$result['Name']=$tmp[0]['AuthorName'];
    	

    	/*$sql="SELECT AuthorName FROM PaperAuthorAffiliations INNER JOIN Authors on PaperAuthorAffiliations.PaperID=Authors.PaperID WHERE Authors.PaperID=? ORDER BY AuthorSequenceNumber";
    	$tmp=$this->db_mag->query($sql,array($id))->result_array();

    	$result['AuthorNum'] = sizeof($tmp);

    	for (var i=0;i<$result['AuthorNum'];i++){
    		$result['Author'][i]=$tmp[i]['AuthorName'];
    	}*/

        echo json_encode($result);



    }
	public function expand(){
		
		
        
		$type=intval($this->input->get('type'));
		
		$id=substr($this->input->get('id'), 1);
        $index=$this->input->get('index');
       
		$this->db_mag = $this->load->database('mag', TRUE); 
		
		if ($type==0){
	
			$sql="SELECT PaperReferenceID AS PaperID, OriginalPaperTitle, PaperPublishYear FROM PaperReferences as A INNER JOIN Papers as B on A.PaperReferenceID=B.PaperID where A.PaperID=?" ;
			$refPaper=$this->db_mag->query($sql,array($id))->result_array();
			//$refPaper=array();
  			/*foreach ($result as $arr){
		    	array_push($refPaperID, $arr['PaperReferenceID']);
		    	array_push($refPaperID, $arr['PaperReferenceID']);
			}*/

			$sql="SELECT RecomID,OriginalPaperTitle, PaperPublishYear FROM PaperRecommenderList INNER JOIN Papers on PaperRecommenderList.RecomID=Papers.PaperID  where PaperRecommenderList.PaperID=?";
			$recPaper=$this->db_mag->query($sql,array($id))->result_array();
			

			$sql = "SELECT A.AuthorID, AuthorName FROM PaperAuthorAffiliations AS A  INNER JOIN Authors AS B on A.AuthorID=B.AuthorID  WHERE PaperID = ? AND AuthorSequenceNumber<=5" ;
			$refAuthor= $this->db_mag->query($sql,array($id))->result_array();
         

			$sql ="SELECT ConferenceSeriesIDMappedToVenueName,FullName FROM Papers AS A INNER JOIN ConferenceSeries AS B on  A.ConferenceSeriesIDMappedToVenueName=B.ConferenceSeriesID WHERE PaperID=?";
            $result= $this->db_mag->query($sql,array($id))->result_array();



            $refConfer=array();
			foreach ($result as $arr){
		    	if ($arr['ConferenceSeriesIDMappedToVenueName']!=null) array_push($refConfer,$arr);
			}

			$sql ="SELECT  AffiliationID, NormalizedAffiliationName FROM PaperAuthorAffiliations where PaperID=? ";
            $refAff= $this->db_mag->query($sql,array($id))->result_array();
        }   
  
        else if ($type==1){
        	
        	$sql = "SELECT A.PaperID,OriginalPaperTitle,PaperPublishYear,ConferenceSeriesIDMappedToVenueName,NormalizedAffiliationName FROM ((SELECT * FROM PaperAuthorAffiliations WHERE AuthorID =?)AS A INNER JOIN Papers on A.PaperID=Papers.PaperID )ORDER BY PaperRank LIMIT 5";
        	$result = $this->db_mag->query($sql,array($id))->result_array();

            
            $refPaper= array();
            $refConfer=array();
           // $refPaper['PaperID']=$result['PaperID'];
		    foreach ($result as $arr){
		    	
		    	if ($arr['PaperID']) {
		    		
		    		array_push($refPaper, array("PaperID"=>$arr['PaperID'], "OriginalPaperTitle"=>$arr['OriginalPaperTitle'], "PaperPublishYear"=>$arr['PaperPublishYear']));

		    	}
		    		
				if ($arr['ConferenceSeriesIDMappedToVenueName']) {
					
					array_push($refConfer, array("ConferenceSeriesIDMappedToVenueName"=>$arr['ConferenceSeriesIDMappedToVenueName'], "NormalizedAffiliationName"=>$arr['NormalizedAffiliationName']));
				}
			}
			array_unique($refPaper,SORT_REGULAR);
			array_unique($refConfer,SORT_REGULAR);
		     
		    $sql="SELECT A.AuthorID, AuthorName from Authors INNER JOIN (SELECT AuthorID,count(*) FROM PaperAuthorAffiliations WHERE PaperID in(Select PaperID FROM PaperAuthorAffiliations WHERE AuthorID=?)GROUP BY AuthorID ORDER BY count(*) desc limit 5 )A on A.AuthorID=Authors.AuthorID";
		   // $sql="SELECT AuthorID FROM PaperAuthorAffiliations WHERE (PaperID in(Select PaperID FROM PaperAuthorAffiliations WHERE AuthorID=?))";
			$result =$this->db_mag->query($sql,array($id))->result_array();
			$refAuthor= array();
			foreach ($result as $arr){
				if ($arr['AuthorID']!=$id){
			
					array_push($refAuthor,array("AuthorID"=>$arr['AuthorID'],"AuthorName"=>$arr['AuthorName']));

				}
				
			}
        }   
        else {
        	$sql="SELECT PaperID, OriginalPaperTitle,PaperPublishYear FROM Papers WHERE ConferenceSeriesIDMappedToVenueName=? ORDER BY PaperRank LIMIT 5";
        	$refPaper =$this->db_mag->query($sql,array($id))->result_array();

			

            $refAuthor=array();
           
        	foreach ($refPaper as $paper){

               $sql = "SELECT Authors.AuthorID,AuthorName FROM PaperAuthorAffiliations INNER JOIN Authors on PaperAuthorAffiliations.AuthorID=Authors.AuthorID WHERE PaperID = ? AND AuthorSequenceNumber<=3";
               $arr1 = $this->db_mag->query($sql,array($paper['PaperID']))->result_array();
               foreach ($arr1 as $arr){			
			    	array_push($refAuthor, $arr);			
			   }
              
        	}
        	array_unique($refAuthor,SORT_REGULAR);
        } 
        array_unique($refPaper,SORT_REGULAR);
		array_unique($refConfer,SORT_REGULAR);
		array_unique($refAuthor,SORT_REGULAR);
        echo json_encode(array(
	    			"fatherIndex" => $index,
	    			"refPaper" => $refPaper,
	    			"refAuthor" =>$refAuthor,
	    			"refConfer" =>[],
	    			"fatherType" =>$type
	    ));
    }

          
		
		
    	
	

    /* end: AcademicMap-new-version back-end APIs */
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
?>
