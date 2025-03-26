let container = document.getElementById('container')

toggle = () => {
container.classList.toggle('sign-in')
container.classList.toggle('sign-up')
}

setTimeout(() => {
container.classList.add('sign-in')
}, 200)

// Add this JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function() {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle the eye icon
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
});