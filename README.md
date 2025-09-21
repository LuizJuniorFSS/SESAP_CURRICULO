## Links de Acesso ao Sistema

- **VSCODE:** https://code.visualstudio.com/download
- **XAMPP:** https://sourceforge.net/projects/xampp/files/

### URLs do Sistema
- **Página Inicial:** http://localhost/sesap_curriculo/public/index.html
- **Área do Usuário:** http://localhost/sesap_curriculo/pages/user/user_login.php
- **Área Administrativa:** http://localhost/sesap_curriculo/pages/admin/login.php

### Credenciais de Administrador
- **Usuário:** SESAP
- **Senha:** admin123

# Sistema de Cadastro de Currículos - SESAP

Sistema web moderno para cadastro e gerenciamento de currículos da Secretaria de Estado da Saúde Pública do Rio Grande do Norte (SESAP-RN).

## ✨ Funcionalidades

### 👤 Para Candidatos
- ✅ Formulário de cadastro intuitivo e responsivo
- ✅ Upload de currículos (PDF, DOC, DOCX) - máximo 1MB
- ✅ Validação em tempo real dos dados
- ✅ Confirmação automática por email
- ✅ Dashboard pessoal para acompanhar status
- ✅ Interface moderna e acessível

### 👨‍💼 Para Administradores
- ✅ Painel administrativo completo
- ✅ Sistema de busca e filtros avançados
- ✅ Visualização detalhada de currículos
- ✅ Download de arquivos anexados
- ✅ Envio de emails de confirmação
- ✅ Gerenciamento completo (editar/excluir)

### 🔧 Recursos Técnicos
- ✅ Arquitetura MVC organizada
- ✅ Validação dupla (frontend/backend)
- ✅ Proteção contra SQL Injection
- ✅ Sistema de logs detalhado
- ✅ Backup automático de dados
- ✅ Interface responsiva (mobile-first)

## 🛠️ Tecnologias Utilizadas

- **Backend:** PHP 8.2+ com PDO
- **Banco de Dados:** MySQL 8.0+
- **Frontend:** HTML5, CSS3, JavaScript ES6+
- **Email:** PHPMailer 6.10+
- **Servidor:** Apache/Nginx
- **Dependências:** Composer

## 📋 Pré-requisitos

- PHP 8.0 ou superior
- MySQL 8.0 ou superior
- Apache ou Nginx
- Composer (gerenciador de dependências)
- Conta SMTP (Gmail recomendado)

## 🚀 Instalação

### 1. Clone o repositório
```bash
git clone [url-do-repositorio]
cd sesap_curriculo
```

### 2. Instale as dependências
```bash
composer install
```

### 3. Configure o banco de dados
```bash
# Crie o banco de dados
mysql -u root -p -e "CREATE DATABASE sesap_curriculo;"

# Execute o script de estrutura
mysql -u root -p sesap_curriculo < database/database.sql

# (Opcional) Execute atualizações de timezone
mysql -u root -p sesap_curriculo < database/update_timezone.sql
```

### 4. Configure o sistema
Edite o arquivo `includes/config.php`:

```php
<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'sesap_curriculo');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');

// Configurações SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'seu_email@gmail.com');
define('SMTP_PASSWORD', 'sua_senha_de_app');
define('EMAIL_FROM', 'seu_email@gmail.com');
define('EMAIL_TO', 'rh@sesap.rn.gov.br');
?>
```

### 5. Configure permissões
```bash
# Linux/Mac
chmod 755 uploads/
chmod 644 uploads/.htaccess

# Windows (PowerShell como Admin)
icacls uploads /grant Everyone:F
```

### 6. Inicie o servidor
```bash
# Servidor PHP integrado (desenvolvimento)
php -S localhost:8000

# Ou configure no Apache/Nginx
```

## 📁 Estrutura do Projeto

```
sesap_curriculo/
├── 📁 database/               # Scripts de banco
│   ├── database.sql          # Estrutura principal
│   └── update_timezone.sql   # Atualizações
├── 📁 includes/               # Arquivos de configuração
│   ├── config.example.php    # Exemplo de configuração
│   ├── config.php            # Configurações gerais
│   └── email.php             # Sistema de email
├── 📁 pages/                  # Páginas da aplicação
│   ├── admin/                # Área administrativa
│   │   ├── admin.php         # Dashboard admin
│   │   ├── login.php         # Login admin
│   │   └── logout.php        # Logout admin
│   ├── user/                 # Área do usuário
│   │   ├── user_dashboard.php # Dashboard usuário
│   │   ├── user_login.php    # Login usuário
│   │   └── user_logout.php   # Logout usuário
│   ├── uploads/              # Currículos enviados
│   │   └── *.pdf             # Arquivos PDF dos currículos
│   ├── download.php          # Download de arquivos
│   ├── processar.php         # Processamento de dados
│   ├── sucesso.php           # Página de sucesso
│   └── visualizar.php        # Visualização detalhada
├── 📁 public/                 # Arquivos públicos
│   └── index.html            # Página inicial
├── 📁 uploads/                # Diretório de uploads
│   └── .htaccess             # Proteção de acesso
├── 📁 vendor/                 # Dependências (auto-gerado)
│   ├── autoload.php          # Autoloader do Composer
│   ├── composer/             # Metadados do Composer
│   └── phpmailer/            # Biblioteca PHPMailer
├── 📄 index.php              # Ponto de entrada
├── 📄 composer.json          # Dependências PHP
├── 📄 composer.lock          # Lock de versões
├── 📄 .gitignore            # Arquivos ignorados
└── 📄 README.md             # Este arquivo
```

