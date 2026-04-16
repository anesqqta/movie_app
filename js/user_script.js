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

let searchFrom = document.querySelector('.header .flex .search_form');
document.querySelector('#search_btn').onclick = () =>{
    searchFrom.classList.toggle('active');
    profile.classList.remove('active')
}
//головна сторілка slider
"use strict"
const leftArrow = document.querySelector('.left-arrow .bxs-left-arrow'),
      rightArrow = document.querySelector('.right-arrow .bxs-right-arrow'),
      slider = document.querySelector('.slider');
//стрілка вправо
    function scrollRight(){
        if (slider.scrollWidth - slider.clientWidth === slider.scrollLeft){
            slider.scrollTo({
                left: 0,
                behavior: "smooth"
            });
        }else{
            slider.scrollBy({
                left:window.innerWidth,
                behavior: "smooth"
            })
        }
    }
//стрілка вліво
    function scrollLeft(){
        slider.scrollBy({
                left: -window.innerWidth,
                behavior: "smooth"
        })
    }

let timerId = setInterval(scrollRight, 7000);
//скинути таймер для прокручування праворуч
    function resetTimer(){
        clearInterval(timerId);
        timerId = setInterval(scrollRight, 7000);
    }
//подія прокручування
slider.addEventListener("click", function(ev){
    if (ev.target === leftArrow){
        scrollLeft();
        resetTimer();
    }
})

slider.addEventListener("click", function(ev){
    if (ev.target === rightArrow){
        scrollRight();
        resetTimer();
    }
})

//лічильник
let count = document.querySelectorAll('.count');
let arr = Array.from(count);

arr.map(function(item){
    let startnumber = 0;

    function counterUp(){
        startnumber++
        item.innerHTML = startnumber

        if (startnumber == item.dataset.number) {
            clearInterval(stop)
        }
    }
    let stop = setInterval(function(){
        counterUp();
    }, 50)
})