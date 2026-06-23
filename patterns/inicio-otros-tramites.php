<?php
/**
 * Title: Inicio — Otros Trámites
 * Slug: intt-theme/inicio-otros-tramites
 * Categories: intt-tramites
 * Inserter: false
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"has-global-padding","style":{"spacing":{"padding":{"top":"var:preset|spacing|sp-64","bottom":"var:preset|spacing|sp-64"}}},"layout":{"type":"constrained","contentSize":"var(--wp--style--global--wide-size)"}} -->
<section class="wp-block-group alignfull has-global-padding" style="padding-top:var(--wp--preset--spacing--sp-64);padding-bottom:var(--wp--preset--spacing--sp-64)">

<!-- wp:heading {"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|sp-32"}}}} -->
<h2 class="wp-block-heading" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--sp-32)">Otros trámites</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":0,"query":{"postType":"tramite","perPage":12,"order":"asc","orderBy":"title","inherit":false},"layout":{"type":"default"}} -->
<div class="wp-block-query">

<!-- wp:post-template {"className":"intt-grid-tramites","style":{"spacing":{"blockGap":"var:preset|spacing|sp-24"}},"layout":{"type":"grid","columnCount":4}} -->
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|sp-8"}},"layout":{"type":"default"}} -->
<div class="wp-block-group">
<!-- wp:post-title {"level":3,"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|sp-0","bottom":"var:preset|spacing|sp-16"}}},"fontSize":"heading-5"} /-->
<!-- wp:post-excerpt {"moreText":"","showMoreOnNewLine":false,"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"gris-800"} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"textColor":"gris-500"} -->
<p class="has-gris-500-color has-text-color">No se encontraron trámites.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->

</div>
<!-- /wp:query -->
<!-- wp:paragraph -->
<p><a href="/tramites/">Ver todos los trámites →</a></p>
<!-- /wp:paragraph -->
</section>
<!-- /wp:group -->
