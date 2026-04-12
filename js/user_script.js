const userBtn = document.querySelector('#user-btn');
const userBox = document.querySelector('.profile');

userBtn.addEventListener('click', function() {
    userBox.classList.toggle('active');
});
