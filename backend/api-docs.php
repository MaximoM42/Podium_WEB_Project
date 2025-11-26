<?php
// Documentación del API Podium

header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>API Podium - Documentación</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        .section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .section h2 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        .endpoint {
            margin: 15px 0;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .method {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.85em;
            margin-right: 10px;
        }
        .get { background: #61affe; color: white; }
        .post { background: #49cc90; color: white; }
        .put { background: #fca130; color: white; }
        .delete { background: #f93e3e; color: white; }
        .url {
            font-family: 'Courier New', monospace;
            color: #333;
            font-weight: bold;
        }
        .description {
            color: #666;
            margin-top: 8px;
            font-size: 0.95em;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8em;
            margin-left: 10px;
        }
        .public { background: #d4edda; color: #155724; }
        .protected { background: #fff3cd; color: #856404; }
        .admin { background: #f8d7da; color: #721c24; }
        .test-btn {
            display: inline-block;
            padding: 6px 15px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 0.9em;
            transition: background 0.3s;
        }
        .test-btn:hover {
            background: #5568d3;
        }
        .status {
            padding: 15px;
            background: #d4edda;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .status h3 {
            color: #155724;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏁 API Podium</h1>
        <p class="subtitle">Sistema de gestión de resultados de carreras</p>
        
        <div class="status">
            <h3>✅ Estado del API: Funcionando</h3>
            <p>Base URL: <strong>http://localhost/backend</strong></p>
        </div>

        <!-- ENDPOINTS PÚBLICOS -->
        <div class="section">
            <h2>📂 Endpoints Públicos (No requieren autenticación)</h2>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/categories</span>
                <span class="badge public">Público</span>
                <div class="description">Obtener todas las categorías de carreras</div>
                <a href="/backend/categories" class="test-btn" target="_blank">Probar →</a>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/categories/{id}</span>
                <span class="badge public">Público</span>
                <div class="description">Obtener una categoría específica (TC, TCP, TCPK)</div>
                <a href="/backend/categories/TC" class="test-btn" target="_blank">Probar con TC →</a>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/vehicles</span>
                <span class="badge public">Público</span>
                <div class="description">Obtener todos los vehículos</div>
                <a href="/backend/vehicles" class="test-btn" target="_blank">Probar →</a>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/vehicles?category_id={id}</span>
                <span class="badge public">Público</span>
                <div class="description">Obtener vehículos filtrados por categoría</div>
                <a href="/backend/vehicles?category_id=TC" class="test-btn" target="_blank">Probar con TC →</a>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/races</span>
                <span class="badge public">Público</span>
                <div class="description">Obtener todas las carreras</div>
                <a href="/backend/races" class="test-btn" target="_blank">Probar →</a>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/races/{id}</span>
                <span class="badge public">Público</span>
                <div class="description">Obtener una carrera específica con sus posiciones</div>
                <a href="/backend/races/1" class="test-btn" target="_blank">Probar con carrera 1 →</a>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/races/{categoryId}/positions</span>
                <span class="badge public">Público</span>
                <div class="description">Obtener todas las carreras de una categoría con posiciones</div>
                <a href="/backend/races/TC/positions" class="test-btn" target="_blank">Probar con TC →</a>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="url">/users</span>
                <span class="badge public">Público</span>
                <div class="description">Crear o actualizar usuario (usado en login/registro)</div>
                <div class="description" style="margin-top:5px; font-style:italic; font-size:0.85em;">
                    Body: { "firebase_uid": "...", "email": "...", "role": "user" }
                </div>
            </div>
        </div>

        <!-- ENDPOINTS PROTEGIDOS -->
        <div class="section">
            <h2>🔒 Endpoints Protegidos (Requieren autenticación)</h2>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/users/current</span>
                <span class="badge protected">Requiere Auth</span>
                <div class="description">Obtener información del usuario actual</div>
                <div class="description" style="margin-top:5px; font-style:italic; font-size:0.85em;">
                    Header: Authorization: Bearer {FIREBASE_UID}
                </div>
            </div>
        </div>

        <!-- ENDPOINTS ADMIN -->
        <div class="section">
            <h2>👑 Endpoints de Administrador (Solo Admin)</h2>
            
            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="url">/categories</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Crear nueva categoría</div>
            </div>

            <div class="endpoint">
                <span class="method put">PUT</span>
                <span class="url">/categories/{id}</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Actualizar categoría</div>
            </div>

            <div class="endpoint">
                <span class="method delete">DELETE</span>
                <span class="url">/categories/{id}</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Eliminar categoría</div>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="url">/vehicles</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Crear nuevo vehículo</div>
            </div>

            <div class="endpoint">
                <span class="method put">PUT</span>
                <span class="url">/vehicles/{id}</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Actualizar vehículo</div>
            </div>

            <div class="endpoint">
                <span class="method delete">DELETE</span>
                <span class="url">/vehicles/{id}</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Eliminar vehículo</div>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="url">/races</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Crear nueva carrera</div>
            </div>

            <div class="endpoint">
                <span class="method put">PUT</span>
                <span class="url">/races/{id}</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Actualizar carrera</div>
            </div>

            <div class="endpoint">
                <span class="method delete">DELETE</span>
                <span class="url">/races/{id}</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Eliminar carrera</div>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="url">/positions</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Crear nueva posición/resultado</div>
            </div>

            <div class="endpoint">
                <span class="method put">PUT</span>
                <span class="url">/positions/{id}</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Actualizar posición/resultado</div>
            </div>

            <div class="endpoint">
                <span class="method delete">DELETE</span>
                <span class="url">/positions/{id}</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Eliminar posición/resultado</div>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/users</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Listar todos los usuarios</div>
            </div>

            <div class="endpoint">
                <span class="method put">PUT</span>
                <span class="url">/users/{id}/role</span>
                <span class="badge admin">Solo Admin</span>
                <div class="description">Cambiar rol de un usuario</div>
            </div>
        </div>

        <!-- ARCHIVOS DE PRUEBA -->
        <div class="section">
            <h2>🧪 Archivos de Prueba</h2>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/test-simple.php</span>
                <span class="badge public">Test</span>
                <div class="description">Test simple de conexión a MySQL</div>
                <a href="/backend/test-simple.php" class="test-btn" target="_blank">Probar →</a>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/test-api.php</span>
                <span class="badge public">Test</span>
                <div class="description">Test completo del API y base de datos</div>
                <a href="/backend/test-api.php" class="test-btn" target="_blank">Probar →</a>
            </div>
        </div>

        <div style="margin-top: 40px; padding: 20px; background: #e7f3ff; border-radius: 5px; border-left: 4px solid #2196F3;">
            <h3 style="color: #1976D2; margin-bottom: 10px;">ℹ️ Información Importante</h3>
            <ul style="color: #666; line-height: 1.8; margin-left: 20px;">
                <li>Los endpoints públicos pueden ser probados directamente desde el navegador</li>
                <li>Los endpoints protegidos requieren el header: <code>Authorization: Bearer {FIREBASE_UID}</code></li>
                <li>Los endpoints de admin requieren que el usuario tenga role='admin' en la base de datos</li>
                <li>Todos los endpoints devuelven JSON</li>
                <li>El frontend se comunica automáticamente con estos endpoints</li>
            </ul>
        </div>
    </div>
</body>
</html>

