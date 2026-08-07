const header = document.querySelector(".sticky-header");

// SCROLL (header + animation sections)
window.addEventListener("scroll", () => {
    if (window.scrollY > 70) {
        header.classList.add("sticky");
    } else {
        header.classList.remove("sticky");
    }

    const sections = document.querySelectorAll("section");
    const screenPosition = window.innerHeight / 1.5;

    sections.forEach((section) => {
        const sectionPosition = section.getBoundingClientRect().top;

        if (sectionPosition < screenPosition) {
            section.classList.add("fade-in");
        } else {
            section.classList.remove("fade-in");
        }
    });
});

// =======================
// ✅ SLIDER QUI MARCHE
// =======================

const slides = document.querySelectorAll(".testimonials");
const prev = document.querySelector(".prev-slide");
const next = document.querySelector(".next-slide");

let currentIndex = 0;

// afficher seulement le premier au début
slides.forEach((slide, i) => {
    slide.classList.remove("active");
});
slides[0].classList.add("active");

function showSlide(index){
    slides.forEach((slide, i) => {
        slide.classList.remove("active");
        if(i === index){
            slide.classList.add("active");
        }
    });
}

next.addEventListener("click", () => {
    currentIndex = (currentIndex + 1) % slides.length;
    showSlide(currentIndex);
});

prev.addEventListener("click", () => {
    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
    showSlide(currentIndex);
});

document.addEventListener("DOMContentLoaded", () => {
    // On sélectionne le bouton avec l'ID 'humberger' (avec un U)
    const boutonHumberger = document.getElementById("humberger");
    // On sélectionne la liste du menu
    const menuNav = document.querySelector("nav ul");

    if (boutonHumberger && menuNav) {
        boutonHumberger.addEventListener("click", () => {
            // Ajoute ou enlève la classe 'open' au menu
            menuNav.classList.toggle("open");
            console.log("Le menu a été cliqué !");
        });
    }
});