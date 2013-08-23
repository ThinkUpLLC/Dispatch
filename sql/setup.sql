drop table if exists crawl_status;
create table crawl_status(
	id MEDIUMINT NOT NULL AUTO_INCREMENT,
	install_name VARCHAR(30) NOT NULL,
	crawl_time MEDIUMINT NOT NULL,
	crawl_start datetime NOT NULL,
	crawl_finish datetime NOT NULL,
	crawl_status int NOT NULL,
	PRIMARY KEY (id),
	KEY install_name_key(install_name),
	KEY crawl_time_key(crawl_time),
KEY crawl_status_key(crawl_status)
) ENGINE=MyISAM;

drop table if exists crawl_log;
create table crawl_log(
	id MEDIUMINT NOT NULL AUTO_INCREMENT,
	crawl_status_id MEDIUMINT NOT NULL,
	crawl_log LONGTEXT NOT NULL,
	PRIMARY KEY (id),
	KEY crawl_status_id_key (crawl_status_id)
) ENGINE=MyISAM;
