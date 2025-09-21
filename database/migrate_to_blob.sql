-- Script de migração para armazenar documentos no MySQL como BLOB
-- Execute este script para adicionar os campos necessários

USE sesap_curriculo;

-- Adicionar campos para armazenamento BLOB
ALTER TABLE curriculos 
ADD COLUMN arquivo_conteudo LONGBLOB AFTER arquivo_caminho,
ADD COLUMN arquivo_tamanho INT AFTER arquivo_conteudo,
ADD COLUMN arquivo_mime_type VARCHAR(100) AFTER arquivo_tamanho;

-- Criar índice para melhor performance nas consultas por tipo MIME
CREATE INDEX idx_mime_type ON curriculos(arquivo_mime_type);

-- Comentários sobre os novos campos:
-- arquivo_conteudo: Armazena o conteúdo binário do documento (LONGBLOB suporta até 4GB)
-- arquivo_tamanho: Tamanho do arquivo em bytes para controle
-- arquivo_mime_type: Tipo MIME do arquivo (application/pdf, application/msword, etc.)

-- Nota: Os campos arquivo_nome e arquivo_caminho serão mantidos para compatibilidade
-- arquivo_nome: Nome original do arquivo enviado pelo usuário
-- arquivo_caminho: Pode ser usado para backup ou cache (opcional)