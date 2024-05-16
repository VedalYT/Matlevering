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
                            <img src="${dish.image_url}" alt="${dish.dish_name}" width="200">
                            <p>${dish.description}</p>
                            <p>Pris: ${dish.price} NOK</p>
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
