ByteJudge
=========

An online code grader application for educational institutes. 

Teachers may add programming problems and group them as a test or assignment.

Students can submit the solution code for the problems. Solutions will be evaluated by executing them on a set of predefined test cases uploaded by the author of the problem.


Installation:
-------------

 - Install LAMP stack (apache2, php, mysql)
 - Make sure they are running
 - Run `bash install.sh`
   - This will setup the database 
 - Copy ByteJudge/ directory to the apache hosting directory (`/var/www/` or `/opt/lampp/htdocs`)
   (Or you can keep the repo in your working directory and create a bytejudge.conf file in `/etc/apache2/sites-available/bytejudge.conf` by copying from `/etc/apache2/sites-available/default.conf` and point `DocumentRoot` to the \<PATH TO BYTEJUDGE\>/ByteJudge/)


initial admin userid: admin password: \<As set during installation\>
