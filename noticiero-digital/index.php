<?php
// Inclusión de archivos
require_once 'functions.php';

// Configuración
$apiKey = "97c4b65660fb437ab29cd9791315b3c2"; // Clave API real
$articlesPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category = isset($_GET['category']) ? $_GET['category'] : 'general';

// Calcular el offset para la paginación
$offset = ($currentPage - 1) * $articlesPerPage;

// Verificación API Key está configurada
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

// Incluir la plantilla del encabezado
include 'templates/header.php';
?>

<div class="container">
    <!-- Mensaje de API Key no configurada -->
    <?php if (!$apiKeyConfigured): ?>
        <div class="api-info mb-4">
            <h4><i class="fas fa-exclamation-triangle me-2"></i>Configuración necesaria</h4>
            <p>Para que este blog funcione correctamente, se debe editar el archivo PHP y reemplazar <code>TU_API_KEY_DE_NEWSAPI</code> con la clave API real de News API.</p>
            <p>Obtener una clave gratuita en <a href="https://newsapi.org/register" target="_blank">newsapi.org</a>.</p>
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
                // Autor aleatorio a cada artículo
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
                                echo "<br><br><strong>Nota:</strong> El plan gratuito de News API solo permite acceder al endpoint 'everything' y solo desde localhost para desarrollo.";
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
                // Mostrar máximo 5 páginas en la paginación
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

<?php
// Incluir la plantilla del pie de página
include 'templates/footer.php';
?>