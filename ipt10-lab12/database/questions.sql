CREATE TABLE questions (
    id SERIAL PRIMARY KEY,
    item_number INT NOT NULL,
    question TEXT NOT NULL,
    choices JSON NOT NULL,
    correct_answer CHAR(1) NOT NULL CHECK (correct_answer IN ('A', 'B', 'C', 'D'))
);

INSERT INTO questions (item_number, question, choices, correct_answer) VALUES
(1, 'What is the primary focus of Integrative Programming and Technologies (IPT)?',
 '[{"letter": "A", "choice": "Enhancing software efficiency"}, {"letter": "B", "choice": "Combining different programming paradigms and technologies"}, {"letter": "C", "choice": "Developing hardware solutions"}, {"letter": "D", "choice": "Improving user interfaces"}]',
 'B'),
(2, 'Which of the following is a key benefit of IPT?',
 '[{"letter": "A", "choice": "Increased compatibility between systems"}, {"letter": "B", "choice": "Reduced development time"}, {"letter": "C", "choice": "Enhanced scalability"}, {"letter": "D", "choice": "All of the above"}]',
 'D'),
(3, 'Which programming paradigm is commonly integrated in IPT?',
 '[{"letter": "A", "choice": "Object-oriented programming"}, {"letter": "B", "choice": "Functional programming"}, {"letter": "C", "choice": "Procedural programming"}, {"letter": "D", "choice": "All of the above"}]',
 'D'),
(4, 'What role do APIs play in Integrative Programming and Technologies?',
 '[{"letter": "A", "choice": "They enhance graphical user interfaces"}, {"letter": "B", "choice": "They allow different software systems to communicate"}, {"letter": "C", "choice": "They provide security features"}, {"letter": "D", "choice": "They manage databases"}]',
 'B'),
(5, 'Which technology is often associated with IPT for building scalable applications?',
 '[{"letter": "A", "choice": "Cloud computing"}, {"letter": "B", "choice": "Virtual reality"}, {"letter": "C", "choice": "Blockchain"}, {"letter": "D", "choice": "None of the above"}]',
 'A');