document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-seacrh-factura-devolucion');
    let listProductos = document.getElementById('listado-productos-factura');
    const totalFactura = document.getElementById('factura-total');
    const valorRecibidoFactura = document.getElementById('factura-valor-recibido');
    const ValorDevueltoFactura = document.getElementById('factura-valor-devuelto');
    const tipoPagoFactura = document.getElementById('factura-tipo-pago');
    const clienteFactura = document.getElementById('factura-cliente');
    const nombreClienteFactura = document.getElementById('factura-nombre');
    const telefonoClienteFactura = document.getElementById('factura-telefono');

    // Seccion tributaria
    const idFactura = document.getElementById('input-factura');
    const facturaDisplay = document.getElementById('input-factura-div');
    const totalDevolucionInput = document.getElementById('total-devolucion');

    // Finalizar la devolucion
    const finalizarDevoBtn = document.getElementById('finalizar-devolucion-btn');

    // Datos a insertarBD
    let totalDevolucionBd = 0;
    let dataProductosDevolucion = []; // Almacenaremos los distintos productos

    if (form) {
        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            let response = await fetch('/search-factura-devolucion', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Network response was not OK');
            }

            let data = await response.json();

            // Procesamos los datos para mostrarlos en pantalla
            listProductos.innerHTML = '';

            data.forEach(data => {
                let cantidad = 1;
                let lisItem = document.createElement('li');
                let canDevoFormatted = parseInt(cantidad, 10);

                let productData = {
                    id_producto: data.id_producto,
                    precio_unitario: data.precio_unitario,
                    cantidad: data.cantidad,
                    monto_venta: data.monto_venta,
                    descuento_aplicado: data.descuento_aplicado,
                    cantidadDevolucion: canDevoFormatted
                };

                //Formateamos los valores que se muestran

                //precio unitario

                let precioUnitarioStr = data.precio_unitario.replace(/,/g, '');
                let preUnitarioNumber = parseFloat(precioUnitarioStr);

                let formattedPreUnitario = preUnitarioNumber.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                //Monto Vendido

                let montoVendidoStr = data.monto_venta.replace(/,/g, '');
                let montoVendidoNumber = parseFloat(montoVendidoStr);

                let formattedMontoVendido = montoVendidoNumber.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })

                lisItem.innerHTML = `
                    <span class="data-devo-product">Id Producto:</span> ${data.id_producto} <span class="data-devo-product">- Nombre:</span> ${data.no_product} <span class="data-devo-product">- Marca:</span> ${data.nombre_marca} <span class="data-devo-product">- Precio Unitario:</span> ${formattedPreUnitario} <span class="data-devo-product">- Cantidad:</span> ${data.cantidad} <span class="data-devo-product">- Monto Vendido:</span> ${formattedMontoVendido} <span class="data-devo-product">- Descuento Aplicado:</span> ${data.descuento_aplicado}
                    <div>
                        <span style="font-size: 16px; font-weight: bold; margin-right: 10px;">Reinsertar Stock</span>
                        <label for="reinsercion-stock-${data.id_producto}-si">Sí:</label>
                        <input id="reinsercion-stock-${data.id_producto}-si" type="radio" name="reinsercion-stock-${data.id_producto}" value="1">
                        <label for="reinsercion-stock-${data.id_producto}-no">No:</label>
                        <input id="reinsercion-stock-${data.id_producto}-no" type="radio" name="reinsercion-stock-${data.id_producto}" value="2">
                    </div>
                    <div style="font-size: 16px; font-weight: bold; margin-right: 10px;">Cantidad Devolver: <span class="cantidad">${cantidad}</span></div>
                    <button class="add-cantidad" cantidad-vendida="${data.cantidad}">+</button>
                    <button class="remove-cantidad">-</button>
                    <button class="insert-wait" data-product='${JSON.stringify(productData)}'>Añadir a Devoluciones</button>
                `;

                listProductos.appendChild(lisItem);
            });

            document.querySelectorAll('.add-cantidad').forEach(button => {
                button.addEventListener('click', function() {
                    let cantidadShow = this.previousElementSibling.querySelector('.cantidad');
                    let cantidad = parseInt(cantidadShow.textContent, 10);
                    let cantidadVendida = parseInt(this.getAttribute('cantidad-vendida'), 10);

                    cantidad += 1;

                    if (cantidad > cantidadVendida) {
                        alert('Superó el límite de la cantidad previamente vendida.');
                        cantidad -= 1;
                    }
                    cantidadShow.textContent = cantidad;
                });
            });

            document.querySelectorAll('.remove-cantidad').forEach(button => {
                button.addEventListener('click', function() {
                    let cantidadShow = this.previousElementSibling.previousElementSibling.querySelector('.cantidad');
                    let cantidad = parseInt(cantidadShow.textContent, 10);

                    cantidad -= 1;

                    if (cantidad < 1) {
                        alert('Límite mínimo para devolución de 1.');
                        cantidad += 1;
                    }

                    cantidadShow.textContent = cantidad;
                });
            });

            //Formateamos el total facturado

            let totalFacturadoStr = data[0].total_venta.replace(/,/g, '');
            let totalFacturadoNumber = parseFloat(totalFacturadoStr);

            let formattedTotalFacturado = totalFacturadoNumber.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            //formateamos el valor recibido durante la facturacion
            let formattedValorRecibido;

            if (data[0].valor_recibido !== null) { 

                let valorRecibidoStr = data[0].valor_recibido.replace(/,/g, '');
                let valorRecibidoNumber = parseFloat(valorRecibidoStr);

                formattedValorRecibido = valorRecibidoNumber.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                formattedValorRecibido = 0;
            }

            //Formateamos el valor devuelto durante la facturacion

            let formattedValorDevuelto;


            if (data[0].valor_devuelto !== null) {

                let valorDevueltoStr = data[0].valor_devuelto.replace(/,/g, '');
                let valorDevueltoNumber = parseFloat(valorDevueltoStr);

                formattedValorDevuelto = valorDevueltoNumber.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                formattedValorDevuelto = 0;
            }




            totalFactura.innerHTML = `<span class="data-devo-product">Total:</span> ${formattedTotalFacturado}`;
            valorRecibidoFactura.innerHTML = `<span class="data-devo-product">Valor Recibido:</span> ${formattedValorRecibido}`;
            ValorDevueltoFactura.innerHTML = `<span class="data-devo-product">Valor Devuelto:</span> ${formattedValorDevuelto}`;
            tipoPagoFactura.innerHTML = `<span class="data-devo-product">Tipo de Pago:</span> ${data[0].tipo_pago ?? 'No reporta'}`;
            clienteFactura.innerHTML = `<span class="data-devo-product">Cédula:</span> ${data[0].identificacion ?? 'No reporta'}`;
            nombreClienteFactura.innerHTML = `<span class="data-devo-product">Nombre Cliente:</span> ${data[0].Nombre ?? 'No reporta'}`;
            telefonoClienteFactura.innerHTML = `<span class="data-devo-product">Teléfono:</span> ${data[0].telefono ?? 'No reporta'}`;

            document.querySelectorAll('.insert-wait').forEach(button => {
                button.addEventListener('click', function() {
                    let product = JSON.parse(this.getAttribute('data-product'));
                    let cantidadShow = this.parentElement.querySelector('.cantidad');
                    let cantidad = parseInt(cantidadShow.textContent, 10);

                    let productoDevolucion = {
                        id_producto: product.id_producto,
                        cantidad: cantidad,
                        precioUnitario: product.precio_unitario,
                        montoDevolucion: product.precio_unitario * cantidad,
                        reinsercion: document.querySelector(`input[name="reinsercion-stock-${product.id_producto}"]:checked`)?.value || null
                    };

                    console.log(productoDevolucion);

                    dataProductosDevolucion.push(productoDevolucion);

                    const listDataTribute = document.getElementById('datos-tributarios');
                    let listItem = document.createElement('tr');
                    let montoDevolucion = product.precio_unitario * cantidad;

                    totalDevolucionBd += montoDevolucion;

                    //Formateamos el total devolucion

                    let formattedMontoDevolucion;

                    if(montoDevolucion !== null || montoDevolucion !== undefined) {
                        let montoDevolucionNumber = parseFloat(montoDevolucion);

                        formattedMontoDevolucion = montoDevolucionNumber.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                    } else {
                        formattedMontoDevolucion = 0;
                    }
                    listItem.innerHTML = `
                        <td>Id Producto: ${product.id_producto}</td>
                        <td>Cantidad Devolución: ${cantidad}</td>
                        <td>Total Devolución: ${formattedMontoDevolucion}</td>
                        <td>Reinserción Stock: ${productoDevolucion.reinsercion ? (productoDevolucion.reinsercion === '1' ? 'Sí' : 'No') : 'No especificado'}</td>
                    `;

                    listDataTribute.appendChild(listItem);

                    //Formateamos el total devolucion input

                    let formattedtotalDevolucionBd;

                    if(totalDevolucionBd !== null || totalDevolucionBd !== undefined) {
                        let totalDevolucionBdNumber = parseFloat(totalDevolucionBd);

                        formattedtotalDevolucionBd = totalDevolucionBdNumber.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    } else {
                        formattedtotalDevolucionBd = 0;
                    }

                    totalDevolucionInput.value = formattedtotalDevolucionBd;
                    facturaDisplay.textContent = `Id Factura: ${idFactura.value}`;
                });
            });

            finalizarDevoBtn.addEventListener('click', function() {
                const idFacturaBd = parseInt(facturaDisplay.textContent.replace(/\D/g, ''));
                const motivo = document.getElementById('motivo-devolucion').value;

                const dataDevolucion = {
                    idFactura: idFacturaBd,
                    motivo: motivo,
                    total: totalDevolucionBd,
                    productos: dataProductosDevolucion,
                };

                fetch('/add-devolucion', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dataDevolucion)
                })  
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not OK');
                    }

                    return response.json(); // Convertir la respuesta a JSON
                })
                .then(data => {
                    if(data.success) {
                        alert('Devolucion realizada');
                        location.reload();
                    } else {
                        alert(`Error: ${data.message}`);
                        location.reload();
                        throw new Error(`Error en la Devolucion: ${data.message}`);
                    }
                })
                .catch(error => {
                    // Manejo de errores en caso de que ocurra una excepción
                    console.error('Error en la solicitud:', error);
                });
            });
        });
    } else {
        console.error("Formulario No encontrado");
    }
});
