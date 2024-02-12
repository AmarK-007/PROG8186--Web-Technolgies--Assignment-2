import React from 'react';
import Product from './Product';
import '../App.css';

// class component for home page
class HomePage extends React.Component {

    // Constructor to initialize state
    constructor(props) {
        super(props);
        this.state = {
            cart: [], // Initialize cart state
            total: 0 // Initialize total state
        };
    }

    // Function to add a product to the cart
    addToCart = (product) => {
        const updatedCart = [...this.state.cart, product];
        const total = this.calculateTotal(updatedCart);
        this.setState({ cart: updatedCart, total });
        console.log("Updated Cart:", updatedCart);

        console.log("Total:", total);
    };

    // Function to remove a product from the cart
    removeFromCart = (index) => {
        const { cart } = this.state;
        const updatedCart = [...cart.slice(0, index), ...cart.slice(index + 1)];
        const total = this.calculateTotal(updatedCart);
        this.setState({ cart: updatedCart, total });
    };

    // Function to calculate the total bill
    calculateTotal = (cart) => {
        return cart.reduce((total, product) => total + product.price * product.quantity, 0);
    };

    render() {
        // Hardcoded product data
        const products = [
            {
                name: 'Air Tennis Shoe',
                description: 'White Blue coloured Shoe for Men provides cushioning and support effect for the feet with Non Slip Sport Tennis, Gym, Walking Shoes Sneakers.',
                price: 46.99,
                images: [
                    '/images/img-white-blue-1.jpg',
                    '/images/img-white-blue-2.jpg',
                    '/images/img-white-blue-3.jpg',
                ]
            },
            {
                name: 'Air Running Shoe',
                description: 'Red coloured Shoe for Men extremely lightweight running shoes great for running, walking, fitness, jogging, outdoor sports, workout, and hiking.',
                price: 56.99,

                images: [
                    '/images/img-red-1.jpg',
                    '/images/img-red-2.jpg',
                    '/images/img-red-3.jpg',
                ]
            },
            {
                name: 'Air Walking Shoe',
                description: 'Yellow coloured Arch Support Designed shoes have good arch support with the memory foam insole and cloudfoam cushioning perfect for long walking.',
                price: 44.99,
                images: [
                    '/images/img-yellow-1.jpg',
                    '/images/img-yellow-2.jpg',
                    '/images/img-yellow-3.jpg',
                ]
            },
            {
                name: 'Air Athletic Shoe',
                description: 'Black coloured unisex shoe with double layered knitted fabric, which is lightweight, breathable and comfortable, keep your feet dry and cool.',
                price: 55.99,
                images: [
                    '/images/img-blue-1.jpg',
                    '/images/img-blue-2.jpg',
                    '/images/img-blue-3.jpg',
                ]
            },
            {
                name: 'Air Casual Shoe',
                description: 'Grey coloured unisex sneakers made from ultra light natural rubber material, flexible grooves, anti-skid and grip, adapts to any road condition',
                price: 40.99,
                images: [
                    '/images/img-grey-1.jpg',
                    '/images/img-grey-2.jpg',
                    '/images/img-grey-3.jpg',
                ]
            }
        ];

        return (
            <div className="App">
                <div className="webpage-content">
                    <h1><i>Welcome to Our Store!</i></h1>
                    <h3>AIR specializes in air-cushioned fashion sneakers. Because it is professional, it is excellent.</h3>
                    <p>At present, our products include men’s air-cushion shoes, women’s air-cushion shoes, children’s air-cushion shoes,men's casual shoes, men's memory foam walking shoes and other series.
                        <h4>Thanks to fans for their support, we will continue to update and optimize new styles to give everyone a better wearing experience.</h4>
                    </p>
                </div>
                <div className="products-container">
                    {products.map((product, index) => (
                        <Product key={index} product={product} addToCart={this.props.addToCart} />
                    ))}
                </div>
            </div>
        );
    }
}

export default HomePage;
