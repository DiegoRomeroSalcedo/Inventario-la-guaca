
    // Inicializamos las variables
    let cantidadInput = document.getElementById('cantidad');
    let rangoCantidadInput = document.getElementById('rango_cantidad');


    function formatValue(value) {

                let formattedValue = value.trim();

                // Convertir a número y manejar la notación científica
                let numberValue = Number(formattedValue);
                
                if (isNaN(numberValue)) {
                    numberValue = 0;
                }
                
                if (numberValue < 0) {
                    numberValue = 0;
                }
                
                return numberValue;
    } 

    function updateValues() {
        let inputValue = cantidadInput.value;
        let formattedValue = formatValue(inputValue);

        //actualizar el campo númerico y el slider
        cantidadInput.value = formattedValue;
        rangoCantidadInput.value = formattedValue;
    }

    cantidadInput.addEventListener('input', updateValues);
    rangoCantidadInput.addEventListener('input', () => {
        cantidadInput.value = rangoCantidadInput.value;
    });

    //Inicializamos los valores al cargar la página
    updateValues();