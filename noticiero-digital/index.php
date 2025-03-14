<?php
// Configuración Para el manejo de la API News API
$apiKey = "97c4b65660fb437ab29cd9791315b3c2"; 
$articlesPerPage = 10; // 10 articulos por pagina
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Manejo de Paginas tipo GET
$category = isset($_GET['category']) ? $_GET['category'] : 'general'; // Manejo de Categorias tipo GET

// Calcular el offset para la paginación
$offset = ($currentPage - 1) * $articlesPerPage;

// Noticias de News API
function getNews($apiKey, $category, $pageSize, $page) {
    // Para planes gratuitos que es mi caso, uso el endpoint de everything 
    // Esto da más resultados y funciona con el plan gratuito
    $url = "https://newsapi.org/v2/everything?q=" . urlencode($category) . "&pageSize={$pageSize}&page={$page}&language=es&sortBy=publishedAt&apiKey={$apiKey}";
    
    // Para futuros trabajos con planes de pagos, descomentar esta línea y comentar la anterior
    // $url = "https://newsapi.org/v2/top-headlines?country=mx&category={$category}&pageSize={$pageSize}&page={$page}&apiKey={$apiKey}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // Agg encabezado User-Agent para identificar la aplicación
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: NoticieroBlogApp/1.0'
    ]);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        return [
            'status' => 'error',
            'message' => curl_error($ch)
        ];
    }
    
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    // Agg depuración si es necesario
    if ($data['status'] === 'error') {
        error_log("News API Error: " . print_r($data, true));
    }
    
    return $data;
}

// Usuarios aleatorios para usar como autores
function getRandomUsers($count) {
    $url = "https://randomuser.me/api/?results={$count}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // También agregar User-Agent aquí para ser consistente
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: NoticieroBlogApp/1.0'
    ]);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        return [];
    }
    
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['results'] ?? [];
}

// Verificar si la API Key está configurada
$apiKeyConfigured = ($apiKey !== "TU_API_KEY_DE_NEWSAPI");

// Solo hacer solicitudes a la API si la clave está configurada
if ($apiKeyConfigured) {
    // Obtener noticias y autores
    $newsData = getNews($apiKey, $category, $articlesPerPage, $currentPage);
    $totalResults = $newsData['totalResults'] ?? 0;
    
    // Solo obtener autores si hay noticias
    if ($totalResults > 0 && isset($newsData['articles']) && !empty($newsData['articles'])) {
        $authors = getRandomUsers(count($newsData['articles']));
    } else {
        $authors = [];
    }
} else {
    // Si no hay API Key, preparar mensajes de error
    $newsData = [
        'status' => 'error',
        'message' => 'API Key no configurada. Por favor, configura tu API Key en el código.',
        'articles' => []
    ];
    $totalResults = 0;
    $authors = [];
}

// Categorías disponibles para el menú (traductor en español)
$categories = [
    'general' => 'General',
    'business' => 'Negocios',
    'entertainment' => 'Entretenimiento',
    'health' => 'Salud',
    'science' => 'Ciencia',
    'sports' => 'Deportes',
    'technology' => 'Tecnología'
];

