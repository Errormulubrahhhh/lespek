// Merchandise data
const merchandiseItems = [
    {
        id: 1,
        name: "Topi INDICAKOPI",
        description: "Topi eksklusif logo INDICAKOPI. snapback indica BW.",
        price: "IDR 65K",
        priceNum: 65000,
        image: "../img/merch/topi.jpg",
        images: ["../img/merch/topi.jpg", "../img/merch/topi1.jpg", "../img/merch/topi2.jpg"],
        category: "Apparel",
        detailedDescription: "",
        features: [
            "Material: Rafel daimaru",
            "Ukuran: All size (adjustable)",
            "Unisex"
        ],
        stock: 50,
        colors: ["Hitam"]
    },
    {
        id: 2,
        name: "Coffee Bean Mandailing",
        description: "Biji kopi speacialty, Roasted. Kemasan 250gr.",
        price: "IDR 97K",
        priceNum: 97000,
        image: "../img/merch/mandailing.jpg",
        images: ["../img/merch/mandailing.jpg", "../img/merch/mandailing.jpg", "../img/merch/mandailing.jpg"],
        category: "Coffee",
        detailedDescription: "",
        features: [
            "Origin: Mandailing, North Sumatra",
            "Processing Method: Semi Wash",
            "Kemasan 250gr",
            "Roast Level: Medium to Dark",
            "Flavor Notes: Chocolate, Spict with subtle Floral hints"
        ],
        stock: 30,
        roastLevels: ["Medium to Dark"]
    },
    {
        id: 3,
        name: "Coffee Bean Ijen Raung ",
        description: "Biji kopi speacialty, Roasted. Kemasan 250gr.",
        price: "IDR 84.5K",
        priceNum: 84500,
        image: "../img/merch/ijenraung.jpg",
        images: ["../img/merch/ijenraung.jpg", "../img/merch/ijenraung.jpg", "../img/merch/ijenraung.jpg"],
        category: "Coffee",
        detailedDescription: "",
        features: [
            "Origin: Ijen Raung, East Java",
            "Processing Method: Natural",
            "Kemasan 250gr",
            "Roast Level: Light to Medium",
            "Flavor Notes: Jack fruit, Brown Sugar, Caramel, Overall Sweetness"
        ],
        stock: 100,
        roastLevels: ["Light to Medium"]
    },
    {
        id: 4,
        name: "Dripp'ca",
        description: "Set untuk manual brewing, termasuk dripper.",
        price: "IDR 220K",
        priceNum: 220000,
        image: "../img/merch/dripper.jpg",
        images: ["../img/merch/dripper.jpg", "../img/merch/dripper.jpg", "../img/merch/dripper.jpg"],
        category: "Equipment",
        detailedDescription: "",
        features: [
            "Pour over hybrid(V60, Flat bottom, Original",
            "Material: Ceramic",
        ],
        stock: 20,
        includes: ["Pour over hybrid(V60, Flat bottom, Original"]
    },
    {
        id: 5,
        name: "Coffee Bean Bali Kintamani",
        description: "Biji kopi specialty, Roasted. Kemasan 250gr.",
        price: "IDR 98K",
        priceNum: 98000,
        image: "../img/merch/kintamani.jpg",
        images: ["../img/merch/kintamani.jpg", "../img/merch/kintamani.jpg", "../img/merch/kintamani.jpg"],
        category: "Coffee",
        detailedDescription: "",
        features: [
            "Origin: Kintamani Highlands, Bali",
            "Processing Method: Full Wash",
            "Kemasan 250gr",
            "Roast Level: Medium to Dark",
            "Flavor Notes: Fruity(Citrus, Orange, Tropical), Floral, Caramel, Chocolate, Honey, Clean cup"
        ],
        stock: 15,
        roastLevels: ["Medium to Dark"] 
    }
];

// Function to get merchandise by ID
function getMerchandiseById(id) {
    return merchandiseItems.find(item => item.id === parseInt(id));
}

// Function to get all merchandise
function getAllMerchandise() {
    return merchandiseItems;
}

// Function to render merchandise cards
function renderMerchandiseCards(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = merchandiseItems.map(item => `
        <div class="bg-[#1a1a1a] rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition duration-300">
            <img src="${item.image}" alt="${item.name}" class="w-full h-64 object-cover">
            <div class="p-6">
                <h3 class="text-white text-xl font-semibold mb-2">${item.name}</h3>
                <p class="text-gray-400 mb-4">${item.description}</p>
                <div class="flex justify-between items-center">
                    <span class="text-[#5d5d5d] text-xl font-bold">${item.price}</span>
                    <a href="merch-detail.html?id=${item.id}" class="bg-[#5d5d5d] text-white px-6 py-2 rounded-md font-semibold hover:bg-[#9a7349] transition duration-300">Lihat Detail</a>
                </div>
            </div>
        </div>
    `).join('');
}