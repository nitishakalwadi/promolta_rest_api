CREATE TABLE `keys` (
       `id` INT(11) NOT NULL AUTO_INCREMENT,
       `user_id` INT(11) NOT NULL,
       `key` VARCHAR(40) NOT NULL,
       `level` INT(2) NOT NULL,
       `ignore_limits` TINYINT(1) NOT NULL DEFAULT '0',
       `is_private_key` TINYINT(1)  NOT NULL DEFAULT '0',
       `ip_addresses` TEXT NULL DEFAULT NULL,
       `date_created` INT(11) NOT NULL,
       `valid_till` INT(11) NOT NULL,
       PRIMARY KEY (`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
   
CREATE TABLE `users` (
 `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `email` varchar(128) NOT NULL,
 `password` varchar(128) NOT NULL,
 PRIMARY KEY (`user_id`),
 UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
   
create table messages(
    message_id int unsigned not null primary key auto_increment,
    subject text,
    body text,
    author_id int not null,
    created_at datetime,
    updated_at datetime
);

create table placeholders(
    placeholder_id int unsigned not null primary key auto_increment,
    code varchar(128),
    name varchar(128),
    created_at datetime,
    updated_at datetime
);

create table threads(
    thread_id int unsigned not null primary key auto_increment,
    belongs_to int unsigned not null,
    placeholder_id int unsigned,
    is_read TINYINT(1) default 0,
    created_at datetime,
    updated_at datetime
);

create table thread_placeholder_map(
    thread_placeholder_map int unsigned not null primary key auto_increment,
    thread_id int unsigned not null,
    placeholder_id int unsigned not null,
    created_at datetime,
    updated_at datetime
);

create table thread_message_map(
    thread_message_map_id int unsigned not null primary key auto_increment,
    thread_id int unsigned not null,
    message_id int unsigned not null,
    created_at datetime,
    updated_at datetime
);

create table message_to(
    message_to_id int unsigned not null primary key auto_increment,
    message_id int unsigned not null,
    to_email varchar(128) NOT NULL,
    created_at datetime,
    updated_at datetime
);

create table attachment(
    attachment_id int unsigned not null primary key auto_increment,
    actual_file_name text,
    internal_file_name text,
    created_at datetime,
    updated_at datetime
);

create table attachment_message_map(
    attachment_message_map_id int unsigned not null primary key auto_increment,
    attachment_id int unsigned not null,
    message_id int unsigned not null,
    created_at datetime,
    updated_at datetime
);


insert into placeholders (code, name) values
("inbox", "Inbox"),
("sent", "Sent"),
("draft", "Draft"),
("trash", "Trash");

truncate table `keys`;
truncate table `messages`;
truncate table `message_to`;
truncate table `threads`;
truncate table `thread_message_map`;
truncate table `attachment`;
truncate table `attachment_message_map`;