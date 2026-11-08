CREATE DATABASE if NOT EXISTS BancoEmbrapa; 

use BancoEmbrapa; 

CREATE TABLE cargo( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    cargo  VARCHAR(50) NOT NULL 
); 

CREATE TABLE categoria( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nome VARCHAR(50) NOT NULL, 
    status BOOLEAN NOT NULL DEFAULT 1 
); 

CREATE TABLE IF NOT EXISTS usuarios( 
    matricula INT PRIMARY KEY NOT NULL, 
    nome VARCHAR (50) NOT NULL, 
    email VARCHAR(100) NOT NULL, 
    id_cargo INT NOT NULL, 
    senha varchar(16) NOT NULL, 
    status BOOLEAN NOT NULL DEFAULT 1, 
    FOREIGN KEY (id_cargo) REFERENCES cargo(id) 
); 

CREATE TABLE IF NOT EXISTS log( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    operacao VARCHAR(100) NOT NULL, 
    matricula_usuario INT NOT NULL, 
    tabela_afetada VARCHAR(30) NOT NULL, 
    data_hora DATETIME NOT NULL, 
    FOREIGN KEY (matricula_usuario) REFERENCES usuarios(matricula) 
); 

CREATE TABLE IF NOT EXISTS acao( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    id_categoria INT NOT NULL, 
    matricula_pesquisador INT NOT NULL, 
    titulo VARCHAR(50) NOT NULL, 
    descricao VARCHAR(100) NOT NULL, 
    data DATE NOT NULL, 
    status VARCHAR(10) NOT NULL DEFAULT 'Em Análise', 
    FOREIGN KEY (id_categoria) REFERENCES categoria(id), 
    FOREIGN KEY (matricula_pesquisador) REFERENCES usuarios(matricula) 
); 

INSERT INTO cargo (cargo) VALUES ('Administrador'), ('Validador'), ('Comitê'), ('Pesquisador'); 
INSERT INTO usuarios(matricula, nome, email, id_cargo, senha) VALUES (12345, 'Admin', 'admin@gmail.com', 1, '123admin'); 

SELECT * FROM cargo; 
SELECT * FROM usuarios; 
SELECT * FROM categoria; 
SELECT * FROM acao; 
SELECT * FROM log;