const btn = document.querySelector('#read-more-btn');
const container = document.querySelector('#read-more');
container.style.transition = "0.5s";
btn.addEventListener('click', function (e) {
    e.preventDefault();
    container.style.height = "max-content";
});