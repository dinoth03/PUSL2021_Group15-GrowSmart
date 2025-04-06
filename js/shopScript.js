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
        document.addEventListener("DOMContentLoaded", function () {
            // Select all "Add to Cart" buttons
            const addToCartButtons = document.querySelectorAll(".add-to-cart");

            addToCartButtons.forEach(button => {
                button.addEventListener("click", function () {
                    // Determine the context: card or modal
                    const productId = this.getAttribute("data-product-id");
                    const isModal = productId.includes("Modal");

                    // Select the appropriate container (card or modal)
                    const container = isModal
                        ? document.querySelector(`#${productId}`)
                        : this.closest(".product-card");

                    // Extract product details
                    const productName = container.querySelector(".card-title").textContent;
                    const productPrice = container.querySelector(".card-text").textContent.replace("Price:", "").trim();
                    const productImage = container.querySelector("img").src;

                    // Create a cart item object
                    const cartItem = {
                        name: productName,
                        price: productPrice,
                        image: productImage,
                    };

                    // Retrieve existing cart from localStorage or initialize an empty array
                    let cart = JSON.parse(localStorage.getItem("cart")) || [];
                    cart.push(cartItem);

                    // Save updated cart back to localStorage
                    localStorage.setItem("cart", JSON.stringify(cart));

                    // Alert the user
                    alert(`${productName} has been added to your cart!`);
                });
            });
        });