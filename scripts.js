document.getElementById('restaurant-select').addEventListener('change', function() {
    const restaurant = this.value;
    if (restaurant) {
        fetch(`get_menu.php?restaurant=${restaurant}`)
            .then(response => response.json())
            .then(data => {
                const menuContainer = document.getElementById('menu-container');
                menuContainer.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(dish => {
                        const dishDiv = document.createElement('div');
                        dishDiv.classList.add('dish');
                        dishDiv.innerHTML = `
                            <h3>${dish.dish_name}</h3>
                            <img src="${dish.image_url}" alt="${dish.dish_name}">
                            <p>${dish.description}</p>
                            <p>Pris: ${dish.price} NOK</p>
                            <button class="cart-button" onclick="addToCart('${dish.dish_name}', ${dish.price}, '${dish.image_url}')">Legg til i Handlekurv</button>
                        `;
                        menuContainer.appendChild(dishDiv);
                    });
                } else {
                    menuContainer.innerHTML = '<p>Ingen retter funnet for denne restauranten.</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('menu-container').innerHTML = '<p>Kunne ikke laste menyen. Vennligst prøv igjen senere.</p>';
            });
    } else {
        document.getElementById('menu-container').innerHTML = '';
    }
});

function addToCart(dishName, price, image) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existingDish = cart.find(item => item.dishName === dishName);
    if (existingDish) {
        existingDish.quantity += 1;
    } else {
        cart.push({ dishName, price, quantity: 1, image });
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    alert(`${dishName} er lagt til i handlekurven.`);
}
