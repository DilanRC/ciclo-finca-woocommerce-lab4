# Ciclo Finca — Laboratorio 4 de WooCommerce

Proyecto independiente para el Laboratorio 4 de EIF4280 — Fundamentos de programación web. La tienda usa WordPress y WooCommerce en Docker Compose, Costa Rica como país, colones como moneda, pago manual de simulación y recogida local gratuita.

El repositorio contiene la configuración Docker, un tema de bloques propio para WordPress, guías, scripts WP-CLI y assets propios de Ciclo Finca.

La configuración usa WordPress 7.1.3 con PHP 8.3, MariaDB 11.4 y WooCommerce 11.2.1.

## Requisitos

- Docker Engine con Docker Compose v2.
- Conexión a Internet para descargar las imágenes y el plugin WooCommerce.
- Navegador web.

El servicio web se enlaza solo a 127.0.0.1 en el puerto 8094 por defecto. La tienda es visible sin iniciar sesión desde el navegador local; este proyecto no despliega el sitio a un servidor público.

Docker Compose usa el nombre de la carpeta como nombre del proyecto para mantener separadas sus redes y volúmenes. Si ejecuta copias distintas que tienen el mismo nombre de carpeta, añada una etiqueta única `-p` a todos sus comandos `docker compose`.

## Instalación local

1. Copie .env.example como .env. Genere dos claves locales diferentes con el comando openssl rand -hex 32 y asígnelas a LAB4_DB_PASSWORD y LAB4_DB_ROOT_PASSWORD. No use claves reales de otros servicios.
2. Inicie la base de datos y WordPress:

   ~~~sh
   docker compose up -d db wordpress
   ~~~

3. Abra http://localhost:8094 y termine el instalador de WordPress. Use un usuario administrador y una contraseña de laboratorio. Para evitar datos personales, puede usar un correo reservado como admin@example.test.
4. Instale WooCommerce, el idioma español de Costa Rica y aplique la configuración de la tienda:

   ~~~sh
   docker compose --profile tools run --rm cli wp plugin install woocommerce --version=11.2.1 --activate
   docker compose --profile tools run --rm cli wp language core install es_CR --activate
   docker compose --profile tools run --rm cli wp language plugin install woocommerce es_CR
   docker compose --profile tools run --rm cli wp eval-file /lab/configure-store.php
   ~~~

5. Cargue categorías, productos simples, inventario e imágenes:

   ~~~sh
   docker compose --profile tools run --rm cli wp eval-file /lab/seed-products.php
   ~~~

6. Cree la portada, páginas informativas y navegación en español:

   ~~~sh
   docker compose --profile tools run --rm cli wp eval-file /lab/setup-storefront.php
   ~~~

7. En WooCommerce > Ajustes, confirme país Costa Rica, moneda CRC y el método de pago manual de prueba. El script crea el método de recogida local gratuita para que el checkout de productos físicos no dependa de transportistas ni tarifas reales.
8. Compruebe la tienda en http://localhost:8094 y en el panel administrativo.

## Edición desde WordPress

