document.addEventListener('DOMContentLoaded', function() {
    async function fetchSalesData() {
        const response = await fetch('/dasboard-ventas');

        if (!response.ok) {
            throw new Error('Network response was not OK');
        }

        const data = await response.json();
        return data;
    }

    async function renderChart() {
        const salesData = await fetchSalesData();

        //Formateamos los datos para el uso de la librearia
        let labels = [];
        let data = [];
        let currentDate = new Date();
        let currentYear = currentDate.getFullYear();

        const monthNames = [
            "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
        ];

        for (let i = 1; i <= 12; i++) {
            let mes = `${currentYear}-${String(i).padStart(2, '0')}`;
            labels.push(monthNames[i - 1]);
            let mesData = salesData.find(entry => entry.mes == mes);
            data.push(mesData ? mesData.total : 0);
        }

        let ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line', //Tipo de grafico, otros son: bar,line, pie, etc.
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return labels[index];
                            }
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    renderChart();
});