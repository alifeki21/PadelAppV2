document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    const patterns = {
        email: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
    };

    const fields = [
        {
            id: 'email',
            groupId: 'emailGroup',
            validate: (value) => {
                if (!value.trim()) return 'L\'adresse e-mail est requise';
                if (!patterns.email.test(value)) return 'Veuillez entrer une adresse e-mail valide';
                return '';
            }
        },
        {
            id: 'password',
            groupId: 'passwordGroup',
            validate: (value) => {
                if (!value) return 'Le mot de passe est requis';
                return '';
            }
        }
    ];

    function validateField(field) {
        const input = document.getElementById(field.id);
        const group = document.getElementById(field.groupId);
        if (!input || !group) return true;

        const errorMessage = field.validate(input.value);
        const errorElement = group.querySelector('.error-message');

        if (errorMessage) {
            group.classList.remove('success');
            group.classList.add('error');
            if (errorElement) errorElement.textContent = errorMessage;
            return false;
        }
        group.classList.remove('error');
        group.classList.add('success');
        return true;
    }

    fields.forEach(field => {
        const input = document.getElementById(field.id);
        if (input) {
            input.addEventListener('input', () => validateField(field));
            input.addEventListener('blur',  () => validateField(field));
        }
    });
    (function addPasswordToggle() {
        const input = document.getElementById('password');
        const group = document.getElementById('passwordGroup');
        if (!input || !group) return;

        const eyeIcon = document.createElement('i');
        eyeIcon.className = 'fas fa-eye password-toggle';
        eyeIcon.style.cursor = 'pointer';
        eyeIcon.addEventListener('click', function () {
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.className = 'fas fa-eye-slash password-toggle';
            } else {
                input.type = 'password';
                eyeIcon.className = 'fas fa-eye password-toggle';
            }
        });
        const wrapper = group.querySelector('.input-with-icon');
        if (wrapper) wrapper.appendChild(eyeIcon);
    })();
    loginForm.addEventListener('submit', function (e) {
        let isValid = true;
        fields.forEach(field => { if (!validateField(field)) isValid = false; });

        if (!isValid) {
            e.preventDefault();
            const firstError = document.querySelector('.form-group.error');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        const submitBtn = loginForm.querySelector('.submit-btn');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connexion...';
            submitBtn.disabled = true;
        }
    });
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    `;
    document.head.appendChild(style);
});
