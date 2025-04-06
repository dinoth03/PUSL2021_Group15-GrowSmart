let searchForm= document.querySelector('.search-form');

document.querySelector('#search-btn').onclick= () =>{
    searchForm.classList.toggle('active');
    loginForm.classList.remove('active');
    navbar.classList.remove('active');
}


let loginForm= document.querySelector('.login-form');

document.querySelector('#login-btn').onclick= () =>{
    loginForm.classList.toggle('active');
    searchForm.classList.remove('active');
    navbar.classList.remove('active');
}


let navbar = document.querySelector('.navbar');

document.querySelector('#menu-btn').onclick= () =>{
    navbar.classList.toggle('active');
    searchForm.classList.remove('active');
    loginForm.classList.remove('active');
    
}

window.onscroll = () =>{
    searchForm.classList.remove('active');
    loginForm.classList.remove('active');
    navbar.classList.remove('active');
}

document.addEventListener("DOMContentLoaded",function(){
    const dropdowns = this.documentElement.querySelectorAll(".dropdown > a");

    dropdowns.forEach((dropdown) => {
        dropdown.addEventListener("click", function(e) {
            e.preventDefault();
            const dropdownContent = this.nextElementSibling;

            document.querySelectorAll(".dropdown-content").forEach((content) => {
                if (content !== dropdownContent) {
                    content.style.display = "none";
                }
            });

            // toggle current dropdown
            dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";

        });
    });
})

document.addEventListener("DOMContentLoaded", function () {
    let dropdowns = document.querySelectorAll(".dropdown");

    dropdowns.forEach((dropdown) => {
        let toggle = dropdown.querySelector(".dropdown-toggle");
        let menu = dropdown.querySelector(".dropdown-menu");

        toggle.addEventListener("click", function (event) {
            event.preventDefault(); // Prevents link navigation
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        });

        // Hide dropdown when clicking outside
        document.addEventListener("click", function (event) {
            if (!dropdown.contains(event.target)) {
                menu.style.display = "none";
            }
        });
    });
});


