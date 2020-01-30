# Mafqoo
Use machine learning model to help identify the lost people

#

_By Mohamed Badawy_
_Mohamed M.Mobark_
_Mostafa M.Fathy_
_Ahmed T.Emam_

**What we think** — We think it is a heavy problem to the people who lose their children or mentally diseased people as they will find it very hard to find them The time is a very important factor in some cases like this so we need to identify and find the people as fast as possible.

The presence on an easy fast tool makes the problem much easier . In this paper, we will

try solve this problems using machine learning(pattern-recognetion) technology, we will make a software that uses a machine learning model to help identify the lost people (face print) throw a platform that receives reportsand collects  the images and information about these people.  this  technology provides us with a well solution to automate the detection of the people Then we will use a more specific piece of art called CNN witch is a part of the ANNmodels.

**I.**** INTRODUCTION**

Have you ever found a lost Child ? Have ever you wounded what to do with him ?

What if the child was too young to say his parents full name or address?

What if it ended putting him in orphanage?

All these questions are asked every time you see a child and you don&#39;t know what to do with him, new and modern technologies has provided the whole world with solutions for almost every problem, so that we could rely

on technology to solve this problem.

We could find a solution to our problem by recognizing lost people.

Using Google Survey Feedback Discussion

These are analyzed findings based on the

34 responses from the survey that the researcher received:

- Respondents who are familiar with the reporting procedure. Figure 1: Respondents Who Are Familiar with  the Reporting Procedure

 

Only 12 respondents (35.3%) werefamiliar with the reporting procedure as shown in Figure 4.1. This proved that including

information of the reporting procedure as well as contacts to all police stations in Kenya would add great value to the proposed solution.

- Respondents whose reported missing person wasfound

Only 20% of respondents found the missing person after reporting to the police station as shown in Figure 4.2. The high failure rate of finding the missing persons shows the need for an alternative solution to the current reporting system.

- Respondents who feel their loved one was found as a result of reporting to the policestation

 

Out of the four respondents whofound their missing person, noneattributed this success to the current reporting system at the police station. This is shown in the results in Figure 4.3. This further proves that the current system has a huge gap that can be filled using the proposed mobile solution.

- Respondents who would download and use a free reporting        mobile

application

a) 96.2% of the respondents would evidently download and make use of a mobile application for reporting and tracing missing persons if made available as shown in Figure 4.5. This data provided further support to the proposed solution if deployed as a freely downloadable mobile application.

**II.**** LITERATURE ****REVIEW**

To the best of our knowledge, the problem addressed in the present paper has never been solved before using free platform, it has been used for commercial use only for private organizations , The current solutions are the old fashioned ways like reporting the problem to the police, but it is a very slow way and most people don&#39;t know the right steps to report a missingperson. Some people use social networks such as facebook as a solution but it&#39;s not the best practice because it&#39;s no one&#39;s job is opening facebook groups and pages collecting and pairing the photos of lost people and dig deep into the old data looking for the people and analyzing thesedata.

We will make a software that uses a

machine learning model to help identify the lost people (face print) throw a platform that receives reports and collects huge amount of data that will help us to understand the kidnapping and lost problem and how to solve it .

the solution will be very positive, it will solve a big problem helping the society to lower the number of lost people and calm down the families when they find their lost ones. The solution will nothave much impact according to commercial use as it only adds a value to the society by solving one of its problems (the core of the system may be useful in commercial use in thefuture).

**III.**** Methodologies**

The solution relies on the reporting lost people by their relatives and reporting finding lost people using their pictures.

The application works a follow

1. 1)A person (A) loses a child or a mentally diseasedperson
2. 2)The person (A) reports the missing person and supports the report with anyavailable pictures and information of the person to the platform (Web or mobile connects to webservice).
3. 3)The app gets the person face print and stores it in the database as a missing person.
4. 4)Someone else (B) finds a person in the
5. 5)The person (B) takes a picture of the lost person then attach it to the platform (the Web or the mobile app connects to the webservice).
6. 6)The application gets the face print of the reported person and scans for a  matchnthedatabaseandifitfindsa

match it notifies the 2 persons (A) and

(B) with the contact methods of each other. notifications are fired throw SMS

1. 7)The app reports these data to the police and uses the data reports to make statistical analysis and dig for new information.

**IV.**** improvement**

In this point we will explain the system in detail

irstly Technical description**: architecture, technology, integration, innovative components, etc.

The solution relies mainly on a web application programmed using modern framework (node js , React js, laravel and python), we used python for ML model, node js and React js for the front end app ,php and laravel for the backend

,services java and Android for simulating public cameras(micro controller&quot; Raspberry pi &quot; + any usual camera and internet connection).

There must be client apps as mobile apps (Android ) and dynamic web application that uses the web application to report and receive notifications.

The main web app service uses a trained machine learning model to gather information from the uploaded photos and store it to the app database.

The innovative part is the usage of machine learning model to get information from reported photos and use them to identify the person and generate reports according to the collected data that make use of statistical analysis to help prevent similarproblems in thefuture.

