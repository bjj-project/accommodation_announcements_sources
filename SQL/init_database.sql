CREATE DATABASE db_apartment_booking_system CHARACTER SET utf8 COLLATE utf8_polish_ci;
USE db_apartment_booking_system;

CREATE TABLE database_version
(
	major INT NOT NULL,
	minior INT NOT NULL,
	bug_fix INT NOT NULL
);
INSERT INTO database_version VALUES(0,0,0);

CREATE TABLE clients_registration
(
	id_client_registration BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
	name VARCHAR(50) NOT NULL,
	surname VARCHAR(50) NOT NULL,
	mail VARCHAR(50) NOT NULL,
	password VARCHAR(100) NOT NULL,
	agreement_for_marketing BOOLEAN NOT NULL,
	agreement_for_rodo BOOLEAN NOT NULL,
	agreement_for_regulations BOOLEAN NOT NULL,
	ip_address_v4 VARCHAR(15) NOT NULL,
	creation_date TIMESTAMP NOT NULL DEFAULT NOW(),
	confirmation BOOLEAN NOT NULL DEFAULT FALSE,
	confirmation_date TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE permissions
(
	id_permission INT NOT NULL PRIMARY KEY,
	name VARCHAR(100) NOT NULL
);
INSERT INTO permissions VALUES	(1, 'Administrator'),
								(2, 'Klient - rejestracja'),
								(3, 'Klient - dodany ręcznie');

CREATE TABLE users
(
	id_user BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
	id_client_registration BIGINT REFERENCES clients_registration(id_client_registration),
	id_permission INT NOT NULL REFERENCES permissions(id_permission),
	name VARCHAR(50) NOT NULL,
	surname VARCHAR(50) NOT NULL,
	mail VARCHAR(50) NOT NULL,
	password VARCHAR(100) NOT NULL,
	active BOOLEAN NOT NULL DEFAULT TRUE
);
INSERT INTO users VALUES(1, NULL, 1, 'Admin', 'Admin', 'admin@o2.pl', 'admin', DEFAULT); 

CREATE TABLE promotions
(
	id_promotion INT NOT NULL PRIMARY KEY,
	name VARCHAR(100) NOT NULL,
	obnizka REAL NOT NULL 
);
INSERT INTO promotions VALUES(1, 'Standardowa cena', 1.0);

CREATE TABLE offers
(
	id_offer BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
	id_user BIGINT NOT NULL REFERENCES users(id_user),
	id_promotion INT NOT NULL REFERENCES promotions(id_promotion),
	title VARCHAR(100) NOT NULL,
	description VARCHAR(200) NOT NULL,
	best BOOLEAN NOT NULL DEFAULT FALSE,
	date_validity_from TIMESTAMP NOT NULL DEFAULT NOW(),
	date_validity_to TIMESTAMP NOT NULL,
	active BOOLEAN NOT NULL DEFAULT TRUE,
	confirmation BOOLEAN NOT NULL DEFAULT FALSE,
	confirmation_date TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE offer_images
(
	id_offer_image BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
	id_offer BIGINT NOT NULL REFERENCES offers(id_offer),
	image LONGBLOB NOT NULL,
	description VARCHAR(200) NOT NULL,
	main BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE reservations
(
	id_reservation BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
	id_user BIGINT NOT NULL REFERENCES users(id_user),
	id_offer BIGINT NOT NULL REFERENCES offers(id_offer),
	id_promotion INT NOT NULL REFERENCES promotions(id_promotion),
	creation_date TIMESTAMP NOT NULL DEFAULT NOW(),
	date_from TIMESTAMP NOT NULL DEFAULT NOW(),
	date_to TIMESTAMP NOT NULL
);

CREATE TABLE type_payment_status
(
	id_type_payment_status INT NOT NULL PRIMARY KEY,
	name VARCHAR(100) NOT NULL,
	created BOOLEAN NOT NULL,
	during BOOLEAN NOT NULL, 
	paid BOOLEAN NOT NULL,
	cancel BOOLEAN NOT NULL
);
INSERT INTO type_payment_status VALUES(1, 'Płatność wygenerowana', TRUE, FALSE, FALSE, FALSE),
									  (2, 'Płatność podczas płacenia', TRUE, TRUE, FALSE, FALSE),
									  (3, 'Płatność zapłacona', TRUE, FALSE, TRUE, FALSE),
									  (4, 'Płatność anulowana', TRUE, FALSE, FALSE, TRUE),
									  (5, 'Zwrot płatności', TRUE, FALSE, TRUE, TRUE);

CREATE TABLE payment
(
	id_payment BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
	id_reservation BIGINT NOT NULL REFERENCES reservations(id_reservation),
	id_type_payment_status INT NOT NULL DEFAULT 1 REFERENCES type_payment_status(id_type_payment_status),
	date_change_payment_status TIMESTAMP NOT NULL DEFAULT NOW(),
	to_pay DECIMAL(5,2) NOT NULL,
	paid DECIMAL(5,2) NOT NULL DEFAULT 0.0,
	date_validity TIMESTAMP NOT NULL DEFAULT NOW()
);


/*DROP PROCEDURE login*/
/*USAGE: CALL login ('admin@o2.pl', 'admin');  RETURN columns:  was_ok, code, message, id_user, admin, name, surname*/
DELIMITER $$
CREATE PROCEDURE login(IN mail_var VARCHAR(50), IN password_var VARCHAR(50))
login_label:BEGIN

	DECLARE m_admin BOOLEAN;
	DECLARE m_id_user BIGINT;
	DECLARE m_name VARCHAR(50);
	DECLARE m_surname VARCHAR(50);
    
	IF NOT EXISTS (SELECT 1 FROM users WHERE mail=mail_var AND active=TRUE) THEN
		SELECT FALSE AS was_ok, 1 AS code, 'Podany email nie istnieje' AS message, NULL AS id_user, NULL AS 'admin', NULL AS name, NULL AS surname;
		LEAVE login_label;
	END IF;
	
	IF NOT EXISTS (SELECT 1 FROM users WHERE mail=mail_var AND password=password_var AND active=TRUE ) THEN
		SELECT FALSE AS was_ok, 2 AS code, 'Podany login lub hasło jest nieprawidłowe.' AS message, NULL AS id_user, NULL AS 'admin', NULL AS name, NULL AS surname;
		LEAVE login_label;
	END IF;
		
	SELECT 
		id_user, id_permission=1 , name, surname 
	INTO 
		m_id_user, m_admin, m_name, m_surname
	FROM 
		users
	WHERE 
		mail=mail_var AND password=password_var AND active=TRUE;
		
	SELECT TRUE AS was_ok, 0  AS code, 'OK' AS message, m_id_user AS id_user, m_admin AS 'admin', m_name AS name, m_surname AS surname;
		
END$$
DELIMITER ;

/*DROP PROCEDURE registration*/
/*CALL registration('imie', 'nazwisko', 'email', 'haslo', marketing(BOOL), rodo(BOOL), regulamin(BOOL), '0.0.0.0') RETURN columns:  was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE registration(name_var VARCHAR(50), surname_var VARCHAR(50), mail_var VARCHAR(50), password_var VARCHAR(100), marketing_var BOOLEAN, rodo_var BOOLEAN, regulations_var BOOLEAN, ip_address_v4_var VARCHAR(15)) 
registration_label:BEGIN

	IF EXISTS (SELECT 1 FROM clients_registration WHERE mail=mail_var AND password=password_var AND confirmation = FALSE) THEN
		SELECT FALSE AS was_ok, 1 AS code, 'Proszę czekać na potwierdzenie konta przez Administratora' AS message;
		LEAVE registration_label;
	END IF;

	IF EXISTS (SELECT 1 FROM users WHERE mail=mail_var AND active=TRUE) THEN
		SELECT FALSE AS was_ok, 2 AS code, 'Podany email już istnieje' AS message;
		LEAVE registration_label;
	END IF;

	INSERT INTO clients_registration VALUES(DEFAULT, name_var, surname_var, mail_var, password_var, marketing_var, rodo_var, regulations_var, ip_address_v4_var, DEFAULT, DEFAULT, DEFAULT);
	
	SELECT TRUE AS was_ok, 0 AS code, 'OK' AS message;
END$$
DELIMITER ;

/*DROP PROCEDURE confirm_registration*/
/*CALL confirm_registration(id_client_registration) RETURN columns:  was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE confirm_registration(IN id_client_registration_var BIGINT) 
confirm_registration_label:BEGIN

	DECLARE m_name VARCHAR(50);
	DECLARE m_surname VARCHAR(50);
	DECLARE m_mail VARCHAR(50);
	DECLARE m_password VARCHAR(100);
	DECLARE m_id_permission INT;	
	
	IF NOT EXISTS (SELECT 1 FROM clients_registration WHERE id_client_registration=id_client_registration_var AND confirmation=FALSE) THEN
		SELECT FALSE AS was_ok, 1 AS code, 'Rejestracja do potwierdzenia nie istnieje' AS message;
		LEAVE confirm_registration_label;
	END IF;
	
	SET m_id_permission = 2;
	
	UPDATE clients_registration SET confirmation=TRUE, confirmation_date=NOW() WHERE id_client_registration=id_client_registration_var;

	SELECT 
		name, surname, mail, password
	INTO 
		m_name, m_surname, m_mail, m_password
	FROM 
		clients_registration
	WHERE 
		id_client_registration=id_client_registration_var;
		
	INSERT INTO users VALUES(DEFAULT, id_client_registration_var, m_id_permission, m_name, m_surname, m_mail, m_password, DEFAULT);
	
	SELECT TRUE AS was_ok, 0 AS code, 'OK' AS message;
END$$
DELIMITER ;

/*DROP PROCEDURE create_user*/
/*CALL create_user(id_permission, 'imie', 'nazwisko', 'mail', 'haslo') RETURN columns:  was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE create_user(IN id_permission_var INT, IN name_var VARCHAR(50), IN surname_var VARCHAR(50), IN mail_var VARCHAR(50), password_var VARCHAR(100)) 
create_user_label:BEGIN

	IF EXISTS (SELECT 1 FROM clients_registration WHERE mail=mail_var AND password=password_var AND confirmation = FALSE) THEN
		SELECT FALSE AS was_ok, 1 AS code, 'Użytkownik o podanym emailu czeka na potwierdzenie konta' AS message;
		LEAVE create_user_label;
	END IF;

	IF EXISTS (SELECT 1 FROM users WHERE mail=mail_var AND active=TRUE) THEN
		SELECT FALSE AS was_ok, 1 AS code, 'Podany email już istnieje' AS message;
		LEAVE create_user_label;
	END IF;
	
	INSERT INTO users VALUES(DEFAULT, NULL, id_permission_var, name_var, surname_var, mail_var, password_var, DEFAULT);
	
	SELECT TRUE AS was_ok, 0 AS code, 'OK' AS message;
END$$
DELIMITER ;

/*DROP PROCEDURE delete_user*/
/*CALL delete_user(id_user) RETURN columns:  was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE delete_user(IN id_user_var BIGINT) 
BEGIN

	UPDATE users SET active=FALSE WHERE id_user=id_user_var AND active=TRUE;

	SELECT TRUE AS was_ok, 0 AS code, 'OK' AS message;
END$$
DELIMITER ;