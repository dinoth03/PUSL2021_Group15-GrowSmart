//voting
let votes = {};

document.addEventListener("DOMContentLoaded", () => {
    loadVotes();
    updateLeaderboard();
});

function vote(plantId) {
    // Count how many plants with this ID are present
    const matchingPlants = document.querySelectorAll(`.plant[data-id='${plantId}']`);
    
    // Increase vote count for this plantId
    if (!votes[plantId]) {
        votes[plantId] = 0;
    }

    votes[plantId]++;

    // Update all matching plants with the same ID
    matchingPlants.forEach(plant => {
        const voteSpan = plant.querySelector(".votes");
        if (voteSpan) {
            voteSpan.innerText = `Votes: ${votes[plantId]}`;
        }
    });

    saveVotes();
    updateLeaderboard();
}

function saveVotes() {
    localStorage.setItem("plantVotes", JSON.stringify(votes));
}

function loadVotes() {
    const storedVotes = localStorage.getItem("plantVotes");
    if (storedVotes) {
        votes = JSON.parse(storedVotes);
        Object.keys(votes).forEach(plantId => {
            const matchingPlants = document.querySelectorAll(`.plant[data-id='${plantId}']`);
            matchingPlants.forEach(plant => {
                const voteSpan = plant.querySelector(".votes");
                if (voteSpan) {
                    voteSpan.innerText = `Votes: ${votes[plantId]}`;
                }
            });
        });
    }
}

function updateLeaderboard() {
    const leaderboard = document.getElementById("leaderboard-list");
    if (!leaderboard) return;

    leaderboard.innerHTML = "";
    const sortedPlants = Object.entries(votes).sort((a, b) => b[1] - a[1]);

    sortedPlants.forEach(([plantId, voteCount]) => {
        const plant = document.querySelector(`.plant[data-id='${plantId}']`);
        if (plant) {
            const plantName = plant.querySelector("h3").innerText;
            const listItem = document.createElement("li");
            listItem.innerText = `${plantName}: ${voteCount} votes`;
            leaderboard.appendChild(listItem);
        }
    });
}






function addComment() {
    let commentInput = document.getElementById("comment-input");
    let commentText = commentInput.value.trim();
    if (commentText !== "") {
        let commentList = document.getElementById("comment-list");
        let newComment = document.createElement("li");
        newComment.textContent = commentText;
        commentList.appendChild(newComment);
        commentInput.value = "";
    }
}





// Dummy seller data
let sellers = [
    { name: "GreenGarden", ratings: [5, 4, 4] },
    { name: "NaturePro", ratings: [5, 5, 5, 4] },
    { name: "Plantify", ratings: [3, 4, 2] },
  ];
  
  // Calculate average rating
  function getAverage(ratings) {
    const total = ratings.reduce((a, b) => a + b, 0);
    return (total / ratings.length).toFixed(1);
  }
  
  // Display sellers
  function renderSellers() {
    const list = document.getElementById("sellerList");
    list.innerHTML = "";
  
    // Sort by average rating descending
    sellers.sort((a, b) => getAverage(b.ratings) - getAverage(a.ratings));
  
    sellers.forEach((seller, index) => {
      const card = document.createElement("div");
      card.className = "seller-card";
  
      card.innerHTML = `
        <h3>${seller.name}</h3>
        <p>Average Rating: ${getAverage(seller.ratings)} / 5</p>
        <div class="rating" data-index="${index}">
          ${[1, 2, 3, 4, 5]
            .map(
              (i) => `<span class="star" data-star="${i}">&#9733;</span>`
            )
            .join("")}
        </div>
      `;
  
      list.appendChild(card);
    });
  
    addRatingEvents();
  }
  
  // Handle star click
  function addRatingEvents() {
    document.querySelectorAll(".rating").forEach((ratingDiv) => {
      const sellerIndex = ratingDiv.getAttribute("data-index");
  
      ratingDiv.querySelectorAll(".star").forEach((star) => {
        star.addEventListener("click", () => {
          const starValue = parseInt(star.getAttribute("data-star"));
          sellers[sellerIndex].ratings.push(starValue);
          renderSellers();
        });
      });
    });
  }
  
  renderSellers();











  document.querySelectorAll('.stars').forEach(starGroup => {
    const stars = starGroup.querySelectorAll('.star');
    stars.forEach(star => {
      star.addEventListener('click', () => {
        const value = parseInt(star.dataset.value);
        stars.forEach(s => {
          s.classList.toggle('selected', parseInt(s.dataset.value) <= value);
        });

        // Optional: Store rating (locally or send to server)
        const sellerCard = star.closest('.seller-card');
        const sellerName = sellerCard.dataset.seller;
        console.log(`Rated ${value} stars for ${sellerName}`);

        // You can also update average rating display if needed
        const avgDisplay = sellerCard.querySelector(".avg-rating");
        avgDisplay.textContent = value.toFixed(1); // for demo purpose
      });
    });
  });
  