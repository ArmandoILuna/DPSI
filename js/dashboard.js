// Funcionalidad del dashboard
document.addEventListener('DOMContentLoaded', function() {
    const asistenciaForm = document.getElementById('asistenciaForm');
    const alertContainer = document.getElementById('alert-container');
    
    // Manejar envío del formulario de asistencia
    asistenciaForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(asistenciaForm);
        
        try {
            const response = await fetch('api_asistencia.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert('success', data.message);
                asistenciaForm.reset();
                
                // Recargar la página después de 2 segundos para actualizar las estadísticas
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showAlert('error', data.message);
            }
        } catch (error) {
            showAlert('error', 'Error al registrar asistencia. Por favor, intente nuevamente.');
            console.error('Error:', error);
        }
    });
    
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
        const alertHTML = `<div class="alert ${alertClass}">${message}</div>`;
        alertContainer.innerHTML = alertHTML;
        
        // Auto-hide después de 5 segundos
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }
    
    // Actualizar hora en tiempo real (opcional)
    updateTime();
    setInterval(updateTime, 1000);
    
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-MX', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
        
        // Si existe un elemento para mostrar la hora, actualizarlo
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }
    
    // Confirmar antes de cerrar sesión
    const logoutLinks = document.querySelectorAll('a[href="logout.php"]');
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro que desea cerrar sesión?')) {
                e.preventDefault();
            }
        });
    });
    
    // Resaltar fila de tabla al pasar el mouse
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#ecf0f1';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});
