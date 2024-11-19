const menuBtn = document.querySelector(' .menu-btn');
const menuItems = document.querySelector(' .menu-items');
const expandBtn = document.querySelectorAll('.expand-btn');

// hamburger toggle
menuBtn.addEventListener("click", () =>{
    menuBtn.classList.toggle("open");
    menuItems.classList.toggle("open");
});

// mobile menu expand
expandBtn.forEach((btn) =>{
    btn.addEventListener("click", () => {
        btn.classList.toggle("open");
    });
});



// Get the button:
let mybutton = document.getElementById("myBtn");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    mybutton.style.display = "block";
    } else {
    mybutton.style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}


// Disappearing nav_-bar
/* When the user scrolls down, hide the navbar. When the user scrolls up, show the navbar */
var prevScrollpos = window.scrollY;
window.onscroll = function() {
  var currentScrollPos = window.scrollY;
  if (prevScrollpos > currentScrollPos) {
    document.getElementById("disappearing_nav").style.top = "0";
  } else {
    document.getElementById("disappearing_nav").style.top = "-120px";
  }
  prevScrollpos = currentScrollPos;
}
