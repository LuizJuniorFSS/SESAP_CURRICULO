# Sistema de Cadastro de Currículos - SESAP

Sistema web para cadastro e gerenciamento de currículos da Secretaria de Estado da Saúde Pública do Rio Grande do Norte (SESAP-RN).

## Funcionalidades

- ✅ Formulário de cadastro de currículos com validação completa
- ✅ Upload de arquivos (PDF, DOC, DOCX) - máximo 1MB
- ✅ Validação de dados no frontend (JavaScript) e backend (PHP)
- ✅ Armazenamento em banco de dados MySQL com IP e data/hora
- ✅ Envio automático de emails com dados e arquivo anexo
- ✅ Sistema de comprovante automático via Gmail
- ✅ Painel administrativo para visualização e gerenciamento
- ✅ Sistema de busca e filtros avançados
- ✅ Download de currículos cadastrados
- ✅ Interface responsiva e moderna

## Tecnologias Utilizadas

- **Backend:** PHP 7.4+
- **Banco de Dados:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript
- **Email:** PHPMailer 6.10+
- **Servidor:** Apache (XAMPP recomendado)
https://www.apachefriends.org/pt_br/download.html

## 📋 Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache (ou servidor web compatível)
- Composer (para gerenciamento de dependências)
- Conta de email SMTP (Gmail recomendado)

## 🔧 Instalação

### 1. Clone ou baixe o projeto
```bash
git clone [url-do-repositorio]
cd sesap_curriculo
```

### 2. Instale as dependências
```bash
composer install
```

### 3. Configure o banco de dados
Execute o script SQL no seu MySQL:
```bash
mysql -u root -p < database.sql
```

### 4. Configure o arquivo de configuração
Crie o arquivo `config.php` baseado no exemplo:
```php
<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sesap_curriculo');

// Configurações SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'seu_email@gmail.com');
define('SMTP_PASSWORD', 'sua_senha_de_app');
define('EMAIL_FROM', 'seu_email@gmail.com');
define('EMAIL_TO', 'rh@empresa.com');

// Conexão com banco
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8");
} catch (Exception $e) {
    die('Erro de conexão: ' . $e->getMessage());
}
?>
```

### 5. Configure as permissões
```bash
chmod 755 uploads/
chmod 644 uploads/.htaccess
```

## 📧 Configuração de Email

### Gmail (Recomendado)
1. Ative a verificação em duas etapas na sua conta Google
2. Gere uma senha de app específica para email
3. Use essa senha no `SMTP_PASSWORD`

### Outros provedores
- **Outlook:** smtp-mail.outlook.com (porta 587)
- **Yahoo:** smtp.mail.yahoo.com (porta 587)

## 📁 Estrutura do Projeto

```
sesap_curriculo/
├── 📄 index.html           # Formulário principal
├── 📄 processar.php        # Processamento do formulário
├── 📄 admin.php            # Painel administrativo
├── 📄 visualizar.php       # Visualização de currículos
├── 📄 editar.php           # Edição de registros
├── 📄 download.php         # Download de arquivos
├── 📄 sucesso.php          # Página de confirmação
├── 📄 email.php            # Sistema de envio de emails
├── 📄 config.php           # Configurações (criar)
├── 📄 database.sql         # Script de criação do BD
├── 📄 update_timezone.sql  # Script de atualização
├── 📄 composer.json        # Dependências PHP
├── 📄 .gitignore          # Arquivos ignorados pelo Git
├── 📁 uploads/            # Diretório de arquivos
│   └── 📄 .htaccess       # Proteção do diretório
└── 📁 vendor/             # Dependências (auto-gerado)
```

## 🔐 Segurança

           Credenciais Padrão
            Usuário: SESAP
            Senha: admin123

            
- ✅ Validação e sanitização de dados
- ✅ Proteção contra SQL Injection
- ✅ Validação de tipos de arquivo
- ✅ Limite de tamanho de upload
- ✅ Proteção do diretório de uploads
- ✅ Logs de erro detalhados

## 📊 Banco de Dados

### Tabela: curriculos
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT (PK) | Identificador único |
| nome | VARCHAR(255) | Nome completo |
| email | VARCHAR(255) | Email do candidato |
| telefone | VARCHAR(20) | Telefone de contato |
| cargo_desejado | TEXT | Cargo pretendido |
| escolaridade | VARCHAR(100) | Nível de escolaridade |
| observacoes | TEXT | Observações adicionais |
| arquivo_nome | VARCHAR(255) | Nome do arquivo |
| arquivo_caminho | VARCHAR(500) | Caminho do arquivo |
| ip_envio | VARCHAR(45) | IP de origem |
| data_envio | DATETIME | Data/hora do envio |
| created_at | DATETIME | Data de criação |
| updated_at | DATETIME | Última atualização |

## 🚀 Como Usar

### Para Candidatos
1. Acesse `index.html`
2. Preencha o formulário com seus dados
3. Anexe seu currículo (PDF, DOC, DOCX)
4. Clique em "Enviar Currículo"
5. Aguarde a confirmação por email

### Para Administradores
1. Acesse `admin.php`
2. Visualize todos os currículos recebidos
3. Use os filtros para buscar candidatos específicos
4. Clique em "Ver Detalhes" para visualizar informações completas
5. Use "Editar" para modificar dados ou "Excluir" para remover

## 🔧 Manutenção

### Logs de Erro
Os logs são salvos automaticamente. Verifique:
- Logs do Apache/PHP
- Logs de email no sistema

### Backup
Faça backup regular de:
- Banco de dados MySQL
- Diretório `uploads/`
- Arquivo `config.php`

### Atualizações
Para atualizar o sistema:
1. Faça backup completo
2. Atualize os arquivos
3. Execute scripts SQL se necessário
4. Teste todas as funcionalidades

## 🐛 Solução de Problemas

### Email não está sendo enviado
- Verifique as configurações SMTP no `config.php`
- Confirme se a senha de app está correta
- Verifique os logs de erro

### Upload de arquivo falha
- Verifique permissões do diretório `uploads/`
- Confirme o tamanho máximo de upload no PHP
- Verifique se o tipo de arquivo é permitido

### Erro de conexão com banco
- Confirme as credenciais no `config.php`
- Verifique se o MySQL está rodando
- Execute o script `database.sql`

## 📞 Suporte

Para suporte técnico ou dúvidas:
- Verifique os logs de erro
- Consulte a documentação do PHP/MySQL
- Revise as configurações de email

## 📄 Licença

Este projeto é de uso interno da SESAP. Todos os direitos reservados.

---

**Desenvolvido para SESAP** | Sistema de Gestão de Currículos v1.0