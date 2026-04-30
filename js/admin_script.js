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

function toggleDropdown(id) {
    document.getElementById(id).style.display =
        document.getElementById(id).style.display === "block" ? "none" : "block";
}

//закрити спадне меню
document.addEventListener("click", function (event) {
    document.querySelectorAll(".dropdown").forEach(function (dropdown) {
        if (!dropdown.contains(event.target)) {
            dropdown.querySelector(".dropdown-content").style.display = "none";
        }
    })
})