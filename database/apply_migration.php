<?php
/**
 * Script para aplicar migração BLOB no banco de dados
 * Execute este arquivo via navegador ou linha de comando para adicionar os campos BLOB
 */

require_once __DIR__ . '/../includes/config.php';

echo "<h2>Aplicando Migração para Armazenamento BLOB</h2>\n";

try {
    $pdo = getConnection();
    
    // Verificar se os campos já existem
    $stmt = $pdo->query("DESCRIBE curriculos");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $campos_necessarios = ['arquivo_conteudo', 'arquivo_tamanho', 'arquivo_mime_type'];
    $campos_existentes = array_intersect($campos_necessarios, $columns);
    
    if (count($campos_existentes) === count($campos_necessarios)) {
        echo "<p style='color: green;'>✅ Migração já foi aplicada! Todos os campos BLOB existem.</p>\n";
    } else {
        echo "<p>📝 Aplicando migração...</p>\n";
        
        // Aplicar migração
        $sql_migration = "
            ALTER TABLE curriculos 
            ADD COLUMN arquivo_conteudo LONGBLOB AFTER arquivo_caminho,
            ADD COLUMN arquivo_tamanho INT AFTER arquivo_conteudo,
            ADD COLUMN arquivo_mime_type VARCHAR(100) AFTER arquivo_tamanho;
        ";
        
        $pdo->exec($sql_migration);
        echo "<p style='color: green;'>✅ Campos BLOB adicionados com sucesso!</p>\n";
        
        // Criar índice
        $pdo->exec("CREATE INDEX idx_mime_type ON curriculos(arquivo_mime_type)");
        echo "<p style='color: green;'>✅ Índice criado com sucesso!</p>\n";
    }
    
    // Verificar estrutura final
    echo "<h3>Estrutura atual da tabela:</h3>\n";
    $stmt = $pdo->query("DESCRIBE curriculos");
    $estrutura = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th></tr>\n";
    
    foreach ($estrutura as $campo) {
        $destaque = in_array($campo['Field'], $campos_necessarios) ? " style='background-color: #e8f5e8;'" : "";
        echo "<tr{$destaque}>";
        echo "<td>{$campo['Field']}</td>";
        echo "<td>{$campo['Type']}</td>";
        echo "<td>{$campo['Null']}</td>";
        echo "<td>{$campo['Key']}</td>";
        echo "<td>{$campo['Default']}</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
    
    echo "<h3>Próximos passos:</h3>\n";
    echo "<ol>\n";
    echo "<li>✅ Migração aplicada com sucesso</li>\n";
    echo "<li>✅ Sistema de upload modificado para usar BLOB</li>\n";
    echo "<li>✅ Sistema de download atualizado</li>\n";
    echo "<li>🔄 Teste o upload de um novo documento</li>\n";
    echo "<li>🔄 Verifique se o download funciona corretamente</li>\n";
    echo "</ol>\n";
    
    echo "<p><strong>Nota:</strong> Documentos enviados antes desta migração continuarão funcionando através do sistema de arquivos físicos.</p>\n";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erro na migração: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p>Verifique se:</p>\n";
    echo "<ul>\n";
    echo "<li>O MySQL está rodando</li>\n";
    echo "<li>As credenciais do banco estão corretas</li>\n";
    echo "<li>O usuário tem permissões para ALTER TABLE</li>\n";
    echo "</ul>\n";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { width: 100%; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>