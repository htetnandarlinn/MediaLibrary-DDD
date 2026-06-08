CREATE DATABASE IF NOT EXISTS `database` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `database`;

-- 1. PEOPLE TABLE
DROP TABLE IF EXISTS `People`;
CREATE TABLE `People` (
  `people_id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  PRIMARY KEY (`people_id`)
);

INSERT INTO `People` (`people_id`, `fullname`) VALUES 
(1,'Erich Gamma'),(2,'Richard Helm'),(3,'Ralph Johnson'),(4,'John Vlissides'),(5,'Robert C. Martin'),
(6,'Andrew Hunt'),(7,'David Thomas'),(8,'Martin Fowler'),(9,'Steve McConnell'),(10,'David Sklar'),
(11,'Matt Zandstra'),(12,'Matt Stauffer'),(13,'Douglas Crockford'),(14,'Marijn Haverbeke'),(15,'Eric Freeman'),
(16,'Elisabeth Robson'),(17,'Thomas H. Cormen'),(18,'Charles E. Leiserson'),(19,'Ronald L. Rivest'),(20,'Clifford Stein'),
(21,'Robert Zemeckis'),(22,'Tom Hanks'),(23,'Robin Wright'),(24,'Gary Sinise'),(25,'Frank Darabont'),
(26,'Tim Robbins'),(27,'Morgan Freeman'),(28,'Christopher Nolan'),(29,'Leonardo DiCaprio'),(30,'Joseph Gordon-Levitt'),
(31,'Brian De Palma'),(32,'Tom Cruise'),(33,'Christian Bale'),(34,'Heath Ledger'),(35,'Rob Reiner'),
(36,'Cary Elwes'),(37,'Mandy Patinkin'),(38,'Michael Jackson'),(39,'AC/DC'),(40,'Pink Floyd'),
(41,'Adele'),(42,'The Beatles'),(43,'Elvis Presley'),(44,'Garth Brooks');

-- 2. GENRES TABLE
DROP TABLE IF EXISTS `Genres`;
CREATE TABLE `Genres` (
  `genre_id` INT NOT NULL AUTO_INCREMENT,
  `genre` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`genre_id`)
);


INSERT INTO `Genres` VALUES 
(1,'Tech'),(2,'Drama'),(3,'Sci-Fi'),(4,'Action'),(5,'Fantasy'),
(6,'Pop'),(7,'Rock'),(8,'Progressive Rock'),(9,'Soul'),(10,'Rock & Roll'),(11,'Country');

-- 3. GENRE_CATEGORIES TABLE
DROP TABLE IF EXISTS `Genre_Categories`;
CREATE TABLE `Genre_Categories` (
  `genre_id` INT NOT NULL,
  `category` VARCHAR(6) NOT NULL, #maybe del
  PRIMARY KEY (`genre_id`),
  CONSTRAINT `fk_genre_category`
    FOREIGN KEY (`genre_id`)
    REFERENCES `Genres` (`genre_id`)
    ON DELETE CASCADE
);


INSERT INTO `Genre_Categories` VALUES 
(1,'Books'),(2,'Movies'),(3,'Movies'),(4,'Movies'),(5,'Movies'),
(6,'Music'),(7,'Music'),(8,'Music'),(9,'Music'),(10,'Music'),(11,'Music');

-- 4. MEDIA TABLE
DROP TABLE IF EXISTS `Media`;
CREATE TABLE `Media` (
  `media_id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `img` VARCHAR(255) NOT NULL,
  `genre_id` INT NOT NULL,
  `format` VARCHAR(25) NOT NULL,
  `year` INT NOT NULL,
  `category` VARCHAR(6) NOT NULL,
  PRIMARY KEY (`media_id`),
  CONSTRAINT `fk_media_genre`
    FOREIGN KEY (`genre_id`)
    REFERENCES `Genres` (`genre_id`)
    ON DELETE RESTRICT
);

