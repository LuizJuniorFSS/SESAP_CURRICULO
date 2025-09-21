# Script para configurar tarefa agendada no Windows
# Executa o upload automático para GitHub a cada minuto

# Verifica se está executando como administrador
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Host "ERRO: Este script precisa ser executado como Administrador!" -ForegroundColor Red
    Write-Host "Clique com o botão direito no PowerShell e selecione 'Executar como administrador'" -ForegroundColor Yellow
    pause
    exit 1
}

# Define variáveis
$taskName = "AutoGitUpload-SESAP"
$scriptPath = "C:\xampp\htdocs\sesap_curriculo\auto-git-upload.ps1"
$workingDirectory = "C:\xampp\htdocs\sesap_curriculo"

try {
    Write-Host "Configurando tarefa agendada para upload automático..." -ForegroundColor Green
    
    # Remove tarefa existente se houver
    $existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
    if ($existingTask) {
        Write-Host "Removendo tarefa existente..." -ForegroundColor Yellow
        Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
    }
    
    # Cria a ação da tarefa
    $action = New-ScheduledTaskAction -Execute "PowerShell.exe" -Argument "-WindowStyle Hidden -ExecutionPolicy Bypass -File `"$scriptPath`"" -WorkingDirectory $workingDirectory
    
    # Cria o gatilho (a cada 1 minuto)
    $trigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes 1) -RepetitionDuration (New-TimeSpan -Days 365)
    
    # Configura as configurações da tarefa
    $settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -RunOnlyIfNetworkAvailable
    
    # Cria o principal (usuário atual)
    $principal = New-ScheduledTaskPrincipal -UserId $env:USERNAME -LogonType Interactive
    
    # Registra a tarefa
    Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger -Settings $settings -Principal $principal -Description "Upload automático para GitHub do projeto SESAP Currículo a cada minuto"
    
    Write-Host "✅ Tarefa agendada criada com sucesso!" -ForegroundColor Green
    Write-Host "Nome da tarefa: $taskName" -ForegroundColor Cyan
    Write-Host "Frequência: A cada 1 minuto" -ForegroundColor Cyan
    Write-Host "Script: $scriptPath" -ForegroundColor Cyan
    
    Write-Host "`n📋 Para gerenciar a tarefa:" -ForegroundColor Yellow
    Write-Host "• Abra o 'Agendador de Tarefas' do Windows" -ForegroundColor White
    Write-Host "• Procure por '$taskName' na lista" -ForegroundColor White
    Write-Host "• Você pode pausar, retomar ou excluir a tarefa conforme necessário" -ForegroundColor White
    
    Write-Host "`n🔧 Para parar a automação:" -ForegroundColor Yellow
    Write-Host "Execute: Disable-ScheduledTask -TaskName '$taskName'" -ForegroundColor White
    
    Write-Host "`n🔄 Para reativar a automação:" -ForegroundColor Yellow
    Write-Host "Execute: Enable-ScheduledTask -TaskName '$taskName'" -ForegroundColor White
    
} catch {
    Write-Host "❌ ERRO ao configurar tarefa agendada: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host "`n✨ Configuração concluída! O upload automático começará em 1 minuto." -ForegroundColor Green