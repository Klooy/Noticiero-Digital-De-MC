# Noticiero Digital

Un portal de noticias dinámico que utiliza News API para mostrar artículos en tiempo real, organizados por categorías y con un diseño responsive.

## Descripción

Noticiero Digital es una aplicación web que muestra noticias actualizadas de diversas categorías (general, negocios, entretenimiento, etc.) utilizando la API pública de News API. El sistema presenta una interfaz limpia y amigable para el usuario, con tarjetas de noticias, paginación y filtrado por categorías.

## Estructura del Proyecto

```
/noticiero-digital/
├── index.php            # Archivo principal con la lógica de presentación
├── functions.php        # Funciones para API requests
├── styles.css           # Estilos personalizados
├── README.md            # Documentación del proyecto
└── templates/
    ├── header.php       # Plantilla del encabezado
    └── footer.php       # Plantilla del pie de página
```

## Características

- **Visualización de noticias**: Muestra noticias en un formato de tarjetas con imagen, título, descripción y fecha de publicación.
- **Categorización**: Permite filtrar las noticias por categorías (General, Negocios, Entretenimiento, etc.).
- **Paginación**: Implementa un sistema de paginación para navegar entre resultados.
- **Diseño Responsive**: Se adapta a diferentes tamaños de pantalla (móvil, tablet, escritorio).
- **Autores aleatorios**: Utiliza la API RandomUser para asociar autores ficticios a cada noticia.
- **Integración con APIs externas**: Se conecta con News API y RandomUser API.

## Tecnologías Utilizadas

- **PHP**: Lenguaje de programación del lado del servidor para procesar las peticiones y generar contenido dinámico.
- **HTML5**: Estructura base de la aplicación web.
- **CSS3**: Estilización de la interfaz de usuario.
- **Bootstrap 5**: Framework CSS para un diseño responsive y moderno.
- **Font Awesome**: Biblioteca de iconos para mejorar la interfaz visual.
- **cURL**: Librería PHP para realizar solicitudes HTTP a las APIs externas.
- **News API**: Proveedor de datos de noticias de diversas fuentes.
- **RandomUser API**: Generador de datos de usuario aleatorios para simular autores.

## Funcionamiento

1. **Obtención de Datos**: El sistema realiza peticiones a News API para obtener noticias actualizadas según la categoría seleccionada.
2. **Procesamiento**: Procesa los datos recibidos para mostrarlos de manera organizada.
3. **Presentación**: Muestra las noticias en formato de tarjetas con diseño responsive.
4. **Navegación**: Permite al usuario filtrar por categorías y navegar entre páginas de resultados.

## Requisitos

- PHP 7.0 o superior
- Servidor web (Apache, Nginx, etc.)
- Conexión a Internet (para acceder a las APIs)
- Clave API de News API (registrarse en [newsapi.org](https://newsapi.org/register))

## Instalación

1. Clona o descarga los archivos del proyecto en tu servidor web.
2. Edita el archivo `index.php` y reemplaza `$apiKey = "97c4b65660fb437ab29cd9791315b3c2";` con tu propia API key de News API.
3. Asegúrate de que el servidor web tenga permisos para ejecutar PHP.
4. Accede al proyecto desde tu navegador web.

## Configuración

### API Key de News API

Es necesario obtener una clave API de News API:

1. Regístrate en [newsapi.org](https://newsapi.org/register)
2. Obtén tu API key
3. Reemplaza la variable `$apiKey` en el archivo `index.php`

### Limitaciones del Plan Gratuito

Si utilizas el plan gratuito de News API, ten en cuenta estas limitaciones:

- Solo funciona en entorno de desarrollo (localhost)
- Acceso limitado a endpoints específicos
- Límite de peticiones diarias

## Personalización

- Para cambiar el estilo visual: modifica el archivo `styles.css`
- Para agregar o modificar categorías: edita el array `$categories` en `index.php`
- Para cambiar el número de artículos por página: modifica la variable `$articlesPerPage` en `index.php`

## Contribuciones

Las contribuciones son bienvenidas. Para contribuir:

1. Haz un fork del repositorio
2. Crea una rama para tu característica (`git checkout -b feature/nueva-caracteristica`)
3. Realiza tus cambios
4. Haz commit de tus cambios (`git commit -m 'Agrega nueva característica'`)
5. Haz push a la rama (`git push origin feature/nueva-caracteristica`)
6. Abre un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo LICENSE para más detalles.

## Créditos

- News API - [newsapi.org](https://newsapi.org)
- Random User API - [randomuser.me](https://randomuser.me)
- Bootstrap - [getbootstrap.com](https://getbootstrap.com)
- Font Awesome - [fontawesome.com](https://fontawesome.com)
