# Script para copiar el backend a XAMPP y verificar
Write-Host "🚀 Copiando archivos al backend de XAMPP..." -ForegroundColor Cyan

$source = "D:\OneDrive data\OneDrive\Desktop\Accesos Directos\Facu\Universidad\Cuatrimestre VIV\Programacion e Interfaces Visuales\Podium_WEB_Project\backend"
$destination = "C:\xampp\htdocs\backend"

# Crear directorio si no existe
if (!(Test-Path $destination)) {
    New-Item -ItemType Directory -Path $destination -Force
    Write-Host "✅ Directorio creado: $destination" -ForegroundColor Green
}

# Copiar todos los archivos
Copy-Item -Path "$source\*" -Destination $destination -Recurse -Force

Write-Host "`n📋 Verificando archivos copiados..." -ForegroundColor Cyan

# Verificar archivos importantes
$files = @(
    ".htaccess",
    "index.php",
    "cors-check.php",
    "test-cors.php",
    "test-cors-frontend.html",
    "config\config.php",
    "config\database.php"
)

foreach ($file in $files) {
    $path = Join-Path $destination $file
    if (Test-Path $path) {
        Write-Host "✅ $file" -ForegroundColor Green
    } else {
        Write-Host "❌ $file (NO ENCONTRADO)" -ForegroundColor Red
    }
}

Write-Host "`n✅ Copia completada!" -ForegroundColor Green
Write-Host "`n🔗 Ahora abre: http://localhost/backend/cors-check.php" -ForegroundColor Yellow

