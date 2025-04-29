async function saveProduct(e) {
    e.preventDefault();
    
    showLoading();
    const productId = document.getElementById('productId').value;
    const isEdit = productId !== '';
    
    // Check for file upload first
    const imageFile = document.getElementById('productImage').files[0];
    let imagePath = null;
    
    if (imageFile) {
      // Upload the image first
      const formData = new FormData();
      formData.append('productImage', imageFile);
      
      if (isEdit) {
        formData.append('productId', productId);
      }
      
      try {
        const uploadResponse = await fetch('upload-image.php', {
          method: 'POST',
          body: formData
        });
        
        const uploadText = await uploadResponse.text(); // Get raw text first
        
        try {
          const uploadResult = JSON.parse(uploadText);
          
          if (uploadResult.status === 'success') {
            imagePath = uploadResult.file_path;
          } else {
            showAlert('Image upload error: ' + uploadResult.message, 'danger');
            hideLoading();
            return;
          }
        } catch (jsonError) {
          console.error('Invalid JSON response from upload:', uploadText);
          showAlert('Error processing server response for upload', 'danger');
          hideLoading();
          return;
        }
      } catch (error) {
        console.error('Error uploading image:', error);
        showAlert('Image upload failed. Please try again.', 'danger');
        hideLoading();
        return;
      }
    }
    
    // Now save the product with or without new image
    const productData = {
      id: isEdit ? parseInt(productId) : 0,
      name: document.getElementById('productName').value,
      category: document.getElementById('productCategory').value,
      price: parseFloat(document.getElementById('productPrice').value),
      stock: parseInt(document.getElementById('productStock').value),
      description: document.getElementById('productDescription').value,
      status: document.getElementById('productStatus').value
    };
    
    // Include image path if available
    if (imagePath) {
      productData.image = imagePath;
    }
    
    try {
      const action = isEdit ? 'update_product' : 'add_product';
      const response = await fetch(`product-api.php?action=${action}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(productData)
      });
      
      const responseText = await response.text();
      
      try {
        const data = JSON.parse(responseText);
        
        if (data.status === 'success') {
          showAlert(isEdit ? 'Product updated successfully' : 'Product added successfully');
          hideProductForm();
          loadProducts();
          loadProductStats();
        } else {
          showAlert('Error: ' + data.message, 'danger');
        }
      } catch (jsonError) {
        console.error('Invalid JSON response:', responseText);
        showAlert('Server returned invalid data. Check console for details.', 'danger');
      }
    } catch (error) {
      console.error('Error saving product:', error);
      showAlert('Error saving product. Please try again.', 'danger');
    } finally {
      hideLoading();
    }
  }