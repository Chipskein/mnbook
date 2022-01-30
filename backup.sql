PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE books(
                    identifier integer not null,
                    name varchar(400) not null,
                    last_page integer default 0,
                    register_time default CURRENT_TIMESTAMP,
                    last_update default CURRENT_TIMESTAMP,
                    PRIMARY KEY(identifier)    
                );
INSERT INTO books VALUES(820926471,'a-startup-enxuta-eric-ries-livro-completo.pdf',0,'2022-01-26 17:19:21','2022-01-26 17:19:21');
INSERT INTO books VALUES(1279185343,'[ALGORITHMS][Introduction to Algorithms. Third Edition].pdf',40,'2022-01-26 17:19:20','2022-01-27 22:48:53');
INSERT INTO books VALUES(1312280940,'Objec-orientarion and analisy and design.pdf',0,'2022-01-26 17:19:20','2022-01-26 17:19:20');
INSERT INTO books VALUES(1507209064,'Lingua latina per se illustrata.pdf',14,'2022-01-26 17:19:20','2022-01-28 09:54:43');
INSERT INTO books VALUES(1913313768,'[PROGRAMMING][Clean Code by Robert C Martin].pdf',0,'2022-01-26 17:19:21','2022-01-26 17:19:21');
INSERT INTO books VALUES(2050437747,'Alfred V. Aho, Monica S. Lam, Ravi Sethi, Jeffrey D. Ullman-Compilers - Principles, Techniques, and Tools-Pearson_Addison Wesley (2006).pdf',0,'2022-01-26 17:19:20','2022-01-26 17:19:20');
INSERT INTO books VALUES(2710514558,'Algorithm+Data Structure=Programs [Wirth].pdf',19,'2022-01-26 17:19:20','2022-01-27 19:06:46');
INSERT INTO books VALUES(3023191101,'104The Pragmatic Programmer, From Journeyman To Master - Andrew Hunt, David Thomas - Addison Wesley - 1999.pdf',0,'2022-01-26 17:19:20','2022-01-26 17:19:20');
INSERT INTO books VALUES(3358057944,'Uml dismified.pdf',0,'2022-01-26 17:19:21','2022-01-26 17:19:21');
INSERT INTO books VALUES(3400333352,'MythicalManMonth.pdf',0,'2022-01-26 17:19:20','2022-01-26 17:19:20');
INSERT INTO books VALUES(3592458726,'The_Unified_Software_Development_Process_and_Frame.pdf',0,'2022-01-26 17:19:20','2022-01-26 17:19:20');
INSERT INTO books VALUES(3926258613,'designpatterns.pdf',0,'2022-01-26 17:19:20','2022-01-26 17:19:20');
COMMIT;
