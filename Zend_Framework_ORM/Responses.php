<?php
/**
 * DB access methods (Zend Framework)
 *
 *
 * @package Application_Model_DbTable_Responses
 * @author Mark Wong
 */

class Application_Model_DbTable_Responses extends Zend_Db_Table_Abstract
{
    protected $_name = 'responses';
    protected $_primary = 'response_id';
    
    const grade_query = "r.age = 'Grade 1' OR
        				   r.age = 'Grade 2' OR
        				   r.age = 'Grade 3' OR
        				   r.age = 'Grade 4' OR
        				   r.age = 'Grade 5' OR
        				   r.age = 'Grade 6' OR
        				   r.age = 'Grade 7' OR
        				   r.age = 'Grade 8'";

    public function getAdultHowBookImprove($book) {
		$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('opinion')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id')  	
        			  ->where('s.complete = ?', 1)
        			  ->where('r.teacher != ?', 'Yes')
        			  ->where('r.age = ?', 'Adult')
        			  ->where('r.book = ?', $book); 
			          
       $row_set = $this->fetchAll($query);
       return $row_set;  			          	          
    }		
    
    
    public function getTeacherHowBookImprove($book) {
		$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('opinion','grade')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id')  	
        			  ->where('s.complete = ?', 1)
        			  ->where('r.teacher = ?', 'Yes' )
        			  ->where('r.book = ?', $book); 
			          
       $row_set = $this->fetchAll($query);
       return $row_set;  			          	          
    }		
	
    public function getTeacherInterestedFiction() {
		$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('future')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id')  	
        			  ->where('s.complete = ?', 1)
        			  ->where('r.teacher = ?', 'Yes' ); 
			          
       	$row_set = $this->fetchAll($query);
       	return $row_set;  			          	          
    }		
	
    public function getFavoriteBooks() {
		$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('justread','gender','age')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id')  	
        			  ->where('s.complete = ?', 1); 
			          
        $row_set = $this->fetchAll($query);
       	return $row_set;  			          	          
    }	
    	
    public function getLikeBest($book) {
		$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('gender','age','best')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id')  	
        			  ->where('s.complete = ?', 1)
        			  ->where('r.book = ?', $book); 
			          
        $row_set = $this->fetchAll($query);
		return $row_set;  			          	          
    }	
	
    public function getQuestions($book) {
		$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('gender','age','questions')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id')  	
        			  ->where('s.complete = ?', 1)
        			  ->where('r.book = ?', $book); 
			          
        $row_set = $this->fetchAll($query);
		return $row_set;  			          	          
    }
    // for % book cover influenced book sale graphs	
    public function getBookCoverOpinionCount($book, $category, $yes_flag = NO) {
		$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('survey_id')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id',
        			  		 array('count' => 'COUNT(*)'))  	
        			  ->where('s.complete = ?', 1);    	

		if($book != BOOK_OVER_ALL) {  // if is not overall summary graph, then limit search by book
        	$query->where('r.book = ?', $book);
        }        			  

        if($yes_flag == YES)
            $query->where('r.cover = ?', 'Yes');
        
        if ($category == UNDER18) {
        	$query->where("r.age != 'Adult'");
        } elseif ($category == ADULT) {
        	$query->where('r.age = ?', 'Adult');    
        }
            