INSERT INTO `Media` VALUES 
(1,'Design Patterns','img/books/design-patterns.jpg',1,'Paperback',1994,'Books'),
(2,'Clean Code','img/books/clean-code.jpg',1,'Paperback',2008,'Books'),
(3,'The Pragmatic Programmer','img/books/pragmatic-programmer.jpg',1,'Paperback',1999,'Books'),
(4,'Refactoring','img/books/refactoring.jpg',1,'Hardcover',1999,'Books'),
(5,'Code Complete','img/books/code-complete.jpg',1,'Paperback',2004,'Books'),
(6,'Learning PHP','img/books/learning-php.jpg',1,'Paperback',2014,'Books'),
(7,'PHP Objects, Patterns, and Practice','img/books/php-patterns.jpg',1,'Paperback',2010,'Books'),
(8,'Laravel Up & Running','img/books/laravel.jpg',1,'Paperback',2019,'Books'),
(9,'JavaScript: The Good Parts','img/books/js-good-parts.jpg',1,'Paperback',2008,'Books'),
(10,'Eloquent JavaScript','img/books/eloquent-js.jpg',1,'Paperback',2018,'Books'),
(11,'Head First Design Patterns','img/books/head-first-design-patterns.jpg',1,'Paperback',2004,'Books'),
(12,'Introduction to Algorithms','img/books/algorithms.jpg',1,'Hardcover',2009,'Books'),
(13,'Forrest Gump','img/movies/forestgump.jpg',2,'DVD',1994,'Movies'),
(14,'The Shawshank Redemption','img/movies/shawshank.jpg',2,'Blu-ray',1994,'Movies'),
(15,'Inception','img/movies/inception.jpg',3,'Blu-ray',2010,'Movies'),
(16,'Mission: Impossible','img/movies/mission-Impossible.jpg',4,'DVD',1996,'Movies'),
(17,'The Dark Knight','img/movies/dark-knight.jpg',4,'Blu-ray',2008,'Movies'),
(18,'The Princess Bride','img/movies/princess.jpg',5,'DVD',1987,'Movies'),
(19,'Thriller','img/music/thriller.jpg',6,'CD',1982,'Music'),
(20,'Back in Black','img/music/back-in-black.jpg',7,'CD',1980,'Music'),
(21,'The Dark Side of the Moon','img/music/dark-side-moon.jpg',8,'Vinyl',1973,'Music'),
(22,'21','img/music/adele-21.jpg',9,'CD',2011,'Music'),
(23,'Abbey Road','img/music/abbey-road.jpg',7,'Vinyl',1969,'Music'),
(24,'Elvis Presley','img/music/ElvisPresley.jpg',10,'CD',1956,'Music'),
(25,'Garth Brooks','img/music/Brooks_Garth.jpg',11,'CD',1990,'Music');

-- 5. MEDIA_PEOPLE TABLE (Linking IDs)
DROP TABLE IF EXISTS `Media_People`;
CREATE TABLE `Media_People` (
  `media_id` INT NOT NULL,
  `people_id` INT NOT NULL,
  `role` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`media_id`, `people_id`, `role`),
  CONSTRAINT `fk_mp_media`
    FOREIGN KEY (`media_id`)
    REFERENCES `Media` (`media_id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_mp_people`
    FOREIGN KEY (`people_id`)
    REFERENCES `People` (`people_id`)
    ON DELETE CASCADE
);


TRUNCATE TABLE `Media_People`;

INSERT INTO `Media_People` VALUES
(1,1,'author'),(1,2,'author'),(1,3,'author'),(1,4,'author'),
(2,5,'author'),(3,6,'author'),(3,7,'author'),(4,8,'author'),
(5,9,'author'),(6,10,'author'),(7,11,'author'),(8,12,'author'),
(9,13,'author'),(10,14,'author'),(11,15,'author'),(11,16,'author'),
(12,17,'author'),(12,18,'author'),(12,19,'author'),(12,20,'author'),
(13,21,'director'),(13,22,'star'),(13,23,'star'),(13,24,'star'),
(14,25,'director'),(14,26,'star'),(14,27,'star'),
(15,28,'director'),(15,29,'star'),(15,30,'star'),
(16,31,'director'),(16,32,'star'),
(17,28,'director'),(17,33,'star'),(17,34,'star'),
(18,35,'director'),(18,36,'star'),(18,23,'star'),(18,37,'star'),
(19,38,'artist'),
(20,39,'artist'),
(21,40,'artist'),
(22,41,'artist'),
(23,42,'artist'),
(24,43,'artist'),
(25,44,'artist');


-- 6. BOOKS TABLE (ISBN & Publisher)
DROP TABLE IF EXISTS `Books`;
CREATE TABLE `Books` (
  `media_id` INT NOT NULL,
  `publisher` VARCHAR(50) NOT NULL,
  `isbn` VARCHAR(14) NOT NULL,
  PRIMARY KEY (`media_id`),
  CONSTRAINT `fk_books_media`
    FOREIGN KEY (`media_id`)
    REFERENCES `Media` (`media_id`)
    ON DELETE CASCADE
);

select * from media;
TRUNCATE TABLE `Books`;

INSERT INTO `Books` VALUES
(1,'Prentice Hall','9780201633610'),
(2,'Prentice Hall','9780132350884'),
(3,'Addison-Wesley','9780201616224'),
(4,'Addison-Wesley','9780201485677'),
(5,'Microsoft Press','9780735619678'),
(6,'O\'Reilly','9781449361068'),
(7,'Apress','9781430229254'),
(8,'O\'Reilly','9781492041214'),
(9,'O\'Reilly','9780596517748'),
(10,'No Starch Press','9781593279509'),
(11,'O\'Reilly','9780596007126'),
(12,'MIT Press','9780262033848');

select * from books;
