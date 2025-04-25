/*
document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".filter-btn");
    const productCards = document.querySelectorAll(".product-card");
    const searchForm = document.getElementById("searchForm");
    const searchInput = document.getElementById("searchInput");

    function filterProducts(category) {
        const productSections = document.querySelectorAll(".row[id^='productList']");
        const headers = document.querySelectorAll(".section-header");

        productSections.forEach(section => {
            const sectionCategory = section.id.replace("productList", "").toLowerCase();
            if (category === "all" || sectionCategory === category) {
                section.style.display = "flex"; // Show matching section
            } else {
                section.style.display = "none"; // Hide others
            }
        });

        headers.forEach(header => {
            const headerCategory = header.getAttribute("data-category");
            if (category === "all" || headerCategory === category) {
                header.style.display = "block"; // Show matching header
            } else {
                header.style.display = "none"; // Hide others
            }
        });

        productCards.forEach(card => {
            const cardCategory = card.getAttribute("data-category");
            card.style.display =
                category === "all" || cardCategory === category ? "block" : "none";
        });
    }

    function searchProducts(keyword) {
        const lowerKeyword = keyword.toLowerCase();
        let visibleCategory = null;

        productCards.forEach(card => {
            const productName = card.getAttribute("data-name").toLowerCase();
            const cardCategory = card.getAttribute("data-category");
            const matches = productName.includes(lowerKeyword);

            card.style.display = matches ? "block" : "none";

            // Determine which category to show if a match is found
            if (matches && !visibleCategory) {
                visibleCategory = cardCategory;
            }
        });

        // Update the visibility of sections and headers
        document.querySelectorAll(".row[id^='productList']").forEach(section => {
            const sectionCategory = section.id.replace("productList", "").toLowerCase();
            section.style.display =
                visibleCategory && sectionCategory === visibleCategory ? "flex" : "none";
        });

        document.querySelectorAll(".section-header").forEach(header => {
            const headerCategory = header.getAttribute("data-category");
            header.style.display =
                visibleCategory && headerCategory === visibleCategory ? "block" : "none";
        });
    }

    // Attach event listener to the search form
    searchForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const keyword = searchInput.value.trim();
        searchProducts(keyword);
    });

    // Add event listeners to filter buttons
   // Add event listeners to filter buttons with selected highlight
filterButtons.forEach(button => {
button.addEventListener("click", () => {
const category = button.getAttribute("data-category");
filterProducts(category);

// Remove green highlight from all buttons
filterButtons.forEach(btn => btn.classList.remove("selected-category"));

// Add green highlight to the clicked one
button.classList.add("selected-category");
});
});


    // Show all products by default
    filterProducts("all");
});


//cart

document.addEventListener("click", function (event) {
    if (event.target.classList.contains("add-to-cart")) {
        const button = event.target;

        // Try to extract data directly from button
        let productName = button.getAttribute("data-name");
        let productPrice = button.getAttribute("data-price");
        let productImage = button.getAttribute("data-image");

        // If not available from data attributes, fallback to closest card
        if (!productName || !productPrice || !productImage) {
            const card = button.closest(".product-card");
            productName = card.querySelector(".card-title").textContent.trim();
            productPrice = card.querySelector(".card-text").textContent.replace("Price:", "").replace("Rs.", "").trim();
            productImage = card.querySelector("img").src;
        }

        // Create a cart item object
        const cartItem = {
            name: productName,
            price: productPrice,
            image: productImage
        };

        // Get existing cart or empty array
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        cart.push(cartItem);

        // Save back to localStorage
        localStorage.setItem("cart", JSON.stringify(cart));

        // Feedback to user
        alert(`${productName} has been added to your cart!`);
    }
});

*/


