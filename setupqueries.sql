

create database autograder;
use autograder;
create user grader_admin@localhost identified by 'password';
create user grader_user@localhost identified by 'password';

create user grader_faculty@localhost identified by 'password';
create user grader_evaluate@localhost identified by 'password';
create user grader_view@localhost identified by 'password';
create user grader_login@localhost identified by 'password';
create user grader_password@localhost identified by 'password';
create user grader_test@localhost identified by 'password';



create table users
(
	userid varchar(20),
	type varchar(20),
	primary key(userid)
);

create table faculty_main
(
	facultyid varchar(20),
	fullname varchar(50) NOT NULL,
	dob date,
	emailid varchar(50),
	college varchar(50),
	branch varchar(50),
	designation varchar(30),
	count_problemsadded int DEFAULT 0,
	constraint fkey_fid_uid foreign key(facultyid) references users(userid) on delete cascade,
	primary key(facultyid)
);

create table students_main 
( 
	rollno varchar(20), 
	fullname varchar(50) NOT NULL, 
	dob date, 
	emailid varchar(50), 
	college varchar(50), 
	branch varchar(50), 
	count_submissions int DEFAULT 0, 
	count_AC int DEFAULT 0, 
	count_WA int DEFAULT 0, 
	count_TLE int DEFAULT 0, 
	count_RTE int DEFAULT 0, 
	count_CTE int DEFAULT 0,
	constraint fkey_rollno_uid foreign key(rollno) references users(userid) on delete cascade,
	primary key(rollno) 
);

create table admin_main
(
	adminid varchar(20),
	adminname varchar(50),
	primary key(adminid),
	constraint fkey_adminid_userid foreign key(adminid) references users(userid) on delete cascade
);
create table problems
(
	problem_code varchar(20),
	addedon date,
	addedby varchar(20),
	number_testfiles int,
	timelimit int,
	memorylimit int,
	type varchar(20),
	
	showmistakes varchar(20),
	problem_title varchar(50),
	visiblesolutions varchar(10),
	totalsubmissions int DEFAULT 0,
	acceptedsubmissions int DEFAULT 0,
	solvedby int DEFAULT 0,
	constraint fkey_probadd_fid foreign key(addedby) references faculty_main(facultyid) on delete set NULL,
	constraint check_type CHECK (type IN ("practice","test")),
	primary key(problem_code)
);
create table problems_languagesallowed
(
	problem_code varchar(20),
	language varchar(20),
	constraint fkey_langallowed_problemcode foreign key(problem_code) references problems(problem_code) on delete cascade,
	primary key(problem_code,language)
);

create table submissions
(
	submissionid int primary key AUTO_INCREMENT,
	problem_code varchar(20),
	username varchar(20),
	submissiontime timestamp,
	verdict varchar(20),
	executiontime float,
	language varchar(20),
	count_ac integer,
	count_wa integer,
	count_rte integer,
	count_tle integer,
	constraint fkey_submit_pid foreign key(problem_code) references problems(problem_code) on delete cascade,
	constraint fkey_submit_uid foreign key(username) references users(userid) on delete cascade,
	check (verdict in ("TLE","RTE","AC","WA","CTE","PENDING"))
);

create table logininfo
(
	username varchar(20) NOT NULL,
	password varchar(40) NOT NULL,
	createdon timestamp DEFAULT 0,
	lastlogin timestamp DEFAULT 0,
	constraint fkey_loginadmin_uid foreign key(username) references users(userid) on delete cascade,
	primary key(username)
);



create table groups_faculty
(
	groupid varchar(20),
	groupname varchar(20) NOT NULL,
	groupdetails varchar(200),
	primary key(groupid)
	
);
create table groups_students
(
	groupid varchar(20),
	groupname varchar(20) NOT NULL,
	groupdetails varchar(200),
	primary key(groupid)
);

create table students_belongtogroups
(
	rollno varchar(20),
	groupid varchar(20),
	constraint fkey_sg_rollno foreign key(rollno) references students_main(rollno) on delete cascade,
	constraint fkey_sg_sgid foreign key(groupid) references groups_students(groupid) on delete cascade,
	primary key(rollno,groupid)
);
create table faculty_belongtogroups
(
	facultyid varchar(20),
	groupid varchar(20),
	constraint fkey_fg_fid foreign key(facultyid) references faculty_main(facultyid) on delete cascade,
	constraint fkey_fg_fgid foreign key(groupid) references groups_faculty(groupid) on delete cascade,
	primary key(facultyid,groupid)
);

