#
# Table structure for table 'tt_content'
#

CREATE TABLE tt_content (

	tx_t3pimper_headercolor text NOT NULL,
	tx_t3pimper_headerdeco text NOT NULL,
	tx_t3pimper_margin text NOT NULL,
	tx_t3pimper_bulletstyle text NOT NULL,
	
	tx_t3pimper_imgmargintop SMALLINT(5) DEFAULT NULL,
	tx_t3pimper_imgmarginleft SMALLINT(5) DEFAULT NULL,
	tx_t3pimper_imgmarginright SMALLINT(5) DEFAULT NULL,
	tx_t3pimper_imgmarginbottom SMALLINT(5) DEFAULT NULL,
	tx_t3pimper_imgrotate VARCHAR(10) DEFAULT NULL,
	
	tx_t3pimper_margintop SMALLINT(5) DEFAULT NULL,
	tx_t3pimper_marginleft SMALLINT(5) DEFAULT NULL,
	tx_t3pimper_marginright SMALLINT(5) DEFAULT NULL,
	tx_t3pimper_marginbottom SMALLINT(5) DEFAULT NULL,
	tx_t3pimper_rotate VARCHAR(10) DEFAULT NULL,
	tx_t3pimper_usepad TINYINT(1) DEFAULT NULL
	
	#spaceBefore SMALLINT(5) NULL DEFAULT  '0'
);


#
# Table structure for table 'sys_file_reference'
#

CREATE TABLE sys_file_reference (

	imgvariants text NOT NULL

);
