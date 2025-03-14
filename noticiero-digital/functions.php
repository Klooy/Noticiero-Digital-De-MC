<?php
// Obtener noticias de News API
function getNews($apiKey, $category, $pageSize, $page) {
    // Para planes gratuitos, Uso el endpoint de everything en lugar de top-headlines
    // Esto da más resultados y funciona con el plan gratuito
    $url = "https://newsapi.org/v2/everything?q=" . urlencode($category) . "&pageSize={$pageSize}&page={$page}&language=es&sortBy=publishedAt&apiKey={$apiKey}";
    
    // Para planes pagos, descomentar esta línea y comentar la anterior
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

// Obtener usuarios aleatorios "autores"
function getRandomUsers($count) {
    $url = "https://randomuser.me/api/?results={$count}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // Agg User-Agent aquí para ser consistente
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