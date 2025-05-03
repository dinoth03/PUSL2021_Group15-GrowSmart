document.addEventListener("DOMContentLoaded", function () {
  const cartItemsContainer = document.getElementById("cartItems");
  const totalPriceElement = document.getElementById("totalPrice");
  const selectAllCheckbox = document.getElementById("selectAll");
  const removeAllButton = document.getElementById("removeAll");
  const checkoutButton = document.getElementById("checkout");

  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  function renderCart() {
    cartItemsContainer.innerHTML = "";
    if (cart.length === 0) {
      cartItemsContainer.innerHTML = "<p>Your cart is empty.</p>";
      totalPriceElement.textContent = "Rs.0.00";
      return;
    }

    cart.forEach((item, index) => {
      const cartItem = document.createElement("div");
      cartItem.className = "cart-item";
      cartItem.innerHTML = ` 
              <input type="checkbox" class="cart-checkbox" data-index="${index}">
              <img src="${item.image}" alt="${item.name}" class="cart-image">
              <span>${item.name}</span>
              <span>${item.price}</span>
              <button class="remove-item btn btn-remove" data-index="${index}">Remove</button>
          `;
      cartItemsContainer.appendChild(cartItem);
    });

    calculateTotal();
  }

  function calculateTotal() {
    const checkboxes = document.querySelectorAll(".cart-checkbox:checked");
    let total = 0;

    checkboxes.forEach((checkbox) => {
      const index = checkbox.getAttribute("data-index");
      const item = cart[index];
      if (item && item.price) {
        const price = parseFloat(item.price.replace("Rs.", "").trim());
        total += isNaN(price) ? 0 : price;
      }
    });

    totalPriceElement.textContent = `Rs.${total.toFixed(2)}`;
  }

  selectAllCheckbox.addEventListener("change", function () {
    const checkboxes = document.querySelectorAll(".cart-checkbox");
    checkboxes.forEach((checkbox) => (checkbox.checked = this.checked));
    calculateTotal();
  });

  removeAllButton.addEventListener("click", function () {
    const selectedItems = document.querySelectorAll(".cart-checkbox:checked");
    if (selectedItems.length === 0) {
      alert("No items selected to remove!");
      return;
    }

    const indexesToRemove = Array.from(selectedItems).map((item) =>
      parseInt(item.getAttribute("data-index"))
    );
    cart = cart.filter((_, index) => !indexesToRemove.includes(index));

    localStorage.setItem("cart", JSON.stringify(cart));
    renderCart();
    alert("Selected items have been removed!");
  });

  cartItemsContainer.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-item")) {
      const index = parseInt(e.target.getAttribute("data-index"));
      cart.splice(index, 1);
      localStorage.setItem("cart", JSON.stringify(cart));
      renderCart();
      alert("Item removed successfully!");
    }
  });

  cartItemsContainer.addEventListener("change", function (e) {
    if (e.target.classList.contains("cart-checkbox")) {
      calculateTotal();
    }
  });

  checkoutButton.addEventListener("click", function () {
    const checkboxes = document.querySelectorAll(".cart-checkbox:checked");
    const selectedItems = [];
    const indexesToRemove = [];

    let total = 0;

    checkboxes.forEach((checkbox) => {
      const index = parseInt(checkbox.getAttribute("data-index"));
      const item = cart[index];
      if (item) {
        selectedItems.push(item);
        indexesToRemove.push(index);
        total += parseFloat(item.price.replace("Rs.", "").trim());
      }
    });

    if (selectedItems.length === 0) {
      alert("No items selected for checkout!");
      return;
    }

    // Save selected items to localStorage for delivery page
    localStorage.setItem("checkout_items", JSON.stringify(selectedItems));

    // Remove selected items from cart
    cart = cart.filter((_, index) => !indexesToRemove.includes(index));
    localStorage.setItem("cart", JSON.stringify(cart));

    // Redirect to delivery page with total
    window.location.href = `delivery.php?total=${total.toFixed(2)}`;
  });

  renderCart();
});
