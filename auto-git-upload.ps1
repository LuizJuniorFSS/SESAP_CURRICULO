# Script para upload automático para GitHub
# Executa git add, commit e push automaticamente

# Define o diretório do projeto
$projectPath = "C:\xampp\htdocs\sesap_curriculo"

# Muda para o diretório do projeto
Set-Location $projectPath

# Função para escrever log com timestamp
function Write-Log {
    param($message)
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logMessage = "[$timestamp] $message"
    Write-Host $logMessage
    Add-Content -Path "$projectPath\auto-git.log" -Value $logMessage
}

try {
    Write-Log "Iniciando processo de upload automático..."
    
    # Verifica se há mudanças
    $status = git status --porcelain
    
    if ($status) {
        Write-Log "Mudanças detectadas. Iniciando upload..."
        
        # Adiciona todos os arquivos modificados
        git add .
        Write-Log "Arquivos adicionados ao staging"
        
        # Cria commit com timestamp
        $commitMessage = "Auto-commit: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
        git commit -m $commitMessage
        Write-Log "Commit criado: $commitMessage"
        
        # Faz push para o repositório remoto
        git push origin main
        Write-Log "Push realizado com sucesso"
        
        Write-Log "Upload automático concluído com sucesso!"
    } else {
        Write-Log "Nenhuma mudança detectada. Nada para fazer."
    }
    
} catch {
    Write-Log "ERRO: $($_.Exception.Message)"
    Write-Log "Falha no upload automático"
}

Write-Log "Processo finalizado."