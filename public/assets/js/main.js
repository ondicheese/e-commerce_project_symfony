
import { displayCompare, displayCart, displayWishlist, formatPrice, addActiveInClass } from './library.js';

window.onload = () =>{
    let mainContent
    
    
    mainContent = document.querySelector('.compare_container')
    let compare = JSON.parse(mainContent?.dataset?.compare || false)
    
    displayCompare(compare)

    /******************************* */
    
    
    mainContent = document.querySelector('.wishlist_content')
    let wishlist = JSON.parse(mainContent?.dataset?.wishlist || false)
    
    displayWishlist(wishlist)
    

     /******************************* */
    
    
    mainContent = document.querySelector('.cart_content')
    let cart = JSON.parse(mainContent?.dataset?.cart || false)

    const form = document.querySelector('.carrier-form form')
    const select = document.querySelector('.carrier-form select')

    let carriers = mainContent ? JSON.parse(mainContent.dataset.carriers) : false

    if (cart) {
        if (select) {
            select.innerHTML = ""
            let selected = ""
            carriers.forEach(carrier => {
                
                if (carrier.id === cart.carrier.id) {
                    console.log('yes')
                    select.innerHTML += `<option selected="${selected}" value="${ carrier.id }"> ${ carrier.name } (${ formatPrice((carrier.price)) }) </option>`
                } else {
                    console.log('no')
                    select.innerHTML += `<option value="${ carrier.id }"> ${ carrier.name } (${ formatPrice((carrier.price)) }) </option>`
                }
            })
        }
    }
    
    const handleSubmit = (event) => {
        event.preventDefault()
    }
    const handleChange = async (event) => {
        event.preventDefault()
        const carrierId = event.target.value

        if (carrierId) {
            const response = await fetch('/api/cart/update/carrier/' + carrierId)
            const result = await response.json()

            if (result.isSuccess) {
                const {data} = result
                displayCart(data)
            }
        }
    }

    form?.addEventListener('submit', handleSubmit)
    select?.addEventListener('change', handleChange)

    displayCart(cart)

    const pageNb = (mainContent?.dataset?.pagenb) || false
    addActiveInClass(pageNb)
}



