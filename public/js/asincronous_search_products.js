document.addEventListener('DOMContentLoaded', function() {
    let form = document.getElementById('search_product_form');

    if (form) { // Verifica que el formulario existe
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío normal del formulario

            var formData = new FormData(this);

            fetch('/get-add-cantidades', { // La ruta debe ser la misma que en tu configuración de rutas
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Asegúrate de que tu servidor responda en formato JSON
            .then(data => {

                // console.log(data);
                // Maneja la respuesta aquí, por ejemplo, actualizando la vista con los resultados
                let resultsDiv = document.getElementById('results');
                resultsDiv.innerHTML = ''; // Limpia el contenido anterior
                
                if (data.productos && data.productos.length > 0) {
                    let tableHtml = `<table class="tabla_resultados">
                        <thead>
                            <tr>
                                <th class="th_results">Id_Producto</th>
                                <th class="th_results">Nombre</th>
                                <th class="th_results">Marca</th>
                                <th class="th_results">Cantidad</th>
                                <th class="th_results">Costo Producto</th>
                                <th class="th_results">Costo Final</th>
                                <th class="th_results">Precio Venta</th>
                                <th class="th_results">Precio con Descuento</th>
                            </tr>
                        </thead>
                        <tbody>`;


                    data.productos.forEach(producto => {

                        let costoProStr = producto.cost_produ.replace(/,/g, '');
                        let costoPro = parseFloat(costoProStr);

                        const formattedCostoPro = costoPro.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                        let preFinproStr = producto.pre_finpro.replace(/,/g, '');
                        let preFinpro = parseFloat(preFinproStr);

                        const formattedPreFinpro = preFinpro.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                        let preVentapStr = producto.pre_ventap.replace(/,/g, '');
                        let preVenta = parseFloat(preVentapStr);

                        const formattedPreVenta = preVenta.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                        let preVentadesStr = producto.pre_ventades.replace(/,/g, '');
                        let preVentades = parseFloat(preVentadesStr);

                        const formattedPreVentades = preVentades.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });


                        // console.log(producto.encriptados);
                        let row = `<tr>
                            <td class="td_results"><a href="/add-cantidad-product?data=${encodeURIComponent(producto.encriptados)}">${producto.id_product}</a></td>
                            <td class="td_results">${producto.no_product}</td>
                            <td class="td_results">${producto.nombre_marca}</td>
                            <td class="td_results">${producto.cantidad !== null ? producto.cantidad : 0}</td>
                            <td class="td_results">${formattedCostoPro}</td>
                            <td class="td_results">${formattedPreFinpro}</td>
                            <td class="td_results">${formattedPreVenta}</td>
                            <td class="td_results">${formattedPreVentades}</td>
                        </tr>`;
                        tableHtml += row; // Agrega cada fila de producto
                    });
                    
                    tableHtml += `</tbody></table>`;
                    resultsDiv.innerHTML = tableHtml; // Agrega la tabla al div de resultados
                } else {
                    resultsDiv.innerHTML = '<p>No se encontraron productos.</p>';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    } else {
        console.error('Formulario no encontrado');
    }
});
