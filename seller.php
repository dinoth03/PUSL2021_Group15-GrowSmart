<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrowSmart - Seller Panel</title>
    <link rel="icon" type="image/png" href="Img/TitleLogo.png">
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
     <!--my css-->
     <link href="style.css" rel="stylesheet">
     <style>
         .mt-4 {
  background: #fff;
  box-shadow: --box-shadow;
  padding: 1rem 1rem;
  text-align: center;
  outline: var(--outline);
  outline-offset: -0.5rem;
    background-color: #fff;
    border: 1px solid white;
    width: 100%;
    padding: 10px 0px 10px 0px;
    border: 20px solid #ddd;
  }

  .btn-submit {
  background: #d5f3d5;
  border: 0.1rem solid #00ff00;
  align-content: center;
}
.btn-submit:hover {
  background: #00ff00;
  color: white;
}

.btn-back {
    background-color: rgb(220, 233, 247);
    border: 0.1rem solid dodgerblue;
  align-content: center;
}
.btn-back:hover {
    background-color: dodgerblue;
    color: white;
}

.container
{
    background-color:rgb(252, 252, 250);
    border: 5px solid #00ff00;
}

body{
    background: linear-gradient(to right, #e2e2e2, #d5ffdd);
}
     </style>
     </head>
     <body>



     <div class="container mt-5">
     <div class="header-4">
    <h2 class="mt-4">Seller Panel</h2>
     </div>
    <!-- Add Product Form -->
    <form id="addProductForm" action="controller/addProducts.php" method="post">
        <div class="row">
            <div class="col-md-6">
                <label for="productName">Product Name</label>
                <input type="text" name="productName" id="productName" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="productCategory">Category</label>
                <select id="productCategory" name="productCategory" class="form-control" required>
                    <option value="" disabled selected>Choose Category</option>
                    <option value="vegetables">Vegetables</option>
                    <option value="plants">Plants</option>
                    <option value="fruits">Fruits</option>
                    <option value="fertilizers">Fertilizers</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label for="productPrice">Price (Rs.)</label>
                <input type="number" name="productPrice" id="productPrice" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="productWeight">Weight (kg)</label>
                <input type="text" name="productWeight" id="productWeight" class="form-control" required>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <label for="productImage">Image URL</label>
                <input type="text" name="imageUrl" id="imageUrl" class="form-control" required>
            </div>
        </div>
        

        <button type="submit" class="btn btn-submit mt-3">Add Product</button>
        <button type="back" class="btn btn-back mt-3" type="button" onclick="window.location.href='shop.php';">Back</button>
        <br>
        <br>
        </div>
    
    </form>

    <br>
    <br>
    <!-- Manage Products -->
     <!--
    <h3 class="mt-5">Manage Products</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Weight</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="productList">
-->
            <!-- Dynamically filled rows -->
             <!--
        </tbody>
    </table>
-->
</div>



<!-- <script>
    document.addEventListener("DOMContentLoaded", function () {
    const addProductForm = document.getElementById("addProductForm");
    const productList = document.getElementById("productList");
    const indexContainer = document.querySelector("#indexContainer"); // Replace with your container selector
    let products = JSON.parse(localStorage.getItem("products")) || [];

    // Render Products in the Seller Panel
    function renderProductList() {
        productList.innerHTML = "";
        products.forEach((product, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${product.name}</td>
                <td>${product.category}</td>
                <td>Rs.${product.price}</td>
                <td>${product.weight} kg</td>
                <td>
                    <button class="btn btn-warning btn-sm edit-btn" data-index="${index}">Edit</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-index="${index}">Delete</button>
                </td>
            `;
            productList.appendChild(row);
        });
    }

    // Render Products on the Index Page
    function renderIndexPage() {
        indexContainer.innerHTML = "";
        products.forEach((product) => {
            const productCard = document.createElement("div");
            productCard.className = `col-md-3 product-card`;
            productCard.setAttribute("data-category", product.category);
            productCard.innerHTML = `
                <div class="card">
                    <img src="${product.image}" class="card-img-top" alt="${product.name}">
                    <div class="card-body">
                        <h5 class="card-title">${product.name}</h5>
                        <p>${product.weight} kg</p>
                        <p class="card-text">Price: Rs.${product.price}</p>
                        <button type="button" class="btn btn-primary">Add to Cart</button>
                        <button type="button" class="btn btn-success">Buy Now</button>
                    </div>
                </div>
            `;
            indexContainer.appendChild(productCard);
        });
    }

    // Add Product
    addProductForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const name = document.getElementById("productName").value;
        const category = document.getElementById("productCategory").value;
        const price = document.getElementById("productPrice").value;
        const weight = document.getElementById("productWeight").value;
        const image = URL.createObjectURL(document.getElementById("productImage").files[0]);

        products.push({ name, category, price, weight, image });
        localStorage.setItem("products", JSON.stringify(products));
        renderProductList();
        renderIndexPage();
        alert("Product added successfully!");
        addProductForm.reset();
    });

    // Edit or Delete Product
    productList.addEventListener("click", function (e) {
        const index = e.target.getAttribute("data-index");
        if (e.target.classList.contains("delete-btn")) {
            products.splice(index, 1);
            localStorage.setItem("products", JSON.stringify(products));
            renderProductList();
            renderIndexPage();
            alert("Product deleted successfully!");
        } else if (e.target.classList.contains("edit-btn")) {
            const product = products[index];
            document.getElementById("productName").value = product.name;
            document.getElementById("productCategory").value = product.category;
            document.getElementById("productPrice").value = product.price;
            document.getElementById("productWeight").value = product.weight;

            products.splice(index, 1);
            renderProductList();
        }
    });

    // Initialize
    renderProductList();
    renderIndexPage();
});

</script> -->
</body>
</html>


