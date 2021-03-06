CREATE TABLE properties (
  id int NOT NULL primary key,
  active smallint NOT NULL DEFAULT '0',
  address varchar(255) NOT NULL,
  admin_fee_amt int NOT NULL DEFAULT '0',
  admin_fee_refund smallint NOT NULL DEFAULT '0',
  airconditioning smallint NOT NULL DEFAULT '0',
  appalcart smallint NOT NULL DEFAULT '0',
  approved smallint NOT NULL DEFAULT '0',
  bathroom_no float NOT NULL DEFAULT '1',
  bedroom_no smallint NOT NULL DEFAULT '1',
  campus_distance smallint NOT NULL DEFAULT '0',
  clean_fee_amt int NOT NULL DEFAULT '0',
  clean_fee_refund smallint NOT NULL DEFAULT '0',
  clubhouse smallint NOT NULL DEFAULT '0',
  contact_id int NOT NULL,
  contract_length smallint NOT NULL DEFAULT '0',
  description text NULL,
  dishwasher smallint NOT NULL DEFAULT '0',
  furnished smallint NOT NULL DEFAULT '0',
  heat_type varchar(255) default null,
  internet_type smallint not null default 1,
  lease_type smallint not null default '0',
  laundry_type smallint NOT NULL DEFAULT '0',
  monthly_rent int NOT NULL DEFAULT '0',
  move_in_date int not null default 0,
  name varchar(255) NULL,
  other_fees text NULL,
  parking_fee int NOT NULL DEFAULT '0',
  parking_per_unit smallint NOT NULL DEFAULT '0',
  pet_deposit int NOT NULL DEFAULT '0',
  pet_fee int NOT NULL DEFAULT '0',
  pet_dep_refund smallint NOT NULL DEFAULT '0',
  pets_allowed smallint NOT NULL DEFAULT '0',
  pet_type text NULL,
  security_amt int NULL,
  security_refund smallint NOT NULL DEFAULT '0',
  student_type smallint NOT NULL DEFAULT '0',
  trash_type smallint NOT NULL DEFAULT '0',
  sublease smallint not NULL default 0,
  utilities_inc smallint NOT NULL DEFAULT '0',
  util_water smallint NOT NULL DEFAULT '0',
  util_trash smallint NOT NULL DEFAULT '0',
  util_power smallint NOT NULL DEFAULT '0',
  util_fuel smallint NOT NULL DEFAULT '0',
  util_cable smallint NOT NULL DEFAULT '0',
  util_internet smallint NOT NULL DEFAULT '0',
  util_phone smallint NOT NULL DEFAULT '0',
  tv_type smallint not null default 0,
  window_number smallint NOT NULL DEFAULT '0',
  workout_room smallint NOT NULL DEFAULT '0',
  created int NOT NULL DEFAULT '0',
  updated int NOT NULL DEFAULT '0',
  timeout INT NOT NULL default '0',
  efficiency smallint not null default '0'
);

CREATE TABLE prop_contacts (
id INT NOT NULL primary key,
username VARCHAR( 50 ) NOT NULL ,
password VARCHAR( 50 ) NOT NULL ,
first_name VARCHAR( 50 ) NOT NULL ,
last_name VARCHAR( 50 ) NOT NULL ,
phone VARCHAR( 30 ) NOT NULL ,
email_address VARCHAR( 50 ) NOT NULL ,
company_name VARCHAR( 100 ) NOT NULL ,
company_address TEXT NULL ,
company_url varchar(255) NULL ,
times_available TEXT NULL,
last_log INT NOT NULL ,
active SMALLINT NOT NULL,
private smallint not null default 0,
approved smallint not null default 0
);

CREATE TABLE prop_photo (
  id int NOT NULL primary key default 0,
  cid int NOT NULL default 0,
  width smallint NOT NULL default 0,
  height smallint NOT NULL default 0,
  pid int NOT NULL default 0,
  path varchar(255) NOT NULL,
  title varchar(255) NOT NULL,
  main_pic smallint not null default 0
);

CREATE INDEX pid_prop on prop_photo (pid);

CREATE TABLE prop_roommate (
  id int NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL,
  active smallint NOT NULL DEFAULT '0',
  appalcart smallint NOT NULL DEFAULT '0',
  campus_distance smallint NOT NULL DEFAULT '0',
  clubhouse smallint NOT NULL DEFAULT '0',
  contract_length smallint NOT NULL,
  description text,
  dishwasher smallint NOT NULL DEFAULT '0',
  gender smallint NOT NULL DEFAULT '0',
  internet_type smallint NOT NULL DEFAULT '1',
  laundry_type smallint NOT NULL DEFAULT '0',
  monthly_rent int NOT NULL DEFAULT '0',
  move_in_date int NOT NULL DEFAULT '0',
  pets_allowed smallint NOT NULL DEFAULT '0',
  share_bedroom smallint NOT NULL,
  share_bathroom smallint NOT NULL,
  smoking smallint NOT NULL DEFAULT '0',
  trash_type smallint NOT NULL DEFAULT '0',
  sublease smallint NOT NULL DEFAULT '0',
  tv_type smallint NOT NULL DEFAULT '0',
  workout_room smallint NOT NULL DEFAULT '0',
  created int NOT NULL DEFAULT '0',
  updated int NOT NULL DEFAULT '0',
  timeout int NOT NULL
);

CREATE TABLE prop_messages (
  id int unsigned NOT NULL default '0',
  to_user_id int NOT NULL default '0',
  from_user_id int NOT NULL default '0',
  body text NOT NULL,
  date_sent int NOT NULL,
  reported smallint NOT NULL default '0',
  sender_name varchar(60) not null,
  hidden smallint not null default '0',
  PRIMARY KEY (id)
);


CREATE TABLE prop_report (
  id int unsigned not null default 0,
  message_id int unsigned not null default 0,
  date_sent int unsigned not null default 0,
  reason text,
  reporter_id integer not null default 0,
  offender_id integer not null default 0,
  block smallint not null default 0,
  block_reason text,
  PRIMARY KEY (id)
);