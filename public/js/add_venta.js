document.addEventListener('DOMContentLoaded', function() {
    let form = document.getElementById('search-add-venta');
    let carritoList = document.getElementById('carrito-list');
    let toggelDataCliente = document.getElementById('data-control');
    let extraFiledsContainer = document.querySelectorAll('#container-data');

    let totalInputValue = document.getElementById('total-input-value');
    let formValueRecibidoVuelto = document.getElementById('form-valor-recibido-devuelto'); //Esto es para el envio de datos al server
    let valorRecibido = document.getElementById('valor-recibido');
    let valorDevuelto = document.getElementById('valor-devuelto');

    let tipoPago = document.getElementById('tipo-pago');

    valorRecibido.addEventListener('blur', ()=> {

        let valor = parseFloat(valorRecibido.value.replace(/,/g, ''));

        if(!isNaN(valor)) {

            const formattedValorrecibidoInput = valor.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
    
            valorRecibido.value = formattedValorrecibidoInput;
        }
    });

    let carrito = [];
    const BASE_URL = '/inventario/public';
    const productStockMap = {};

    if (form) {
        form.addEventListener('submit', async function(event) {
            event.preventDefault(); // Evitamos el envío del formulario de manera normal
            let formData = new FormData(this);

            try {
                let response = await fetch('/search-add-venta', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Network response was not OK');
                }

                let data = await response.json();
                let resultsDiv = document.getElementById('results');

                resultsDiv.innerHTML = '';

                if (data && data.length > 0) {
                    data.forEach(producto => {

                        let cantidad = 1;
                        let cantidadStock;

                        if (productStockMap[producto.id_product] !== undefined) {
                            cantidadStock = productStockMap[producto.id_product];
                        } else {
                            cantidadStock = producto.cantidad ?? 0;
                        }

                        let preVentaStr = producto.pre_ventap.replace(/,/g, '');
                        let preVenta = parseFloat(preVentaStr);
                        let preventaFinStr = producto.pre_ventades.replace(/,/g, '');
                        let preVentaFin = parseFloat(preventaFinStr);

                        const formattedPreVenta = preVenta.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                        const formattedPreVentaFin = preVentaFin.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                        producto.pre_ventades = formattedPreVentaFin;

                        let productDiv = document.createElement('div');
                        productDiv.classList.add('product-item'); // Añadimos la clase
                        productDiv.innerHTML = `
                            <div class="container-data-venta-product"><span class="data-venta-product">Nombre:</span> ${producto.no_product}</div>
                            <div class="container-data-venta-product"><span class="data-venta-product">Marca:</span> ${producto.nombre_marca}</div>
                            <div class="container-data-venta-product"><span class="data-venta-product">Precio:</span> ${formattedPreVenta}</div>
                            <div class="container-data-venta-product"><span class="data-venta-product">Descuento:</span> ${producto.desc_produ}<span>%</span></div>
                            <div class="container-data-venta-product"><span class="data-venta-product">Precio Final:</span> ${formattedPreVentaFin}</div>
                            <div class="container-data-venta-product"><span class="data-venta-product">Detalle:</span> ${producto.detalle_product ?? 'Sin detalle'}</div>
                            <div class="container-data-venta-product"><span class="data-venta-product">Cantidad Stock:</span> <span class="stock">${cantidadStock}</span></div>
                            <div class="container-data-venta-product"><span class="data-venta-product">Cantidad:</span> <span class="cantidad">${cantidad}</span></div>
                            <button class="add-cantidad" data-stock="${cantidadStock}">+</button>
                            <button class="remove-cantidad">-</button>
                            <button class="add-to-cart" data-product='${JSON.stringify(producto)}'>Añadir al carrito</button>
                        `;
                        console.log
                        resultsDiv.appendChild(productDiv);
                    });

                    document.querySelectorAll('.add-cantidad').forEach(button => {
                        button.addEventListener('click', function() {
                            let cantidadElem = this.previousElementSibling.querySelector('.cantidad');
                            let cantidad = parseInt(cantidadElem.textContent, 10);
                            let cantidadStock = parseInt(this.getAttribute('data-stock'), 10);

                            cantidad += 1;

                            if (cantidad > cantidadStock) {
                                alert("Se supero la cantidad en Stock");
                                cantidad -= 1;
                            }
                            cantidadElem.textContent = cantidad;
                        });
                    });

                    document.querySelectorAll('.remove-cantidad').forEach(button => {
                        button.addEventListener('click', function() {
                            let cantidadElem = this.previousElementSibling.previousElementSibling.querySelector('.cantidad');
                            let cantidad = parseInt(cantidadElem.textContent, 10);

                            cantidad -= 1;

                            if (cantidad < 1) {
                                alert('Limite minimo 1');
                                cantidad += 1;
                            }

                            cantidadElem.textContent = cantidad;
                        });
                    });

                    document.querySelectorAll('.add-to-cart').forEach(button => {
                        button.addEventListener('click', function() {
                            let product = JSON.parse(this.getAttribute('data-product'));
                            let cantidadElem = this.previousElementSibling.previousElementSibling.previousElementSibling.querySelector('.cantidad');
                            let cantidad = parseInt(cantidadElem.textContent, 10);

                            if (productStockMap[product.id_product] == 0) {
                                alert("El producto está agotado y no se puede añadir al carrito");
                                return;
                            }

                            if (cantidad > product.cantidad) {
                                alert("El producto está agotado y no se puede añadir al carrito");
                                return; //No añadimos el producto al carrito
                            }

                            // Actualizar la cantidad del producto antes de añadirlo al carrito
                            product.cantidad = cantidad;
                            product.totalIndCarrito = parseFloat(product.pre_ventades.replace(/,/g, '') * product.cantidad);

                            let existingProduct = carrito.find(p => p.id_product === product.id_product);

                            if (existingProduct) {
                                existingProduct.cantidad += cantidad;
                            } else {
                                carrito.push(product);
                            }

                            updateStock(product.id_product, cantidad);
                            actualizarCarrito();

                            let productDiv = this.closest('.product-item');
                            if (productDiv) {
                                productDiv.remove();
                            }
                        });
                    });
                } else {
                    resultsDiv.innerHTML = "No se encontró el producto";
                }
            } catch (error) {
                console.error("Error: ", error);
            }
        });

        let cedulaClienteInput = document.getElementById('id-client');

            cedulaClienteInput.addEventListener('blur', async function() {
                const cedula = cedulaClienteInput.value;

                if(cedula) {
                    try {
                        let response = await fetch(`/check-client`, {
                            method: 'POST', 
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(cedula)
                        });

                        if(!response.ok) {
                            throw new Error('Network response was not');
                        }

                        let data = await response.json();

                        console.log(data.cliente);

                        let cedulaClienteInput = document.getElementById('id-client');
                        let nombreClienteInput = document.getElementById('nombre');
                        let numCelularInput = document.getElementById('nro-celular');
                        let emailClienteInput = document.getElementById('email-cliente');
                        let direccionClienteInput = document.getElementById('direccion-cliente');

                        cedulaClienteInput.value = data.cliente[0].identificacion;
                        nombreClienteInput.value = data.cliente[0].Nombre;
                        numCelularInput.value = data.cliente[0].telefono;
                        emailClienteInput.value = data.cliente[0].email;
                        direccionClienteInput.value = data.cliente[0].direccion;

                    } catch (error) {
                        console.error("Error: ", error);
                    }
                }
            });

        document.getElementById('finalizar-venta').addEventListener('click', async function() {
            if (carrito.length === 0) {
                alert('El carrito está vacío.');
                return;
            }

            const total = carrito.reduce((sum, product) => sum + product.totalIndCarrito, 0);

            //Valores que se enviaran a la BD

            let valorRecibiBd = parseFloat(valorRecibido.value.replace(/,/g, ''));
            let valorDevueltoBd = parseFloat(valorDevuelto.value.replace(/,/g, ''));
            console.log(valorDevueltoBd);

            let totalesRecibidoDevuelto = {
                valorRecibido: valorRecibiBd,
                valorDevuelto: valorDevueltoBd,
            };

            let clienteData = {};

            let tipoPagoData = {
                tipoDePago: tipoPago.value
            };


            clienteData = {
                cedulaCliente: document.getElementById('id-client').value,
                nomCliente: document.getElementById('nombre').value,
                nroCelular: document.getElementById('nro-celular').value,
                emailCliente: document.getElementById('email-cliente').value,
                dirCliente: document.getElementById('direccion-cliente').value
            };

            
            try {
                let response = await fetch(`/finalizar-venta`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ carrito, total, clienteData, totalesRecibidoDevuelto, tipoPagoData})
                });

                if (!response.ok) {
                    throw new Error('Network response was not OK');
                }

                let data = await response.json();

                if (data.succes) {
                    const invoiceId = data.invoiceId;

                    window.location.href = `/get-factura?id=${invoiceId}`;
                } else {
                    alert("Error al insertar Venta intente nuevamente");
                }

            } catch (error) {
                console.error("Error: ", error);
            }
        });

        function actualizarCarrito() {
            carritoList.innerHTML = '';
            let totalValue = 0;


            carrito.forEach(product => {
                let listItem = document.createElement('li');

                let totalIndividual = parseFloat(product.totalIndCarrito);

                let formatTotalIndividual = totalIndividual.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                
                listItem.innerHTML = `
                    <span class="data-venta-product">Producto:</span> ${product.no_product} <span class="data-venta-product">- Marca:</span> ${product.nombre_marca} <span class="data-venta-product">- Precio:</span> ${product.pre_ventades} <span class="data-venta-product">- Cantidad:</span> ${product.cantidad} <span class="data-venta-product">- Total:</span> ${formatTotalIndividual}
                    <button class="remove-from-cart" data-product='${JSON.stringify(product)}'>Quitar</button>
                    <span class="line__separator"><hr></span>
                `;

                totalValue += totalIndividual;

                carritoList.appendChild(listItem);
            });

            totalValue = parseFloat(totalValue);

            const formattedTotalValue = totalValue.toLocaleString('en-US', {
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2
            });

            totalInputValue.value = formattedTotalValue;

            // Hermos el calculo para dar el valor devuelto

            valorRecibido.addEventListener('blur', () => {

                const valorRecibidoNumber = parseFloat(valorRecibido.value.replace(/,/g, ''));

                const totalDevueltoNumber = valorRecibidoNumber - totalValue;

                // Vlor devuelto formateado para el input.
                const formattedTotalDevueltoInput = totalDevueltoNumber.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                valorDevuelto.value = formattedTotalDevueltoInput;
            });
            
            document.querySelectorAll('.remove-from-cart').forEach(button => {
                button.addEventListener('click', function() {
                    let product = JSON.parse(this.getAttribute('data-product'));
                    carrito = carrito.filter(p => p.id_product !== product.id_product);

                    restoreStock(product.id_product, product.cantidad);
                    actualizarCarrito();
                });
            });
        }

        function updateStock(productId, quantity) {
            let productDivs = document.querySelectorAll(`.product-item button.add-to-cart[data-product*="${productId}"]`);
            productDivs.forEach(button => {
                let productDiv = button.closest('.product-item');
                if (productDiv) {
                    let stockElem = productDiv.querySelector('.stock');
                    let currentStock = parseInt(stockElem.textContent, 10);
                    let newStock = currentStock - quantity;
                    stockElem.textContent = newStock;
        
                    let addCantidadButton = productDiv.querySelector('.add-cantidad');
                    addCantidadButton.setAttribute('data-stock', newStock);

                    //Actualiar el stcok en el mapa global
                    productStockMap[productId] = newStock

                    //Desabilitamos el botn de añadir al carrito
                    if(newStock <= 0 ) {
                        button.disabled = true;
                        addCantidadButton.disabled = true;
                    }
                }
            });
        }
        
        function restoreStock(productId, quantity) {
            // Seleccionamos todos los elementos que podrían contener el producto
            let productDivs = document.querySelectorAll(`.product-item button.add-to-cart[data-product*="${productId}"]`);
        
            productDivs.forEach(button => {
                let productDiv = button.closest('.product-item');
                if (productDiv) {
                    let stockElem = productDiv.querySelector('.stock');
                    let currentStock = parseInt(stockElem.textContent, 10);
                    stockElem.textContent = currentStock + quantity;
        
                    let addCantidadButton = productDiv.querySelector('.add-cantidad');
                    addCantidadButton.setAttribute('data-stock', currentStock + quantity);
                }
            });

            // Eliminar el producto del mapa global productStockMap
            productStockMap[productId] = productStockMap[productId] + quantity;
        }

        toggelDataCliente.addEventListener('change', () => {
            if (toggelDataCliente.checked) {

                extraFiledsContainer.forEach(campos => {
                    campos.classList.remove('hidden');
                    campos.querySelector('input').required = true;
                });

            } else {
                extraFiledsContainer.forEach(campos => {
                    campos.classList.add('hidden');
                    campos.querySelector('input').required = false;
                });
            }
        });
    } else {
        console.error("Formulario no encontrado");
    }
});
