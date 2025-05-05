<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GrowSmart - Checkout</title>
    <link rel="icon" type="image/png" href="Img/TitleLogo.png" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <!-- Stripe JS -->
    <script src="https://js.stripe.com/v3/"></script>
    
    <!-- PayPal Sandbox Configuration Load -->
    <!-- Include the PayPal JavaScript SDK -->
    <script src="https://www.paypal.com/sdk/js?client-id=AS8svvryxGm8WdNhTp-2NZJcHdVJvTfTEfQB6w7yij6sfRCsHd-6pBiHe61dv7UrQ_x04bcaZ-j_OkBo&currency=USD"></script>
    <!-- This is ABCCinemas App under ABCCinemas Sandbox Account -->
    <!-- Username & Password for the sandbox account of the site -->
    <!-- Email :- CinemasABC@gmail.com  /  Password :- ABC@cinemas -->
    
    <!--my css-->
    <link href="css/shopStyle.css" rel="stylesheet" />
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

        .section-1 {
            background-color: rgb(252, 252, 250);
            border: 5px solid #00ff00;
            padding: 20px;
            width: 1000px;
            margin: 0 auto;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(to right, #e2e2e2, #d5ffdd);
        }

        .form-section {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-continue {
            background: #d5f3d5;
            border: 0.1rem solid #00ff00;
        }

        .btn-continue:hover {
            background: #00ff00;
            color: white;
        }

        .btn-back {
            background-color: rgb(220, 233, 247);
            border: 0.1rem solid dodgerblue;
        }

        .btn-back:hover {
            background-color: dodgerblue;
            color: white;
        }

        .order-summary {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        #paymentMethods .form-check {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 5px;
        }

        #paymentMethods .form-check:hover {
            background-color: #f7f7f7;
        }

        #stripeCardFields {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 10px;
            background-color: #fafafa;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #00ff00;
            border-top: 5px solid #f3f3f3;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Stripe Elements custom styling */
        .StripeElement {
            box-sizing: border-box;
            height: 40px;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: white;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }

        .card-errors {
            color: #fa755a;
            font-size: 14px;
            margin-top: 8px;
        }

        .stripe-badge {
            margin-top: 10px;
            text-align: right;
        }

        .stripe-badge img {
            height: 24px;
        }

        #paypal-button-container {
            margin-top: 15px;
            min-height: 45px;
            padding: 10px; 
            border-radius: 4px;
        }

        .payment-logo {
            height: 24px;
            margin-left: 10px;
            vertical-align: middle;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (min-width: 768px) {
            .section-1 {
                width: 1000px;
            }
        }

        @media (max-width: 767px) {
            .section-1 {
                width: 95%;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Loading overlay for form submission -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <p class="mt-3">Processing your order...</p>
    </div>

    <br />
    <br />
    <div class="section-1">
        <div>
            <h2 class="mt-4">Checkout</h2>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <form id="checkoutForm" action="process_order.php" method="POST" class="needs-validation" novalidate>
                        <!-- Stripe token field (hidden) -->
                        <input type="hidden" id="stripeToken" name="stripeToken">
                        <!-- PayPal order details (hidden) -->
                        <input type="hidden" id="paypalOrderId" name="paypalOrderId">
                        <input type="hidden" id="paypalPayerId" name="paypalPayerId">
                        
                        <!-- Delivery Information -->
                        <div class="form-section">
                            <h3>Delivery Information</h3>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="firstName" class="form-label">First Name*</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                                    <div class="invalid-feedback">
                                        Please provide your first name.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="lastName" class="form-label">Last Name*</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" required>
                                    <div class="invalid-feedback">
                                        Please provide your last name.
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address*</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="form-text">Your receipt will be sent to this email address.</div>
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number*</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                                <div class="invalid-feedback">
                                    Please provide your phone number.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address*</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                                <div class="invalid-feedback">
                                    Please provide your delivery address.
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="city" class="form-label">City*</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                    <div class="invalid-feedback">
                                        Please provide your city.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="zipCode" class="form-label">ZIP/Postal Code*</label>
                                    <input type="text" class="form-control" id="zipCode" name="zipCode" required>
                                    <div class="invalid-feedback">
                                        Please provide your ZIP/postal code.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="form-section">
                            <h3>Payment Method</h3>
                            <div id="paymentMethods">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="creditcard" value="creditcard" checked>
                                    <label class="form-check-label" for="creditcard">
                                        Credit/Debit Card
                                        
                                    </label>
                                </div>
                                
                                <div id="stripeCardFields" class="mt-3 mb-3">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label for="card-element" class="form-label">Card Information*</label>
                                            <div id="card-element" class="form-control"></div>
                                            <div id="card-errors" class="card-errors" role="alert"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <p class="small text-muted">
                                                Your payment is securely processed by Stripe. Your card details are never stored on our servers.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                 
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="paypal" value="paypal">
                                    <label class="form-check-label" for="paypal">
                                        PayPal
                                        
                                    </label>
                                    <div id="paypal-button-container" style="display: none;"></div>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="cod" value="cod">
                                    <label class="form-check-label" for="cod">
                                        Cash on Delivery
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button class="btn btn-back" type="button" onclick="window.location.href='cart.php';">Back to Cart</button>
                            <button type="submit" class="btn btn-continue" id="completeOrder">Complete Order</button>
                        </div>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="col-md-4">
                    <div class="form-section">
                        <h3>Order Summary</h3>
                        <div id="orderItems" class="order-summary"></div>
                        <div class="summary-item" style="font-weight: bold;">
                            <span>Total:</span>
                            <span id="orderTotal"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Page loaded, PayPal SDK available:", typeof paypal !== 'undefined');
            
            // Replace with your Stripe publishable key
            const stripe = Stripe('pk_test_51QtROfQj3VpmfbLODDrqB2lIFQdoNXmrx93njiZbMZtpwgl6Jg7X3AGDofGrzGy7d4wEz2J1oaWVaJCtvjdbwk6u00EfkrgXS9');
            const elements = stripe.elements();
            
            // Create and mount the Stripe card Element
            const cardElement = elements.create('card', {
                style: {
                    base: {
                        color: '#32325d',
                        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                        fontSmoothing: 'antialiased',
                        fontSize: '16px',
                        '::placeholder': {
                            color: '#aab7c4'
                        }
                    },
                    invalid: {
                        color: '#fa755a',
                        iconColor: '#fa755a'
                    }
                }
            });
            
            cardElement.mount('#card-element');
            
            // Handle validation errors on the card Element
            cardElement.on('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
            
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const total = urlParams.get('total') || localStorage.getItem('checkoutTotal') || 'Rs.0.00';
            
            // Get selected items from localStorage
            const selectedItems = JSON.parse(localStorage.getItem('selectedItems') || '[]');
            const orderItemsContainer = document.getElementById('orderItems');
            const orderTotalElement = document.getElementById('orderTotal');
            
            // Display order total
            orderTotalElement.textContent = total;
            
            // Display selected items
            selectedItems.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'summary-item';
                itemElement.innerHTML = `
                    <span>${item.name}</span>
                    <span>${item.price}</span>
                `;
                orderItemsContainer.appendChild(itemElement);
            });
            
            // Payment method toggle
            const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
            const stripeCardFields = document.getElementById('stripeCardFields');
            const paypalButtonContainer = document.getElementById('paypal-button-container');
            const completeOrderButton = document.getElementById('completeOrder');

            paymentMethods.forEach(method => {
                method.addEventListener('change', function() {
                    console.log("Payment method changed to:", this.value);
                    
                    // Reset all payment method containers
                    stripeCardFields.style.display = 'none';
                    paypalButtonContainer.style.display = 'none';
                    completeOrderButton.style.display = 'block';
                    
                    // Show the selected payment method container
                    if (this.value === 'creditcard') {
                        stripeCardFields.style.display = 'block';
                    } else if (this.value === 'paypal') {
                        console.log("PayPal selected, initializing button...");
                        // Display PayPal button container
                        paypalButtonContainer.style.display = 'block';
                        completeOrderButton.style.display = 'none';
                        
                        // Clear current PayPal button and re-render
                        paypalButtonContainer.innerHTML = '';
                        setTimeout(renderPayPalButton, 100);
                    }
                });
            });

            // Render PayPal button
            function renderPayPalButton() {
                console.log("Rendering PayPal button...");
                // Get total amount from order summary
                const totalText = document.getElementById('orderTotal').textContent;
                const numericValue = totalText.replace(/[^0-9.]/g, '');
                const totalAmount = parseFloat(numericValue);
                
                console.log("Total amount:", totalAmount);
                
                if (isNaN(totalAmount) || totalAmount <= 0) {
                    paypalButtonContainer.innerHTML = '<p class="text-danger">Invalid order amount</p>';
                    return;
                }
                
                // Render the PayPal button using the provided code
                paypal.Buttons({
                    style: {
                        layout: 'horizontal'
                    },
                    
                    createOrder: function(data, actions) {
                        // Validate the form first
                        if (!validateForm()) {
                            return Promise.reject('Please fill in all required fields');
                        }
                        
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: totalAmount.toFixed(2) // Dynamic total from your cart
                                },
                                description: 'Purchase from GrowSmart'
                            }]
                        });
                    },
                    
                    onApprove: function(data, actions) {
                        // Show loading overlay
                        document.getElementById('loadingOverlay').style.display = 'flex';
                        
                        return actions.order.capture().then(function(details) {
                            // Alert on success (as per your code)
                            console.log('Transaction completed by ' + details.payer.name.given_name + '!');
                            
                            // Set PayPal order details in hidden fields
                            document.getElementById('paypalOrderId').value = data.orderID;
                            document.getElementById('paypalPayerId').value = details.payer.payer_id || '';
                            
                            // Get form data
                            const form = document.getElementById('checkoutForm');
                            const formData = new FormData(form);
                            
                            // Add PayPal-specific details and other order data
                            formData.append('paymentMethod', 'paypal');
                            formData.append('items', JSON.stringify(selectedItems));
                            formData.append('total', total);
                            formData.append('paypalDetails', JSON.stringify(details));
                            
                            // Submit the form to process_order.php
                            fetch('process_order.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Hide loading overlay
                                document.getElementById('loadingOverlay').style.display = 'none';
                                
                                if (data.success) {
                                    // Show success message
                                    alert('Transaction completed by ' + details.payer.name.given_name + '!');
                                    
                                    // Clear cart after successful order
                                    localStorage.removeItem('selectedItems');
                                    localStorage.removeItem('checkoutTotal');
                                    
                                    // Redirect to confirmation page
                                    window.location.href = 'order_confirmation.php?id=' + data.orderId + 
                                                      '&orderNumber=' + data.orderNumber + 
                                                      '&emailSent=' + (data.emailSent ? '1' : '0');
                                } else {
                                    alert('Error processing your order: ' + data.message);
                                }
                            })
                            .catch(error => {
                                // Hide loading overlay
                                document.getElementById('loadingOverlay').style.display = 'none';
                                console.error('Error:', error);
                                alert('There was an error processing your order. Please try again.');
                            });
                        });
                    },
                    
                    onError: function(err) {
                        console.error('PayPal error:', err);
                        alert('There was an error with PayPal. Please try another payment method or try again later.');
                    },
                    
                    onCancel: function() {
                        console.log('Transaction cancelled by user');
                    }
                }).render('#paypal-button-container');
                
                console.log("PayPal button render call completed");
            }
            
            // Function to validate the form
            function validateForm() {
                const form = document.getElementById('checkoutForm');
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    
                    // Scroll to the first invalid element
                    const firstInvalidElement = form.querySelector(':invalid');
                    if (firstInvalidElement) {
                        firstInvalidElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    return false;
                }
                return true;
            }
            
            // Form validation and submission
            const form = document.getElementById('checkoutForm');
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Validate form
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }
                
                const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
                
                // If PayPal is selected, inform user to use the PayPal button
                if (paymentMethod === 'paypal') {
                    alert('Please use the PayPal button to complete your payment.');
                    return;
                }
                
                // If credit card is selected, get Stripe token first
                if (paymentMethod === 'creditcard') {
                    // Show loading overlay
                    loadingOverlay.style.display = 'flex';
                    
                    try {
                        const result = await stripe.createToken(cardElement);
                        
                        if (result.error) {
                            // Handle errors
                            const errorElement = document.getElementById('card-errors');
                            errorElement.textContent = result.error.message;
                            loadingOverlay.style.display = 'none';
                            return;
                        }
                        
                        // Set the token in the hidden field
                        document.getElementById('stripeToken').value = result.token.id;
                    } catch (error) {
                        console.error('Stripe error:', error);
                        alert('There was an error processing your payment. Please try again.');
                        loadingOverlay.style.display = 'none';
                        return;
                    }
                }
                
                // Collect form data
                const formData = new FormData(form);
                formData.append('items', JSON.stringify(selectedItems));
                formData.append('total', total);
                
                // Submit the form via AJAX
                fetch('process_order.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    // Hide loading overlay
                    loadingOverlay.style.display = 'none';
                    
                    if (data.success) {
                        // Clear cart after successful order
                        localStorage.removeItem('selectedItems');
                        localStorage.removeItem('checkoutTotal');
                        
                        // Redirect to confirmation page with email status
                        window.location.href = 'order_confirmation.php?id=' + data.orderId + 
                                            '&orderNumber=' + data.orderNumber + 
                                            '&emailSent=' + (data.emailSent ? '1' : '0');
                    } else {
                        alert('Error processing your order: ' + data.message);
                    }
                })
                .catch(error => {
                    // Hide loading overlay
                    loadingOverlay.style.display = 'none';
                    
                    console.error('Error:', error);
                    alert('There was an error processing your order. Please try again.');
                });
            });
            
            // Basic form field validation
            const emailInput = document.getElementById('email');
            emailInput.addEventListener('input', function() {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(this.value) && this.value.length > 0) {
                    this.setCustomValidity('Please enter a valid email address');
                } else {
                    this.setCustomValidity('');
                }
            });
            
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function() {
                // Allow only numbers and some special characters
                this.value = this.value.replace(/[^\d\+\-\(\) ]/g, '');
                
                if (this.value.length < 10 && this.value.length > 0) {
                    this.setCustomValidity('Please enter a valid phone number');
                } else {
                    this.setCustomValidity('');
                }
            });
            
            // Check if PayPal SDK is loaded properly
            console.log("PayPal SDK check at end of script:", typeof paypal !== 'undefined');
        });
    </script>
</body>
</html>