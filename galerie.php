/**
 * Gallery — Masonry + GLightbox
 *
 * @description Enqueue GLightbox (CDN) + libs natives WP (Masonry, imagesLoaded)
 *              et injection du JS d'initialisation inline.
 *
 * @libs        GLightbox v3 (CDN jsDelivr)
 *              Masonry + imagesLoaded (natifs WP core)
 *
 * @usage       Coller dans functions.php ou via WPCode (type : PHP Snippet)
 *
 * @author      Ton Nom
 * @version     1.0.0
 */

add_action( 'wp_enqueue_scripts', function () {

    // ── CSS ──────────────────────────────────────────────────────────────────
    wp_enqueue_style(
        'glightbox',
        'https://cdn.jsdelivr.net/npm/glightbox@3/dist/css/glightbox.min.css',
        [], '3'
    );

    // ── JS externe ───────────────────────────────────────────────────────────
    wp_enqueue_script(
        'glightbox',
        'https://cdn.jsdelivr.net/npm/glightbox@3/dist/js/glightbox.min.js',
        [], '3', true
    );

    // ── Libs natives WP core ─────────────────────────────────────────────────
    wp_enqueue_script( 'imagesloaded' );
    wp_enqueue_script( 'masonry' );

    // ── Init JS inline ───────────────────────────────────────────────────────
    $js = <<<'JS'
document.addEventListener("DOMContentLoaded", function () {

  document.querySelectorAll(".wp-block-gallery").forEach(function (gallery, gIdx) {

    var items = Array.from(gallery.querySelectorAll("figure")).map(function (fig) {
      var img  = fig.querySelector("img");
      var link = fig.querySelector("a");
      return {
        img:     img,
        fullSrc: link ? link.href : (img ? img.src : "")
      };
    }).filter(function (item) { return item.img; });

    if (!items.length) return;

    var grid  = document.createElement("div");
    grid.className = "masonry-grid";

    var sizer = document.createElement("div");
    sizer.className = "masonry-grid-sizer";
    grid.appendChild(sizer);

    items.forEach(function (item) {
      var div = document.createElement("div");
      div.className = "masonry-item";

      var a = document.createElement("a");
      a.className       = "glightbox";
      a.href            = item.fullSrc;
      a.dataset.gallery = "gallery-" + gIdx;
      a.appendChild(item.img.cloneNode(true));

      div.appendChild(a);
      grid.appendChild(div);
    });

    gallery.innerHTML = "";
    gallery.appendChild(grid);

    imagesLoaded(grid, function () {
      new Masonry(grid, {
        itemSelector:       ".masonry-item",
        columnWidth:        ".masonry-grid-sizer",
        percentPosition:    true,
        gutter:             16,
        transitionDuration: "0.25s"
      });
    });
  });

  GLightbox({
    selector:        ".glightbox",
    touchNavigation: true,
    loop:            true,
    openEffect:      "zoom",
    closeEffect:     "fade",
    slideEffect:     "slide",
    descPosition:    "bottom"
  });

});
JS;

    wp_add_inline_script( 'glightbox', $js );
} );
