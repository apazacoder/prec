# JUST DELETION OF ALL TABLES
SET FOREIGN_KEY_CHECKS = 0;
DELETE
FROM wp_cs_answers
WHERE id > 0;
DELETE
FROM wp_cs_categories
WHERE id > 0;
DELETE
FROM wp_cs_customers
WHERE id > 0;
DELETE
FROM wp_cs_enablers
WHERE id > 0;
DELETE
FROM wp_cs_plans
WHERE id > 0;
DELETE
FROM wp_cs_plans_services
WHERE id > 0;
DELETE
FROM wp_cs_questions
WHERE id > 0;
DELETE
FROM wp_cs_services
WHERE id > 0;
SET FOREIGN_KEY_CHECKS = 1;

# DISABLE PLUGIN AND DROP ALL TABLES TO RESET ALL
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE wp_cs_answers;
DROP TABLE wp_cs_categories;
DROP TABLE wp_cs_customers;
DROP TABLE wp_cs_enablers;
DROP TABLE wp_cs_plans;
DROP TABLE wp_cs_plans_services;
DROP TABLE wp_cs_questions;
DROP TABLE wp_cs_services;
SET FOREIGN_KEY_CHECKS = 1;

# ENABLE PLUGIN AND EXECUTE TO POBLATE THE DB
INSERT INTO wp_cs_categories(id, name, question)
VALUES ('1', 'Productos', 'Dar a conocer mis productos'),
       ('2', 'Servicios Profesionales', 'Dar a conocer mis servicios profesionales');

INSERT INTO wp_cs_questions(id, text, id_category)
VALUES (1, '¿Cuentas con un logo e imagen institucional?', '1'),
       (2, '¿Necesitas mostrar un catálogo de tus productos en tu sitio?', '1'),
       (3, '¿Quieres vender y que te hagan los pagos en línea?', '1'),
       (4, '¿Quisieras promocionarte en redes sociales?', '1'),
       (5, '¿Cuentas con un logo e imagen institucional?', '2'),
       (6, '¿Necesitas mostrar tus servicios desde tu sitio?', '2'),
       (7, '¿Quieres que te hagan los pagos en línea?', '2'),
       (8, '¿Quisieras promocionarte en  redes sociales?', '2');

INSERT INTO wp_cs_plans(id, name, is_custom, priority, id_category)
VALUES ('1', 'Basic', '0', '1', '1'),
       ('2', 'Professional', '0', '2', '1'),
       ('3', 'Premium', '0', '3', '1'),
       ('4', 'Enterprise', '0', '4', '1'),
       ('5', 'Inicial', '0', '1', '2'),
       ('6', 'Medio', '0', '2', '2'),
       ('7', 'Avanzado', '0', '3', '2');
INSERT INTO wp_cs_services(id, description, detail, price, is_generic, priority, id_category)
VALUES ('1', 'Consultoría', 'Servicio profesional especializado en el asesoramiento relacionado a productos', '15', '1',
        '1', '1'),
       ('2', 'Desarrollo del sitio', 'Análisis, diseño e implementación de un sitio web de sus productos', '20', '1',
        '2',
        '1'),
       ('3', 'Formulario de contacto',
        'Creación de un formulario de contacto para que pueda recibir e-mails de los clientes',
        '10', '0', '3', '1'),
       ('4', 'Logo de tu empresa', 'Diseño de un logo personalizado para su marca', '25', '0', '4', '1'),
       ('5', 'Diseño imagen institucional', 'Diseño de imagen acorde al branding de su empresa', '12', '0', '5', '1'),
       ('6', 'Estructuración redes sociales', 'Establecer y/o mejorar su presencia en redes sociales', '17', '0', '6',
        '1'),
       ('7', 'Catálogo de productos', 'Creación de un catálogo con el listado de sus productos ofertados', '27', '0',
        '7',
        '1'),
       ('8', 'Plataforma de pagos',
        'Implementación de una pasarela de pagos para que los clientes puedan hacer compras en línea', '35', '0', '8',
        '1'),
       ('9', 'Consultoría', 'Servicio profesional especializado en el asesoramiento relacionado a servicios', '20', '1',
        '1',
        '2'),
       ('10', 'Desarrollo del sitio', 'Análisis, diseño e implementación de un sitio web de sus servicios', '25', '1',
        '2',
        '2'),
       ('11', 'Formulario de contacto',
        'Creación de un formulario de contacto para que pueda recibir e-mails de los clientes',
        '15', '1', '3', '2'),
       ('12', 'Logo de tu empresa', 'Diseño de un logo personalizado para su marca', '30', '0', '4', '2'),
       ('13', 'Diseño imagen institucional', 'Diseño de imagen acorde al branding de su empresa', '17', '0', '5', '2'),
       ('14', 'Estructuración redes sociales', 'Establecer y/o mejorar su presencia en redes sociales', '22', '0', '6',
        '2'),
       ('15', 'Catálogo de servicios', 'Creación de un catálogo con el listado de sus servicios ofertados', '32', '0',
        '7',
        '2'),
       ('16', 'Sistema de reserva',
        'Implementación de un sistema para que los clientes hagan las reservas por los servicios',
        '100', '0', '8', '2');


INSERT INTO wp_cs_plans_services(id, id_plan, id_service)
VALUES ('1', '1', '1'),
       ('2', '1', '2'),
       ('3', '1', '3'),
       ('4', '2', '1'),
       ('5', '2', '2'),
       ('6', '2', '3'),
       ('7', '2', '4'),
       ('8', '2', '5'),
       ('9', '3', '1'),
       ('10', '3', '2'),
       ('11', '3', '3'),
       ('12', '3', '4'),
       ('13', '3', '5'),
       ('14', '3', '6'),
       ('15', '4', '1'),
       ('16', '4', '2'),
       ('17', '4', '3'),
       ('18', '4', '4'),
       ('19', '4', '5'),
       ('20', '4', '6'),
       ('21', '4', '7'),
       ('22', '4', '8'),
       ('23', '5', '9'),
       ('24', '5', '10'),
       ('25', '5', '11'),
       ('26', '6', '9'),
       ('27', '6', '10'),
       ('28', '6', '11'),
       ('29', '6', '12'),
       ('30', '6', '13'),
       ('31', '6', '14'),
       ('32', '7', '9'),
       ('33', '7', '10'),
       ('34', '7', '11'),
       ('35', '7', '12'),
       ('36', '7', '13'),
       ('37', '7', '14'),
       ('38', '7', '15'),
       ('39', '7', '16');

INSERT INTO wp_cs_enablers(id, desired_answer, id_service, id_question)
VALUES ('1', '0', '4', '1'),
       ('2', '1', '7', '2'),
       ('3', '1', '8', '3'),
       ('4', '1', '6', '4'),
       ('5', '0', '5', '1'),
       ('6', '0', '12', '5'),
       ('7', '1', '15', '6'),
       ('8', '1', '16', '7'),
       ('9', '1', '14', '8'),
       ('10', '0', '13', '5');
