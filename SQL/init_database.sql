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
	price_reduction REAL NOT NULL 
);
INSERT INTO promotions VALUES(1, 'Standardowa cena', 1.0);
INSERT INTO promotions VALUES(2, 'Promocja świąteczna', 0.7);

CREATE TABLE offers
(
	id_offer BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
	id_user BIGINT NOT NULL REFERENCES users(id_user),
	id_promotion INT NOT NULL REFERENCES promotions(id_promotion),
	title VARCHAR(100) NOT NULL,
	description TEXT NOT NULL,
	best BOOLEAN NOT NULL DEFAULT FALSE,
	cost_per_day DECIMAL(5,2) NOT NULL,
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
INSERT INTO type_payment_status VALUES(1, 'Płatność do zapłacenia', TRUE, FALSE, FALSE, FALSE),
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
	
	SELECT TRUE AS was_ok, 0 AS code, 'Rezerwacja została ukończona pomyślnie, poczekaj na akceptację Administratora' AS message;
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
	
	SELECT TRUE AS was_ok, 0 AS code, 'Użytkownik został potwierdzony' AS message;
END$$
DELIMITER ;


/*DROP VIEW get_users_to_confirm*/
/*SELECT * FROM get_users_to_confirm RETURN columns:  id_client_registration, name, surname, mail, password, agreement_for_marketing, agreement_for_rodo, agreement_for_regulations, ip_address_v4, creation_date    */
CREATE VIEW get_users_to_confirm
AS
	SELECT 
		u.id_client_registration,
		u.name,
		u.surname,
		u.mail,
		'****' AS password,
		u.agreement_for_marketing,
		u.agreement_for_rodo,
		u.agreement_for_regulations,
		u.ip_address_v4,
		u.creation_date
	FROM 
		clients_registration AS u
	WHERE 
		u.confirmation=FALSE;

/*DROP VIEW get_all_users*/
/*SELECT * FROM get_all_users RETURN columns:  id_user, id_permission, permission_name, name, surname, mail, password    */
CREATE VIEW get_all_users
AS
	SELECT 
		u.id_user,
		u.id_permission,
		p.name AS permission_name,
		u.name,
		u.surname,
		u.mail,
		'****' AS password,
		cr.ip_address_v4
	FROM 
		users AS u
		INNER JOIN permissions AS p ON p.id_permission=u.id_permission
		LEFT JOIN clients_registration AS cr ON cr.id_client_registration=u.id_client_registration
	WHERE 
		u.active=TRUE;
		

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
		SELECT FALSE AS was_ok, 2 AS code, 'Podany email już istnieje' AS message;
		LEAVE create_user_label;
	END IF;
	
	INSERT INTO users VALUES(DEFAULT, NULL, id_permission_var, name_var, surname_var, mail_var, password_var, DEFAULT);
	
	SELECT TRUE AS was_ok, 0 AS code, 'Użytkownik został utworzony' AS message;
END$$
DELIMITER ;

/*DROP PROCEDURE delete_user*/
/*CALL delete_user(id_user) RETURN columns:  was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE delete_user(IN id_user_var BIGINT) 
BEGIN

	UPDATE users SET active=FALSE WHERE id_user=id_user_var AND active=TRUE;

	SELECT TRUE AS was_ok, 0 AS code, 'Użytkownik został usuniety' AS message;
END$$
DELIMITER ;

/*DROP PROCEDURE modification_user*/
/*CALL modification_user(id_user, id_permission, 'imie', 'nazwisko', 'mail', 'haslo') RETURN columns:  was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE modification_user(IN id_user_var BIGINT, IN id_permission_var INT, IN name_var VARCHAR(50), IN surname_var VARCHAR(50), IN mail_var VARCHAR(50), password_var VARCHAR(100)) 
modification_user_label:BEGIN


	IF NOT EXISTS (SELECT 1 FROM users WHERE id_user=id_user_var) THEN
		SELECT FALSE AS was_ok, 1 AS code, 'Podany użytkownik nie istnieje' AS message;
		LEAVE modification_user_label;
	END IF;
	
	UPDATE users SET 
		id_permission=COALESCE(id_permission_var, id_permission), 
		name=COALESCE(name_var, name),
		surname=COALESCE(surname_var, surname),
		mail=COALESCE(mail_var, mail),
		password=COALESCE(password_var, password)
	WHERE 
		id_user=id_user_var AND
		(
			id_permission<>COALESCE(id_permission_var, id_permission) OR 
			name<>COALESCE(name_var, name) OR
			surname<>COALESCE(surname_var, surname) OR
			mail<>COALESCE(mail_var, mail) OR
			password<>COALESCE(password_var, password)
		);
	
	SELECT TRUE AS was_ok, 0 AS code, 'Dane klienta zostały zmienione' AS message;
END$$
DELIMITER ;



/*DROP PROCEDURE create_offer*/
/*CALL create_offer(id_user, id_permission, 'imie', 'title', 'description', '2018-11-09', '2018-11-23') RETURN columns:  was_ok, code, message, id_offer */
DELIMITER $$
CREATE PROCEDURE create_offer(IN id_user_var BIGINT, IN id_promotion_var INT, IN title_var VARCHAR(100), IN description_var TEXT, IN cost_per_day_var DECIMAL(5,2), IN date_validity_from_var DATE, IN date_validity_to_var DATE) 
create_offer_label:BEGIN

	IF id_user_var < 1 THEN
		SELECT FALSE AS was_ok, 1 AS code, 'Nieprawidłowy ID klienta' AS message;
		LEAVE create_offer_label;
	END IF;
	
	IF date_validity_from_var < (SELECT NOW()) THEN
		SELECT FALSE AS was_ok, 2 AS code, 'Oferty nie mozna utworzyć w przeszłości' AS message;
		LEAVE create_offer_label;
	END IF;
	
	IF date_validity_to_var <= date_validity_from_var THEN
		SELECT FALSE AS was_ok, 3 AS code, 'Okres oferty jest nieprawidłowy' AS message;
		LEAVE create_offer_label;
	END IF;

	INSERT INTO offers VALUES(DEFAULT, id_user_var, id_promotion_var, title_var, description_var, FALSE, cost_per_day_var, date_validity_from_var, date_validity_to_var, TRUE, FALSE, NULL);

	SELECT TRUE AS was_ok, 0 AS code, 'Oferta została utworzona' AS message;
END$$
DELIMITER ;


/*DROP PROCEDURE modification_offer*/
/*CALL modification_offer(id_user, id_permission, 'imie', 'nazwisko', 'mail', 'haslo') RETURN columns:  was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE modification_offer(IN id_offer_var BIGINT, IN id_promotion_var INT, IN title_var VARCHAR(100), IN description_var VARCHAR(200), IN date_validity_from_var DATE, IN date_validity_to_var DATE) 
modification_offer_label:BEGIN

	/*TODO*/

	SELECT TRUE AS was_ok, 0 AS code, 'OK' AS message;
END$$
DELIMITER ;


/*DROP PROCEDURE delete_offer*/
/*CALL delete_offer(id_offer_var) RETURN columns:  was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE delete_offer(IN id_offer_var BIGINT) 
delete_offer_label:BEGIN

	UPDATE offers SET active=FALSE WHERE id_offer = id_offer_var;

	SELECT TRUE AS was_ok, 0 AS code, 'Oferta została usunięta' AS message;
END$$
DELIMITER ;


/*DROP PROCEDURE confirm_offer*/
/*CALL confirm_offer(id_offer_var) RETURN columns:  was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE confirm_offer(IN id_offer_var BIGINT) 
confirm_offer_label:BEGIN

	UPDATE offers SET confirmation=TRUE WHERE id_offer = id_offer_var;

	SELECT TRUE AS was_ok, 0 AS code, 'Oferta została potwierdzona' AS message;
END$$
DELIMITER ;


/*DROP VIEW get_all_offer*/
/*SELECT * FROM get_all_offer RETURN columns:  id_offer, id_user, id_promotion, m_promotion_name, m_promotion_reduction, m_price_per_day, m_promotion_price_per_day, m_title, m_description, m_best, m_date_validity_from, m_date_validity_to    */
CREATE VIEW get_all_offer
AS
	SELECT 
		o.id_offer,
		o.id_user,
		o.id_promotion,
		pr.name,
		pr.price_reduction,
		cost_per_day AS price_per_day,
		cost_per_day * pr.price_reduction AS promotion_price_per_day, 
		o.title,
		o.description,
		o.best,
		DATE(o.date_validity_from) AS date_validity_from,
		DATE(o.date_validity_to) AS date_validity_to
	FROM 
		offers AS o 
		INNER JOIN promotions AS pr ON pr.id_promotion=o.id_promotion
	WHERE 
		o.active=TRUE 
		AND o.confirmation = TRUE
		AND DATE(NOW()) <= DATE(o.date_validity_to);
		
		
/*DROP VIEW get_offer_to_confirm*/
/*SELECT * FROM get_offer_to_confirm RETURN columns:  id_offer, id_user, id_promotion, m_promotion_name, m_promotion_reduction, m_price_per_day, m_promotion_price_per_day, m_title, m_description, m_best, m_date_validity_from, m_date_validity_to    */
CREATE VIEW get_offer_to_confirm
AS
	SELECT 
		o.id_offer,
		o.id_user,
		o.id_promotion,
		pr.name,
		pr.price_reduction,
		cost_per_day AS price_per_day,
		cost_per_day * pr.price_reduction AS promotion_price_per_day, 
		o.title,
		o.description,
		o.best,
		DATE(o.date_validity_from) AS date_validity_from,
		DATE(o.date_validity_to) AS date_validity_to
	FROM 
		offers AS o 
		INNER JOIN promotions AS pr ON pr.id_promotion=o.id_promotion
	WHERE 
		o.active=TRUE 
		AND o.confirmation = FALSE;
		
		
/*DROP VIEW get_all_promotions*/
/*SELECT * FROM get_all_promotions RETURN columns: id_promotion, promotion_name, price_reduction*/
CREATE VIEW get_all_promotions
AS
	SELECT 
		id_promotion,
		name AS promotion_name,
		price_reduction
	FROM 
		promotions;
		
		
/*TODO: dodanie pobieranie zdjęcia*/
/*DROP PROCEDURE get_offer_by_id*/
/*CALL get_offer_by_id(id_offer_var) RETURN columns:  id_offer, id_user, id_promotion, m_promotion_name, m_promotion_reduction, m_price_per_day, m_promotion_price_per_day, m_title, m_description, m_best, m_date_validity_from, m_date_validity_to, o.active, o.confirmation */
DELIMITER $$
CREATE PROCEDURE get_offer_by_id(IN id_offer_var BIGINT)
BEGIN
	SELECT 
		o.id_offer,
		o.id_user,
		o.id_promotion,
		pr.name,
		pr.price_reduction,
		cost_per_day AS price_per_day,
		cost_per_day * pr.price_reduction AS promotion_price_per_day, 
		o.title,
		o.description,
		o.best,
		DATE(o.date_validity_from) AS date_validity_from,
		DATE(o.date_validity_to) AS date_validity_to,
		o.active,
		o.confirmation
	FROM 
		offers AS o 
		INNER JOIN promotions AS pr ON pr.id_promotion=o.id_promotion
	WHERE
		o.id_offer = id_offer_var;
END$$
DELIMITER ;


/*DROP PROCEDURE get_offer_by_client_id*/
/*CALL get_offer_by_client_id(id_client_var) RETURN columns:  */
DELIMITER $$
CREATE PROCEDURE get_offer_by_client_id(IN id_client_var BIGINT)
BEGIN
	SELECT 
		o.id_offer,
		o.id_user,
		o.id_promotion,
		pr.name,
		pr.price_reduction,
		cost_per_day AS price_per_day,
		cost_per_day * pr.price_reduction AS promotion_price_per_day, 
		o.title,
		o.description,
		o.best,
		DATE(o.date_validity_from) AS date_validity_from,
		DATE(o.date_validity_to) AS date_validity_to,
		o.active,
		o.confirmation
	FROM 
		offers AS o 
		INNER JOIN promotions AS pr ON pr.id_promotion=o.id_promotion
	WHERE
		o.id_user = id_client_var;
END $$
DELIMITER ;

		
/*DROP PROCEDURE add_image*/
/*CALL add_image(id_offer_var, image_var, description_var, main_var) RETURN columns:  was_ok, code, message, id_offer_image */
DELIMITER $$
CREATE PROCEDURE add_image(IN id_offer_var BIGINT, IN image_var LONGBLOB, IN description_var VARCHAR(200), IN main_var BOOLEAN) 
add_image_label:BEGIN

	/*TODO*/

	SELECT TRUE AS was_ok, 0 AS code, 'OK' AS message, -1 AS id_offer_image;
END$$
DELIMITER ;


/*DROP PROCEDURE delete_image*/
/*CALL delete_image(id_offer_image_var) RETURN columns:  was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE delete_image(IN id_offer_image_var BIGINT) 
delete_image_label:BEGIN

	/*TODO*/

	SELECT TRUE AS was_ok, 0 AS code, 'OK' AS message;
END$$
DELIMITER ;


/*DROP PROCEDURE modification_image*/
/*CALL modification_image(id_offer_image_var) RETURN columns:  was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE modification_image(IN id_offer_image_var BIGINT, IN description_var VARCHAR(200), IN main_var BOOLEAN) 
modification_image_label:BEGIN

	/*TODO*/
	
	SELECT TRUE AS was_ok, 0 AS code, 'OK' AS message;
END$$
DELIMITER ;


/*DROP PROCEDURE create_reservation*/
/*CALL create_reservation(id_user_var, id_offer_var, id_promotion_var, date_from, date_to) RETURN columns:  was_ok, code, message, id_reservation */
DELIMITER $$
CREATE PROCEDURE create_reservation(IN id_user_var BIGINT, IN id_offer_var BIGINT, IN date_from_var DATE, IN date_to_var DATE) 
create_reservation_label:BEGIN

	DECLARE m_days INT;
	DECLARE m_cost_per_day DECIMAL(5,2);
	DECLARE m_cost_per_day_promo DECIMAL(5,2);
	DECLARE m_date_validity_from DATE; 
	DECLARE m_date_validity_to DATE;
	DECLARE m_id_user BIGINT;
	
	IF date_from_var < (SELECT NOW()) THEN
		SELECT FALSE AS was_ok, 1 AS code, 'Rezerwacji nie mozna utworzyć w przeszłości' AS message;
		LEAVE create_reservation_label;
	END IF;
	
	IF date_to_var <= date_from_var THEN
		SELECT FALSE AS was_ok, 2 AS code, 'Okres rezerwacji jest nieprawidłowy' AS message;
		LEAVE create_reservation_label;
	END IF;
	
	SELECT DATEDIFF(date_to_var, date_from_var) INTO m_days;

	SELECT 
		o.cost_per_day,
		o.cost_per_day * pr.price_reduction,
		o.date_validity_from,
		o.date_validity_to,
		o.id_user
	INTO 
		m_cost_per_day,
		m_cost_per_day_promo,
		m_date_validity_from,
		m_date_validity_to,
		m_id_user
	FROM 
		offers AS o 
		INNER JOIN promotions AS pr ON pr.id_promotion=o.id_promotion
	WHERE
		o.id_offer = id_offer_var;
	
	IF m_id_user = id_user_var THEN
		SELECT FALSE AS was_ok, 3 AS code, 'Nie możesz zarezerwować własnej oferty' AS message;
		LEAVE create_reservation_label;
	END IF;
	
	IF m_date_validity_from > date_from_var OR m_date_validity_to < date_to_var THEN
		SELECT FALSE AS was_ok, 4 AS code, 'Okres rezerwacji nie mieści się w okresie ważności oferty' AS message;
		LEAVE create_reservation_label;
	END IF;
	
	IF EXISTS(SELECT 1 FROM reservations WHERE id_offer=id_offer_var AND ((date_from_var BETWEEN date_from AND date_to) OR (date_to_var BETWEEN date_from AND date_to))) THEN
		SELECT FALSE AS was_ok, 5 AS code, 'Na podany okres istnieje już inna rezerwacja, wybierz inny okres' AS message;
		LEAVE create_reservation_label;
	END IF;
	
	INSERT INTO reservations VALUES(DEFAULT, id_user_var, id_offer_var, (SELECT id_promotion FROM offers WHERE id_offer=id_offer_var), NOW(), date_from_var, date_to_var);
	
	INSERT INTO payment VALUES(DEFAULT, LAST_INSERT_ID(), 1, NOW(), m_days * m_cost_per_day_promo, 0.0, NOW());  
	
	SELECT TRUE AS was_ok, 0 AS code, 'Rezerwacja została utworzona' AS message;
END$$
DELIMITER ;


/*DROP PROCEDURE modification_reservation*/
/*CALL modification_reservation(id_user_var, id_offer_var, id_promotion_var, date_from, date_to) RETURN columns:  was_ok, code, message, id_reservation */
DELIMITER $$
CREATE PROCEDURE modification_reservation(IN id_reservation_var BIGINT) 
modification_reservation_label:BEGIN

	/*TODO*/
	
	SELECT TRUE AS was_ok, 0 AS code, 'OK' AS message, -1 AS id_reservation;
END$$
DELIMITER ;


/*DROP PROCEDURE get_reservation_by_client_id*/
/*CALL get_reservation_by_client_id(id_client_var) RETURN columns: id_reservation, id_user, id_offer, title, id_promotion, name, price_reduction, creation_date, date_from, date_to, to_pay, paid */
DELIMITER $$
CREATE PROCEDURE get_reservation_by_client_id(IN id_client_var BIGINT)
BEGIN
	SELECT 	
		r.id_reservation,
		r.id_user,
		r.id_offer,
		o.title,
		r.id_promotion,
		p.name AS promotion_name,
		p.price_reduction,
		r.creation_date,
		DATE(r.date_from) AS date_from,
		DATE(r.date_to) AS date_to,
		pay.to_pay,
		pay.paid,
		type_payment.name AS payment_name
	FROM 
		reservations AS r 
		INNER JOIN offers AS o ON o.id_offer=r.id_offer
		INNER JOIN promotions AS p ON p.id_promotion=r.id_promotion
		INNER JOIN payment AS pay ON pay.id_reservation=r.id_reservation
		INNER JOIN type_payment_status AS type_payment ON type_payment.id_type_payment_status=pay.id_type_payment_status
	WHERE
		r.id_user=id_client_var;
END $$
DELIMITER ;


/*DROP VIEW get_all_reservations*/
/*SELECT * FROM get_all_reservations RETURN columns: id_reservation, id_user, id_offer, name, surname, mail, title, id_promotion, name, price_reduction, creation_date, date_from, date_to, to_pay, paid */
CREATE VIEW get_all_reservations
AS
	SELECT 	
		r.id_reservation,
		r.id_user,
		r.id_offer,
		u.name,
		u.surname,
		u.mail,
		o.title,
		r.id_promotion,
		p.name AS promotion_name,
		p.price_reduction,
		r.creation_date,
		DATE(r.date_from) AS date_from,
		DATE(r.date_to) AS date_to,
		pay.to_pay,
		pay.paid,
		type_payment.name AS payment_name
	FROM 
		reservations AS r 
		INNER JOIN offers AS o ON o.id_offer=r.id_offer
		INNER JOIN promotions AS p ON p.id_promotion=r.id_promotion
		INNER JOIN payment AS pay ON pay.id_reservation=r.id_reservation
		INNER JOIN type_payment_status AS type_payment ON type_payment.id_type_payment_status=pay.id_type_payment_status
		INNER JOIN users AS u ON u.id_user=r.id_user;

/*DROP PROCEDURE pay_reservation*/
/*CALL pay_reservation(id_client_var) RETURN columns: was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE pay_reservation(IN id_reservation_var BIGINT)
pay_reservation_label:BEGIN

	IF EXISTS (SELECT 1 FROM payment WHERE id_reservation=id_reservation_var AND id_type_payment_status=3) THEN
		SELECT FALSE AS was_ok, 1 AS code, 'Płatność był już zapłacona.' AS message;
		LEAVE pay_reservation_label;
	END IF;
	
	UPDATE payment SET id_type_payment_status=3, date_change_payment_status=NOW(), paid=to_pay WHERE id_reservation=id_reservation_var;

	SELECT TRUE AS was_ok, 0 AS code, 'Płatność została zapłacona, rezerwacja została potwierdzona' AS message;
END $$
DELIMITER ;


/*DROP PROCEDURE cancel_reservation*/
/*CALL cancel_reservation(id_client_var) RETURN columns: was_ok, code, message */
DELIMITER $$
CREATE PROCEDURE cancel_reservation(IN id_reservation_var BIGINT)
cancel_reservation_label:BEGIN

	IF EXISTS (SELECT 1 FROM payment WHERE id_reservation=id_reservation_var AND id_type_payment_status=3) THEN
		UPDATE payment SET id_type_payment_status=5, date_change_payment_status=NOW() WHERE id_reservation=id_reservation_var;
	ELSE
		UPDATE payment SET id_type_payment_status=4, date_change_payment_status=NOW() WHERE id_reservation=id_reservation_var;
	END IF;

	SELECT TRUE AS was_ok, 0 AS code, 'Płatność została anulowana' AS message;
END $$
DELIMITER ;


/*DROP VIEW get_all_payments*/
/*SELECT * FROM get_all_payments RETURN columns: id_payment, id_type_payment_status, name, date_change_payment_status, to_pay, paid, date_validity */
CREATE VIEW get_all_payments
AS
SELECT
	p.id_payment,
	p.id_type_payment_status,
	tps.name,
	p.date_change_payment_status,
	p.to_pay,
	p.paid,
	p.date_validity
FROM 
	payment AS p
	INNER JOIN type_payment_status AS tps ON tps.id_type_payment_status=p.id_type_payment_status
ORDER BY 
	p.id_payment ASC;