        $result = $this->fetchRow($query);
        return $result->count;        			  
    }		
    // for helpfulness of fact/fiction section graphs
    public function getAverageHelpfulness($book, $category) {
		if ($category == AVERAGE_GRADE) 
			$calculation_arr = array('average_grade' => new Zend_Db_Expr('AVG(RIGHT(r.age,1))'));
		else 
			$calculation_arr = array('average_helpfulness' => new Zend_Db_Expr('AVG(r.helpful)'));
    	
    	$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id',
        			  		 $calculation_arr)
        			  ->where('s.complete = ?', 1);    

		if($book != BOOK_OVER_ALL) {  // if is not overall summary graph, then limit search by book
		        	$query->where('r.book = ?', $book);
        }
        
	    switch ($category) {
	    	
		case BOYS_AVERAGE:
	    	$query->where("r.gender = ?", 'Male');
	        $query->where(self::grade_query);  
	        break;
	    case GIRLS_AVERAGE:
	        $query->where("r.gender = ?", 'Female');
	        $query->where(self::grade_query);
	        break;
	    case AVERAGE_GRADE:
	        $query->where(self::grade_query);
	        break;
	    case TEACHER_AVERAGE:
	    	$query->where("r.teacher = ?", 'Yes');
	    	break;
	    case OVERALL_AVERAGE:
	        $query->where('(' . self::grade_query . " AND (r.gender = 'Male' OR r.gender = 'Female')) OR r.teacher = 'Yes'"  );
			break;
	    }       
        
        $result = $this->fetchRow($query);
        
        if($category == AVERAGE_GRADE) {
        	$value = $result->average_grade;
        } else {
        	$value = $result->average_helpfulness;
        }
        return $value;
    }	
	
    public function getFavoriteCharacters($book) {
		$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('character')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id',
        			  		 array('char_count' => 'COUNT(`character`)'))  	
        			  ->group('character')
        			  ->order('char_count DESC')
        			  ->limit(2)
        			  ->where('s.complete = ?', 1);  
        			  
    		if($book != BOOK_OVER_ALL) {  // if is not overall summary graph, then limit search by book
        		$query->where('r.book = ?', $book);
    		}     			  
      	$result = $this->fetchAll($query);
        return $result; 
    }

    // for % would read other books graphs	
    public function getReadOtherBooksCount($book, $category, $yes_flag = NO) {
		$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('survey_id')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id',
        			  		 array('count' => 'COUNT(*)'))  	
        			  ->where('s.complete = ?', 1);    	

		if($book != BOOK_OVER_ALL) {  // if is not overall summary graph, then limit search by book
        	$query->where('r.book = ?', $book);
        }        			  

        if($yes_flag == YES)
            $query->where('r.read = ?', 'Yes');
        
        if ($category == UNDER18) {
        	$query->where("r.age != 'Adult'");
        } elseif ($category == ADULT) {
        	$query->where('r.age = ?', 'Adult');    
        }
            
        $result = $this->fetchRow($query);
        return $result->count;        			  
    }	
    // for % Recommended graphs
    public function getRecommendCount($book, $category, $yes_flag = NO) {
		$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('survey_id')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id',
        			  		 array('count' => 'COUNT(*)'))  	
        			  ->where('s.complete = ?', 1);    	

		if($book != BOOK_OVER_ALL) {  // if is not overall summary graph, then limit search by book
        	$query->where('r.book = ?', $book);
        }        			  

        if($yes_flag == YES)
            $query->where('r.recommend = ?', 'Yes');
        
        if ($category == UNDER18) {
        	$query->where("r.age != 'Adult'");
        } elseif ($category == ADULT) {
        	$query->where('r.age = ?', 'Adult');    
        }
            
        $result = $this->fetchRow($query);
        return $result->count;        			  
    }
    // for average ratings graphs
    public function getAverageRatings($book, $category) {
		if ($category == AVERAGE_GRADE) 
			$calculation_arr = array('average_grade' => new Zend_Db_Expr('AVG(RIGHT(r.age,1))'));
		else 
			$calculation_arr = array('average_rating' => new Zend_Db_Expr('AVG(r.rating)'));
    	
    	$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id',
        			  		 $calculation_arr)
        			  ->where('s.complete = ?', 1);    

		if($book != BOOK_OVER_ALL) {  // if is not overall summary graph, then limit search by book
		        	$query->where('r.book = ?', $book);
        }
        
	    switch ($category) {
	    	
		case BOYS_AVERAGE:
	    	$query->where("r.gender = ?", 'Male');
	        $query->where(self::grade_query);  
	        break;
	    case GIRLS_AVERAGE:
	        $query->where("r.gender = ?", 'Female');
	        $query->where(self::grade_query);
	        break;
	    case AVERAGE_GRADE:
	        $query->where(self::grade_query);
	        break;
	    case TEACHER_AVERAGE:
	    	$query->where("r.teacher = ?", 'Yes');
	    	break;
	    case OVERALL_AVERAGE:
	        $query->where('(' . self::grade_query . " AND (r.gender = 'Male' OR r.gender = 'Female')) OR r.teacher = 'Yes'"  );
			break;
	}
        
        $result = $this->fetchRow($query);
        
        if($category == AVERAGE_GRADE) {
        	$value = $result->average_grade;
        } else {
        	$value = $result->average_rating;
        	
        }
        return $value;
    }
	// for Completed Survey Count graphs
    public function getCompletedSurveyCount($book, $age_cat, $not_complete_flag = FALSE)
	{
		$query = $this->select()
        			  ->setIntegrityCheck(false)
        			  ->from(array('r' => 'responses'),
        			         array('survey_id','book')
        			  )
        			  ->join(array('s' => 'surveys'),
        			         'r.survey_id = s.survey_id',
        			  		 array('count' => 'COUNT(*)'));
        			  		 
        if($not_complete_flag) 			  
        	$query->where('s.complete = ?', 0);  // search for incomplete surveys
        else
        	$query->where('s.complete = ?', 1);	// search for completed surveys
        	
		if($book != BOOK_OVER_ALL) {  // if is not overall summary graph, then limit search by book
		        	$query->where('r.book = ?', $book);
        }

		switch ($age_cat) {
	
		case UNDER18:
	        $query->where("r.age != 'Adult'");
	        break;
	    case GRADES_3_5:
	        $query->where("r.age = 'Grade 3' OR
	        			   r.age = 'Grade 4' OR
	        			   r.age = 'Grade 5'");
	        break;
	    case ADULT:
	        $query->where('r.age = ?', 'Adult');      	
	        break;
	    case TEACHER:
	        $query->where('r.teacher = ?', 'Yes');      	
	    	break;
		}
        $result = $this->fetchRow($query);
        return $result->count;
    }
    
    public function newResponse ($age, $book, $key)
    {
        $data = array('survey_id' => $key, 'age' => $age, 'book' => $book);
        $new_key = $this->insert($data);
        return $new_key;
    }
    public function getBook ($survey_key)
    {
        $query = $this->select()->where('survey_id = ?', $survey_key);
        $result = $this->fetchRow($query);
        return $result->book;
    }
    public function enterAdultGenderOccupation ($gender, $teacher, $survey_key, 
    $ip)
    {
        $data = array('gender' => $gender, 'teacher' => $teacher);
        $where = $this->getAdapter()->quoteInto('survey_id = ?', $survey_key);
        $this->update($data, $where);
    }
    public function enterAdultTeacherGrade ($grade, $comment, $survey_key, $ip)
    {
        $data = array('grade' => $grade, 'favourite' => $comment);
        $where = $this->getAdapter()->quoteInto('survey_id = ?', $survey_key);
        $this->update($data, $where);
    }
    public function enterAdultMainSurvey ($rating, $recommend, $helpful, 
    $character, $comment, $opinion, $email, $ip, $survey_key)
    {
        $data = array('rating' => $rating, 'recommend' => $recommend, 
        'helpful' => $helpful, 'character' => $character, 'future' => $comment, 'opinion' => $opinion, 
        'email' => $email);
        $where = $this->getAdapter()->quoteInto('survey_id = ?', $survey_key);
        $this->update($data, $where);
    }
    public function enterAdultWWIT ($comment, $ip, $survey_key)
    {
        $data = array('full' => $comment);
        $where = $this->getAdapter()->quoteInto('survey_id = ?', $survey_key);
        $this->update($data, $where);
    }
    public function enterKidSurvey ($gender, $rating, $character, $recommend, 
    $read, $helpful, $cover, $genre, $best, $justread, $questions, $ip, $survey_key)
    {
        $data = array('rating' => $rating, 'gender' => $gender, 
        'character' => $character, 'recommend' => $recommend, 'read' => $read, 
        'helpful' => $helpful, 'cover' => $cover, 'best' => $best, 
        'justread' => $justread, 'questions' => $questions);
        $where = $this->getAdapter()->quoteInto('survey_id = ?', $survey_key);
        $this->update($data, $where);
    }
}