/*
document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".filter-btn");
    const productCards = document.querySelectorAll(".product-card");
    const searchForm = document.getElementById("searchForm");
    const searchInput = document.getElementById("searchInput");
    const addProductBtn = document.getElementById("addProductBtn");

    function filterProducts(category) {
        const productSections = document.querySelectorAll(".row[id^='productList']");
        const headers = document.querySelectorAll(".section-header");

        productSections.forEach(section => {
            const sectionCategory = section.id.replace("productList", "").toLowerCase();
            if (category === "all" || sectionCategory === category) {
                section.style.display = "flex"; // Show matching section
            } else {
                section.style.display = "none"; // Hide others
            }
        });

        headers.forEach(header => {
            const headerCategory = header.getAttribute("data-category");
            if (category === "all" || headerCategory === category) {
                header.style.display = "block"; // Show matching header
            } else {
                header.style.display = "none"; // Hide others
            }
        });

        productCards.forEach(card => {
            const cardCategory = card.getAttribute("data-category");
            card.style.display =
                category === "all" || cardCategory === category ? "block" : "none";
        });
    }

    function searchProducts(keyword) {
        const lowerKeyword = keyword.toLowerCase();
        let visibleCategory = null;

        productCards.forEach(card => {
            const productName = card.getAttribute("data-name").toLowerCase();
            const cardCategory = card.getAttribute("data-category");
            const matches = productName.includes(lowerKeyword);

            card.style.display = matches ? "block" : "none";

            // Determine which category to show if a match is found
            if (matches && !visibleCategory) {
                visibleCategory = cardCategory;
            }
        });

        // Update the visibility of sections and headers
        document.querySelectorAll(".row[id^='productList']").forEach(section => {
            const sectionCategory = section.id.replace("productList", "").toLowerCase();
            section.style.display =
                visibleCategory && sectionCategory === visibleCategory ? "flex" : "none";
        });

        document.querySelectorAll(".section-header").forEach(header => {
            const headerCategory = header.getAttribute("data-category");
            header.style.display =
                visibleCategory && headerCategory === visibleCategory ? "block" : "none";
        });
    }

    // Attach event listener to the search form
    searchForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const keyword = searchInput.value.trim();
        searchProducts(keyword);
    });

    // Add event listeners to filter buttons with selected highlight
    filterButtons.forEach(button => {
        button.addEventListener("click", () => {
            const category = button.getAttribute("data-category");
            filterProducts(category);

            // Remove green highlight from all buttons
            filterButtons.forEach(btn => btn.classList.remove("selected-category"));

            // Add green highlight to the clicked one
            button.classList.add("selected-category");
        });
    });

    // Show all products by default
    filterProducts("all");

    // 🔐 Add Products login check
    /*
    if (addProductBtn) {
        addProductBtn.addEventListener("click", function (e) {
            const isLoggedIn = addProductBtn.getAttribute("data-loggedin") === "true";
            if (!isLoggedIn) {
                e.preventDefault();
                alert("Please log in to add products.");
            }
        });
    }
    //

    if (addProductBtn) {
        addProductBtn.addEventListener("click", function (e) {
            // Get the login status from the data-loggedin attribute
            const isLoggedIn = addProductBtn.getAttribute("data-loggedin") === "true";

            if (!isLoggedIn) {
                e.preventDefault(); // Prevent navigation
                alert("Please log in to add products.");
                // Optionally redirect to the login page:
                // window.location.href = "login.php"; // Uncomment if you want redirection
            }
        });
    }
});

// 🛒 Cart functionality
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("add-to-cart")) {
        const button = event.target;

        // Try to extract data directly from button
        let productName = button.getAttribute("data-name");
        let productPrice = button.getAttribute("data-price");
        let productImage = button.getAttribute("data-image");

        // If not available from data attributes, fallback to closest card
        if (!productName || !productPrice || !productImage) {
            const card = button.closest(".product-card");
            productName = card.querySelector(".card-title").textContent.trim();
            productPrice = card.querySelector(".card-text").textContent.replace("Price:", "").replace("Rs.", "").trim();
            productImage = card.querySelector("img").src;
        }

        // Create a cart item object
        const cartItem = {
            name: productName,
            price: productPrice,
            image: productImage
        };

        // Get existing cart or empty array
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        cart.push(cartItem);

        // Save back to localStorage
        localStorage.setItem("cart", JSON.stringify(cart));

        // Feedback to user
        alert(`${productName} has been added to your cart!`);
    }
});
*/



