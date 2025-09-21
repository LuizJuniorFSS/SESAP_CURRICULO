-- Estrutura do banco de dados para sistema de currículos
-- Execute este script no MySQL para criar a tabela necessária

CREATE DATABASE IF NOT EXISTS sesap_curriculo;
USE sesap_curriculo;

CREATE TABLE IF NOT EXISTS curriculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    cargo_desejado TEXT NOT NULL,
    escolaridade VARCHAR(100) NOT NULL,
    observacoes TEXT,
    arquivo_nome VARCHAR(255) NOT NULL,
    arquivo_caminho VARCHAR(500) NOT NULL,
    ip_envio VARCHAR(45) NOT NULL,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Índices para melhor performance
CREATE INDEX idx_email ON curriculos(email);
CREATE INDEX idx_data_envio ON curriculos(data_envio);
CREATE INDEX idx_cargo ON curriculos(cargo_desejado(100));