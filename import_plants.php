<?php
// Import plant data into the database

$server = "localhost";
$username = "root";
$password = "";
$db = "growsmartDB";

// Connect to MySQL
$conn = mysqli_connect($server, $username, $password, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Extract plant data directly from your JavaScript code
$plant_data = [
    "Indoor Plants" => [
        [
            "name" => "Snake Plant",
            "scientificName" => "Sansevieria trifasciata",
            "care" => [
                "sunlight" => "Low to bright indirect light",
                "watering" => "Every 2-3 weeks, allow soil to dry completely",
                "temperature" => "60-85°F (15-29°C)",
                "humidity" => "Tolerates low humidity",
                "toxicity" => "Mildly toxic to pets",
                "propagation" => "Leaf cuttings, division",
                "commonIssues" => "Root rot from overwatering"
            ],
            "benefits" => [
                "Excellent air purifier",
                "Removes toxins like formaldehyde",
                "Converts CO2 to oxygen at night"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Pothos",
            "scientificName" => "Epipremnum aureum",
            "care" => [
                "sunlight" => "Low to bright indirect light",
                "watering" => "Every 1-2 weeks, allow top inch of soil to dry",
                "temperature" => "65-85°F (18-29°C)",
                "humidity" => "Average household humidity",
                "toxicity" => "Toxic to pets",
                "propagation" => "Stem cuttings in water or soil",
                "commonIssues" => "Yellow leaves from overwatering"
            ],
            "benefits" => [
                "Removes indoor air pollutants",
                "Fast-growing and low maintenance",
                "Tolerant of neglect"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Peace Lily",
            "scientificName" => "Spathiphyllum spp.",
            "care" => [
                "sunlight" => "Low to medium indirect light",
                "watering" => "Weekly, keep soil moist but not soggy",
                "temperature" => "65-80°F (18-27°C)",
                "humidity" => "High humidity preferred",
                "toxicity" => "Toxic to pets and humans",
                "propagation" => "Division",
                "commonIssues" => "Brown tips from low humidity or over-fertilizing"
            ],
            "benefits" => [
                "Filters airborne toxins",
                "Produces white flowers",
                "Increases humidity"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "ZZ Plant",
            "scientificName" => "Zamioculcas zamiifolia",
            "care" => [
                "sunlight" => "Low to bright indirect light",
                "watering" => "Every 2-3 weeks, let soil dry completely",
                "temperature" => "60-75°F (16-24°C)",
                "humidity" => "Average",
                "toxicity" => "Toxic to pets and humans",
                "propagation" => "Leaf or stem cuttings, division",
                "commonIssues" => "Yellowing from overwatering"
            ],
            "benefits" => [
                "Highly drought tolerant",
                "Great for beginners",
                "Improves indoor air quality"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Spider Plant",
            "scientificName" => "Chlorophytum comosum",
            "care" => [
                "sunlight" => "Bright, indirect light",
                "watering" => "Weekly, allow soil to dry slightly",
                "temperature" => "65-75°F (18-24°C)",
                "humidity" => "Average to high",
                "toxicity" => "Non-toxic to pets",
                "propagation" => "Offsets (pups), division",
                "commonIssues" => "Brown tips from fluoride in water"
            ],
            "benefits" => [
                "Air-purifying",
                "Produces baby plants (spiderettes)",
                "Pet-friendly"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Aloe Vera",
            "scientificName" => "Aloe barbadensis miller",
            "care" => [
                "sunlight" => "Bright, indirect to direct light",
                "watering" => "Every 2-3 weeks, allow soil to dry",
                "temperature" => "55-80°F (13-27°C)",
                "humidity" => "Low to average",
                "toxicity" => "Toxic to pets",
                "propagation" => "Offsets (pups)",
                "commonIssues" => "Root rot from overwatering"
            ],
            "benefits" => [
                "Medicinal uses for skin",
                "Air purifier",
                "Low maintenance"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Rubber Plant",
            "scientificName" => "Ficus elastica",
            "care" => [
                "sunlight" => "Bright indirect light",
                "watering" => "Every 1-2 weeks, keep soil evenly moist",
                "temperature" => "60-75°F (16-24°C)",
                "humidity" => "Moderate to high",
                "toxicity" => "Toxic to pets",
                "propagation" => "Stem cuttings",
                "commonIssues" => "Leaf drop from changes in environment"
            ],
            "benefits" => [
                "Removes toxins",
                "Attractive glossy leaves",
                "Humidity booster"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Chinese Evergreen",
            "scientificName" => "Aglaonema spp.",
            "care" => [
                "sunlight" => "Low to medium indirect light",
                "watering" => "Weekly, allow top inch of soil to dry",
                "temperature" => "65-80°F (18-27°C)",
                "humidity" => "High humidity preferred",
                "toxicity" => "Toxic to pets",
                "propagation" => "Stem cuttings, division",
                "commonIssues" => "Leaf yellowing from overwatering"
            ],
            "benefits" => [
                "Tolerates low light",
                "Air-purifying",
                "Colorful foliage varieties"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Philodendron Heartleaf",
            "scientificName" => "Philodendron hederaceum",
            "care" => [
                "sunlight" => "Medium to bright indirect light",
                "watering" => "Every 1-2 weeks, allow soil to dry slightly",
                "temperature" => "65-80°F (18-27°C)",
                "humidity" => "Moderate to high",
                "toxicity" => "Toxic to pets",
                "propagation" => "Stem cuttings",
                "commonIssues" => "Leggy growth in low light"
            ],
            "benefits" => [
                "Beautiful trailing plant",
                "Low maintenance",
                "Improves indoor air"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Calathea",
            "scientificName" => "Calathea spp.",
            "care" => [
                "sunlight" => "Low to medium indirect light",
                "watering" => "Keep soil consistently moist but not soggy",
                "temperature" => "65-80°F (18-27°C)",
                "humidity" => "High humidity needed",
                "toxicity" => "Non-toxic to pets",
                "propagation" => "Division",
                "commonIssues" => "Crispy leaf edges from low humidity"
            ],
            "benefits" => [
                "Beautiful patterned leaves",
                "Pet-safe",
                "Prayer-like leaf movement"
            ],
            "difficulty" => "Moderate"
        ]
    ],
    "Outdoor Plants" => [
        [
            "name" => "Lavender",
            "scientificName" => "Lavandula angustifolia",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Once or twice a week, allow soil to dry between waterings",
                "temperature" => "60-80°F (15-27°C)",
                "humidity" => "Low humidity preferred",
                "toxicity" => "Non-toxic to humans and pets",
                "propagation" => "Stem cuttings, seeds",
                "commonIssues" => "Root rot from poor drainage"
            ],
            "benefits" => [
                "Attracts pollinators",
                "Aromatic and calming",
                "Repels pests like mosquitoes"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Rose",
            "scientificName" => "Rosa spp.",
            "care" => [
                "sunlight" => "Full sun (6+ hours daily)",
                "watering" => "1-2 times per week, deep watering",
                "temperature" => "55-80°F (13-27°C)",
                "humidity" => "Average",
                "toxicity" => "Non-toxic, but thorns can injure",
                "propagation" => "Cuttings, grafting",
                "commonIssues" => "Aphids, black spot, powdery mildew"
            ],
            "benefits" => [
                "Fragrant blooms",
                "Great for pollinators",
                "Edible petals (organic)"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Marigold",
            "scientificName" => "Tagetes spp.",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Once or twice a week, more in dry weather",
                "temperature" => "70-85°F (21-29°C)",
                "humidity" => "Tolerant of varying humidity",
                "toxicity" => "Non-toxic, some may cause skin irritation",
                "propagation" => "Seeds",
                "commonIssues" => "Spider mites, root rot from overwatering"
            ],
            "benefits" => [
                "Repels garden pests",
                "Bright, long-lasting flowers",
                "Easy to grow from seeds"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Hibiscus",
            "scientificName" => "Hibiscus rosa-sinensis",
            "care" => [
                "sunlight" => "Full sun to partial shade",
                "watering" => "Frequent in hot weather, keep soil moist",
                "temperature" => "60-90°F (15-32°C)",
                "humidity" => "High humidity preferred",
                "toxicity" => "Non-toxic to pets",
                "propagation" => "Stem cuttings",
                "commonIssues" => "Aphids, spider mites, yellowing leaves"
            ],
            "benefits" => [
                "Large, colorful flowers",
                "Attracts hummingbirds and butterflies",
                "Can be used in teas (some species)"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Jasmine",
            "scientificName" => "Jasminum officinale",
            "care" => [
                "sunlight" => "Full sun to partial shade",
                "watering" => "Regular watering, keep soil slightly moist",
                "temperature" => "60-75°F (15-24°C)",
                "humidity" => "Moderate to high",
                "toxicity" => "Non-toxic",
                "propagation" => "Cuttings, layering",
                "commonIssues" => "Pests like aphids, fungal leaf spots"
            ],
            "benefits" => [
                "Strong, sweet fragrance",
                "Beautiful white blooms",
                "Used in perfumes and teas"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Lantana",
            "scientificName" => "Lantana camara",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Once or twice a week",
                "temperature" => "60-95°F (15-35°C)",
                "humidity" => "Tolerates low to high humidity",
                "toxicity" => "Toxic to pets and livestock",
                "propagation" => "Cuttings, seeds",
                "commonIssues" => "Powdery mildew, root rot"
            ],
            "benefits" => [
                "Attracts butterflies",
                "Drought tolerant",
                "Long blooming season"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Oleander",
            "scientificName" => "Nerium oleander",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Moderate; drought tolerant once established",
                "temperature" => "65-90°F (18-32°C)",
                "humidity" => "Tolerates dry air",
                "toxicity" => "Highly toxic to humans and animals",
                "propagation" => "Cuttings",
                "commonIssues" => "Scale insects, oleander leaf scorch"
            ],
            "benefits" => [
                "Long blooming flowers",
                "Drought tolerant",
                "Good for hedges"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Geranium",
            "scientificName" => "Pelargonium spp.",
            "care" => [
                "sunlight" => "Full sun to partial shade",
                "watering" => "Allow soil to dry between waterings",
                "temperature" => "60-75°F (15-24°C)",
                "humidity" => "Average",
                "toxicity" => "Mildly toxic to pets",
                "propagation" => "Stem cuttings",
                "commonIssues" => "Botrytis, root rot, leaf spots"
            ],
            "benefits" => [
                "Bright, colorful blooms",
                "Repels some insects",
                "Easy to propagate"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Boxwood",
            "scientificName" => "Buxus sempervirens",
            "care" => [
                "sunlight" => "Full sun to partial shade",
                "watering" => "Weekly, deep watering in dry periods",
                "temperature" => "60-80°F (15-27°C)",
                "humidity" => "Average",
                "toxicity" => "Toxic to pets and humans",
                "propagation" => "Cuttings",
                "commonIssues" => "Boxwood blight, root rot"
            ],
            "benefits" => [
                "Great for topiary and hedges",
                "Evergreen foliage",
                "Low maintenance once established"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Bougainvillea",
            "scientificName" => "Bougainvillea spp.",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Infrequent deep watering",
                "temperature" => "65-95°F (18-35°C)",
                "humidity" => "Low to moderate",
                "toxicity" => "Mildly toxic",
                "propagation" => "Cuttings",
                "commonIssues" => "Leaf drop from overwatering or low light"
            ],
            "benefits" => [
                "Vibrant, papery bracts",
                "Fast-growing vine",
                "Drought tolerant once established"
            ],
            "difficulty" => "Moderate"
        ]
    ],
    "Vegetables" => [
        [
            "name" => "Tomato",
            "scientificName" => "Solanum lycopersicum",
            "care" => [
                "sunlight" => "Full sun (6-8 hours daily)",
                "watering" => "Regular, keep soil consistently moist",
                "temperature" => "70-85°F (21-29°C)",
                "humidity" => "Moderate",
                "toxicity" => "Leaves are toxic to pets and humans",
                "propagation" => "Seeds",
                "commonIssues" => "Blight, blossom-end rot, aphids"
            ],
            "benefits" => [
                "Rich in vitamins A and C",
                "Contains lycopene, an antioxidant",
                "Supports heart health"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Carrot",
            "scientificName" => "Daucus carota",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Moderate, keep soil moist but not soggy",
                "temperature" => "60-75°F (15-24°C)",
                "humidity" => "Average",
                "toxicity" => "Non-toxic",
                "propagation" => "Seeds",
                "commonIssues" => "Carrot flies, forked roots"
            ],
            "benefits" => [
                "High in beta-carotene (vitamin A)",
                "Good for eye health",
                "Supports immune system"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Spinach",
            "scientificName" => "Spinacia oleracea",
            "care" => [
                "sunlight" => "Full sun to partial shade",
                "watering" => "Regular, keep soil evenly moist",
                "temperature" => "50-75°F (10-24°C)",
                "humidity" => "Average",
                "toxicity" => "Non-toxic",
                "propagation" => "Seeds",
                "commonIssues" => "Bolting in hot weather, downy mildew"
            ],
            "benefits" => [
                "Rich in iron and folate",
                "Good for bone health",
                "Low in calories, high in fiber"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Cucumber",
            "scientificName" => "Cucumis sativus",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Frequent, keep soil consistently moist",
                "temperature" => "70-95°F (21-35°C)",
                "humidity" => "Moderate",
                "toxicity" => "Non-toxic",
                "propagation" => "Seeds",
                "commonIssues" => "Powdery mildew, cucumber beetles"
            ],
            "benefits" => [
                "Hydrating vegetable",
                "Aids in digestion",
                "Low in calories"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Bell Pepper",
            "scientificName" => "Capsicum annuum",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Moderate, water when top inch of soil dries",
                "temperature" => "70-85°F (21-29°C)",
                "humidity" => "Moderate",
                "toxicity" => "Non-toxic",
                "propagation" => "Seeds",
                "commonIssues" => "Blossom-end rot, aphids"
            ],
            "benefits" => [
                "High in vitamin C",
                "Good for immune support",
                "Variety of colors and flavors"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Lettuce",
            "scientificName" => "Lactuca sativa",
            "care" => [
                "sunlight" => "Full sun to partial shade",
                "watering" => "Regular, keep soil moist",
                "temperature" => "45-75°F (7-24°C)",
                "humidity" => "Moderate",
                "toxicity" => "Non-toxic",
                "propagation" => "Seeds",
                "commonIssues" => "Aphids, slugs, bolting in heat"
            ],
            "benefits" => [
                "Low in calories",
                "Rich in fiber",
                "Quick-growing leafy green"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Zucchini",
            "scientificName" => "Cucurbita pepo",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Consistent moisture, avoid wetting leaves",
                "temperature" => "70-90°F (21-32°C)",
                "humidity" => "Moderate",
                "toxicity" => "Non-toxic",
                "propagation" => "Seeds",
                "commonIssues" => "Powdery mildew, squash vine borers"
            ],
            "benefits" => [
                "High in vitamin C and potassium",
                "Good for digestion",
                "Very productive plant"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Radish",
            "scientificName" => "Raphanus sativus",
            "care" => [
                "sunlight" => "Full sun to partial shade",
                "watering" => "Regular, keep soil evenly moist",
                "temperature" => "50-75°F (10-24°C)",
                "humidity" => "Average",
                "toxicity" => "Non-toxic",
                "propagation" => "Seeds",
                "commonIssues" => "Root maggots, bolting"
            ],
            "benefits" => [
                "Quick to harvest (in 3-4 weeks)",
                "Good source of antioxidants",
                "Supports liver health"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Eggplant",
            "scientificName" => "Solanum melongena",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Moderate, consistent moisture",
                "temperature" => "70-85°F (21-29°C)",
                "humidity" => "Moderate",
                "toxicity" => "Leaves and unripe fruit toxic to pets",
                "propagation" => "Seeds, transplants",
                "commonIssues" => "Flea beetles, wilting from poor drainage"
            ],
            "benefits" => [
                "Rich in fiber and antioxidants",
                "Supports heart health",
                "Versatile in cooking"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Broccoli",
            "scientificName" => "Brassica oleracea var. italica",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Keep soil consistently moist",
                "temperature" => "60-70°F (15-21°C)",
                "humidity" => "Average",
                "toxicity" => "Non-toxic",
                "propagation" => "Seeds or transplants",
                "commonIssues" => "Cabbage worms, bolting in warm temps"
            ],
            "benefits" => [
                "Rich in vitamin C and K",
                "Anti-inflammatory properties",
                "Supports detoxification"
            ],
            "difficulty" => "Moderate"
        ]
    ],
    "Herbs" => [
        [
            "name" => "Basil",
            "scientificName" => "Ocimum basilicum",
            "care" => [
                "sunlight" => "Full sun (6+ hours)",
                "watering" => "Keep soil consistently moist",
                "temperature" => "70-90°F (21-32°C)",
                "humidity" => "Moderate",
                "toxicity" => "Non-toxic",
                "propagation" => "Seeds, stem cuttings",
                "commonIssues" => "Aphids, downy mildew"
            ],
            "benefits" => [
                "Rich in antioxidants",
                "Supports digestion",
                "Anti-inflammatory properties"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Mint",
            "scientificName" => "Mentha spp.",
            "care" => [
                "sunlight" => "Partial shade to full sun",
                "watering" => "Regular, keep soil moist",
                "temperature" => "55-70°F (13-21°C)",
                "humidity" => "High humidity preferred",
                "toxicity" => "Non-toxic",
                "propagation" => "Cuttings, runners",
                "commonIssues" => "Invasive growth, spider mites"
            ],
            "benefits" => [
                "Aids in digestion",
                "Soothes nausea and headaches",
                "Freshens breath"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Cilantro",
            "scientificName" => "Coriandrum sativum",
            "care" => [
                "sunlight" => "Full sun to partial shade",
                "watering" => "Regular, keep soil evenly moist",
                "temperature" => "50-85°F (10-29°C)",
                "humidity" => "Average",
                "toxicity" => "Non-toxic",
                "propagation" => "Seeds",
                "commonIssues" => "Bolts quickly in heat, aphids"
            ],
            "benefits" => [
                "Rich in vitamins A, C, and K",
                "Supports detoxification",
                "Antibacterial properties"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Parsley",
            "scientificName" => "Petroselinum crispum",
            "care" => [
                "sunlight" => "Full sun to partial shade",
                "watering" => "Keep soil consistently moist",
                "temperature" => "60-70°F (15-21°C)",
                "humidity" => "Average",
                "toxicity" => "Non-toxic",
                "propagation" => "Seeds",
                "commonIssues" => "Leaf spot, aphids"
            ],
            "benefits" => [
                "Rich in vitamin K",
                "Supports kidney function",
                "Freshens breath"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Thyme",
            "scientificName" => "Thymus vulgaris",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Low; let soil dry out between watering",
                "temperature" => "60-80°F (15-27°C)",
                "humidity" => "Low to moderate",
                "toxicity" => "Non-toxic",
                "propagation" => "Cuttings, division",
                "commonIssues" => "Root rot from overwatering"
            ],
            "benefits" => [
                "Antibacterial and antifungal",
                "Supports respiratory health",
                "Aromatic culinary herb"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Rosemary",
            "scientificName" => "Salvia rosmarinus",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Low; water when top inch of soil is dry",
                "temperature" => "60-80°F (15-27°C)",
                "humidity" => "Low",
                "toxicity" => "Non-toxic",
                "propagation" => "Cuttings",
                "commonIssues" => "Powdery mildew, root rot"
            ],
            "benefits" => [
                "Improves concentration",
                "Stimulates hair growth",
                "Antioxidant-rich"
            ],
            "difficulty" => "Moderate"
        ],
        [
            "name" => "Oregano",
            "scientificName" => "Origanum vulgare",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Low to moderate; allow soil to dry out",
                "temperature" => "60-80°F (15-27°C)",
                "humidity" => "Low to moderate",
                "toxicity" => "Non-toxic",
                "propagation" => "Cuttings, seeds",
                "commonIssues" => "Root rot, aphids"
            ],
            "benefits" => [
                "Strong antibacterial properties",
                "Rich in antioxidants",
                "Flavorful culinary herb"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Chives",
            "scientificName" => "Allium schoenoprasum",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Keep soil moist, but not soggy",
                "temperature" => "60-70°F (15-21°C)",
                "humidity" => "Average",
                "toxicity" => "Mildly toxic to pets",
                "propagation" => "Division, seeds",
                "commonIssues" => "Rust, aphids"
            ],
            "benefits" => [
                "Supports immune function",
                "Rich in vitamin C",
                "Adds mild onion flavor"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Sage",
            "scientificName" => "Salvia officinalis",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Low; allow soil to dry between waterings",
                "temperature" => "60-75°F (15-24°C)",
                "humidity" => "Low",
                "toxicity" => "Non-toxic",
                "propagation" => "Cuttings, division",
                "commonIssues" => "Powdery mildew, root rot"
            ],
            "benefits" => [
                "Improves memory and cognition",
                "Anti-inflammatory",
                "Used in teas and seasoning"
            ],
            "difficulty" => "Easy"
        ],
        [
            "name" => "Lemongrass",
            "scientificName" => "Cymbopogon citratus",
            "care" => [
                "sunlight" => "Full sun",
                "watering" => "Keep soil consistently moist",
                "temperature" => "70-95°F (21-35°C)",
                "humidity" => "High",
                "toxicity" => "Non-toxic",
                "propagation" => "Division",
                "commonIssues" => "Root rot, aphids"
            ],
            "benefits" => [
                "Used in herbal teas and cooking",
                "Aids digestion",
                "Repels insects naturally"
            ],
            "difficulty" => "Moderate"
        ]
    ],
    "Diagnosis" => [
        [
            "name" => "Type 2 Diabetes",
            "scientificName" => "Diabetes Mellitus Type 2",
            "care" => [
                "symptoms" => "Increased thirst, frequent urination, fatigue, blurred vision",
                "diagnosisMethod" => "Blood glucose tests (FPG, A1C), glucose tolerance test",
                "treatment" => "Lifestyle changes, oral medications, insulin therapy",
                "monitoring" => "Regular blood sugar monitoring, HbA1c tests every 3-6 months",
                "lifestyle" => "Healthy diet, regular exercise, weight management",
                "complications" => "Neuropathy, kidney disease, eye damage",
                "followUp" => "Every 3-6 months or as recommended"
            ],
            "benefits" => [
                "Manageable with early intervention",
                "Improved quality of life with proper control",
                "Reduces risk of complications"
            ],
            "difficulty" => "Chronic"
        ],
        [
            "name" => "Hypertension",
            "scientificName" => "High Blood Pressure",
            "care" => [
                "symptoms" => "Often asymptomatic; may include headaches, shortness of breath, nosebleeds",
                "diagnosisMethod" => "Blood pressure measurement (≥130/80 mmHg)",
                "treatment" => "Lifestyle modifications, antihypertensive medications",
                "monitoring" => "Regular BP checks, annual physicals",
                "lifestyle" => "Low-sodium diet, regular exercise, stress management",
                "complications" => "Heart attack, stroke, kidney failure",
                "followUp" => "Every 3-6 months or as advised"
            ],
            "benefits" => [
                "Easily monitored",
                "Manageable with treatment",
                "Prevents cardiovascular complications"
            ],
            "difficulty" => "Chronic"
        ],
        [
            "name" => "Asthma",
            "scientificName" => "Bronchial Asthma",
            "care" => [
                "symptoms" => "Shortness of breath, wheezing, coughing, chest tightness",
                "diagnosisMethod" => "Spirometry, peak flow testing",
                "treatment" => "Inhalers (bronchodilators, corticosteroids), allergy medications",
                "monitoring" => "Peak flow monitoring, symptom tracking",
                "lifestyle" => "Avoid triggers, regular checkups",
                "complications" => "Asthma attacks, respiratory failure (rare)",
                "followUp" => "Every 6-12 months or after flare-ups"
            ],
            "benefits" => [
                "Symptoms controllable with proper management",
                "Improved quality of life",
                "Effective medications available"
            ],
            "difficulty" => "Chronic"
        ],
        [
            "name" => "Depression",
            "scientificName" => "Major Depressive Disorder",
            "care" => [
                "symptoms" => "Persistent sadness, loss of interest, sleep/appetite changes, fatigue",
                "diagnosisMethod" => "Clinical evaluation, DSM-5 criteria, questionnaires (PHQ-9)",
                "treatment" => "Therapy (CBT), antidepressants, lifestyle changes",
                "monitoring" => "Regular mental health check-ins, therapy sessions",
                "lifestyle" => "Exercise, sleep hygiene, social support",
                "complications" => "Suicidal thoughts, chronic pain, work impairment",
                "followUp" => "Every 4-8 weeks during treatment"
            ],
            "benefits" => [
                "Treatable with therapy and medication",
                "Support systems and resources widely available",
                "Improves overall well-being"
            ],
            "difficulty" => "Mental Health"
        ],
        [
            "name" => "Migraine",
            "scientificName" => "Migraine with or without Aura",
            "care" => [
                "symptoms" => "Throbbing headache, nausea, sensitivity to light/sound, visual disturbances",
                "diagnosisMethod" => "Patient history, symptom tracking",
                "treatment" => "Pain relief (NSAIDs, triptans), preventive medications",
                "monitoring" => "Headache diary, follow-up with neurologist",
                "lifestyle" => "Avoid triggers, regular sleep, hydration",
                "complications" => "Chronic migraines, medication overuse headache",
                "followUp" => "As needed, especially with new symptoms"
            ],
            "benefits" => [
                "Preventive strategies reduce frequency",
                "Various treatment options available",
                "Improves quality of life with management"
            ],
            "difficulty" => "Neurological"
        ],
        [
            "name" => "Hypothyroidism",
            "scientificName" => "Underactive Thyroid",
            "care" => [
                "symptoms" => "Fatigue, weight gain, cold intolerance, depression, slow heart rate",
                "diagnosisMethod" => "TSH and T4 blood tests",
                "treatment" => "Daily thyroid hormone replacement (levothyroxine)",
                "monitoring" => "TSH tests every 6-12 months",
                "lifestyle" => "Healthy diet, regular medication",
                "complications" => "Goiter, heart problems, infertility",
                "followUp" => "Annually or as directed"
            ],
            "benefits" => [
                "Well-controlled with medication",
                "Minimal side effects",
                "Supports metabolism and energy"
            ],
            "difficulty" => "Chronic"
        ],
        [
            "name" => "Osteoarthritis",
            "scientificName" => "Degenerative Joint Disease",
            "care" => [
                "symptoms" => "Joint pain, stiffness, decreased range of motion, swelling",
                "diagnosisMethod" => "X-rays, MRI, physical exam",
                "treatment" => "Pain relievers, physical therapy, joint injections, surgery (severe cases)",
                "monitoring" => "Pain and mobility assessments",
                "lifestyle" => "Weight control, joint exercises, supportive devices",
                "complications" => "Chronic pain, reduced mobility",
                "followUp" => "Every 6-12 months"
            ],
            "benefits" => [
                "Pain can be managed effectively",
                "Lifestyle adjustments help reduce symptoms",
                "Improves mobility with treatment"
            ],
            "difficulty" => "Chronic"
        ],
        [
            "name" => "GERD",
            "scientificName" => "Gastroesophageal Reflux Disease",
            "care" => [
                "symptoms" => "Heartburn, regurgitation, chest pain, chronic cough",
                "diagnosisMethod" => "Endoscopy, pH monitoring, symptom history",
                "treatment" => "Antacids, PPIs, dietary changes",
                "monitoring" => "Symptom tracking, follow-up endoscopy (if needed)",
                "lifestyle" => "Avoid trigger foods, eat smaller meals, elevate head during sleep",
                "complications" => "Esophagitis, Barrett's esophagus",
                "followUp" => "As needed or if symptoms worsen"
            ],
            "benefits" => [
                "Symptoms manageable with lifestyle and medication",
                "Reduces risk of esophageal damage",
                "Improves quality of life"
            ],
            "difficulty" => "Digestive"
        ],
        [
            "name" => "Anemia",
            "scientificName" => "Iron Deficiency Anemia",
            "care" => [
                "symptoms" => "Fatigue, pale skin, shortness of breath, dizziness",
                "diagnosisMethod" => "CBC, iron panel, ferritin levels",
                "treatment" => "Iron supplements, dietary iron, treat underlying cause",
                "monitoring" => "Blood tests every 1-3 months until resolved",
                "lifestyle" => "Iron-rich diet (spinach, red meat), vitamin C intake",
                "complications" => "Heart strain, developmental delays in children",
                "followUp" => "As per physician's guidance"
            ],
            "benefits" => [
                "Easily diagnosed with blood test",
                "Treatable with iron and diet",
                "Full recovery possible"
            ],
            "difficulty" => "Treatable"
        ],
        [
            "name" => "Urinary Tract Infection (UTI)",
            "scientificName" => "Cystitis / Urinary Tract Infection",
            "care" => [
                "symptoms" => "Burning sensation while urinating, frequent urge, cloudy urine",
                "diagnosisMethod" => "Urinalysis, urine culture",
                "treatment" => "Antibiotics, increased fluid intake",
                "monitoring" => "Follow-up urine tests if recurrent",
                "lifestyle" => "Good hygiene, hydration, urinate after intercourse",
                "complications" => "Kidney infection, recurrent infections",
                "followUp" => "If symptoms persist or return"
            ],
            "benefits" => [
                "Quick diagnosis and treatment",
                "Usually resolves with antibiotics",
                "Preventable with good hygiene"
            ],
            "difficulty" => "Acute"
        ]
    ],
    "Plant Health" => [
        [
            "name" => "Healthy Plant Indicators",
            "care" => [
                "signs" => [
                    "Vibrant, green leaves",
                    "New growth",
                    "Strong stem structure",
                    "No visible pest damage"
                ],
                "tips" => [
                    "Regular fertilization",
                    "Proper watering",
                    "Adequate light",
                    "Good air circulation"
                ]
            ]
        ],
        [
            "name" => "Overwatering Signs",
            "care" => [
                "signs" => [
                    "Yellowing leaves",
                    "Wilting despite wet soil",
                    "Mushy stems or roots",
                    "Fungus gnats around soil"
                ],
                "tips" => [
                    "Allow soil to dry before watering",
                    "Ensure drainage holes are clear",
                    "Use well-draining potting mix",
                    "Water less frequently in cooler months"
                ]
            ]
        ],
        [
            "name" => "Underwatering Signs",
            "care" => [
                "signs" => [
                    "Crispy, dry leaf edges",
                    "Drooping leaves",
                    "Slow or no new growth",
                    "Soil pulling away from pot"
                ],
                "tips" => [
                    "Water deeply when topsoil is dry",
                    "Use moisture meter if unsure",
                    "Group plants to maintain humidity",
                    "Avoid drafts that dry out soil"
                ]
            ]
        ],
        [
            "name" => "Light Stress Symptoms",
            "care" => [
                "signs" => [
                    "Scorched or bleached leaves (too much light)",
                    "Leggy growth (too little light)",
                    "Faded leaf color",
                    "No flowering in bloomers"
                ],
                "tips" => [
                    "Rotate plants regularly",
                    "Match plant with suitable light level",
                    "Use grow lights in dark rooms",
                    "Shield sensitive plants from direct sun"
                ]
            ]
        ],
        [
            "name" => "Nutrient Deficiency",
            "care" => [
                "signs" => [
                    "Pale or yellowing leaves",
                    "Leaf discoloration or spots",
                    "Stunted growth",
                    "Leaf drop"
                ],
                "tips" => [
                    "Use balanced fertilizer monthly",
                    "Flush soil to prevent salt buildup",
                    "Test soil pH if issues persist",
                    "Follow proper feeding schedule"
                ]
            ]
        ],
        [
            "name" => "Pest Infestation",
            "care" => [
                "signs" => [
                    "Sticky residue on leaves",
                    "Webbing between stems",
                    "Tiny visible insects (aphids, spider mites)",
                    "Distorted or curling leaves"
                ],
                "tips" => [
                    "Isolate affected plants",
                    "Spray with neem oil or insecticidal soap",
                    "Wipe leaves with damp cloth",
                    "Inspect regularly for early signs"
                ]
            ]
        ],
        [
            "name" => "Root Health",
            "care" => [
                "signs" => [
                    "Roots firm and white",
                    "No foul odor from soil",
                    "Steady plant growth",
                    "Stable plant when tugged gently"
                ],
                "tips" => [
                    "Repot every 1–2 years",
                    "Avoid compacted or soggy soil",
                    "Check roots during repotting",
                    "Use breathable pots (terracotta)"
                ]
            ]
        ],
        [
            "name" => "Fungal Issues",
            "care" => [
                "signs" => [
                    "White powdery coating",
                    "Black or brown leaf spots",
                    "Mold on soil surface",
                    "Sudden leaf drop"
                ],
                "tips" => [
                    "Increase air circulation",
                    "Avoid wetting leaves",
                    "Use fungicide if needed",
                    "Remove affected leaves promptly"
                ]
            ]
        ],
        [
            "name" => "Temperature Stress",
            "care" => [
                "signs" => [
                    "Leaf curling or browning",
                    "Slow or halted growth",
                    "Wilting during heatwaves",
                    "Frost damage on tips"
                ],
                "tips" => [
                    "Keep away from heaters or cold drafts",
                    "Maintain room temperature between 60-75°F (15-24°C)",
                    "Use a thermometer to monitor",
                    "Acclimate plants gradually when moving"
                ]
            ]
        ],
        [
            "name" => "Humidity Sensitivity",
            "care" => [
                "signs" => [
                    "Brown leaf tips or edges",
                    "Crisp, dry foliage",
                    "Curled leaves in tropical plants",
                    "Leaf drop in sensitive species"
                ],
                "tips" => [
                    "Group humidity-loving plants together",
                    "Use a pebble tray or humidifier",
                    "Misting (for non-fuzzy-leafed plants)",
                    "Avoid placing near vents"
                ]
            ]
        ]
    ]
];

// Import quiz questions
$quiz_questions = [
    [
        "question" => "What is the ideal sunlight for most indoor plants?",
        "options" => [
            "Direct sunlight all day",
            "Bright, indirect light",
            "Complete darkness",
            "Artificial light only"
        ],
        "correctAnswer" => 1
    ],
    [
        "question" => "How often should you water a typical houseplant?",
        "options" => [
            "Daily",
            "Once a month",
            "When the top inch of soil is dry",
            "Never"
        ],
        "correctAnswer" => 2
    ],
    [
        "question" => "Which plant is known for air purification?",
        "options" => [
            "Cactus",
            "Snake Plant",
            "Artificial Plant",
            "Palm Tree"
        ],
        "correctAnswer" => 1
    ],
    [
        "question" => "What does NPK stand for in fertilizers?",
        "options" => [
            "Natural Plant Kare",
            "Nitrogen, Phosphorus, Potassium",
            "New Plant Kit",
            "Nutrient Plant Kindness"
        ],
        "correctAnswer" => 1
    ],
    [
        "question" => "Which condition leads to root rot in most plants?",
        "options" => [
            "Too much sunlight",
            "High humidity",
            "Overwatering",
            "Cold temperatures"
        ],
        "correctAnswer" => 2
    ],
    [
        "question" => "What is propagation in plant care?",
        "options" => [
            "Applying fertilizer",
            "Pruning plants",
            "Creating new plants from existing ones",
            "Removing dead leaves"
        ],
        "correctAnswer" => 2
    ],
    [
        "question" => "Which of these plants requires the least water?",
        "options" => [
            "Fern",
            "Tomato",
            "Cactus",
            "Peace Lily"
        ],
        "correctAnswer" => 2
    ],
    [
        "question" => "What does 'indirect light' mean for plants?",
        "options" => [
            "No light at all",
            "Light not directly hitting the plant",
            "Light from artificial sources",
            "Noon sunlight"
        ],
        "correctAnswer" => 1
    ],
    [
        "question" => "Which nutrient helps plants develop strong roots?",
        "options" => [
            "Nitrogen",
            "Potassium",
            "Phosphorus",
            "Calcium"
        ],
        "correctAnswer" => 2
    ],
    [
        "question" => "What is the primary function of photosynthesis?",
        "options" => [
            "Water absorption",
            "Pest protection",
            "Converting light to energy",
            "Soil nutrition"
        ],
        "correctAnswer" => 2
    ]
];

// Check if tables exist, create them if they don't
$tableCreationQueries = [
    "CREATE TABLE IF NOT EXISTS plants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        scientific_name VARCHAR(100),
        category VARCHAR(50) NOT NULL,
        difficulty VARCHAR(50)
    )",
    
    "CREATE TABLE IF NOT EXISTS plant_care (
        id INT AUTO_INCREMENT PRIMARY KEY,
        plant_id INT,
        sunlight TEXT,
        watering TEXT,
        temperature TEXT,
        humidity TEXT,
        toxicity TEXT,
        propagation TEXT,
        common_issues TEXT,
        symptoms TEXT,
        diagnosis_method TEXT,
        treatment TEXT,
        monitoring TEXT,
        lifestyle TEXT,
        complications TEXT,
        follow_up TEXT,
        signs TEXT,
        tips TEXT,
        FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS plant_benefits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        plant_id INT,
        benefit TEXT NOT NULL,
        FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS plant_quiz (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question TEXT NOT NULL,
        option1 TEXT NOT NULL,
        option2 TEXT NOT NULL,
        option3 TEXT NOT NULL,
        option4 TEXT NOT NULL,
        correct_answer INT NOT NULL
    )"
];

foreach ($tableCreationQueries as $query) {
    if (!mysqli_query($conn, $query)) {
        die("Error creating table: " . mysqli_error($conn));
    }
}

// Clean existing data (optional - remove if you want to keep existing data)
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($conn, "TRUNCATE TABLE plant_benefits");
mysqli_query($conn, "TRUNCATE TABLE plant_care");
mysqli_query($conn, "TRUNCATE TABLE plant_quiz");
mysqli_query($conn, "TRUNCATE TABLE plants");
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

// Import plant data
$importCount = 0;
foreach ($plant_data as $category => $plants) {
    echo "<h3>Importing category: $category</h3>";
    
    foreach ($plants as $plant) {
        // Insert into plants table
        $name = mysqli_real_escape_string($conn, $plant['name']);
        $scientific_name = mysqli_real_escape_string($conn, $plant['scientificName'] ?? '');
        $difficulty = mysqli_real_escape_string($conn, $plant['difficulty'] ?? '');
        
        $sql = "INSERT INTO plants (name, scientific_name, category, difficulty) 
                VALUES ('$name', '$scientific_name', '$category', '$difficulty')";
        
        if (mysqli_query($conn, $sql)) {
            $plant_id = mysqli_insert_id($conn);
            $importCount++;
            
            echo "Imported: $name<br>";
            
            // Insert care data
            if (isset($plant['care'])) {
                $care = $plant['care'];
                
                // Handle both array of signs/tips and direct key-value pairs
                if (isset($care['signs']) && is_array($care['signs'])) {
                    // Special case for Plant Health category
                    $signs = mysqli_real_escape_string($conn, implode(", ", $care['signs']));
                    $tips = isset($care['tips']) ? mysqli_real_escape_string($conn, implode(", ", $care['tips'])) : '';
                    
                    $sql = "INSERT INTO plant_care (plant_id, common_issues, propagation) 
                            VALUES ($plant_id, '$signs', '$tips')";
                    mysqli_query($conn, $sql);
                } elseif (isset($care['symptoms'])) {
                    // Special case for Diagnosis category
                    $symptoms = mysqli_real_escape_string($conn, $care['symptoms'] ?? '');
                    $diagnosis_method = mysqli_real_escape_string($conn, $care['diagnosisMethod'] ?? '');
                    $treatment = mysqli_real_escape_string($conn, $care['treatment'] ?? '');
                    $monitoring = mysqli_real_escape_string($conn, $care['monitoring'] ?? '');
                    $lifestyle = mysqli_real_escape_string($conn, $care['lifestyle'] ?? '');
                    $complications = mysqli_real_escape_string($conn, $care['complications'] ?? '');
                    $follow_up = mysqli_real_escape_string($conn, $care['followUp'] ?? '');
                    
                    $sql = "INSERT INTO plant_care (plant_id, symptoms, diagnosis_method, treatment, monitoring, lifestyle, complications, follow_up)
                            VALUES ($plant_id, '$symptoms', '$diagnosis_method', '$treatment', '$monitoring', '$lifestyle', '$complications', '$follow_up')";
                    mysqli_query($conn, $sql);
                } else {
                    // Regular plant care data
                    $sunlight = mysqli_real_escape_string($conn, $care['sunlight'] ?? '');
                    $watering = mysqli_real_escape_string($conn, $care['watering'] ?? '');
                    $temperature = mysqli_real_escape_string($conn, $care['temperature'] ?? '');
                    $humidity = mysqli_real_escape_string($conn, $care['humidity'] ?? '');
                    $toxicity = mysqli_real_escape_string($conn, $care['toxicity'] ?? '');
                    $propagation = mysqli_real_escape_string($conn, $care['propagation'] ?? '');
                    $common_issues = mysqli_real_escape_string($conn, $care['commonIssues'] ?? '');
                    
                    $sql = "INSERT INTO plant_care (plant_id, sunlight, watering, temperature, humidity, toxicity, propagation, common_issues)
                            VALUES ($plant_id, '$sunlight', '$watering', '$temperature', '$humidity', '$toxicity', '$propagation', '$common_issues')";
                    mysqli_query($conn, $sql);
                }
            }
            
            // Insert benefits
            if (isset($plant['benefits']) && is_array($plant['benefits'])) {
                foreach ($plant['benefits'] as $benefit) {
                    $benefit_text = mysqli_real_escape_string($conn, $benefit);
                    $sql = "INSERT INTO plant_benefits (plant_id, benefit) VALUES ($plant_id, '$benefit_text')";
                    mysqli_query($conn, $sql);
                }
            }
        } else {
            echo "Error importing $name: " . mysqli_error($conn) . "<br>";
        }
    }
}

// Import quiz questions
$quizCount = 0;
foreach ($quiz_questions as $question) {
    $q = mysqli_real_escape_string($conn, $question['question']);
    $opt1 = mysqli_real_escape_string($conn, $question['options'][0]);
    $opt2 = mysqli_real_escape_string($conn, $question['options'][1]);
    $opt3 = mysqli_real_escape_string($conn, $question['options'][2]);
    $opt4 = mysqli_real_escape_string($conn, $question['options'][3]);
    $correct = $question['correctAnswer'];
    
    $sql = "INSERT INTO plant_quiz (question, option1, option2, option3, option4, correct_answer) 
            VALUES ('$q', '$opt1', '$opt2', '$opt3', '$opt4', $correct)";
    
    if (mysqli_query($conn, $sql)) {
        $quizCount++;
    } else {
        echo "Error importing quiz question: " . mysqli_error($conn) . "<br>";
    }
}

echo "<h2>Import completed!</h2>";
echo "<p>Successfully imported $importCount plants and $quizCount quiz questions.</p>";
echo "<p><a href='components/Virtual Assistant.php'>Go back to Virtual Assistant</a></p>";

mysqli_close($conn);
?>