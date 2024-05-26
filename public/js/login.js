document.addEventListener("DOMContentLoaded", function() {
    const loginError = document.getElementById("loginError").value;
    const inputsContainer = document.getElementById("inputs-container");
    const addButton = document.getElementById("add-input");
    const inputTitle = document.querySelector(".input-title");
    const csrfToken = document.getElementById('csrfToken').value;
    let currentIndex = 0;

    const inputTypes = [
        { type: "text", placeholder: "Enter your email or username...", errorMessage: "Please enter a valid email or username", image: '/images/auth/login/1.png'},
        { type: "password", placeholder: "Enter your password...", errorMessage: "Please enter a password of at least 8 characters", image: '/images/auth/login/3.png' }
    ];


    DisplayInput(inputsContainer, 1);

    addButton.addEventListener("click", handleAddInput);

    function handleAddInput() {
        const inputContainer = document.getElementById("inputs-container");
        currentIndex = parseInt(addButton.dataset.currentIndex || "0");

        if (currentIndex < inputTypes.length - 1) {
            currentIndex++;
            addButton.dataset.currentIndex = currentIndex;

            const { type, placeholder, image } = inputTypes[currentIndex];
            const input = inputContainer.querySelector(".input");
            input.value = '';
            input.type = type;
            input.placeholder = placeholder;

            const inputImage = inputContainer.querySelector(".input-img");
            inputImage.src = image;

            updateText(currentIndex);
        } else {
            document.querySelector("form").submit();  // Submit the form when all inputs are complete
        }
    }

    function updateText(index) {
        const titles = ["Enter your credentials", "Enter your password"];
        document.querySelector(".input-title").textContent = titles[index];
    }

    function DisplayInput(container, itemsCount) {
        const products = inputTypes.slice(0, itemsCount);
        container.innerHTML = products.map((item, index) => {
            const { type, placeholder, image } = item;
            return (
                `<div class="input-lox">
                    <img class="input-img" src="${image}" alt="">
                </div>
                <div class="input-input">
                    <input type="${type}" class="input" placeholder="${placeholder}">
                    <input type="hidden" name="_token" value="${csrfToken}">
                </div>`
            );
        }).join('');
    }
});
