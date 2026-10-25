let events = [];
let currentPage = 1;
const eventsPerPage = 3;


// Fetch JSON data
fetch("data/events.json")
    .then(response => response.json())
    .then(data => {

        events = data;

        displayEvents();

    })
    .catch(error => {

        console.log("Error loading events:", error);

    });


// Display events
function displayEvents() {

    let searchText =
        document.getElementById("searchInput").value.toLowerCase();

    let filterType =
        document.getElementById("filterType").value;

    let sortType =
        document.getElementById("sortEvents").value;


    // Search
    let filteredEvents = events.filter(event => {

        return event.title.toLowerCase().includes(searchText) ||
               event.description.toLowerCase().includes(searchText);

    });


    // Filter
    if (filterType !== "all") {

        filteredEvents = filteredEvents.filter(event => {

            return event.type === filterType;

        });

    }


    // Sorting
    if (sortType === "dateAsc") {

        filteredEvents.sort((a, b) =>
            new Date(a.date) - new Date(b.date)
        );

    }

    else if (sortType === "dateDesc") {

        filteredEvents.sort((a, b) =>
            new Date(b.date) - new Date(a.date)
        );

    }

    else if (sortType === "titleAsc") {

        filteredEvents.sort((a, b) =>
            a.title.localeCompare(b.title)
        );

    }

    else if (sortType === "titleDesc") {

        filteredEvents.sort((a, b) =>
            b.title.localeCompare(a.title)
        );

    }


    // Pagination
    let start =
        (currentPage - 1) * eventsPerPage;

    let end =
        start + eventsPerPage;

    let pageEvents =
        filteredEvents.slice(start, end);


    let container =
        document.getElementById("eventContainer");

    container.innerHTML = "";


    // Display cards
    pageEvents.forEach(event => {

        let eventCard = document.createElement("div");

        eventCard.className = "event";

        eventCard.innerHTML = `

            <h2>${event.title}</h2>

            <p>
                ${event.description}
            </p>

            <p>
                <b>Event Type:</b> ${event.type}
            </p>

            <p>
                <b>Date:</b> ${event.date}
            </p>

        `;

        container.appendChild(eventCard);

    });


    createPagination(filteredEvents.length);

}


// Pagination buttons
function createPagination(totalEvents) {

    let pagination =
        document.getElementById("pagination");

    pagination.innerHTML = "";


    let totalPages =
        Math.ceil(totalEvents / eventsPerPage);


    for (let i = 1; i <= totalPages; i++) {

        let button =
            document.createElement("button");

        button.textContent = i;

        if (i === currentPage) {

            button.classList.add("active-page");

        }

        button.onclick = function() {

            currentPage = i;

            displayEvents();

        };

        pagination.appendChild(button);

    }

}


// Search
document.getElementById("searchInput")
    .addEventListener("input", function() {

        currentPage = 1;

        displayEvents();

    });


// Filter
document.getElementById("filterType")
    .addEventListener("change", function() {

        currentPage = 1;

        displayEvents();

    });


// Sort
document.getElementById("sortEvents")
    .addEventListener("change", function() {

        currentPage = 1;

        displayEvents();

    });