# DROP TABLES
DROP TABLE wp_cs_precios;
DROP TABLE wp_cs_lecturas;


INSERT INTO `wp_cs_precios` (`id`, `aceite`, `precio`, `fecha_precio`, `fuente`) VALUES (0, 'virgen', '3161.90', '2021-05-11 10:45:26 ', 'poolred');


INSERT INTO `wp_cs_precios` (`id`, `aceite`, `precio`, `precio_historico`, `precio_arbequina`, `fecha_precio`, `fecha_precio_rango`, `fuente`, `id_lectura`) VALUES (NULL, 'virgen', '3.1416666666667', '3.125', '0', '2021-05-14 00:00:00', '2021-05-20 00:00:00', 'infaoliva', '12');




SELECT * FROM wp_cs_precios WHERE aceite='extra_virgen' AND precio_historico='3.267' AND fecha_precio='2021-05-14 00:00:00' AND fuente='infaoliva';

SELECT * FROM wp_cs_precios WHERE aceite='extra_virgen' AND precio_historico=ROUND(3.267, 9) AND fecha_precio='2021-05-14 00:00:00' AND fuente='infaoliva';

SELECT * FROM wp_cs_precios WHERE fuente='infaoliva' ORDER BY id desc LIMIT 3;


(SELECT * FROM wp_cs_precios WHERE fuente='infaoliva' AND aceite='extra_virgen' ORDER BY id desc LIMIT 1) UNION
(SELECT * FROM wp_cs_precios WHERE fuente='infaoliva' AND aceite='virgen' ORDER BY id desc LIMIT 1) UNION
(SELECT * FROM wp_cs_precios WHERE fuente='infaoliva' AND aceite='lampante' ORDER BY id desc LIMIT 1);


SELECT * FROM wp_cs_precios WHERE fuente='almazaras';


SELECT p.id as id, p.aceite, p.precio, p.precio_historico, p.precio_arbequina, p.fecha_precio, p.fecha_precio_rango, p.fuente, p.id_lectura, l.leido_el, l.codigo_respuesta FROM wp_cs_precios p
				JOIN wp_cs_lecturas l
				ON p.id_lectura = l.id
				ORDER BY p.fecha_precio_rango ASC;


INSERT INTO dbpreciodelaceite.wp_cs_precios (id, aceite, precio, precio_historico, precio_arbequina, fecha_precio, fecha_precio_rango, fuente, id_lectura) VALUES (50, 'extra_virgen', 3.272333333, 4.000000000, 0.000000000, '2021-05-17 00:00:00', '2021-05-21 00:00:00', 'infaoliva', 31);
INSERT INTO dbpreciodelaceite.wp_cs_precios (id, aceite, precio, precio_historico, precio_arbequina, fecha_precio, fecha_precio_rango, fuente, id_lectura) VALUES (51, 'virgen', 3.141666667, 4.000000000, 0.000000000, '2021-05-17 00:00:00', '2021-05-21 00:00:00', 'infaoliva', 31);
INSERT INTO dbpreciodelaceite.wp_cs_precios (id, aceite, precio, precio_historico, precio_arbequina, fecha_precio, fecha_precio_rango, fuente, id_lectura) VALUES (52, 'lampante', 3.039666667, 4.000000000, 0.000000000, '2021-05-17 00:00:00', '2021-05-21 00:00:00', 'infaoliva', 31);

SELECT * FROM dbpreciodelaceite.wp_cs_precios WHERE aceite = 'virgen' AND fuente='infaoliva' ORDER BY fecha_precio_rango DESC;
SELECT * FROM dbpreciodelaceite.wp_cs_precios WHERE aceite = 'virgen' AND fuente='poolred' ORDER BY fecha_precio_rango DESC;
SELECT * FROM dbpreciodelaceite.wp_cs_precios WHERE aceite = 'virgen' AND fuente='almazaras' ORDER BY fecha_precio_rango DESC;