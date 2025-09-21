-- Script para atualizar o fuso horário da tabela existente
-- Execute este script se a tabela já foi criada anteriormente

USE sesap_curriculo;

-- Alterar as colunas TIMESTAMP para DATETIME
ALTER TABLE curriculos 
MODIFY COLUMN data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
MODIFY COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
MODIFY COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Verificar a estrutura atualizada
DESCRIBE curriculos;