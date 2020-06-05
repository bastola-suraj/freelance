create table institute(
id int auto_increment primary key,
name varchar(100)
);

create table company(
id int auto_increment primary key,
name varchar(100)
);

create table positions(
id int auto_increment primary key,
name varchar(100)
);

create table skills(
id int auto_increment primary key,
name varchar(100)
);

create table educations(
id int auto_increment primary key,
start_year varchar(20),
end_year varchar(20),
description text,
institute_id int,
constraint edu_institutefk
foreign key (institute_id) 
references institute(id)
on delete cascade on update cascade
);

create table job_history(
id int auto_increment primary key,
start_year varchar(20),
end_year varchar(20),
position_id int,
company_id int,
description text,
constraint job_company
foreign key (company_id)
references company(id)
on delete cascade on update cascade,
constraint job_position
foreign key (position_id)
references positions(id)
on delete cascade on update cascade
);

create table freelancer(
id int auto_increment primary key,
users_id int,
education_id int,
jobhistory_id int,
join_date varchar(20),
skills_id int,
description text,
headline varchar(50),
constraint freelancer_education
foreign key (education_id) 
references educations(id)
on delete cascade on update cascade,
constraint freelancer_jobs
foreign key (jobhistory_id) 
references job_history(id)
on delete cascade on update cascade,
constraint freelancer_skills
foreign key (skills_id) 
references skills(id)
on delete cascade on update cascade 
);