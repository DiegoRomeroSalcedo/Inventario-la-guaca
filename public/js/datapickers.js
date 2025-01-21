document.addEventListener('DOMContentLoaded', () => {

    $("#startDate, #endDate").datepicker({
        dateFormat: "yy-mm-dd"
    });

    $("#searchBtninventario").on('click', function() {
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();
        let errorMessage = $('#error-message');

        errorMessage.text('').hide();

        //Validamos fechas, para que fecha inial no sea mayor a fecha final
        if(startDate && endDate && new Date(startDate) > new Date(endDate)) {
            errorMessage.text('La fecha inicial no puede ser mayor a la fecha final');
            return; // Salimos sin la busueda
        }
    });
});