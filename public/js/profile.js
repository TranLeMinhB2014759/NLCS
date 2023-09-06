const profile = document.querySelector("#tab1");
const profile1 = document.querySelector(".tab1");
const borrow = document.querySelector("#tab2");
const borrow1 = document.querySelector(".tab2");
const giveback = document.querySelector("#tab3");
const giveback1 = document.querySelector(".tab3");
const expired = document.querySelector("#tab4");
const expired1 = document.querySelector(".tab4");

function active_profile() {
    profile.classList.add("active");
    profile1.classList.add("animate__fadeIn");
    profile1.style.display = 'block';

    borrow.classList.remove("active");
    borrow1.style.display = 'none';

    giveback.classList.remove("active");
    giveback1.style.display = 'none';

    expired.classList.remove("active");
    expired1.style.display = 'none';
}

function active_borrow() {
    profile.classList.remove("active");
    profile1.style.display = 'none';

    borrow.classList.add("active");
    borrow1.classList.add("animate__fadeIn");
    borrow1.style.display = 'block';

    giveback.classList.remove("active");
    giveback1.style.display = 'none';

    expired.classList.remove("active");
    expired1.style.display = 'none';
}

function active_giveback() {
    profile.classList.remove("active");
    profile1.style.display = 'none';

    borrow.classList.remove("active");
    borrow1.style.display = 'none';

    giveback.classList.add("active");
    giveback1.classList.add("animate__fadeIn");
    giveback1.style.display = 'block';

    expired.classList.remove("active");
    expired1.style.display = 'none';
}

function active_expired() {
    profile.classList.remove("active");
    profile1.style.display = 'none';

    borrow.classList.remove("active");
    borrow1.style.display = 'none';

    giveback.classList.remove("active");
    giveback1.style.display = 'none';

    expired.classList.add("active");
    expired1.classList.add("animate__fadeIn");
    expired1.style.display = 'block';
}