create table test_main
(
	testid varchar(20),
	testname varchar(30),
	testdetails varchar(100),
	createdby varchar(20),
	createdon timestamp,
	visiblefrom timestamp,
	visibletill timestamp,
	constraint fkey_testcb_fid foreign key(createdby) references faculty_main(facultyid) on delete set NULL,
	constraint compare_timestamps CHECK (visiblefrom<visibletill),
	primary key(testid)
);
create table test_problems
(
	testid varchar(20),
	problem_code varchar(20),
	totalsubmissions int DEFAULT 0,
	acceptedsubmissions int DEFAULT 0,
	solvedby int DEFAULT 0,
	constraint fkey_prob_tid foreign key(testid) references test_main(testid) on delete cascade,
	constraint fkey_prob_pcode foreign key(problem_code) references problems(problem_code) on delete cascade,
	primary key(testid,problem_code)
);
create table test_attemptedby
(
	testid varchar(20),
	rollno varchar(20),
	constraint fkey_testattempt_tid foreign key(testid) references test_main(testid) on delete cascade,
	constraint fkey_testattempt_rollno foreign key(rollno) references students_main(rollno) on delete cascade,
	primary key(testid,rollno)
);
create table test_visibleto
(
	testid varchar(20),
	groupid varchar(20),
	constraint fkey_visibleto_tid foreign key(testid) references test_main(testid) on delete cascade,
	constraint fkey_visibleto_gid foreign key(groupid) references groups_students(groupid) on delete cascade,
	primary key(testid,groupid)
);

create table test_submissions
(
	testid varchar(20),
	submissionid int,
	constraint fkey_testsubmissions_subid foreign key(submissionid) references submissions(submissionid) on delete cascade,
	constraint fkey_testsubmissions_testid foreign key(testid) references test_main(testid) on delete cascade,
	primary key(testid,submissionid)
);

DELIMITER |
create trigger incrementverdictstats after update on submissions
for each row
begin
	declare v_count int;
	if new.username in (select rollno from students_main) 
	then
		update problems set totalsubmissions=totalsubmissions+1 where problem_code=new.problem_code;
		update students_main set count_submissions=count_submissions+1 where rollno=new.username;
		if new.verdict="AC" 
		then	
			update problems set acceptedsubmissions=acceptedsubmissions+1 where problem_code=new.problem_code;
			update students_main set count_AC=count_AC + 1 where rollno=new.username;
			select count(*) into v_count from submissions where submissionid!=new.submissionid and verdict="AC" and problem_code=new.problem_code and username=new.username;
			if v_count=0
			then
				update problems set solvedby=solvedby+1 where problem_code=new.problem_code;
			end if;
		elseif new.verdict="WA"
		then 
			update students_main set count_WA=count_WA + 1 where rollno=new.username;
		elseif new.verdict="CTE"
		then 
			update students_main set count_CTE=count_CTE + 1 where rollno=new.username;
		elseif new.verdict="RTE"
		then 
			update students_main set count_RTE=count_RTE + 1 where rollno=new.username;
		elseif new.verdict="TLE" 
		then 
			update students_main set count_TLE=count_TLE + 1 where rollno=new.username;
		end if;
	end if;
end
|

create trigger incrementtestproblemstats after insert on test_submissions
for each row
begin
	declare v_verdict varchar(20);
	declare v_count int;
	declare v_problem_code varchar(20);
	declare v_username varchar(20);
	
	select problem_code,username into v_problem_code,v_username from submissions where submissionid=new.submissionid;
	if v_username IN (select rollno from students_main)
	then
		
		update test_problems set totalsubmissions=totalsubmissions+1 where testid=new.testid and problem_code=v_problem_code;
		select verdict into v_verdict from submissions where submissionid=new.submissionid;
		if v_verdict="AC"
		then
			update test_problems set acceptedsubmissions=acceptedsubmissions+1 where testid=new.testid and problem_code=v_problem_code;
		
			select count(*) into v_count from (select * from test_submissions where testid=new.testid) as A natural join (select * from submissions where problem_code=v_problem_code and username=v_username) as B where submissionid!=new.submissionid and verdict="AC";
			if v_count=0
			then
				update test_problems set solvedby=solvedby+1 where testid=new.testid and problem_code=v_problem_code;
			end if;
		end if;
		
	end if;