- En **Páginas > Inicio**, edite la portada con bloques. El bloque Portada permite cambiar imagen, textos, botones, ancho, altura, espaciado y colores. Categorías, productos, pasos y enlaces también son bloques que puede mover, quitar o reemplazar.
- Para editar la cabecera y el pie, abra **Páginas > Inicio > Editar** y despliegue el panel **Diseño global** en los ajustes de la página. Sus enlaces abren directamente las partes de plantilla. También puede abrir **Plantilla > Mostrar plantilla**, seleccionar **Encabezado** o **Pie de página** en la vista de lista y pulsar **Editar original**. Edite el texto de los enlaces directamente en el lienzo; en sus ajustes puede cambiar la dirección, el icono, el tamaño y el color. La navegación también permite cambiar su etiqueta accesible y el botón móvil.
- Para cambiar el icono de marca, selecciónelo y use **Ajustes del icono**. Para ajustar el ancho, la altura, los márgenes, el relleno o el color de Portada, Grupo, Título, Párrafo, Botón, Imagen, Icono, Enlace con icono y Navegación, abra **Estilos > Dimensiones** o los paneles de color y tipografía del bloque. La opción de ancho completo se controla con la alineación **Ancho completo**.
- En **Apariencia > Editor > Estilos**, cambie la paleta y la tipografía global.
- Las páginas **Nuestra historia**, **Cómo comprar**, **Preguntas frecuentes** y **Privacidad** también usan bloques nativos de párrafo, título y enlace.
- Las páginas **Carrito** y **Finalizar compra** usan los bloques oficiales de WooCommerce. **Mi cuenta** combina títulos y texto editables con el formulario dinámico de la tienda en un bloque propio.
- En **Páginas > Tienda**, edite el catálogo con bloques. La plantilla de WooCommerce muestra ese contenido y mantiene el encabezado y el pie globales. Puede cambiar el texto, el ordenamiento, las columnas y cada bloque de tarjeta de producto desde el editor de la página.
- El instalador crea la portada una sola vez y conserva su contenido cuando se vuelve a ejecutar. Las páginas informativas antiguas se convierten solo cuando aún coinciden exactamente con el contenido original; los cambios del administrador se respetan. El encabezado y el pie son partes de plantilla globales; se editan desde el panel **Diseño global** del editor de páginas o desde **Apariencia > Editor**.

Si la configuración de WordPress todavía no terminó, espere a que cargue el instalador antes de ejecutar los comandos WP-CLI. El contenedor CLI utiliza el mismo volumen que el servidor web, pero no instala WordPress por sí solo.

El entorno no configura correo SMTP. WooCommerce guarda los pedidos en la base local, pero no se deben esperar mensajes de confirmación por email.

## Catálogo de demostración

El script crea cuatro categorías y doce productos simples. Cada ficha queda publicada con nombre, descripción, precio, imagen, SKU único e inventario administrado. El tema propio de bloques Ciclo Finca aplica la identidad visual de la tienda sin añadir plugins de diseño.

| Categoría | Producto | Precio | Stock inicial |
| --- | --- | ---: | ---: |
| Bicicletas | Bicicleta MTB de doble suspensión | ₡1.500.000 | 6 |
| Bicicletas | Bicicleta MTB hardtail | ₡350.000 | 8 |
| Bicicletas | Bicicleta gravel | ₡425.000 | 5 |
| Accesorios | Sillín MTB negro y rojo | ₡18.000 | 12 |
| Accesorios | Sillín negro y azul | ₡25.000 | 10 |
| Nutrición | Bebidas para ciclismo | ₡20.000 | 35 |
| Accesorios | Casco MTB Trail | ₡42.000 | 12 |
| Accesorios | Guantes MTB Trail | ₡18.500 | 20 |
| Accesorios | Botella de ciclismo | ₡14.000 | 24 |
| Accesorios | Kit de herramientas para ruta | ₡22.500 | 15 |
| Accesorios | Luces LED para bicicleta | ₡28.000 | 16 |
| Ropa | Jersey de ciclismo verde | ₡36.000 | 10 |

Los artículos y existencias corresponden a esta instalación local. Las doce imágenes de producto y la portada se generaron para el proyecto; los nombres y las descripciones se adaptaron a la tienda Ciclo Finca. El detalle está en `ASSETS.md`.

La semilla valida primero las doce imágenes. Si se vuelve a ejecutar, actualiza los productos que ya tengan esos SKU y repara imágenes cuando cambian los assets, sin restablecer existencias administradas ni duplicar productos o categorías.

