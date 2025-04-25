//voting
let votes = {};

document.addEventListener("DOMContentLoaded", () => {
    loadVotes();
    updateLeaderboard();
});

function vote(ProfileId) {
    // Count how many Profile with this ID are present
    const matchingProfile = document.querySelectorAll(`.Profile[data-id='${ProfileId}']`);
    
    // Increase vote count for this plantId
    if (!votes[ProfileId]) {
        votes[ProfileId] = 0;
    }

    votes[ProfileId]++;

    // Update all matching plants with the same ID
    matchingProfile.forEach(plant => {
        const voteSpan = plant.querySelector(".votes");
        if (voteSpan) {
            voteSpan.innerText = `Votes: ${votes[ProfileId]}`;
        }
    });

    saveVotes();
    updateLeaderboard();
}

function saveVotes() {
    localStorage.setItem("ProfileVotes", JSON.stringify(votes));
}

function loadVotes() {
  const storedVotes = localStorage.getItem("ProfileVotes");
  if (storedVotes) {
      votes = JSON.parse(storedVotes);
  }

  // Ensure all profiles start with vote count (even if 0)
  const allProfiles = document.querySelectorAll(".Profile");
  allProfiles.forEach(Profile => {
      const ProfileId = Profile.getAttribute("data-id");

      if (!votes[ProfileId]) {
          votes[ProfileId] = 0; // Initialize if not present
      }

      const voteSpan = Profile.querySelector(".votes");
      if (voteSpan) {
          voteSpan.innerText = `Votes: ${votes[ProfileId]}`;
      }
  });
}


function updateLeaderboard() {
    const leaderboard = document.getElementById("leaderboard-list");
    if (!leaderboard) return;

    leaderboard.innerHTML = "";
    const sortedProfile = Object.entries(votes).sort((a, b) => b[1] - a[1]);

    sortedProfile.forEach(([ProfileId, voteCount]) => {
        const Profile = document.querySelector(`.Profile[data-id='${ProfileId}']`);
        if (Profile) {
            const ProfileName = Profile.querySelector("h3").innerText;
            const listItem = document.createElement("li");
            listItem.innerText = `${ProfileName}: ${voteCount} votes`;
            leaderboard.appendChild(listItem);
        }
    });
}



// Dummy seller data
let sellers = [
    { name: "Dinoth", ratings: [5, 4, 4] },
    { name: "Dualj", ratings: [5, 5, 5, 4] },
    { name: "Amanda", ratings: [3, 4, 2] },
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
  