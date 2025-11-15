# 📋 Guia de Instalação - SESAP Currículo

## 🚀 Pré-requisitos

### 1. Instalação do XAMPP
- Localize o arquivo `XAMPP.exe` na pasta **INSTALADORES**
- Execute o instalador como administrador
- Siga as instruções de instalação padrão

### 2. Configuração do MySQL
Após a instalação do XAMPP, é necessário substituir a versão padrão do MySQL:

1. **Localizar arquivos:**
   - Pasta de origem: `INSTALADORES/mysql/`
   - Pasta de destino: `C:\xampp\`

2. **Processo de cópia:**
   - Copie toda a pasta `mysql` da pasta **INSTALADORES**
   - Cole em `C:\xampp\` sobrepondo a pasta `mysql` existente
   - ⚠️ **Importante:** Confirme a substituição quando solicitado

## 📁 Estrutura de Diretórios

```
C:\xampp\
├── mysql/          ← Pasta substituída
├── htdocs/
│   └── sesap_curriculo/  ← Projeto atual
└── ...
```

## ✅ Verificação da Instalação

1. Inicie o XAMPP Control Panel
2. Verifique se Apache e MySQL iniciam corretamente
3. Acesse `http://localhost/sesap_curriculo` para testar o projeto

---
*Guia de instalação para o sistema SESAP Currículo*

