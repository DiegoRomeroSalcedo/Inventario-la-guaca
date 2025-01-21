document.addEventListener('DOMContentLoaded', function() {

    let form = document.getElementById('form-list-data');

    if (form) {
        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            const BASE_URL = '/inventario/public';
            let formData = new FormData(this);


            let response = await fetch(`${BASE_URL}/facturas`, {
                method: 'POST',
                body: formData
            });

            if(!response.ok) {
                throw new Error('Network response was not OK');
            }

            let data = response.json();

            console.log(data);

            let resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '';


        })
    }
});