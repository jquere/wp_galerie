/**
 * Gallery — Masonry + GLightbox
 *
 * @description Transforme automatiquement tous les blocs Galerie natifs
 *              WordPress en grille Masonry + lightbox GLightbox.
 *              Aucune classe CSS manuelle requise sur les blocs.
 *
 * @requires    masonry (WP core), imagesloaded (WP core), glightbox (CDN)
 *
 * @author      Ton Nom
 * @version     1.0.0
 *
 * @note        Fourni à titre de référence.
 *              En production, injecté via wp_add_inline_script() dans enqueue.php
 */

document.addEventListener("DOMContentLoaded", function () {

  /* ─────────────────────────────────────────────────────────
     Tous les .wp-block-gallery → Masonry + GLightbox
  ───────────────────────────────────────────────────────── */
  document.querySelectorAll(".wp-block-gallery").forEach(function (gallery, gIdx) {

    // Extrait les images depuis les figures natives WP
    var items = Array.from(gallery.querySelectorAll("figure")).map(function (fig) {
      var img  = fig.querySelector("img");
      var link = fig.querySelector("a");
      return {
        img:     img,
        fullSrc: link ? link.href : (img ? img.src : "")
      };
    }).filter(function (item) { return item.img; });

    if (!items.length) return;

    // Construit la grille Masonry
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

    // Remplace le contenu natif WP
    gallery.innerHTML = "";
    gallery.appendChild(grid);

    // Init Masonry après chargement des images
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

  /* ─────────────────────────────────────────────────────────
     GLightbox — init après reconstruction du DOM
  ───────────────────────────────────────────────────────── */
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
