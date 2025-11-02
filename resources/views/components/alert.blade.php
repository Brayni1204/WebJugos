<div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "{{ $type }}", // success, error, warning, info, question
                title: "{{ $title }}",
                text: "{{ $message }}",
                confirmButtonText: "Aceptar",
                timer: 5000, // La alerta se cierra en 5 segundos automáticamente
                showCancelButton: false
            });
        });
    </script>

</div>
