const userBtn = document.querySelector('#user-btn');
const userBox = document.querySelector('.profile');

userBtn.addEventListener('click', function() {
    userBox.classList.toggle('active');
});

const toggle = document.querySelector('#menu-btn');
toggle.addEventListener('click', function() {
    const navbar = document.querySelector('.navbar');
    navbar.classList.toggle('active');
})
