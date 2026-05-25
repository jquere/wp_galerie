/**
 * Gallery — Masonry + GLightbox
 *
 * @description Enqueue GLightbox (CDN) + libs natives WP (Masonry, imagesLoaded)
 *              et injection du JS d'initialisation inline.
 *              Spinner SVG CSS minimaliste + ratio natif préservé avant chargement.
 *
 * @libs        GLightbox v3 (CDN jsDelivr)
 *              Masonry + imagesLoaded (natifs WP core)
 *
 * @usage       Coller dans functions.php ou via WPCode (type : PHP Snippet)
 *
 * @author      Ton Nom
 * @version     1.3.0
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

  document.querySelectorAll(".wp-block-gallery img[loading='lazy']").forEach(function (img) {
    img.setAttribute("loading", "eager");
    if (img.dataset.src) {
      img.src = img.dataset.src;
    }
  });

  setTimeout(function () {

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

      var grid = document.createElement("div");
      grid.className = "masonry-grid";

      var sizer = document.createElement("div");
      sizer.className = "masonry-grid-sizer";
      grid.appendChild(sizer);

      var msnry;

      items.forEach(function (item) {
        var div = document.createElement("div");
        div.className = "masonry-item";

        var w = parseInt(item.img.getAttribute("width"));
        var h = parseInt(item.img.getAttribute("height"));
        if (w && h) {
          div.style.paddingBottom = ((h / w) * 100) + "%";
        } else {
          div.style.paddingBottom = "75%";
        }

        var spinner = document.createElement("div");
        spinner.className = "img-spinner";
        spinner.innerHTML =
          '<svg width="40" height="40" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">' +
            '<rect x="16" y="16" width="16" height="16" fill="none" stroke="currentColor" stroke-width="0.5"/>' +
            '<rect x="16" y="16" width="16" height="16" fill="none" stroke="currentColor" stroke-width="0.5"/>' +
          '</svg>';
        div.appendChild(spinner);

        var a = document.createElement("a");
        a.className       = "glightbox";
        a.href            = item.fullSrc;
        a.dataset.gallery = "gallery-" + gIdx;

        var clonedImg = item.img.cloneNode(true);
        clonedImg.removeAttribute("loading");
        clonedImg.removeAttribute("decoding");
        clonedImg.setAttribute("loading", "eager");

        clonedImg.addEventListener("load", function () {
          div.style.paddingBottom = "";
          this.classList.add("loaded");
          spinner.classList.add("hidden");
          if (msnry) msnry.layout();
        });

        a.appendChild(clonedImg);
        div.appendChild(a);
        grid.appendChild(div);
      });

      gallery.innerHTML = "";
      gallery.appendChild(grid);

      imagesLoaded(grid, function () {
        msnry = new Masonry(grid, {
          itemSelector:       ".masonry-item",
          columnWidth:        ".masonry-grid-sizer",
          percentPosition:    true,
          gutter:             16,
          transitionDuration: "0.25s"
        });
        [300, 600, 1200, 2500].forEach(function (delay) {
          setTimeout(function () {
            if (msnry) msnry.layout();
          }, delay);
        });
      });

      grid.querySelectorAll("img").forEach(function (img) {
        img.addEventListener("load", function () {
          if (msnry) msnry.layout();
        });
      });

    });

  }, 300);

  setTimeout(function () {
    GLightbox({
      selector:        ".glightbox",
      touchNavigation: true,
      loop:            true,
      openEffect:      "zoom",
      closeEffect:     "fade",
      slideEffect:     "slide",
      descPosition:    "bottom"
    });
  }, 500);

});
JS;

    wp_add_inline_script( 'glightbox', $js );
} );
