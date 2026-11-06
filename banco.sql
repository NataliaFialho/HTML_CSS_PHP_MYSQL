CREATE DATABASE if NOT EXISTS BancoEmbrapa;

use BancoEmbrapa;

CREATE TABLE cargo(
    id INT AUTO_INCREMENT PRIMARY KEY ,
    cargo  VARCHAR(50) NOT NULL
);

CREATE TABLE categoria(
    id INT AUTO_INCREMENT PRIMARY KEY ,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS usuarios(
    matricula INT PRIMARY KEY NOT NULL,
    nome VARCHAR (50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    id_cargo INT NOT NULL,
    FOREIGN KEY (id_cargo) REFERENCES cargo(id)
);

CREATE TABLE IF NOT EXISTS acao(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    matricula_pesquisador INT NOT NULL,
    titulo VARCHAR(50) NOT NULL,
    descricao VARCHAR(100) NOT NULL,
    data DATE NOT NULL,
    status VARCHAR(10) NOT NULL,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id),
    FOREIGN KEY (matricula_pesquisador) REFERENCES usuarios(matricula)
);

CREATE TABLE IF NOT EXISTS log(
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    operacao VARCHAR(50) NOT NULL,
    matricula_usuario INT NOT NULL,
    tabela_afetada VARCHAR(50) NOT NULL,
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (matricula_usuario) REFERENCES usuarios(matricula)
);

INSERT INTO cargo VALUES
    (1, "Administrador"),
    (2, "Validador"),
    (3, "Comitê"),
    (4, "Pesquisador");

INSERT INTO usuarios VALUES
    (12345, "admin", "admin@gmail.com", 1);