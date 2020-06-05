create table institute(
id integer auto_increment primary key,
name varchar(100)
);

create table company(
id integer auto_increment primary key,
name varchar(100)
);

create table positions(
id integer auto_increment primary key,
name varchar(100)
);

create table skills(
id integer auto_increment primary key,
name varchar(100)
);

create table educations(
id integer auto_increment primary key,
start_year varchar(20),
end_year varchar(20),
description text,
institute_id integer,
constraint edu_institutefk
foreign key (institute_id) 
references institute(id)
on delete cascade on update cascade
);

create table job_history(
id integer auto_increment primary key,
start_year varchar(20),
end_year varchar(20),
position_id integer,
company_id integer,
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
id integer auto_increment primary key,
users_id integer,
education_id integer,
jobhistory_id integer,
join_date varchar(20),
skills_id integer,
description text,
headline varchar(50),
constraint freelancer_education
foreign key (education_id) 
references education(id)
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