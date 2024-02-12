import React, { Component } from 'react';
import Slider from 'react-slick';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import '../App.css';

// class component for product
class Product extends Component {
    constructor(props) {
        super(props);
        this.state = {
            quantity: 0,
            size: '0',
            warning: ''
        };
    }

    // Function to handle quantity change
    handleQuantityChange = (amount) => {
        this.setState(prevState => {
            const newQuantity = prevState.quantity + amount;
            return { quantity: Math.max(1, Math.min(99, newQuantity)) }; // Ensure quantity is between 1 and 99
        });
    };

    // Function to handle size change
    handleSizeChange = (event) => {
        this.setState({ size: event.target.value });
    };

    // Function to handle adding product to cart
    handleAddToCart = () => {
        const { product, addToCart } = this.props;
        const { quantity, size } = this.state;

        if (quantity === 0) {
            this.setState({ warning: 'Please select a quantity.' });
            return;
        }

        if (size === '0') {
            this.setState({ warning: 'Please select a size.' });
            return;
        }

        const productToAdd = { ...product, quantity, size };
        addToCart(productToAdd);
        console.log(productToAdd.quantity, productToAdd.size, productToAdd.name, productToAdd.price);

        // Clear the warning
        this.setState({ warning: '' });
    };

    render() {
        const { product } = this.props;
        const { name, description, price, images } = product;
        const { quantity, size } = this.state;

        const settings = {
            dots: true,
            infinite: false,
            speed: 500,
            slidesToShow: 1,
            slidesToScroll: 1,
            centerMode: true,
            centerPadding: '30px',
        };

        return (
            <div className="product">
                <div className="product-images">
                    <Slider {...settings}>
                        {images.map((image, index) => (
                            <div key={index}>
                                <img
                                    className="product-image"
                                    src={process.env.PUBLIC_URL + image}
                                    alt={name}
                                    style={{ width: '300px', height: '250px', objectFit: 'cover' }}
                                />
                            </div>
                        ))}
                    </Slider>
                </div>
                <div className="product-details">
                    <br />
                    <h3>{name}</h3>
                    <p>{description}</p>
                    <p>Price: ${price}</p>
                    <div className="quantity-selector">
                        <label htmlFor="quantity">Quantity:&nbsp;</label>
                        <button className="quantity-button" onClick={() => this.handleQuantityChange(-1)}>-</button>
                        &nbsp;
                        <input type="text" className="quantity-input" value={quantity} readOnly />
                        &nbsp;
                        <button className="quantity-button" onClick={() => this.handleQuantityChange(1)}>+</button>
                    </div>
                    <div>
                        <label htmlFor="size">Size (US):&nbsp;</label>
                        <select id="size" className="size-input" value={size} onChange={this.handleSizeChange}>
                            <option value="0">Select</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    {this.state.warning !== '' ? (
                        <p className="warning" style={{ color: 'red' }}>{this.state.warning}</p>
                    ) : (
                        <div><br /><br /> </div>
                    )}
                    <button onClick={this.handleAddToCart}>Add to Cart</button>
                </div>
            </div>
        );
    }
}

export default Product;
