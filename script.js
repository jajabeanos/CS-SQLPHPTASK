const allStars = document.querySelectorAll('.star');
const ratingInput = document.getElementById('starRatingValue');
allStars.forEach((star) => {
    star.addEventListener('click', (event) => {
        event.preventDefault();
        const starId = parseInt(star.id);
        ratingInput.value = starId;
        console.log(`Selected Rating: ${starId}`);

        allStars.forEach((s) => {
            if (parseInt(s.id) <= starId) {
                s.innerHTML = '&#9733;';
            } else {
                s.innerHTML = '&#9734;';
            }
        });
    });
});