La portada incluye accesos a las cuatro categorías, ocho productos destacados con descripciones cortas, información sobre precios en CRC, pago manual y recogida local, y una guía de compra. El catálogo completo se guarda en la página Tienda como bloques WooCommerce; sus tarjetas muestran imagen, nombre, precio y botón de compra, y cada producto incluye una descripción más completa en su ficha. También hay páginas de información de la tienda, preguntas frecuentes y privacidad. El carrito y el checkout muestran el flujo en español. El instalador elimina el contenido de ejemplo “Hello world!” y “Sample Page”.

## Configuración de pago, moneda y entrega

El script de configuración establece Costa Rica (CR), CRC, zona horaria America/Costa_Rica, cero decimales, impuestos desactivados, checkout para visitantes, visibilidad pública sin modo “Próximamente” y una zona de recogida local gratuita. Activa BACS como pago manual con instrucciones explícitas de simulación, sin datos de cuentas bancarias, y desactiva Contra entrega y Cheque.

Un pedido BACS queda En espera para revisión manual. En este laboratorio, los cambios a Procesando o Completado son ejercicios administrativos; no se debe transferir dinero ni marcar un pedido real como pagado.

## Tres pedidos de prueba

Desde la tienda pública, abra productos, añádalos al carrito, cambie cantidades, confirme el total y finalice como visitante con datos ficticios. El retiro es gratuito y no se calculan impuestos.

| Pedido | Productos y cantidades | Total esperado | Estado de práctica |
| --- | --- | ---: | --- |
| 1 | Bicicleta MTB de doble suspensión × 1; Sillín MTB negro y rojo × 2 | ₡1.536.000 | En espera |
| 2 | Bicicleta MTB hardtail × 2; Bebidas para ciclismo × 3 | ₡760.000 | Procesando |
| 3 | Bicicleta gravel × 1; Sillín negro y azul × 2 | ₡475.000 | Completado |

Use nombres como Cliente de Prueba 1, correos cliente1@example.test, cliente2@example.test y cliente3@example.test, y una dirección claramente ficticia. No escriba información real de estudiantes ni datos financieros.

En WooCommerce > Pedidos, revise para cada pedido el cliente, los productos, las cantidades y el total. Mantenga uno En espera, cambie otro a Procesando y complete el tercero para demostrar los estados solicitados.

## Video público de entrega

La consigna requiere un video breve con acceso público y el enlace compartido en Aula Virtual. El video debe mostrar un pedido completo desde la tienda pública, el carrito y el checkout; luego debe mostrar el mismo pedido en el panel, sus detalles y el cambio de estado. Incluya en la grabación la lista de los tres pedidos y los estados En espera, Procesando y Completado. Compruebe el enlace en una ventana privada antes de entregar.

La grabación queda pendiente mientras se termina de pulir la tienda. Cuando esté lista, debe mostrar el checkout, los tres pedidos y sus estados. Este repositorio no contiene una URL de video inventada.

## Comprobaciones útiles

- Validar la configuración de Docker: docker compose config
- Ver los servicios: docker compose ps
- Comprobar los títulos y productos de Tienda y Bicicletas: `python3 scripts/verify-storefront.py` (opcionalmente, indique la URL base local como argumento).
- Revisar el catálogo: en Productos > Todos los productos del panel de WordPress.
- Consultar los registros del servidor: docker compose logs -f wordpress

Para detener los contenedores y conservar el sitio, use docker compose down. El comando docker compose down -v elimina la base de datos, el catálogo, los pedidos y los medios locales de este laboratorio.

## Referencias técnicas

- [Imagen oficial de WordPress para Docker](https://github.com/docker-library/docs/blob/master/wordpress/README.md)
- [Requisitos de servidor de WooCommerce](https://woocommerce.com/document/update-php-wordpress/)
- [Transferencia bancaria directa (BACS)](https://woocommerce.com/document/bacs/)
- [Resumen de la consigna del Laboratorio 4](docs/CONSIGNA.md)
- [Procedencia de imágenes](ASSETS.md)
