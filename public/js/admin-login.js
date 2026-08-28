document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (!toggle || !password) return;

    toggle.addEventListener('click', function () {
        const show = password.type === 'password';

        password.type = show ? 'text' : 'password';

        toggle.innerHTML = show
            ? '<i class="fa-solid fa-eye"></i>'
            : '<i class="fa-solid fa-eye-slash"></i>';
    });
});