end
|

create trigger incrementproblemsadded after insert on problems
for each row
begin
	update faculty_main set count_problemsadded=count_problemsadded+1 where facultyid=new.addedby;
end
|


DELIMITER ;

grant select on autograder.test_main to grader_test;
grant select on autograder.test_attemptedby to grader_test;
grant insert on autograder.test_attemptedby to grader_test;
grant select on autograder.test_visibleto to grader_test;
grant select on autograder.students_belongtogroups to grader_test;
grant select on autograder.problems to grader_test;
grant select on autograder.test_problems to grader_test;
grant select on autograder.test_submissions to grader_test;
grant select on autograder.submissions to grader_test;
grant select on autograder.students_main to grader_test;

grant select on autograder.users to grader_password;
grant update(password) on autograder.logininfo to grader_password;
grant select(username) on autograder.logininfo to grader_password;

grant select on autograder.users to grader_login;
grant select on autograder.logininfo to grader_login;
grant update(lastlogin) on autograder.logininfo to grader_login;
grant select(rollno) on autograder.students_main to grader_login;
grant select(facultyid) on autograder.faculty_main to grader_login;
grant select(adminid) on autograder.admin_main to grader_login;

grant select on autograder.problems to grader_view;
grant select on autograder.problems_languagesallowed to grader_view;
grant select on autograder.students_main to grader_view;
grant select on autograder.faculty_main to grader_view;
grant select on autograder.admin_main to grader_view;
grant select on autograder.groups_faculty to grader_view;
grant select on autograder.groups_students to grader_view;
grant select on autograder.students_belongtogroups to grader_view;
grant select on autograder.faculty_belongtogroups to grader_view;
grant select on autograder.submissions to grader_view;
grant select on autograder.test_main to grader_view;
grant select on autograder.test_problems to grader_view;
grant select on autograder.test_visibleto to grader_view;
grant select on autograder.users to grader_view;
grant select on autograder.test_attemptedby to grader_view;
grant select on autograder.test_submissions to grader_view;

grant insert on autograder.submissions to grader_evaluate;
grant update on autograder.submissions to grader_evaluate;
grant select on autograder.submissions to grader_evaluate;
grant select on autograder.problems to grader_evaluate;
grant insert on autograder.test_submissions to grader_evaluate;

grant select on autograder.groups_students to grader_faculty;
grant insert on autograder.groups_students to grader_faculty;
grant update on autograder.groups_students to grader_faculty;
grant delete on autograder.groups_students to grader_faculty;
grant insert on autograder.students_belongtogroups to grader_faculty;
grant select on autograder.students_belongtogroups to grader_faculty;
grant delete on autograder.students_belongtogroups to grader_faculty;
grant insert on autograder.problems to grader_faculty;
grant insert on autograder.problems_languagesallowed to grader_faculty;
grant select on autograder.problems to grader_faculty;
grant select on autograder.problems_languagesallowed to grader_faculty;
grant insert on autograder.test_main to grader_faculty;
grant update on autograder.test_main to grader_faculty;
grant select on autograder.test_main to grader_faculty;
grant delete on autograder.test_main to grader_faculty;
grant insert on autograder.test_problems to grader_faculty;
grant delete on autograder.test_problems to grader_faculty;
grant select on autograder.test_problems to grader_faculty;
grant update on autograder.test_problems to grader_faculty;
grant insert on autograder.test_visibleto to grader_faculty;
grant delete on autograder.test_visibleto to grader_faculty;
grant select on autograder.test_visibleto to grader_faculty;
grant delete on autograder.test_submissions to grader_faculty;
grant delete on autograder.test_attemptedby to grader_faculty;
grant select on autograder.test_submissions to grader_faculty;
grant select on autograder.test_attemptedby to grader_faculty;
grant delete on autograder.problems to grader_faculty;
grant select on autograder.faculty_main to grader_faculty;
grant update(count_problemsadded) on autograder.faculty_main to grader_faculty;
grant update on autograder.problems to grader_faculty;
grant delete on autograder.problems_languagesallowed to grader_faculty;

grant all on autograder.* to grader_admin;

grant update on autograder.* to grader_user;
grant select on autograder.* to grader_user;
flush privileges;

insert into users values('admin','admin');
insert into admin_main values('admin','Admin');
insert into logininfo values('admin',SHA1('password'),NULL,NULL);

