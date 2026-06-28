// Helper to wire DataTables processing state to the custom Bootstrap modal.
// Defined outside $(document).ready() so it's available when page scripts
// (loaded via @stack('scripts')) register their ready callbacks — those
// fire before this module's ready callback because they register first.
window.sbDatatableProcessing = function(table) {
    var modalEl = document.getElementById('datatableProcessingModal');
    if (!modalEl) return;
    var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    table.on('processing.dt', function(e, settings, processing) {
        if (processing) {
            modal.show();
        } else {
            modal.hide();
        }
    });
};

$(document).ready(function() {
    // Select2 global init
    $('.select2').select2({
        width: '100%',
        placeholder: "Seleccionar..."
    });

    // Flatpickr global init
    $('.datepicker').flatpickr({
        dateFormat: "Y-m-d"
    });

    // SB Admin 2 — sidebar toggle
    $("#sidebarToggle, #sidebarToggleTop").on("click", function(e) {
        e.preventDefault();
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");

        if ($(".sidebar").hasClass("toggled")) {
            $(".sidebar .collapse").collapse("hide");
        }
    });

    // SB Admin 2 — auto-collapse sidebar on small screens
    function sbAutoCollapse() {
        if ($(window).width() < 768 && !$("body").hasClass("sidebar-toggled")) {
            $("body").addClass("sidebar-toggled");
            $(".sidebar").addClass("toggled");
            $(".sidebar .collapse").collapse("hide");
        }
    }

    $(window).on("resize", sbAutoCollapse);
    sbAutoCollapse();

    // SB Admin 2 — scroll to top button
    $(document).on("scroll", function() {
        if ($(this).scrollTop() > 100) {
            $(".scroll-to-top").fadeIn();
        } else {
            $(".scroll-to-top").fadeOut();
        }
    });

    $(document).on("click", "a.scroll-to-top", function(e) {
        e.preventDefault();
        var target = $(this).attr("href");
        $("html, body").stop().animate({
            scrollTop: $(target).offset().top
        }, 1000, "easeInOutExpo");
    });

    // Prevent content scroll when scrolling sidebar on fixed-nav layouts
    $("body.fixed-nav .sidebar").on("mousewheel DOMMouseScroll wheel", function(e) {
        if ($(window).width() > 768) {
            var e0 = e.originalEvent;
            var delta = e0.wheelDelta || -e0.detail;
            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        }
    });

});
