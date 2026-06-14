# INTT Theme

Tema de bloque para el portal institucional del **Instituto Nacional de Transporte Terrestre (INTT)** — República Bolivariana de Venezuela.

## Activación

1. Copiar la carpeta `intt-theme` en `wp-content/themes/`
2. Activar desde **Apariencia → Temas**
3. Verificar que el Editor de Sitio esté disponible (Apariencia → Editor)
4. Todos los templates deben aparecer como archivos de tema (sin opción "Restablecer")

## Bloques personalizados

| Bloque | Slug | Descripción |
|--------|------|-------------|
| Alert Bar | `intt/alert-bar` | Muestra la alerta activa del sitio (CPT `alerta_intt`) |
| Hub List | `intt/hub-list` | Lista de trámites de la categoría actual |
| Hub Sidebar | `intt/hub-sidebar` | Navegación lateral de términos `tipo_tramite` |
| Mega Menú | `intt/megamenu` | Barra de navegación con panel desplegable |
| Descripción del Trámite | `intt/tramite-descripcion` | Muestra el campo `descripcion_corta` del trámite |

## Tipos de contenido personalizados

- **`tramite`** — Procedimientos gubernamentales. Taxonomía: `tipo_tramite`
- **`alerta_intt`** — Alertas del sitio con fecha de expiración

## Tipografía

Georama (variable TTF) — `assets/fonts/Georama-VariableFont_wdth,wght.ttf`

## Compilación (bloques JS)

```bash
npm install
npm run build
```

Requerido para el bloque `megamenu`. Los demás bloques son solo PHP/HTML.

## Fuente de referencia

Los archivos fuente del tema anterior se encuentran en:
```
C:\Users\aangulodelacruz\Local Sites\intt\app\public\wp-content\themes\intt-portal-theme\
```
Al copiar contenido de la fuente, reemplazar `intt-portal-theme` → `intt-theme`.

## Reglas de desarrollo

- Templates solo en archivos (nunca guardar en base de datos)
- Colores y tipografía solo en `theme.json` y `style.css`
- CSS de componentes: solo estructura y espaciado, nunca redefinir colores globales
- Toda documentación, comentarios y código de interfaz en **español**
