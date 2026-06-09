// Form validation and interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Password strength indicator (on registration page)
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            updateStrengthIndicator(strength);
        });
    }
});

function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    return strength;
}

function updateStrengthIndicator(strength) {
    let indicator = document.getElementById('password-strength');
    if (!indicator) {
        const passwordField = document.getElementById('password');
        if (passwordField) {
            indicator = document.createElement('div');
            indicator.id = 'password-strength';
            indicator.className = 'mt-2';
            passwordField.parentNode.appendChild(indicator);
        }
    }
    
    if (indicator) {
        const strengths = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        const colors = ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#28a745'];
        
        if (strength > 0) {
            indicator.innerHTML = `<small>Password Strength: <span style="color: ${colors[strength-1]}">${strengths[strength-1]}</span></small>`;
        } else {
            indicator.innerHTML = '';
        }
    }
}