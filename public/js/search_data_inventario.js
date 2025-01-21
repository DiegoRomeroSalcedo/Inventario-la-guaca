document.addEventListener('DOMContentLoaded', () => {
    // Inicializa DataTable solo si no está inicializado
    if (!$.fn.DataTable.isDataTable('#inventoryTable')) {
        let table = $('#inventoryTable').DataTable({
            scrollX: true,
            buttons: [
                'copy', 'excel', 'pdf'
            ]
        });

        // Mueve el contenedor de botones a la tabla
        table.buttons().container().prependTo(table.table().container());
    }

    // Manejar el formulario de búsqueda
    document.querySelector('#searchForm').addEventListener('submit', event => {
        event.preventDefault();
        fetchInventory();
    });
});

async function fetchInventory() {
    const formData = new FormData(document.querySelector('#searchForm'));
    const params = new URLSearchParams(formData);

    const response = await fetch('/inventario', {
        method: 'post',
        body: params
    });

    if (response.ok) {
        const data = await response.json();
        updateTable(data);
    } else {
        console.log('Error Fetching inventory Data');
    }
}

function updateTable(data) {
    // Usa DataTables API para actualizar la tabla
    const table = $('#inventoryTable').DataTable();

    // Limpia los datos existentes
    table.clear();

    // Añadir nuevos datos
    table.rows.add(data.array.map(item => {
        
        const costoProdString = item.cost_produ.replace(/,/g, ''); 
        const costoProd = parseFloat(costoProdString); 

        const costoFinalProd = item.pre_finpro.replace(/,/g, '');
        const costoFinal = parseFloat(costoFinalProd);

        const precioVentaStr = item.pre_ventap.replace(/,/g, '');
        const precioVenta = parseFloat(precioVentaStr);

        const preVenDescStr = item.pre_ventades.replace(/,/g, '');
        const precioVentaDesc = parseFloat(preVenDescStr);

        // Convierte la cantidad a número flotante
        const cantidad = parseFloat(item.cantidad); 

        let totalCosProd, totalPrecioVenta, difCosProdPreVenta, totalPrecioVentaDesc, difCosProdPreVenDesc;

        if (isNaN(cantidad) || cantidad === 0) {
            totalCosProd = costoFinal;
            totalPrecioVenta = precioVenta;
            difCosProdPreVenta = totalPrecioVenta - totalCosProd; // diferencia entre los totales
            totalPrecioVentaDesc = precioVentaDesc;
            difCosProdPreVenDesc = totalPrecioVentaDesc - totalCosProd;

        } else {
            totalCosProd = costoFinal * cantidad;
            totalPrecioVenta = precioVenta * cantidad;
            difCosProdPreVenta = totalPrecioVenta - totalCosProd; // diferencia entre los totales
            totalPrecioVentaDesc = precioVentaDesc * cantidad;
            difCosProdPreVenDesc = totalPrecioVentaDesc - totalCosProd;
        }

        // Formatea el número con comas y dos decimales
        const formattedTotalCosProd = totalCosProd.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        const formattedTotalPrecioVenta = totalPrecioVenta.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        const formattedDifCosProdPreVenta = difCosProdPreVenta.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        const formattedTotalPrecioVentaDesc = totalPrecioVentaDesc.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        const fomattedDifCosProdPreVenDesc = difCosProdPreVenDesc.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        const formattedPrecioCosto = costoProd.toLocaleString('en-US', {
            minimumFractionDigits: 2, 
            maximumFractionDigits: 2 
        })

        const formattedcostoFinal = costoFinal.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })

        const formattedPrecioVenta = precioVenta.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })

        const formattedprecioVentaDesc = precioVentaDesc.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })

        return [
            item.id_product,
            item.no_product,
            item.id_marca,
            item.nombre_marca,
            item.cantidad,
            formattedPrecioCosto,
            item.rte_fuente,
            item.flet_produ,
            item.iva_produc,
            formattedcostoFinal,
            item.uti_produc,
            formattedPrecioVenta,
            item.desc_produ,
            formattedprecioVentaDesc,
            item.ren_product,
            item.detalle_product,
            formattedTotalCosProd,
            formattedTotalPrecioVenta,
            formattedDifCosProdPreVenta,
            formattedTotalPrecioVentaDesc,
            fomattedDifCosProdPreVenDesc,
            item.usuario_insercion,
            item.feha_insercion,
            item.user_actual,
            item.fech_actual,
        ]
    })).draw();
}
