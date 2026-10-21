# Validación del Laboratorio 4

Pruebas iniciales: 20 de octubre de 2026. Revalidación final: 21 de octubre de 2026 (Costa Rica).

## Resultado local

| Revisión | Estado | Evidencia |
| --- | --- | --- |
| Docker Compose | PASS | `docker compose config` validado antes de iniciar el entorno aislado. |
| Instalación local | PASS | WordPress 7.1.3, WooCommerce 11.2.1, PHP 8.3.35 y MariaDB 11.4.13 iniciaron en Docker. |
| Configuración de tienda | PASS | País Costa Rica, moneda CRC sin decimales, zona horaria `America/Costa_Rica`, impuestos apagados y checkout de visitante. |
| Pago y entrega | PASS | BACS aparece como “Pago manual”, sin cuentas bancarias; la zona de Costa Rica ofrece recogida local sin costo. |
| Visibilidad del catálogo | PASS | Se desactivó el modo WooCommerce “Próximamente”; la portada y la tienda cargaron sin iniciar sesión. El contenedor sigue limitado a `127.0.0.1`. |
| Catálogo | PASS | Cuatro categorías y doce productos simples publicados; las doce fichas tienen descripción, precio, inventario administrado e imagen. |
| Tienda y categorías | PASS | Catálogo completo, cantidades, precios en CRC, filtros de categoría y fichas se revisaron en el navegador. No se encontraron desbordamientos horizontales en el viewport móvil usado. |
| Contenido del sitio | PASS | Portada, guía de compra, Nuestra historia, preguntas frecuentes y privacidad. La navegación incluye inicio, tienda, categorías y carrito. |
| Carrito y checkout | PASS | Flujo probado con recogida local y BACS; los controles visibles del cliente están en español. La política de privacidad del checkout y la moneda muestran CRC. |
| Repetición de scripts | PASS | `configure-store.php`, `seed-products.php` y `setup-storefront.php` se ejecutaron en Docker; las fichas conservaron su inventario administrado y no se duplicaron productos ni categorías. |
| Pulido visual | PASS | Inicio revisado en escritorio y a 390 × 844; barra y héroe a ancho completo, encuadre del ciclista ajustado para móvil, botones con alturas consistentes y menú móvil accesible. |
| Copy visible | PASS | El escaneo de textos públicos de páginas y productos no encontró referencias académicas ni avisos de simulación. |
| Validación técnica final | PASS | `docker compose config`, lint de PHP, comprobación de JavaScript, `git diff --check` e Impeccable detect (`[]`) pasaron. |

## Pedidos creados y gestionados

Los tres pedidos se enviaron desde el checkout de la tienda con nombres, direcciones y correos sintéticos `example.test`. Los estados se cambiaron en WooCommerce → Pedidos.

| Pedido local | Productos | Total comprobado | Estado final |
| --- | --- | ---: | --- |
| #81 | Bicicleta MTB de doble suspensión × 1; Sillín MTB negro y rojo × 2 | ₡1.536.000 | En espera |
| #82 | Bicicleta MTB hardtail × 2; Bebidas para ciclismo × 3 | ₡760.000 | Procesando |
| #83 | Bicicleta gravel × 1; Sillín negro y azul × 2 | ₡475.000 | Completado |

Los tres pedidos usan el método manual de prueba y la recogida gratuita simulada. No se ingresó información financiera ni se realizó una transferencia. Los IDs corresponden únicamente a la base de datos local usada para esta verificación; esa base no forma parte del repositorio.

## Correo y video

- El entorno Docker no tiene servicio SMTP. WooCommerce guarda los pedidos, pero no puede enviar las notificaciones por correo; la página de preguntas frecuentes lo explica.
- **Video público: pendiente.** El usuario pidió explícitamente no grabarlo todavía mientras se termina de pulir el sitio. No se creó ni compartió ningún video.

## Seguridad y procedencia

- La instalación usa un archivo `.env` local ignorado por Git. El repositorio contiene solo `.env.example`, configuración, guías, tema y assets de demostración.
- Los scripts `configure-store.php`, `seed-products.php` y `setup-storefront.php` rechazan hosts que no sean `localhost` o `127.0.0.1`.
- El checkout no usa pasarela real, no guarda cuentas bancarias y no calcula impuestos.
- Las doce imágenes de producto y la portada son ilustraciones generadas para el proyecto. No se distribuyen las fotografías del repositorio privado. Ver `ASSETS.md`.
- Se omitieron imágenes de referencia con marcas de agua y una fotografía cuyo metadata identificaba a una persona.

## Revisión Thermos

Completadas dos revisiones independientes de corrección/seguridad y mantenibilidad. Se resolvieron las observaciones antes de publicar el repositorio.
