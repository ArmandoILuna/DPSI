// Validación del formulario de login
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    
    // Auto-hide error messages after 5 seconds
    const errorMessage = document.getElementById('error-message');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.display = 'none';
        }, 5000);
    }
    
    // Validación en tiempo real
    usernameInput.addEventListener('input', function() {
        if (this.value.trim().length < 3) {
            this.style.borderColor = '#e74c3c';
        } else {
            this.style.borderColor = '#27ae60';
        }
    });
    
    passwordInput.addEventListener('input', function() {
        if (this.value.length < 6) {
            this.style.borderColor = '#e74c3c';
        } else {
            this.style.borderColor = '#27ae60';
        }
    });
    
    // Validación al enviar
    loginForm.addEventListener('submit', function(e) {
        const username = usernameInput.value.trim();
        const password = passwordInput.value;
        
        if (username.length < 3) {
            e.preventDefault();
            showError('El usuario debe tener al menos 3 caracteres');
            usernameInput.focus();
            return false;
        }
        
        if (password.length < 6) {
            e.preventDefault();
            showError('La contraseña debe tener al menos 6 caracteres');
            passwordInput.focus();
            return false;
        }
    });
    
    function showError(message) {
        const existingError = document.querySelector('.alert-error');
        if (existingError) {
            existingError.textContent = message;
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-error';
            errorDiv.textContent = message;
            loginForm.insertBefore(errorDiv, loginForm.firstChild);
        }
    }
});
