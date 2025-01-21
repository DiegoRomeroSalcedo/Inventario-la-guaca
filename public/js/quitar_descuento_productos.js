document.addEventListener('DOMContentLoaded', function() {

    const buttons = document.querySelectorAll('.quitar-descuento-btn');
    const BASE_URL = '/inventario/public';


    buttons.forEach(button => {

        button.addEventListener('click', function() {

            //Obtenemos los datos almacenados
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const costoFinal = this.getAttribute('data-costo-final');
            const precioVenta = this.getAttribute('data-precio-venta');
            const fecVencimiento = this.getAttribute('fec-vencimiento-descuento');
            const descuento = this.getAttribute('data-descuento-producto');

            const data = {
                id: id,
                nombre: nombre,
                costoFinal: costoFinal,
                precioVenta: precioVenta,
                fecVencimiento: fecVencimiento,
                descuento: descuento
            };

            fetch('/quitar-descuentos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success == true) {
                    alert('Descuento quitado.');
                    this.closest('tr').remove();
                } else {
                    alert('Error al quitar el descuento.');
                }
            })
            .catch(error => console.error("Error: ", error));
        });
    });
});