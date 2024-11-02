<?php

namespace App\Models;

use App\Models\BaseModel;
use \PDO;

class UserAnswer extends BaseModel
{
    protected $user_id;
    protected $answers;

    public function save($user_id, $answers)
    {
        $this->user_id = $user_id;
        $this->answers = $answers;

        $sql = "INSERT INTO users_answers
                SET
                    user_id=:user_id,
                    answers=:answers";        
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'user_id' => $user_id,
            'answers' => $answers
        ]);
    
        return $statement->rowCount();
    }

    public function saveAttempt($user_id, $exam_items, $score)
    {
        $sql = "INSERT INTO exam_attempts
                SET
                    user_id=:user_id,
                    exam_items=:exam_items,
                    score=:score";   
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'user_id' => $user_id,
            'exam_items' => $exam_items,
            'score' => $score
        ]);
    }

    public function getAllExamAttempts()
    {
        $sql = "SELECT 
                u.complete_name, 
                u.email,
                ea.id, 
                ea.created_at AS attempt_date, 
                ea.exam_items AS exam_number_of_items, 
                ea.score AS total_score
            FROM 
                exam_attempts AS ea
            JOIN 
                users AS u ON ea.user_id = u.id
            ORDER BY 
                ea.created_at DESC";
    $statement = $this->db->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExamAttemptById($id)
    {
        $sql = "SELECT 
                ea.*, 
                u.complete_name, 
                u.email,
                ea.score AS total_score, 
                ea.created_at AS attempt_date
            FROM 
                exam_attempts AS ea
            JOIN 
                users AS u ON ea.user_id = u.id
            WHERE 
                ea.id = :id";

        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

}