## 🔐 Configuração de Segurança

### Credenciais Padrão
```
👤 Administrador:
   Usuário: SESAP
   Senha: admin123

⚠️ IMPORTANTE: Altere essas credenciais em produção!
```

### Medidas de Segurança Implementadas
- ✅ Validação e sanitização de dados
- ✅ Proteção contra SQL Injection (PDO)
- ✅ Validação de tipos de arquivo
- ✅ Limite de tamanho de upload (1MB)
- ✅ Proteção do diretório uploads/
- ✅ Headers de segurança HTTP
- ✅ Logs de auditoria

## 📧 Configuração de Email

### Gmail (Recomendado)
1. Ative a verificação em duas etapas
2. Gere uma senha de app específica
3. Use a senha de app no `SMTP_PASSWORD`

### Outros Provedores
- **Outlook:** smtp-mail.outlook.com (porta 587)
- **Yahoo:** smtp.mail.yahoo.com (porta 587)
- **Servidor próprio:** Configure conforme documentação

## 📊 Banco de Dados

### Tabela Principal: `curriculos`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | INT (PK) | Identificador único |
| `nome` | VARCHAR(255) | Nome completo |
| `email` | VARCHAR(255) | Email do candidato |
| `telefone` | VARCHAR(20) | Telefone de contato |
| `cargo_desejado` | TEXT | Cargo pretendido |
| `escolaridade` | VARCHAR(100) | Nível de escolaridade |
| `observacoes` | TEXT | Observações adicionais |
| `arquivo_nome` | VARCHAR(255) | Nome original do arquivo |
| `arquivo_caminho` | VARCHAR(500) | Caminho no servidor |
| `ip_envio` | VARCHAR(45) | IP de origem |
| `data_envio` | DATETIME | Data/hora do envio |
| `created_at` | DATETIME | Data de criação |
| `updated_at` | DATETIME | Última atualização |

### Índices para Performance
- `idx_email` - Busca por email
- `idx_data_envio` - Ordenação por data
- `idx_cargo` - Busca por cargo

## 🎯 Como Usar

### Para Candidatos
1. **Acesse:** `http://localhost/sesap_curriculo/pages/user/user_login.php`
2. **Cadastre-se:** Preencha o formulário completo
3. **Anexe:** Seu currículo (PDF, DOC, DOCX)
4. **Envie:** Clique em "Enviar Currículo"
5. **Confirme:** Verifique o email de confirmação
6. **Acompanhe:** Use o dashboard pessoal

### Para Administradores
1. **Acesse:** `http://localhost/sesap_curriculo/pages/admin/login.php`
2. **Login:** Use as credenciais administrativas
3. **Gerencie:** Visualize todos os currículos
4. **Filtre:** Use busca avançada por critérios
5. **Ações:** Visualizar, editar, excluir ou baixar
6. **Comunique:** Envie emails de confirmação

## 🔧 Manutenção e Monitoramento

### Logs do Sistema
```bash
# Logs de erro PHP
tail -f /var/log/apache2/error.log

# Logs de acesso
tail -f /var/log/apache2/access.log

# Logs personalizados (se configurado)
tail -f logs/sistema.log
```

### Backup Recomendado
```bash
# Backup do banco de dados
mysqldump -u root -p sesap_curriculo > backup_$(date +%Y%m%d).sql

# Backup dos arquivos
tar -czf uploads_backup_$(date +%Y%m%d).tar.gz uploads/

# Backup das configurações
cp includes/config.php config_backup_$(date +%Y%m%d).php
```

### Monitoramento de Performance
- Monitor de espaço em disco (uploads/)
- Verificação de conectividade SMTP
- Análise de logs de erro
- Teste de funcionalidades críticas

## 🐛 Solução de Problemas

### ❌ Email não está sendo enviado
```bash
# Verifique as configurações
grep -n "SMTP_" includes/config.php

# Teste a conectividade SMTP
telnet smtp.gmail.com 587

# Verifique os logs
tail -f /var/log/mail.log
```

### ❌ Upload de arquivo falha
```bash
# Verifique permissões
ls -la uploads/

# Verifique configurações PHP
php -i | grep upload

# Teste o diretório
touch uploads/test.txt && rm uploads/test.txt
```

### ❌ Erro de conexão com banco
```bash
# Teste a conexão
mysql -u root -p -e "SELECT 1;"

# Verifique o status do MySQL
systemctl status mysql

# Teste as credenciais
mysql -u [usuario] -p[senha] sesap_curriculo -e "SHOW TABLES;"
```

### ❌ Problemas de performance
```sql
-- Analise consultas lentas
SHOW PROCESSLIST;

-- Verifique índices
SHOW INDEX FROM curriculos;

-- Otimize tabelas
OPTIMIZE TABLE curriculos;
```

### Próximas Funcionalidades
- 🔄 API REST para integrações
- 🔄 Sistema de notificações push
- 🔄 Dashboard com gráficos
- 🔄 Exportação em múltiplos formatos
- 🔄 Sistema de templates de email

## 📞 Suporte e Contribuição

### Suporte Técnico
- 📧 Email: juniorrsilva50@gmail.com
- 📱 Telefone: (84) 99420-xxxx

### Contribuindo
1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

### Reportando Bugs
Use o template de issue com:
- Descrição detalhada
- Passos para reproduzir
- Ambiente (OS, PHP, MySQL)
- Screenshots se aplicável

---

<div align="center">

**🏥 Desenvolvido para PROCESSO SELETIVO SESAP**  
*Sistema de Gestão de Currículos v1.0*

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)](https://mysql.com)

</div>
