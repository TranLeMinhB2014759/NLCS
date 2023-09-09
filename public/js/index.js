const notification = document.querySelector("#notification");
const loaded = document.querySelector("body");

notification.scrollIntoView({
    behavior: "smooth",
    block: "start",
});


loaded.classList.add("loaded");