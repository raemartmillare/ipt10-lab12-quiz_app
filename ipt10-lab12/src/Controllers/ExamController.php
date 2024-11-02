<?php 

namespace App\Controllers;

use App\Models\Question;
use App\Models\UserAnswer;
use App\Models\User;
use TCPDF;

require_once __DIR__ . '/../../vendor/autoload.php';
class ExamController extends BaseController
{
    public function registrationForm()
    {
        $this->initializeSession();

        return $this->render('registration-form');
    }

    public function register()
    {
        $this->initializeSession();
        $data = $_POST;
        
        $userObj = new User();
        $user_id = $userObj->save($data);

        $_SESSION['user_id'] = $user_id;
        $_SESSION['complete_name'] = $data['complete_name'];
        $_SESSION['email'] = $data['email'];

        return $this->render('login-form', $data);
    }

    public function loginForm()
    {
        $this->initializeSession();

        return $this->render('login-form');
    }

    public function login()
    {
        $this->initializeSession();
        $data = $_POST;

        // Retrieve user by email
        $userObj = new User();
        $user = $userObj->findByEmail($data['email']);

        if ($user && password_verify($data['password'], $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['complete_name'] = $user['complete_name'];
            $_SESSION['email'] = $user['email'];
            return $this->render('pre-exam');

        } else {
            $error = "Invalid email or password.";
            return $this->render('login-form', ['error' => $error]);
        }
    }

    public function exam()
    {
        $this->initializeSession();
        $item_number = 1;

        // If request is coming from the form, save the inputs to the session
        if (isset($_POST['item_number']) && isset($_POST['answer'])) {
            array_push($_SESSION['answers'], $_POST['answer']);
            $_SESSION['item_number'] = $_POST['item_number'] + 1;
        }

        if (!isset($_SESSION['item_number'])) {
            // Initialize session variables
            $_SESSION['item_number'] = $item_number;
            $_SESSION['answers'] = [false];
        } else {
            $item_number = $_SESSION['item_number'];
        }

        $data = $_POST;
        $questionObj = new Question();
        $question = $questionObj->getQuestion($item_number);

        // if there are no more questions, save the answers
        if (is_null($question) || !$question) {
            $user_id = $_SESSION['user_id'];
            $json_answers = json_encode($_SESSION['answers']);

            error_log('FINISHED EXAM, SAVING ANSWERS');
            error_log('USER ID = ' . $user_id);
            error_log('ANSWERS = ' . $json_answers);

            $userAnswerObj = new UserAnswer();
            $userAnswerObj->save(
                $user_id,
                $json_answers
            );
            $score = $questionObj->computeScore($_SESSION['answers']);
            $items = $questionObj->getTotalQuestions();
            $userAnswerObj->saveAttempt($user_id, $items, $score);

            header("Location: /result");
            exit;
        }

        $question['choices'] = json_decode($question['choices']);

        return $this->render('exam', $question);
    }

    public function result()
    {
        $this->initializeSession();
        $data = $_SESSION;
        $questionObj = new Question();
        $data['questions'] = $questionObj->getAllQuestions();
        $answers = $_SESSION['answers'];
        foreach ($data['questions'] as &$question) {
            $question['choices'] = json_decode($question['choices']);
            $question['user_answer'] = $answers[$question['item_number']];
        }
        $data['total_score'] = $questionObj->computeScore($_SESSION['answers']);
        $data['question_items'] = $questionObj->getTotalQuestions();

        session_destroy();

        return $this->render('result', $data);
    }

    public function examinees()
    {
        $this->initializeSession();
        $userAnswerObj = new UserAnswer();
        $examAttempts = $userAnswerObj->getAllExamAttempts();

        foreach ($examAttempts as &$attempt) {
            $attempt['attempt_date'] = date('Y-m-d H:i:s', strtotime($attempt['attempt_date']));
    }

        return $this->render('examinees', ['examAttempts' => $examAttempts]);
    }

    public function examAttempt($id)
    {
        $userAnswerObj = new UserAnswer();
    $examAttemptDetails = $userAnswerObj->getExamAttemptById($id);

    if (!$examAttemptDetails) {
        return $this->render('error', ['message' => 'Exam attempt not found']);
    }

    // Create new PDF document
    $pdf = new TCPDF();
    $pdf->AddPage();

    // Set PDF content
    $pdf->SetFont('helvetica', '', 12); // Use helvetica font, which is available in TCPDF
    $pdf->Cell(0, 10, 'Examinee Name: ' . $examAttemptDetails['complete_name'], 0, 1);
    $pdf->Cell(0, 10, 'Email: ' . $examAttemptDetails['email'], 0, 1);
    $pdf->Cell(0, 10, 'Total Score: ' . $examAttemptDetails['total_score'], 0, 1);
    $pdf->Cell(0, 10, 'Exam Attempt Date: ' . $examAttemptDetails['attempt_date'], 0, 1);

    // Output PDF
    $pdf->Output('exam_attempt_' . $id . '.pdf', 'D'); // Force download

    exit;
    }
}