The solution should run as a hostedweb service 24/7 on any operating system as any webapplication.

-The web app consists of an application that uses a main Machine learning model (Face print) to analyze the uploaded and reported pictures throw the client apps.

Most of these models are state of the art. Client apps (mobile apps or dynamic web apps) should use the service to report the cases and receive notifications about the reports. -The web app responds to the client application if it finds a match for the reportedcase.

The web app has a back-end control panel that generates reports and make analysis on the data to dig for new information.

Second The key ****technology**

The technology that we are talking about is the machine learning (pattern recognition) this technology provides us with a well solution to automate the detection of the people

Then we will use a more specific piece  of art called CNN which is a part of the ANNmodels.

But there are some struggles that faces the CNN and doesn&#39;t face the other ML models to solve isolated problems that have only one step [— estimating the price](https://medium.com/%40ageitgey/machine-learning-is-fun-80ea3ec3c471)[of a house](https://medium.com/%40ageitgey/machine-learning-is-fun-80ea3ec3c471), [generating new data based on](https://medium.com/%40ageitgey/machine-learning-is-fun-part-2-a26a10b68df3)[existing data](https://medium.com/%40ageitgey/machine-learning-is-fun-part-2-a26a10b68df3)and [telling if an image](https://medium.com/%40ageitgey/machine-learning-is-fun-part-3-deep-learning-and-convolutional-neural-networks-f40359318721)[contains a certain object](https://medium.com/%40ageitgey/machine-learning-is-fun-part-3-deep-learning-and-convolutional-neural-networks-f40359318721). All of those problems can be solved by choosing one machine learning algorithm, feeding in data, and getting the result.But face recognition is really a series of several related problems:

1. First, look at a picture and find all the faces init

1. Third, be able to pick out unique features of the face that you canuse to tell it apart from other people— like how big the eyes are, how long the face is,

 

1. Finally, compare the uniquefeatures of that face to all the people you already know to determine the person&#39;s

As a human, your brain is wired to do all

of this automatically and instantly. In fact, humans are too good at recognizing faces and end up seeing faces in everyday objects

Computers are not capable of this kind of high-level generalization (at least not yet…), so we have to teach them how to do each step in this process separately.We need to build

a pipeline where we solve each step of face recognition separately and pass the result of the current step to the next step. In other words, we will chain together several machine learning algorithms.

Laravel its a backend (php) mvc framework that runs on composer/symfony components if that&#39;s what you mean. You of course have to build a front end of the website as the whole point is to output html data (or at least data to a front end service), laravel facilitates this, but it&#39;s comparable to ruby on rails, django etc .Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, and caching. Laravel aims to make the development process a pleasing one for the Laravel is the most famous as it helps to develop a website using a simple anda clean code in a short time. This web application framework has elegant and expressive syntax. Tasks in the web projects such as authentication,routing

**10 Features That Make PHP Laravel Framework A Winning Platform**

- **--** Authorization and Logical Technique for Coding.The

authentication part is the most important aspect when anyone develops code to create an app. ...

- Innovative Template Engine....
- Effective ORM (Object Relational Mapping)...
- Libraries &amp; Modular....
- MVC Architecture Support....
- Secure Migration System....
- UniqueUnit-Testing.

Python is the best Because of the run-time typing, **Python&#39;s** run time must workharder thanJava        For these reasons, **Python** is

Third **a typical scenario**

First thing you would do is to report it to the police, Then you open the Lost people app and upload every proper picture you have for this person.

Now the application would take these pictures and recognize them and store them to the database with the reporter contacts, so the application marks this person as missing. And On the other hand If you are walking and you found a lost person, a child or a sick person, First thing is to report it and deliver theperson to the police station, Then you would take pictures to the person you found to upload it to the Lost Peopleapp

- First the platform takes the pictures of the person then compares it to the missing people in thedatabase
- If the person matches the platform notifies the parent and the person who found the lost kid ( the notification is the 2 parties contacts by SMS).

So what happens next is that The Parent would call the finder after getting information by report id that received in message to get

information about the kid&#39;s place ( police station or a hospital),

After the match the lost person is

marked found. the platform would take a further step to try to freduce the lost people number.

 

The platform would analyze the number of the lost people compared to the places they were lost in, This would give recommendations to the places where the kids go missing the most. Parents would take thisanalysis to be more careful about the places and the reasons to lose their kids in, and if there was a criminal organization to kidnap kids, it would predict the standards that the kidnappers put to choose the location (Machine Learning) And more steps to betaken

The Platform could detect person initial info by seeing only his photo. The platform could grant access to public places cameras and do a runtime detection to the people to search for the missing people, or to find the relationship between some people(suspect) and some kids getting lost by camera reports or by users reports

 

Through the camera report  extract the stamps from the images, get every one stamp and compare it with every oneonthesamecity―fromonly missingreports―,ifthedistanceis less than0.3

- match reports and recordthem
- fire notification to the missing reporter or 2 parties telling them about the case with camerareport(id).

 

if there is no similarity :
  - delete all the received stamps if there is similarity:
  - delete every stamp that has similarity. get all stamps that has suspects and get them
