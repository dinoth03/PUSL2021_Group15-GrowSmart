document.addEventListener("DOMContentLoaded", function () {
  const filterButtons = document.querySelectorAll(".filter-btn");
  const productCards = document.querySelectorAll(".product-card");
  const searchForm = document.getElementById("searchForm");
  const searchInput = document.getElementById("searchInput");
  const addProductBtn = document.getElementById("addProductBtn");

  function filterProducts(category) {
    const productSections = document.querySelectorAll(
      ".row[id^='productList']"
    );
    const headers = document.querySelectorAll(".section-header");

    productSections.forEach((section) => {
      const sectionCategory = section.id
        .replace("productList", "")
        .toLowerCase();
      section.style.display =
        category === "all" || sectionCategory === category ? "flex" : "none";
    });

    headers.forEach((header) => {
      const headerCategory = header.getAttribute("data-category");
      header.style.display =
        category === "all" || headerCategory === category ? "block" : "none";
    });

    productCards.forEach((card) => {
      const cardCategory = card.getAttribute("data-category");
      card.style.display =
        category === "all" || cardCategory === category ? "block" : "none";
    });
  }

  function searchProducts(keyword) {
    const lowerKeyword = keyword.toLowerCase();
    let visibleCategory = null;

    productCards.forEach((card) => {
      const productName = card.getAttribute("data-name").toLowerCase();
      const cardCategory = card.getAttribute("data-category");
      const matches = productName.includes(lowerKeyword);

      card.style.display = matches ? "block" : "none";

      if (matches && !visibleCategory) {
        visibleCategory = cardCategory;
      }
    });

    document.querySelectorAll(".row[id^='productList']").forEach((section) => {
      const sectionCategory = section.id
        .replace("productList", "")
        .toLowerCase();
      section.style.display =
        visibleCategory && sectionCategory === visibleCategory
          ? "flex"
          : "none";
    });

    document.querySelectorAll(".section-header").forEach((header) => {
      const headerCategory = header.getAttribute("data-category");
      header.style.display =
        visibleCategory && headerCategory === visibleCategory
          ? "block"
          : "none";
    });
  }

  searchForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const keyword = searchInput.value.trim();
    searchProducts(keyword);
  });

  filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const category = button.getAttribute("data-category");
      filterProducts(category);

      filterButtons.forEach((btn) => btn.classList.remove("selected-category"));
      button.classList.add("selected-category");
    });
  });

  filterProducts("all");

  function getCorrectImagePath(storedPath) {
    // Check if the path is a full URL (starts with http or https)
    if (storedPath.startsWith('http://') || storedPath.startsWith('https://')) {
        return storedPath;
    }
    
    // Extract just the filename from the stored path
    const filename = storedPath.split('/').pop();
    
    // Return the correct path
    return "admin/images/products/" + filename;
  }

  // Then update any code that uses product images
  document.querySelectorAll('.add-to-cart').forEach(button => {
      button.addEventListener('click', function() {
          const productName = this.getAttribute('data-name');
          const productPrice = this.getAttribute('data-price');
          let productImage = this.getAttribute('data-image');
          
          // Use the corrected image path
          productImage = getCorrectImagePath(productImage);
          
          // Rest of your code...
      });
  });

  // 🔐 Login check removed - buttons will work directly without login requirement
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
      productPrice = card
        .querySelector(".card-text")
        .textContent.replace("Price:", "")
        .replace("Rs.", "")
        .trim();
      productImage = card.querySelector("img").src;
    }

    const cartItem = {
      name: productName,
      price: productPrice,
      image: productImage,
    };

    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.push(cartItem);
    localStorage.setItem("cart", JSON.stringify(cart));

    alert(`${productName} has been added to your cart!`);
  }
});