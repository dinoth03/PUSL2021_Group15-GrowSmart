document.addEventListener("DOMContentLoaded", function () {
  // Filter and Search functionality
  const filterButtons = document.querySelectorAll(".filter-btn");
  const productCards = document.querySelectorAll(".product-card");
  const searchForm = document.getElementById("searchForm");
  const searchInput = document.getElementById("searchInput");

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

  // Default load all products
  filterProducts("all");

  // Handle Add to Cart functionality
  function handleAddToCart(event) {
    const button = event.currentTarget;

    const name = button.dataset.name;
    const price = button.dataset.price;
    const image = button.dataset.image;

    if (!name || !price || !image) {
      console.error("Missing product data:", { name, price, image });
      return;
    }

    const product = { name, price, image };

    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.push(product);
    localStorage.setItem("cart", JSON.stringify(cart));

    alert(`${name} has been added to your cart!`);
  }

  // Attach add-to-cart handler for all buttons (including modals)
  document.querySelectorAll(".add-to-cart").forEach((button) => {
    button.addEventListener("click", handleAddToCart);
  });
});
