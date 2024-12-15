const toggleFormLink = document.getElementById('toggle-form'); // Pastikan ID benar
const formTitle = document.getElementById('form-title'); // Pastikan ID benar
const registerForm = document.getElementById('register-form');
const loginForm = document.getElementById('login-form');

toggleFormLink.addEventListener('click', (e) => {
    e.preventDefault();
    if (registerForm.style.display === 'none') {
        // Tampilkan form registrasi
        registerForm.style.display = 'block';
        loginForm.style.display = 'none';
        formTitle.textContent = 'Daftar';
        toggleFormLink.textContent = 'Masuk';
    } else {
        // Tampilkan form login
        registerForm.style.display = 'none';
        loginForm.style.display = 'block';
        formTitle.textContent = 'Masuk';
        toggleFormLink.textContent = 'Daftar';
    }
});