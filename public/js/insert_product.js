document.addEventListener('DOMContentLoaded', () => {
    const costoInput = document.getElementById('precio_costo');
    const retencionInput = document.getElementById('retefuente');
    const fleteInput = document.getElementById('costo_flete');
    const ivaInput = document.getElementById('costo_iva');
    const costoFinalInput = document.getElementById('costo_final');
    const utilidadInput = document.getElementById('utilidad');
    const precioVentaInput = document.getElementById('precio_venta');
    const descuentoInput = document.getElementById('descuento');
    const preciodescuentoInput = document.getElementById('precioventa_desc');
    const rentabilidadInput = document.getElementById('rentabilidad');

    const toggleCheckboxInput = document.getElementById('toggleCheckbox');
    const extrafiledsContainer = document.getElementById('extrafileds');
    const extrafiledsTwoContainer = document.getElementById('extrafileds_two');

    const endDateInput = document.getElementById('endDate');

    function formatPrice(price) {
        return price.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function roundToTwoDecimals(number) {
        return Math.round(number * 100) / 100;
    }

    function actualizarCostoFinal() {
        const costo = parseFloat(costoInput.value.replace(/,/g, '')) || 0;
        const retencion = parseFloat(retencionInput.value.replace(/,/g, '')) || 0;
        const flete = parseFloat(fleteInput.value.replace(/,/g, '')) || 0;
        const iva = parseFloat(ivaInput.value.replace(/,/g, '')) || 0;

        const reteTotal = roundToTwoDecimals(costo * (retencion / 100));
        const fleteTotal = roundToTwoDecimals(costo * (flete / 100));
        const ivaTotal = roundToTwoDecimals(costo * (iva / 100));

        const costoFinal = roundToTwoDecimals(costo + reteTotal + fleteTotal + ivaTotal);
        costoFinalInput.value = formatPrice(costoFinal);

        actualizarPrecioVenta();
    }

    function actualizarPrecioVenta() {
        const costoFinal = parseFloat(costoFinalInput.value.replace(/,/g, '')) || 0;
        const utilidad = parseFloat(utilidadInput.value.replace(/,/g, '')) || 0;

        const utilidadTotal = roundToTwoDecimals(costoFinal * (utilidad / 100));
        const ventaFinal = roundToTwoDecimals(costoFinal + utilidadTotal);

        precioVentaInput.value = formatPrice(ventaFinal);

        descuentos();
        updateRentabilidad();
    }

    function descuentos() {
        const precioVenta = parseFloat(precioVentaInput.value.replace(/,/g, '')) || 0;
        const descuento = parseFloat(descuentoInput.value.replace(/,/g, '')) || 0;

        const descuentoTotal = roundToTwoDecimals(precioVenta * (descuento / 100));
        const precioTotal = roundToTwoDecimals(precioVenta - descuentoTotal);

        preciodescuentoInput.value = formatPrice(precioTotal);

        updateRentabilidad();
    }

    function updateRentabilidad() {
        const costoFinal = parseFloat(costoFinalInput.value.replace(/,/g, '')) || 0;
        const precioVenta = parseFloat(precioVentaInput.value.replace(/,/g, '')) || 0;
        const preciodescuento = parseFloat(preciodescuentoInput.value.replace(/,/g, '')) || 0;

        let rentabilidadBruta;
        if (costoFinal === 0) {
            rentabilidadBruta = 0;
        } else {
            let gananciaBruta;
            if (extrafiledsContainer.classList.contains('hidden')) {
                gananciaBruta = precioVenta - costoFinal;
            } else {
                gananciaBruta = preciodescuento - costoFinal;
            }
            rentabilidadBruta = (gananciaBruta / costoFinal) * 100;
        }
        rentabilidadInput.value = formatPrice(rentabilidadBruta);
    }

    function toggleFileds() {
        if (toggleCheckboxInput.checked) {
            extrafiledsContainer.classList.remove('hidden');
            extrafiledsTwoContainer.classList.remove('hidden');
            extrafiledsContainer.querySelector('input').required = true;
            extrafiledsTwoContainer.querySelector('input').required = true;
        } else {
            extrafiledsContainer.classList.add('hidden');
            extrafiledsTwoContainer.classList.add('hidden');
            extrafiledsContainer.querySelector('input').required = false;
            extrafiledsTwoContainer.querySelector('input').required = false;

            descuentoInput.value = '';
            preciodescuentoInput.value = '';

            endDateInput.value = ''; // Limpiamos el valor cuando está oculto
        }

        actualizarCostoFinal();
        updateRentabilidad();
    }

    toggleCheckboxInput.addEventListener('change', toggleFileds);
    costoInput.addEventListener('input', actualizarCostoFinal);
    retencionInput.addEventListener('input', actualizarCostoFinal);
    fleteInput.addEventListener('input', actualizarCostoFinal);
    ivaInput.addEventListener('input', actualizarCostoFinal);
    utilidadInput.addEventListener('input', actualizarPrecioVenta);
    descuentoInput.addEventListener('input', descuentos);

    // Inicializamos el datePicker
    $('#endDate').datepicker({
        dateFormat: "yy-mm-dd",
        timeFromat: "HH:mm:ss"
    });

    toggleFileds(); // Llamada inicial para configurar el estado correcto
});
