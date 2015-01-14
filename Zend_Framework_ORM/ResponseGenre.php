<?php
class Application_Model_DbTable_ResponseGenre extends Zend_Db_Table_Abstract
{
    protected $_name = 'response_genre';

    public function enterResponseGenre ($survey_key, $genre)
    {
        foreach ($genre as $response) {
            $genre_data = array('survey_id' => $survey_key, 
            'genre' => $response);
            $this->insert($genre_data);
        }
    }
    
    public function getGenreCount ($book, $category) {
        //sub_query	
        $sub_query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('survey_id')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id',array())  	
        			  ->where('s.complete = ?', 1);    

		if($book != BOOK_OVER_ALL) {  // if is not overall summary graph, then limit search by book
		        	$sub_query->where('r.book = ?', $book);
        } 
   
        //main_query
    	$genre_query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('rg' => 'response_genre'),array('survey_id')
        			        )
        			  ->join(array('sq' => $sub_query),
        			         "rg.survey_id = sq.survey_id",array('count' => 'COUNT(*)'));	      			  

        if ($category != NONE)
			$genre_query->where('rg.genre = ?', $category);
		     	
        $result = $this->fetchRow($genre_query);   			  
        return $result->count;        			  
    }
}
