<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    $(document).on('submit', '.form-secured', function(e) {
        e.preventDefault();
        let form = this;
        let customMessage = $(form).data('secured-message') || '\u00bfDesea proceder?';
        
        Swal.fire({
            title: customMessage,
            inputLabel: 'Ingrese el PIN de seguridad de 4 d\u00edgitos:',
            input: 'password',
            inputAttributes: { 
                autocapitalize: 'off', 
                autocorrect: 'off', 
                maxlength: 4,
                style: 'text-align: center; font-size: 24px; letter-spacing: 8px;'
            },
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'S\u00ed, continuar',
            cancelButtonText: 'Cancelar',
            preConfirm: (pin) => {
                if (!pin || pin.length !== 4) {
                    Swal.showValidationMessage('Ingrese un PIN v\u00e1lido de 4 d\u00edgitos');
                }
                return pin;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let ip = document.createElement('input');
                ip.type = 'hidden'; 
                ip.name = 'pin'; // <-- Mantenemos 'pin' tal como lo lee tu middleware original
                ip.value = result.value;
                form.appendChild(ip);
                form.submit();
            }
        });
    });
});
</script>