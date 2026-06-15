<?php
/**
 * Title: Inicio — Noticias
 * Slug: intt-theme/inicio-noticias
 * Description: Sección de noticias con artículo destacado y dos secundarios.
 * Categories: intt-componentes
 * Inserter: false
 *
 * REGLA DE CONTENIDO — leer antes de editar:
 *
 * Columna izquierda (destacada): muestra ÚNICAMENTE el post marcado como sticky.
 * Columna derecha: muestra los 2 posts más recientes que NO sean sticky.
 *
 * Reglas para el editor:
 * - Siempre debe haber exactamente UN post marcado como sticky.
 *   Si no hay ninguno, la columna izquierda muestra "No hay noticias publicadas".
 * - Si hay dos o más stickies, solo aparece el más reciente en la columna
 *   izquierda; los demás no aparecen en esta sección.
 * - Para cambiar el post destacado: marcar el nuevo como sticky y
 *   desmarcar el anterior (Entradas → Edición rápida → "Fijar en la parte superior").
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"has-global-padding intt-noticias-home","backgroundColor":"azul-electrico-50","style":{"spacing":{"padding":{"top":"var:preset|spacing|sp-64","bottom":"var:preset|spacing|sp-64"}}},"layout":{"type":"constrained","contentSize":"var(--wp--style--global--wide-size)"}} -->
<section class="wp-block-group alignfull has-global-padding has-azul-electrico-50-background-color has-background intt-noticias-home" style="padding-top:var(--wp--preset--spacing--sp-64);padding-bottom:var(--wp--preset--spacing--sp-64)">

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|sp-8"}},"typography":{"textTransform":"uppercase"}}} -->
<p style="margin-top:0;margin-bottom:var(--wp--preset--spacing--sp-8);text-transform:uppercase">Comunicaciones</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|sp-32"}}}} -->
<h2 class="wp-block-heading" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--sp-32)">Noticias INTT</h2>
<!-- /wp:heading -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|sp-32","left":"var:preset|spacing|sp-24"}}}} -->
<div class="wp-block-columns">

<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%">
<!-- wp:query {"queryId":1,"query":{"postType":"post","perPage":1,"sticky":"only","order":"desc","orderBy":"date","inherit":false},"layout":{"type":"default"}} -->
<div class="wp-block-query">
<!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|sp-16"}},"layout":{"type":"default"}} -->
<div class="wp-block-group">
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","style":{"border":{"radius":{"topLeft":"var(--wp--custom--radius--lg)","topRight":"var(--wp--custom--radius--lg)","bottomLeft":"var(--wp--custom--radius--lg)","bottomRight":"var(--wp--custom--radius--lg)"}},"spacing":{"margin":{"bottom":"var:preset|spacing|sp-16"}}}} /-->
<!-- wp:post-title {"level":3,"isLink":true,"style":{"elements":{"link":{"color":{"text":"var:preset|color|azul-marino-600"}}},"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"bottom":"var:preset|spacing|sp-16"}}},"fontSize":"heading-2"} /-->
<!-- wp:post-excerpt {"moreText":"","showMoreOnNewLine":false,"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"bottom":"var:preset|spacing|sp-16"}}}} /-->
<!-- wp:post-date {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->
<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>No hay noticias publicadas.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%">
<!-- wp:query {"queryId":2,"query":{"postType":"post","perPage":2,"sticky":"exclude","order":"desc","orderBy":"date","inherit":false},"layout":{"type":"default"}} -->
<div class="wp-block-query">
<!-- wp:post-template {"className":"intt-noticias-secundarias","style":{"spacing":{"blockGap":"var:preset|spacing|sp-24"}},"layout":{"type":"default"}} -->
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|sp-16"}},"layout":{"type":"default"}} -->
<div class="wp-block-group">
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","style":{"border":{"radius":{"topLeft":"var(--wp--custom--radius--md)","topRight":"var(--wp--custom--radius--md)","bottomLeft":"var(--wp--custom--radius--md)","bottomRight":"var(--wp--custom--radius--md)"}},"spacing":{"margin":{"bottom":"var:preset|spacing|sp-16"}}}} /-->
<!-- wp:post-title {"level":3,"isLink":true,"style":{"elements":{"link":{"color":{"text":"var:preset|color|azul-marino-600"}}}},"fontSize":"heading-4"} /-->
<!-- wp:post-date /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->
<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>No hay más noticias.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:column -->

</div>
<!-- /wp:columns -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|sp-32","bottom":"0"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--sp-32);margin-bottom:0"><a href="#">Ver todas las noticias →</a></p>
<!-- /wp:paragraph -->

</section>
<!-- /wp:group -->
