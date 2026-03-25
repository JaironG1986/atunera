
CREATE DATABASE IF NOT EXISTS industria_atunera_pro;
USE industria_atunera_pro;

CREATE TABLE IF NOT EXISTS proveedores (
    id INT PRIMARY KEY,
    nombre VARCHAR(100),
    nivel_riesgo ENUM('bajo', 'medio', 'alto')
);

INSERT INTO proveedores (id, nombre, nivel_riesgo) VALUES 
(1, 'Pesquera del Pacífico', 'bajo'),
(2, 'Mar Adentro S.A.', 'medio'),
(3, 'Proveedores Locales', 'alto')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);


CREATE TABLE IF NOT EXISTS predicciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    peso_neto DECIMAL(10,2) NOT NULL,
    temperatura DECIMAL(5,2) NOT NULL,
    proveedor_id INT,
    talla VARCHAR(20),
    rendimiento_esperado DECIMAL(5,4),
    kg_utilizables DECIMAL(10,2),
    alerta_status TINYINT(1),
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
    INDEX (fecha_registro)
);
