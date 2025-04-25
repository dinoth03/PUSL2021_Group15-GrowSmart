// Shopping Cart Data
let cart = [];

function toggleCart() {
    document.getElementById("cart").classList.toggle("active");
}

function addToCart(productId) {
    const productElement = document.querySelector(`.product[data-id='${productId}']`);
    const productName = productElement.getAttribute("data-name");
    const productPrice = parseFloat(productElement.getAttribute("data-price"));

    let existingProduct = cart.find(item => item.id === productId);
    if (existingProduct) {
        existingProduct.quantity += 1;
    } else {
        cart.push({ id: productId, name: productName, price: productPrice, quantity: 1 });
    }
    updateCart();
}





function updateCart() {
    const cartItemsContainer = document.getElementById("cart-items");
    cartItemsContainer.innerHTML = "";
    let total = 0;
    let itemCount = 0;

    cart.forEach(item => {
        total += item.price * item.quantity;
        itemCount += item.quantity; // Count total items in cart
        let li = document.createElement("li");
        li.innerHTML = `
            <span>${item.name} - Rs.${item.price} x ${item.quantity}</span>
            <div>
                <button onclick="changeQuantity(${item.id}, -1)">➖</button>
                <button onclick="changeQuantity(${item.id}, 1)">➕</button>
                <button onclick="removeFromCart(${item.id})">❌</button>
            </div>
        `;
        cartItemsContainer.appendChild(li);
    });

    document.getElementById("cart-total").innerText = `Total: Rs.${total.toFixed(2)}`;
    document.getElementById("cart-count").innerText = itemCount; // 🔴 This updates the red circle
}






function changeQuantity(productId, change) {
    let product = cart.find(item => item.id === productId);
    if (product) {
        product.quantity += change;
        if (product.quantity <= 0) {
            removeFromCart(productId);
        }
    }
    updateCart();
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCart();
}

function checkout() {
    if (cart.length === 0) {
        alert("Your cart is empty!");
    } else {
        alert("Thank you for your purchase!");
        cart = [];
        updateCart();
        toggleCart();
    }
}









