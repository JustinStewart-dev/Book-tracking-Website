function openReviewPopup(bookId) {
    var modal = document.getElementById('modal-' + bookId);
    modal.style.display = "block";
}

function closeReviewPopup(bookId) {
    var modal = document.getElementById('modal-' + bookId);
    modal.style.display = "none";
}

function setRating(bookId, rating) {
    var stars = document.querySelectorAll('#modal-' + bookId + ' .star');
    var ratingInput = document.getElementById('rating_' + bookId);
    stars.forEach(function(star, index) {
        if (index < rating) {
            star.classList.add('selected');
        } else {
            star.classList.remove('selected');
        }
    });
    ratingInput.value = rating;
}

function validateForm(bookId) {
    var ratingInput = document.getElementById('rating_' + bookId);
    var rating = ratingInput.value;

    if (!rating || rating === "") {
        alert("Please select a rating.");
        return false;
    }
    return true;
}