document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".filter-btn");
    const productCards = document.querySelectorAll(".product-card");
    const searchForm = document.getElementById("searchForm");
    const searchInput = document.getElementById("searchInput");
    const addProductBtn = document.getElementById("addProductBtn");

    function filterProducts(category) {
        const productSections = document.querySelectorAll(".row[id^='productList']");
        const headers = document.querySelectorAll(".section-header");

        productSections.forEach(section => {
            const sectionCategory = section.id.replace("productList", "").toLowerCase();
            section.style.display = (category === "all" || sectionCategory === category) ? "flex" : "none";
        });

        headers.forEach(header => {
            const headerCategory = header.getAttribute("data-category");
            header.style.display = (category === "all" || headerCategory === category) ? "block" : "none";
        });

        productCards.forEach(card => {
            const cardCategory = card.getAttribute("data-category");
            card.style.display = (category === "all" || cardCategory === category) ? "block" : "none";
        });
    }

    function searchProducts(keyword) {
        const lowerKeyword = keyword.toLowerCase();
        let visibleCategory = null;

        productCards.forEach(card => {
            const productName = card.getAttribute("data-name").toLowerCase();
            const cardCategory = card.getAttribute("data-category");
            const matches = productName.includes(lowerKeyword);

            card.style.display = matches ? "block" : "none";

            if (matches && !visibleCategory) {
                visibleCategory = cardCategory;
            }
        });

        document.querySelectorAll(".row[id^='productList']").forEach(section => {
            const sectionCategory = section.id.replace("productList", "").toLowerCase();
            section.style.display = (visibleCategory && sectionCategory === visibleCategory) ? "flex" : "none";
        });

        document.querySelectorAll(".section-header").forEach(header => {
            const headerCategory = header.getAttribute("data-category");
            header.style.display = (visibleCategory && headerCategory === visibleCategory) ? "block" : "none";
        });
    }

    searchForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const keyword = searchInput.value.trim();
        searchProducts(keyword);
    });

    filterButtons.forEach(button => {
        button.addEventListener("click", () => {
            const category = button.getAttribute("data-category");
            filterProducts(category);

            filterButtons.forEach(btn => btn.classList.remove("selected-category"));
            button.classList.add("selected-category");
        });
    });

    filterProducts("all");

    // 🔐 Add Product login check
    if (addProductBtn) {
        addProductBtn.addEventListener("click", function (e) {
            const isLoggedIn = addProductBtn.getAttribute("data-loggedin") === "true";
            if (!isLoggedIn) {
                e.preventDefault();
                alert("Please log in to add products.");
            }
        });
    }

    // 🔐 Check login for all Add to Cart & Buy Now buttons
    document.querySelectorAll(".add-to-cart, .buy-now").forEach(button => {
        button.addEventListener("click", function (e) {
            const isLoggedIn = button.getAttribute("data-loggedin");
            if (isLoggedIn === "false") {
                e.preventDefault();
                alert("Please log in to perform this action.");
                // window.location.href = "login.php"; // Optional redirect
            }
        });
    });
});

// 🛒 Cart functionality
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("add-to-cart")) {
        const button = event.target;

        // Extract product details
        let productName = button.getAttribute("data-name");
        let productPrice = button.getAttribute("data-price");
        let productImage = button.getAttribute("data-image");

        // Fallback to card details
        if (!productName || !productPrice || !productImage) {
            const card = button.closest(".product-card");
            productName = card.querySelector(".card-title").textContent.trim();
            productPrice = card.querySelector(".card-text").textContent.replace("Price:", "").replace("Rs.", "").trim();
            productImage = card.querySelector("img").src;
        }

        const cartItem = {
            name: productName,
            price: productPrice,
            image: productImage
        };

        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        cart.push(cartItem);
        localStorage.setItem("cart", JSON.stringify(cart));

        alert(`${productName} has been added to your cart!`);
    }
});
