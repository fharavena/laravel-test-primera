create database if not exists api_rest_laravel;
use api_rest_laravel;

create table  users(
id              int(255) auto_increment not null,
name            varchar(50) NOT NULL,
surname         varchar(100),
role            varchar(20),
email           varchar(255) NOT NULL,
password        varchar(255) NOT NULL,
description     text,
image           varchar(255),
create_at       datetime DEFAULT NULL,
update_at       datetime DEFAULT NULL,
remember_token  varchar(255),
CONSTRAINT pk_users PRIMARY KEY (id)
)ENGINE=InnoDB;

create table categories(
id              int(255) auto_increment not null,
name            varchar(100) NOT NULL,
create_at       datetime DEFAULT NULL,
update_at       datetime DEFAULT NULL,
CONSTRAINT pk_categories PRIMARY KEY (id)
)ENGINE=InnoDB;

create table posts(
id               int(255) auto_increment not null,       
user_id          INT(255) not null,
category_id      int(255) not null,
title            VARCHAR(255) not NULL,
content         text not null,
image           VARCHAR(255),
create_at       datetime DEFAULT NULL,
update_at       datetime DEFAULT NULL,
CONSTRAINT pk_posts PRIMARY KEY (id),
CONSTRAINT fk_post_user FOREIGN KEY(user_id) REFERENCES users(id),
CONSTRAINT fk_post_category FOREIGN KEY(category_id) REFERENCES categories(id)
)ENGINE=InnoDB;