// Calcular número total de páginas para la paginación
$totalPages = ceil($totalResults / $articlesPerPage);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticiero Digital MC</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .news-card {
            margin-bottom: 25px;
            transition: transform 0.3s;
            height: 100%;
        }
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .author-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .pagination {
            justify-content: center;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .category-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .news-date {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.8rem;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .card-text {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 4.5rem;
        }
        .api-info {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-newspaper me-2"></i>Noticiero Digital De MC Klooy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php foreach ($categories as $key => $name): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $category === $key ? 'active' : ''; ?>" 
                               href="?category=<?php echo $key; ?>">
                                <?php echo $name; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Mensaje de API Key no configurada -->
        <?php if (!$apiKeyConfigured): ?>
            <div class="api-info mb-4">
                <h4><i class="fas fa-exclamation-triangle me-2"></i>Configuración necesaria</h4>
                <p>Para que este blog funcione correctamente <code>TU_API_KEY_DE_NEWSAPI</code> con tu clave API real de News API.</p>
                <p>Obtener una clave gratuita registrándote en <a href="https://newsapi.org/register" target="_blank">newsapi.org</a>.</p>
            </div>
        <?php endif; ?>
        
        <!-- Título de la sección -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="text-center"><?php echo $categories[$category]; ?></h2>
                <hr>
            </div>
        </div>

        <!-- Noticias -->
        <div class="row">
            <?php 
            if (isset($newsData['articles']) && !empty($newsData['articles'])):
                foreach ($newsData['articles'] as $index => $article):
                    // Asignación de un autor aleatorio a cada artículo
                    $author = $index < count($authors) ? $authors[$index] : null;
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card news-card">
                        <?php if (isset($article['urlToImage']) && $article['urlToImage']): ?>
                            <img src="<?php echo htmlspecialchars($article['urlToImage']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($article['title']); ?>"
                                 onerror="this.onerror=null;this.src='https://via.placeholder.com/400x200?text=No+Image+Available';">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-image fa-3x text-secondary"></i>
                            </div>
                        <?php endif; ?>
                        
                        <span class="badge bg-primary category-badge"><?php echo $categories[$category]; ?></span>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($article['description'] ?: 'Sin descripción disponible'); ?></p>
                        </div>
                        
                        <div class="card-footer bg-white">
                            <?php if ($author): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <img src="<?php echo $author['picture']['thumbnail']; ?>" 
                                         alt="Autor" 
                                         class="author-img me-2">
                                    <span><?php echo $author['name']['first'] . ' ' . $author['name']['last']; ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="news-date">
                                    <?php 
                                        if (isset($article['publishedAt']) && $article['publishedAt']) {
                                            $date = new DateTime($article['publishedAt']);
                                            echo $date->format('d M Y, H:i');
                                        } else {
                                            echo "Fecha no disponible";
                                        }
                                    ?>
                                </span>
                                <a href="<?php echo htmlspecialchars($article['url']); ?>" 
                                   class="btn btn-sm btn-outline-primary" 
                                   target="_blank">Leer más</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                endforeach;
            else: 
            ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <?php 
                            if (isset($newsData['message'])) {
                                echo "<strong>Mensaje de la API:</strong> " . htmlspecialchars($newsData['message']);
                                
                                // Mensaje adicional para planes gratuitos
                                if (strpos($newsData['message'], 'developer') !== false) {
                                    echo "<br><br><strong>Nota:</strong> El plan gratuito de News API solo permite acceder al endpoint 'everything' y solo desde localhost para desarrollo. El código ha sido ajustado para usar este endpoint, pero podría haber restricciones adicionales.";
                                }
                            } else {
                                echo "No se encontraron noticias para esta categoría.";
                                
                                // Sugerencia para planes gratuitos
                                echo "<br><br>Sugerencias:";
                                echo "<ul>";
                                echo "<li>Si estás usando un plan gratuito, intenta con diferentes categorías.</li>";
                                echo "<li>Verifica que tu clave API sea válida.</li>";
                                echo "<li>News API tiene restricciones para cuentas gratuitas (solo desarrollo local).</li>";
                                echo "</ul>";
                            }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Paginación -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Paginación de noticias">
                <ul class="pagination">
                    <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage-1; ?>&category=<?php echo $category; ?>">
                            &laquo; Anterior
                        </a>
                    </li>
                    
                    <?php 
                    // 5 páginas en la paginación
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($startPage + 4, $totalPages);
                    
                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage+1; ?>&category=<?php echo $category; ?>">
                            Siguiente &raquo;
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">© <?php echo date('Y'); ?> Noticiero Digital | Desarrollado con HTML, PHP, Bootstrap.
               </i> Usando News API y Random User</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>