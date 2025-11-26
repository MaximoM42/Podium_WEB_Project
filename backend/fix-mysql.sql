-- Script para solucionar "MySQL server has gone away"
-- Ejecuta esto en phpMyAdmin o desde la línea de comandos

-- Aumentar límites de MySQL
SET GLOBAL max_allowed_packet=67108864; -- 64MB
SET GLOBAL wait_timeout=28800; -- 8 horas
SET GLOBAL interactive_timeout=28800; -- 8 horas
SET GLOBAL connect_timeout=600; -- 10 minutos

-- Verificar los valores actuales
SHOW VARIABLES LIKE 'max_allowed_packet';
SHOW VARIABLES LIKE 'wait_timeout';
SHOW VARIABLES LIKE 'interactive_timeout';

