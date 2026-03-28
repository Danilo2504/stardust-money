import './bootstrap';

$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        placeholder: "Seleccionar..."
    });

    $('.datepicker').flatpickr({
        dateFormat: "Y-m-d"
    });
});