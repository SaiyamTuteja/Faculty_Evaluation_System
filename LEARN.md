# Faculty_Evaluation_System
<!-- TABLE OF CONTENTS -->

<details open="open">
  <summary><h2 style="display: inline-block">Table of Contents</h2></summary>
  <ol>
    <li>
      <a href="#1 Introduction">Introduction</a>
    </li>
    <li>
      <a href="#2 The ProfPraisal">The ProfPraisal</a>
    </li>
    <li>
      <a href="#3 System Requirements">System Requirements</a>
    </li>
    <li>
      <a href="#4 Developing the ProfPraisal">Developing the ProfPraisal</a>
    </li>
    <li>
      <a href="#5 Integration and Testing">Integration and Testing</a>
    </li>
    <li>
      <a href="#6 Summary and Conclusion">Summary and Conclusion</a>
    </li>
  </ol>
</details>

# <a name="1 Introduction">Introduction:</a>


The ProfPraisal was developed as an automated aid for evaluating faculty. The system allows the evaluations to be processed at a central location by data entry personnel. The system was set up and documented to allow unexperienced data entry personnel to use the system. The user's manual and the system manual are described in appendix A and appendix B respectively.

The ProfPraisal is composed of three parts. The first part reads in the information on the instructors that is needed to produce questionnaire forms.
The first part of the system produces the questionnaire forms on Scantron readable cards. The second part of the system uses a Scantron card reader to read in the answers to the questions and generate statistics about them. A report for the instructor and the faculty evaluation committee is then produced showing the results of the questionnaires. 

The instructor receives a report on both the general questions and on the questions that he picked from the data base. The faculty evaluation committee receives a report with only the results to the general questions. The third part of the system is a maintenance program for the question data base and the standard questionnaire form data base.


# <a name="2 The ProfPraisal">The ProfPraisal:</a>

<img src="https://github.com/SaiyamTuteja/Faculty_Evaluation_System/assets/128183101/83907109-887a-4d48-a49e-eed1303aec0f)" align="center" height="500" width="800">

The ProfPraisal is being used by the university because a collective bargaining agreement between the University of Montana Teacher's Union and the Board of Regents requires that there be some method of evaluating faculty. Each faculty member is rated as to how well he performs in the following categories:
       a) Research 
       b) Public Service
       c) Teaching - The faculty evaluation will measure this.
       
The ProfPraisal creates a questionnaire with 10 general questions and up to 23 private questions. The 10 general questions consists of seven demographics questions about the students taking the course. The other three general questions are used for evaluating the instructor. The 23 private questions are chosen by the instructor from a data base of questions.

The ProfPraisal will aid the faculty evaluation committee by providing standard reports. The committee will use the global questions to make general personnel decisions/ deciding, for example, if a faculty member should be promoted. The global questions will also provide information on what the students think about the course. 

The ProfPraisal is composed of three basic parts: production of instructor questionnaire forms, generation of the reports on the answers to the forms, and maintenance of a data base of evaluation questions and standard questionnaire forms.

# <a name="3 System Requirements">System Requirements:</a>

1. Question Requests:
Each instructor that is using the ProfPraisal will have the first ten questions of the evaluation questionnaire generated automatically. These questions consist of two types of questions. The first seven questions pertain to demographic questions about the students. The next three questions are to obtain general information about the instructor.

2. Questionnaire Generation:
The information about each instructor and the questions that he is using will be used to print out the instructors requested number of forms. The questionnaire forms will be printed on a special form that can also be fed through a Scantron card reader. The ten questions referenced above will be preprinted on these forms. The questionnaires will then be distributed to the instructor's to be passed out to
the students at the end of the quarter.

3. Report Generation:
The questionnaire answers will be entered into the computer by the Scantron card reader along with the general information on the instructor. The questionnaire answers will be converted into statistics to be used in generating the reports. The statistics generated will be a frequency of the occurrence for each answer to each question, and the mean and standard deviation for each question.

4. Program Maintenance:
There will be a automated method for updating the list of questions and the standard forms. There will also be a method used to print out the list of questions and the standard forms.


# <a name="4 Developing the ProfPraisal">Developing the ProfPraisal:</a>

There were some problems in creating the models using PSL/PSA, since nobody had any knowledge about how to use the automated design aid. This problem was overcome by doing a lot of reading and trial and error. Technologies used are:
<br>
    a) HTML/CSS
</br>
<br>
    b) JavaScript
</br>
<br>
    c) PHP
</br>
<br> 
    d) MySQL
</br>

<h2 style="display: inline-block">How to set up?</h2>

1. Clone the repository: git clone (https://github.com/SaiyamTuteja/Faculty_Evaluation_System.git)

2. Import the database schema (faculty_evaluation_system.sql) into your MySQL database

3. Update the database connection details in config.php file
   
4. Start the PHP server: php -S localhost:8000

# <a name="5 Integration and Testing">Integration and Testing:</a>

The ProfPraisal was tested under actual operating conditions. The routines were set up and checked to see if they were free of most of the errors. The data entry personnel were then allowed to use the programs. There were relatively few errors found by the data entry personnel.

The routines were set up in three basic groups: the questionnaire generation routines, the report generation routines and the questionnaire maintenance routines.

The report generation routines were the first set of programs written. The initial versions of these routines were used to print the reports from the data bases created by the original ICES system. The relevant data shall be captured continually. Google Forms & Sheets can be used for data capture. Dashboard can be designed to glean through data at any day.

# <a name="6 Summary and Conclusion">Summary and Conclusion:</a>

The ProfPraisal was designed and implemented to aid in the evaluation of faculty. The system was set up so that the evaluations could be tabulated in a single area instead of having each department form a student
committee to tabulate the results. The system was also set up so that instructors could choose a wide variety of questions and improve their teaching by the feedback.

The system was implemented after extensive modeling of the old system was done. The system was then redesigned and implemented after a quarter of testing. The current system is set up so that it can be easily changed as new ideas are
presented.


## 🙏 Support

This project needs a ⭐️ from you. Don't forget to leave a star ⭐️
