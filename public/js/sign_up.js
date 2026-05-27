document.addEventListener('DOMContentLoaded', function () {
    const signupForm = document.getElementById('signupForm');
    if (!signupForm) return;
    const passwordInput = document.getElementById('registration_form_plainPassword_first');
    const confirmInput  = document.getElementById('registration_form_plainPassword_second');
    (function injectEyeStyles() {
        if (document.getElementById('password-eye-style')) return;
        const style = document.createElement('style');
        style.id = 'password-eye-style';
        style.textContent = `
            .input-with-icon i.password-toggle {
                left: auto;
                right: 15px;
                cursor: pointer;
                color: #777;
            }
            .input-with-icon input.has-eye {
                padding-right: 45px;
            }
        `;
        document.head.appendChild(style);
    })();

    function makeEyeToggle(input) {
        if (!input) return;
        const wrapper = input.closest('.input-with-icon');
        if (!wrapper) return;

        input.classList.add('has-eye');

        const eye = document.createElement('i');
        eye.className = 'fas fa-eye password-toggle';

        eye.addEventListener('click', function () {
            if (input.type === 'password') {
                input.type = 'text';
                eye.className = 'fas fa-eye-slash password-toggle';
            } else {
                input.type = 'password';
                eye.className = 'fas fa-eye password-toggle';
            }
        });

        wrapper.appendChild(eye);
    }
    makeEyeToggle(passwordInput);
    makeEyeToggle(confirmInput);
    signupForm.addEventListener('submit', function () {
        const submitBtn = signupForm.querySelector('.submit-btn');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création du compte...';
            submitBtn.disabled = true;
        }
    });
});
