<?php

return "

create table users (
  id int auto_increment primary key,
  first_name varchar(100) not null,
  last_name varchar(100) not null,
  username varchar(100) unique not null ,
  email varchar(255) unique  not null,
  password varchar(255) not null,
  role enum('admin', 'owner', 'customer') default 'customer',
  phone varchar(100),
  avatar varchar(255) default  null,
  created_at timestamp default current_timestamp,
  updated_at timestamp default  current_timestamp
                   on update current_timestamp
);

";
