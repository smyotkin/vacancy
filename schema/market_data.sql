
CREATE TABLE `market_data`(
  id_value varchar(20) NOT NULL,
  price varchar(10) NOT NULL,
  is_noon  ENUM("N", "S", "O", "Y") DEFAULT "N",
  update_date DATETIME
);