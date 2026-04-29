const userBtn = document.querySelector('#user-btn');
const userBox = document.querySelector('.profile');

userBtn.addEventListener('click', function() {
    userBox.classList.toggle('active');
});
const toggle = document.querySelector('.toggle-btn');
toggle.addEventListener('click', function() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('active');
})